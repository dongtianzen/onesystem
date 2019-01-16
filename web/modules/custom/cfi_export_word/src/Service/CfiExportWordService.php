<?php

/**
 * @file
 * Contains Drupal\cfi_export_word\Service\CfiExportWordService.php
 */
namespace Drupal\cfi_export_word\Service;

/**
 * CfiExportWord Service container.
 * \Drupal::getContainer()->get('cfi_export_word.node.service')->demoPage();
 */
class CfiExportWordService {

  public function demoPage() {
    $output = 'From node';

    return $output;
  }

}
