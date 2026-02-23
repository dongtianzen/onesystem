<?php

namespace Drupal\dashchart\Controller;

use Drupal\Core\Controller\ControllerBase;

class CompatibilityController extends ControllerBase {

  public function page(): array {
    // 10 个示例产品（随便造的）
    $devices = [
      'ProView 8140',
      'ProView 8120',
      'Harmonic XOS',
      'ATEME Titan',
      'PBI StreamGate',
      'Imagine Selenio',
      'Grass Valley Edge',
      'Harley DecoderPro',
      'LiveU LU800',
      'OneBand RX200',
    ];

    // 依据你图里列的功能点，整理成“兼容性维度”
    $features = [
      'DVB-S/S2/S2X Input',
      'DVB-ASI Input',
      'IP Input',
      'IP Output',
      'SD/HD MPEG2/AVC Decode',
      'HD-SDI Output',
      'SD-SDI Output',
      'HDMI Output',
      'SRT Input',
      'BISS',
      'DVB-CI',
      '4:2:0 Sampling',
      'Web UI',
      'T2MI to MPEG-TS',
      'Redundancy (1+1)',
    ];

    // 兼容性矩阵值：0=No, 1=Partial, 2=Full
    // 这里随便造一些合理分布：传统解码器更偏 DVB/ASI/SDI，IP/SRT/Redundancy 不一定全支持。
    $matrix = [
      // ProView 8140（参考你截图内容：DVB-S/S2/S2X、ASI、IP、SDI/HDMI、SRT、BISS、DVB-CI、Web UI、T2MI、冗余）
      'ProView 8140' => [2,2,2,2,2,2,2,2,2,2,2,2,2,2,2],

      'ProView 8120' => [2,2,2,1,2,2,2,1,1,2,1,2,2,1,1],
      'Harmonic XOS' => [2,2,2,2,2,2,2,1,1,2,1,2,2,2,1],
      'ATEME Titan' => [1,1,2,2,2,1,1,0,2,1,0,2,2,0,2],
      'PBI StreamGate' => [1,2,1,1,1,2,2,1,0,1,0,1,2,0,0],
      'Imagine Selenio' => [2,1,2,2,2,2,2,2,1,2,1,2,2,1,1],
      'Grass Valley Edge' => [0,0,2,2,2,1,0,2,2,0,0,2,2,0,2],
      'Harley DecoderPro' => [2,2,1,1,2,2,2,1,0,2,2,2,2,2,1],
      'LiveU LU800' => [0,0,2,2,1,0,0,1,2,0,0,1,2,0,2],
      'OneBand RX200' => [1,1,2,2,2,1,1,1,2,1,0,2,2,1,2],
    ];

    // 组装成 matrix plugin 需要的点数据：x=设备索引, y=功能索引, v=0/1/2
    $points = [];
    foreach ($devices as $x => $device) {
      $vals = $matrix[$device] ?? [];
      foreach ($features as $y => $feature) {
        $v = isset($vals[$y]) ? (int) $vals[$y] : 0;
        $points[] = ['x' => $x, 'y' => $y, 'v' => $v];
      }
    }

    return [
      '#theme' => 'dashchart_compatibility',
      '#attached' => [
        'library' => ['dashchart/compatibility_matrix'],
        'drupalSettings' => [
          'dashchart' => [
            'compatibility' => [
              'devices' => $devices,
              'features' => $features,
              'points' => $points,
            ],
          ],
        ],
      ],
      '#cache' => [
        'contexts' => ['url.path', 'languages:language_interface'],
      ],
    ];
  }

}
