<?php

namespace Drupal\site_rag_chat\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Flood\FloodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * 处理前端聊天窗口发来的问答请求，转发给阿里云百炼「知识问答」应用。
 */
class ChatController extends ControllerBase {

  const FLOOD_EVENT = 'site_rag_chat.ask';

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * @var \Drupal\Core\Flood\FloodInterface
   */
  protected $flood;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  public function __construct(ClientInterface $http_client, FloodInterface $flood, LoggerInterface $logger) {
    $this->httpClient = $http_client;
    $this->flood = $flood;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('flood'),
      $container->get('logger.factory')->get('site_rag_chat')
    );
  }

  /**
   * POST /site-rag-chat/ask
   */
  public function ask(Request $request) {
    $config = $this->config('site_rag_chat.settings');

    if (!$config->get('enabled')) {
      return new JsonResponse(['error' => '问答功能当前未启用。'], 503);
    }

    // --- 限流：按客户端 IP，防止被刷 ---
    $identifier = $request->getClientIp() ?: 'unknown';
    $limit = (int) $config->get('flood_limit') ?: 20;
    $window = (int) $config->get('flood_window') ?: 60;

    if (!$this->flood->isAllowed(self::FLOOD_EVENT, $limit, $window, $identifier)) {
      return new JsonResponse(['error' => '请求过于频繁，请稍后再试。'], 429);
    }
    $this->flood->register(self::FLOOD_EVENT, $window, $identifier);

    // --- 解析并校验输入 ---
    $payload = json_decode($request->getContent(), TRUE);
    $question = is_array($payload) ? trim((string) ($payload['question'] ?? '')) : '';

    if ($question === '') {
      return new JsonResponse(['error' => '问题不能为空。'], 400);
    }

    $max_length = (int) $config->get('max_question_length') ?: 300;
    if (mb_strlen($question) > $max_length) {
      return new JsonResponse(['error' => "问题过长，请控制在 {$max_length} 字以内。"], 400);
    }

    $endpoint = $config->get('api_endpoint');
    $api_key = $config->get('api_key');
    $agent_id = $config->get('agent_id');

    if (empty($endpoint) || empty($api_key) || empty($agent_id)) {
      $this->logger->error('Site RAG Chat 未配置完整（endpoint / api key / agent id）。');
      return new JsonResponse(['error' => '服务尚未配置完成，请联系网站管理员。'], 500);
    }

    try {
      $answer = $this->callRagApi($endpoint, $api_key, $agent_id, $question);
    }
    catch (GuzzleException $e) {
      $this->logger->error('调用 RAG API 失败: @message', ['@message' => $e->getMessage()]);
      return new JsonResponse(['error' => '智能助手暂时无法回答，请稍后再试。'], 502);
    }

    return new JsonResponse($answer);
  }

  /**
   * 实际调用阿里云百炼「知识问答」应用接口。
   *
   * 该接口返回的是 SSE（Server-Sent Events）流式数据，即便请求体里
   * 传 stream:false 也可能仍按流式返回（已通过控制台实测确认）。
   * Guzzle 默认的阻塞式请求会等整个响应结束后，把所有 SSE 帧拼成一个
   * 完整字符串放进 body，所以这里不需要手动处理长连接，只要在
   * extractAnswer() 里把这一长串文本按 SSE 格式解析、拼接回答片段即可。
   */
  protected function callRagApi(string $endpoint, string $api_key, string $agent_id, string $question): array {
    $body = [
      'input' => [
        'messages' => [
          ['role' => 'user', 'content' => $question],
        ],
      ],
      'parameters' => [
        'agent_options' => [
          'agent_id' => $agent_id,
        ],
      ],
      'stream' => TRUE,
    ];

    $response = $this->httpClient->request('POST', $endpoint, [
      'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $api_key,
      ],
      'json' => $body,
      // Agent 需要先检索、再逐步生成回答，比普通接口耗时更长，放宽超时。
      'timeout' => 60,
      'connect_timeout' => 5,
    ]);

    $raw = (string) $response->getBody();

    return $this->extractAnswer($raw);
  }

  /**
   * 解析百炼 Agent 接口返回的 SSE 流，拼出完整回答 + 参考来源。
   *
   * 每个 SSE 事件是一行 `data:{...json...}`，按 role 分三类：
   * - role=control：规划/工具调用阶段，跳过
   * - role=tool：工具（语义检索）返回的原始文档片段，从这里提取参考来源
   * - role=assistant：真正的回答内容，是分片流式吐出的，需要按顺序拼接
   */
  protected function extractAnswer(string $raw): array {
    // 兼容万一真的返回单个非流式 JSON 的情况（有 output.text 就直接用）。
    $single = json_decode($raw, TRUE);
    if (is_array($single) && isset($single['output']['text'])) {
      return ['answer' => $single['output']['text'], 'references' => []];
    }

    if (!preg_match_all('/^data:\s*(\{.*\})\s*$/m', $raw, $matches)) {
      return ['answer' => '（未能解析回答内容，返回格式不是预期的 SSE 流，请检查 extractAnswer()）', 'references' => []];
    }

    $answer = '';
    $references = [];

    foreach ($matches[1] as $json_line) {
      $event = json_decode($json_line, TRUE);
      if (!is_array($event)) {
        continue;
      }

      $message = $event['output']['choices'][0]['message'] ?? NULL;
      if (!$message) {
        continue;
      }

      $role = $message['role'] ?? '';

      // 真正的回答文字，按顺序拼接分片。
      if ($role === 'assistant' && isset($message['content'])) {
        $answer .= $message['content'];
      }

      // 工具（语义检索）返回的文档片段，从中提取参考来源。
      if ($role === 'tool') {
        $docs = $message['additional_kwargs']['extra_json']['docs'] ?? [];
        foreach ($docs as $doc) {
          $doc_id = $doc['doc_id'] ?? $doc['doc_name'] ?? NULL;
          if (!$doc_id || isset($references[$doc_id])) {
            // 用 doc_id 去重，同一篇文档的多个切片只保留一条引用。
            continue;
          }

          $title = $doc['doc_name'] ?? '';
          $content = $doc['content'] ?? '';

          // 我们导出内容时自己写入了「原文链接：https://...」这行，
          // 从中提取真实的 Drupal 文章 URL，而不是用阿里云临时的 OSS 下载链接。
          $url = '';
          if (preg_match('/原文链接[：:]\s*(https?:\/\/\S+)/u', $content, $url_match)) {
            $url = $url_match[1];
          }

          if ($url) {
            $references[$doc_id] = ['title' => $title, 'url' => $url];
          }
        }
      }
    }

    $answer = trim($answer);
    if ($answer === '') {
      $answer = '（未能获取到回答内容，请稍后重试或联系管理员）';
    }

    return [
      'answer' => $answer,
      'references' => array_values($references),
    ];
  }

}
