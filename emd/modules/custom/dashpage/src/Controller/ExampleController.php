<?php

/**
 * @file
 * Contains \Drupal\dashpage\Controller\ExampleController.
 */

namespace Drupal\dashpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * An example controller.
 */
class ExampleController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function flatpickrDemo() {
    $markup = '';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#theme' => 'example_flatpickr_demo',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::service('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function exampleJson($section, $entity_id) {
    $object_content_data[] = [1, 2];

    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

}
