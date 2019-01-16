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
    $markup = t('Export Node to Word');
    $markup .= \Drupal::getContainer()->get('cfi_export_word.node.service')->demoPage();;

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

}
