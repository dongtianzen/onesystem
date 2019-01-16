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
  public function demoPage($entity_id) {
    $entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);
    if ($entity) {
      $this->initWordDocument($entity);
    }

    $output = 'From node';
    return $output;
  }

  /**
   *
   */
  public function initWordDocument($entity = NULL) {
    require_once \Drupal::moduleHandler()->getModule('cfi_export_word')->getPath() .'/vendor/autoload.php';

    $node_title = '"ccc Learn from yesterday, live for today, hope for tomorrow. '
            . 'The important thing is not to stop questioning." '
            . '(Albert Einstein)';

    // Creating the new document...
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $fontStyle = new \PhpOffice\PhpWord\Style\Font();

    $fontStyle->setBold(true);
    $fontStyle->setName('Arial');
    $fontStyle->setSize(12);

    // Adding an empty Section to the document...
    $section = $phpWord->addSection();

    // Adding Text element to the Section having font styled by default...
    $section->addText($field_text);

    // Adding Text element with font customized using explicitly created font style object...
    $fontStyle->setBold(true);
    $fontStyle->setName('Arial');
    $fontStyle->setSize(24);
    $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
    $myTextElement->setFontStyle($fontStyle);

    // Saving the document as OOXML file...
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment;filename="fromsite.docx"');

    // Saving the document as OOXML file...
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save('php://output');

    return;
  }

}
