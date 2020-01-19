<?php

/**
 * @file
 * Contains \Drupal\flextable\Controller\FlextableController.
 */

namespace Drupal\flextable\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\flexpage\Content\FlexpageContentGenerator;

/**
 * An example controller.
 */
class FlextableController extends ControllerBase {

  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public function standardJson($section, $entity_id, $start, $end) {
    $object_content_data = $this->standardContentData($section, $entity_id, $start, $end);
    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * @param $entity_id is int or "all"
   */
  public function standardTable($section, $entity_id, $start, $end) {
    // load and use DashpageContent templage
    $FlexpageContentGenerator = new FlexpageContentGenerator();
    $markup = $FlexpageContentGenerator->angularSnapshotWrapper();

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * @param $entity_id is int or "all"
   */
  public function demoTable() {
    // load and use DashpageContent templage
    $FlexpageContentGenerator = new FlexpageContentGenerator();
    $markup = $FlexpageContentGenerator->angularSnapshotWrapper();
    $markup = 'demo table';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

}
