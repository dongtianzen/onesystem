<?php

/**
 * @file
 * Contains \Drupal\flexpage\Controller\FlexdemoController.
 */

namespace Drupal\flexpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * An example controller.
 */
class FlexdemoController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function demoPage() {
    $build = array(
      '#type' => 'markup',
      '#markup' => t('Hello World!'),
    );
    return $build;
  }

}
