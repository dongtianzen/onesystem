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

    $node_title = $entity->getTitle();

    // Creating the new document...
    $phpWord = new \PhpOffice\PhpWord\PhpWord();

    // Adding an empty Section to the document...
    $section = $phpWord->addSection();

    // node title
    $fontStyle = $this->getFontStyleNodeTitle();
    $titleElement = $section->addText($node_title);
    $titleElement->setFontStyle($fontStyle);

    // node field
    $fontStyle = $this->getFontStyleNodeField();
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

  /**
   * @section Adding Text element to the Section having font style for Node Title
   */
  public function getFontStyleNodeTitle() {
    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setBold(true);
    $fontStyle->setName($this->getFontFamily());
    $fontStyle->setSize(24);

    return $fontStyle;
  }

  /**
   * @sectionAdding Text element with font customized using explicitly created font style object for Node Text Field
   */
  public function getFontStyleNodeField() {
    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setBold(false);
    $fontStyle->setName($this->getFontFamily());
    $fontStyle->setSize(12);

    return $fontStyle;
  }

  /**
   * @return string
   */
  public function getFontFamily() {
    $output = 'Arial';
    return $output;
  }

}
