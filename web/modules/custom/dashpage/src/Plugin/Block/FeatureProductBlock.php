<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Custom Feature Block' block.
 *
 * @Block(
 *   id = "feature_product_block",
 *   admin_label = @Translation("Feature Product Block"),
 *   category = @Translation("Custom")
 * )
 */
class FeatureProductBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'feature_product_block',
      '#productlogos' => [
        [
          'src' => 'themes/custom/wanbo/images/product-logo/liveu_logo.png',
          'alt' => 'liveu_logo',
          'url' => '/taxonomy/term/10',
        ],
        [
          'src' => 'themes/custom/wanbo/images/product-logo/Phabrix_logo.jpg',
          'alt' => 'Phabrix_logo',
          'url' => '/taxonomy/term/23',
        ],
        [
          'src' => 'themes/custom/wanbo/images/product-logo/Harmonic_logo_1.png',
          'alt' => 'Harmonic_logo_1',
          'url' => '/taxonomy/term/27',
        ],
        [
          'src' => 'themes/custom/wanbo/images/product-logo/ATEME_logo.png',
          'alt' => 'ATEME_logo',
          'url' => '/taxonomy/term/134',
        ],
        [
          'src' => 'themes/custom/wanbo/images/product-logo/Appear_logo.png',
          'alt' => 'Appear_logo',
          'url' => '/taxonomy/term/15',
        ],
      ],
      '#details' => [
        [
          'title' => 'LiveU 5G背包产品目录 2025',
          'url' => '/node/LiveU背包',
          'description' => 'LiveU正在为新闻、赛事和企业塑造视频直播的未来。基于我们的全球领导地位和创新能力。',
        ],
        [
          'title' => '哈雷视频产品系列',
          'url' => '/node/哈雷视频产品',
          'description' => 'Harmonic哈雷视频产品目录-2025。',
        ],
        [
          'title' => 'Arkora 智能监控系统',
          'url' => '/taxonomy/term/291',
          'description' => 'Arkora 提供基于 AI 的智能监控解决方案，支持实时分析和预警，适用于安防和工业监控场景。',
        ],
        [
          'title' => 'Appear 视频传输设备',
          'url' => '/taxonomy/term/15',
          'description' => 'Appear 提供高性能的视频传输设备，支持低延迟、高可靠性的视频流传输，适用于广电和网络运营商。',
        ],
        [
          'title' => 'Ateme 视频编码器',
          'url' => '/taxonomy/term/134',
          'description' => 'Ateme 提供先进的视频编码技术，支持 H.265/HEVC 编码，显著降低带宽需求，适用于流媒体和广电行业。',
        ],
        [
          'title' => 'ETL Systems 卫星通信设备',
          'url' => '/taxonomy/term/168',
          'description' => 'ETL Systems 提供高性能的卫星通信设备，支持全球范围内的信号传输和接收，适用于广电和通信行业。',
        ],
      ],
      '#content' => $this->t('This is a custom product block.'),
    ];

    return $build;
  }

}
