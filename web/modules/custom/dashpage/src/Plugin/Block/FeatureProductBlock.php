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
      '#product-details' => [
        [
          'icon' => '<svg ...>...</svg>', // 替换为 LiveU 的 SVG 图标
          'title' => 'LiveU 5G 直播解决方案',
          'description' => 'LiveU 提供基于 5G 网络的实时视频传输解决方案，支持多机位同步直播，适用于新闻、体育和大型活动。',
        ],
        [
          'icon' => '<svg ...>...</svg>', // 替换为 Harmonic 的 SVG 图标
          'title' => 'Harmonic 视频处理平台',
          'description' => 'Harmonic 提供高效能的视频处理和分发平台，支持 4K/8K 超高清视频流，适用于广电和流媒体服务。',
        ],
        [
          'icon' => '<svg ...>...</svg>', // 替换为 Arkora 的 SVG 图标
          'title' => 'Arkora 智能监控系统',
          'description' => 'Arkora 提供基于 AI 的智能监控解决方案，支持实时分析和预警，适用于安防和工业监控场景。',
        ],
        [
          'icon' => '<svg ...>...</svg>', // 替换为 Appear 的 SVG 图标
          'title' => 'Appear 视频传输设备',
          'description' => 'Appear 提供高性能的视频传输设备，支持低延迟、高可靠性的视频流传输，适用于广电和网络运营商。',
        ],
        [
          'icon' => '<svg ...>...</svg>', // 替换为 Ateme 的 SVG 图标
          'title' => 'Ateme 视频编码器',
          'description' => 'Ateme 提供先进的视频编码技术，支持 H.265/HEVC 编码，显著降低带宽需求，适用于流媒体和广电行业。',
        ],
        [
          'icon' => '<svg ...>...</svg>', // 替换为 ETL Systems 的 SVG 图标
          'title' => 'ETL Systems 卫星通信设备',
          'description' => 'ETL Systems 提供高性能的卫星通信设备，支持全球范围内的信号传输和接收，适用于广电和通信行业。',
        ],
      ],
      '#product-logos' => [
        ['src' => 'themes/custom/wanbo/images/product-logo/liveu_logo.png', 'alt' => 'Microsoft'],
        ['src' => 'themes/custom/wanbo/images/product-logo/clients-logo-2.svg', 'alt' => 'Office'],
        ['src' => 'themes/custom/wanbo/images/product-logo/clients-logo-3.svg', 'alt' => 'LinkedIn'],
        ['src' => 'themes/custom/wanbo/images/product-logo/clients-logo-4.svg', 'alt' => 'Google'],
        ['src' => 'themes/custom/wanbo/images/product-logo/clients-logo-5.svg', 'alt' => 'Facebook'],
      ],
      '#content' => $this->t('This is a custom product block.'),
    ];

    return $build;
  }

}
