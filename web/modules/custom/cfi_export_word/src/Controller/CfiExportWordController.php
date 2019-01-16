<?php

/**
 * @file
 */
namespace Drupal\cfi_export_word\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class CfiExportWordController
 * @package Drupal\cfi_export_word\Controller
 */
class CfiExportWordController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function exportNodeToWord($entity_id = NULL) {
    $markup = 'Export Node to Word';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

}
