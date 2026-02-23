<?php

namespace Drupal\dashchart\Controller;

use Drupal\Core\Controller\ControllerBase;

class DevicesController extends ControllerBase {

  public function page(): array {

    $data = [
      'labels' => ['CPU', 'RAM', 'Storage', 'IOPS', 'Network'],
      'values' => [75, 60, 85, 50, 70],
    ];

    return [
      '#theme' => 'dashchart_devices',
      '#attached' => [
        'library' => ['dashchart/chart'],
        'drupalSettings' => [
          'dashchart' => [
            'devices' => $data,
          ],
        ],
      ],
      '#cache' => [
        'contexts' => ['url.path'],
      ],
    ];
  }

}
