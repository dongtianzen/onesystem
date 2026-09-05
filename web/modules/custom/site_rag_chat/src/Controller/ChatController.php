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
 * 处理前端聊天窗口发来的问答请求，转发给阿里云 OpenSearch LLM 智能问答版。
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

    // 可选：前端传来的会话 ID，用于多轮对话上下文（如目标 API 支持）。
    $session_id = is_array($payload) ? substr((string) ($payload['session_id'] ?? ''), 0, 64) : '';

    $endpoint = $config->get('api_endpoint');
    $api_key = $config->get('api_key');

    if (empty($endpoint) || empty($api_key)) {
      $this->logger->error('Site RAG Chat 未配置 API endpoint 或 API key。');
      return new JsonResponse(['error' => '服务尚未配置完成，请联系网站管理员。'], 500);
    }

    try {
      $answer = $this->callRagApi($endpoint, $api_key, $question, $session_id);
    }
    catch (GuzzleException $e) {
      $this->logger->error('调用 RAG API 失败: @message', ['@message' => $e->getMessage()]);
      return new JsonResponse(['error' => '智能助手暂时无法回答，请稍后再试。'], 502);
    }

    return new JsonResponse($answer);
  }

  /**
   * 实际调用阿里云 OpenSearch LLM 智能问答版接口。
   *
   * 注意：不同实例（Serverless / 专业版）返回的 JSON 结构可能略有差异，
   * 这里做了常见字段的兼容解析；请在联调时用浏览器开发者工具或
   * Postman 先看一次真实响应体，再按需调整下面 extractAnswer() 里的字段名。
   */
  protected function callRagApi(string $endpoint, string $api_key, string $question, string $session_id = ''): array {
    $body = [
      // 阿里云控制台的“问答测试”默认参数名一般是 query / question，
      // 具体以你实例的 API 文档为准，如不一致改这里即可。
      'query' => $question,
    ];
    if (!empty($session_id)) {
      $body['session_id'] = $session_id;
    }

    $response = $this->httpClient->request('POST', $endpoint, [
      'headers' => [
        'Content-Type' => 'application/json',
        // 常见两种鉴权方式，任选其一（按你实例的接入文档调整）：
        // 1) Bearer Token 风格：
        'Authorization' => 'Bearer ' . $api_key,
        // 2) 若实例要求专属 Header，可改成：
        // 'X-API-Key' => $api_key,
      ],
      'json' => $body,
      'timeout' => 30,
      'connect_timeout' => 5,
    ]);

    $raw = (string) $response->getBody();
    $data = json_decode($raw, TRUE);

    return $this->extractAnswer($data, $raw);
  }

  /**
   * 从阿里云返回的 JSON 里提取回答文本、参考链接等，做统一格式返回给前端。
   */
  protected function extractAnswer($data, string $raw): array {
    if (!is_array($data)) {
      return ['answer' => $raw ?: '（未获取到有效回答）'];
    }

    // 兼容几种常见返回结构；实际联调后按需精简。
    $answer = $data['result']['text']
      ?? $data['data']['answer']
      ?? $data['answer']
      ?? $data['message']
      ?? '（未能解析回答内容，请检查 API 返回结构）';

    $references = $data['result']['references']
      ?? $data['data']['references']
      ?? $data['references']
      ?? [];

    return [
      'answer' => $answer,
      'references' => $references,
    ];
  }

}
