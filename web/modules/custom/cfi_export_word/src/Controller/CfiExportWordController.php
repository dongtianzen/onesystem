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
   * @return A renderable array
   */
  public function exportNodeToWord($entity_id = NULL) {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    \Drupal::getContainer()->get('cfi_export_word.node.service')->exportWordPageFromEntityId($entity_id, $language);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $this->t('Export Node to Word'),
      '#cache' => ['max-age' => 0],
    );

    return $build;
  }

}
