<?php

namespace Drupal\site_rag_chat\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * 提供“站内智能问答”悬浮窗 Block。
 *
 * @Block(
 *   id = "site_rag_chat_block",
 *   admin_label = @Translation("站内智能问答窗口"),
 *   category = @Translation("Custom"),
 * )
 */
class RagChatBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('site_rag_chat.settings');

    if (!$config->get('enabled')) {
      return [];
    }

    return [
      '#theme' => 'site_rag_chat_widget',
      '#attached' => [
        'library' => ['site_rag_chat/chat_widget'],
        'drupalSettings' => [
          'siteRagChat' => [
            'endpoint' => \Drupal\Core\Url::fromRoute('site_rag_chat.ask')->toString(),
            'title' => $config->get('widget_title'),
            'welcomeMessage' => $config->get('welcome_message'),
            'maxLength' => (int) $config->get('max_question_length'),
          ],
        ],
      ],
      '#cache' => [
        'tags' => ['config:site_rag_chat.settings'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access site rag chat');
  }

}
