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

  /**
   *
   */
  public function demoPage() {
    $this->initWordDocument();

    $output = 'From node';
    return $output;
  }

  /**
   *
   */
  public function initWordDocument() {
    require \Drupal::moduleHandler()->getModule('cfi_export_word')->getPath() .'/vendor/autoload.php';

    // Creating the new document...
    $phpWord = new \PhpOffice\PhpWord\PhpWord();

    return;
  }

}
