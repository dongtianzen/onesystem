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

    // Creating the new document...
    $phpWord = new \PhpOffice\PhpWord\PhpWord();

    // Adding an empty Section to the document...
    $section = $phpWord->addSection();

    // node title
    $titleElement = $section->addText($entity->getTitle());
    $titleElement->setFontStyle($this->getFontStyleNodeTitle());

    // text break
    $section->addTextBreak();

    // node field
    $node_fields = $this->getNodeFieldList();
    if ($node_fields && is_array($node_fields)) {
      foreach ($node_fields as $node_field) {
        $section->addTextBreak();

        $fieldElement = $section->addText($this->getNodeFieldLabel($entity, $node_field));
        $fieldElement->setFontStyle($this->getFontStyleFieldLabel());

        $fieldElement = $section->addText($this->getNodeFieldValue($entity, $node_field));
        $fieldElement->setFontStyle($this->getFontStyleFieldValue());
      }
    }

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
   * @section Adding Text element to the Section having font style for Node Title
   */
  public function getFontStyleFieldLabel() {
    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setBold(true);
    $fontStyle->setName($this->getFontFamily());
    $fontStyle->setSize(14);

    return $fontStyle;
  }

  /**
   * @sectionAdding Text element with font customized using explicitly created font style object for Node Text Field
   */
  public function getFontStyleFieldValue() {
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

  /**
   * @return string
   */
  public function getNodeFieldList() {
    $output = array(
      'field_article_text',
      // 'field_article_long_text',
    );

    return $output;
  }

  /**
   * @return string
   */
  public function getNodeFieldLabel($entity, $node_field = NULL) {
    $output = $entity->{$node_field}->getFieldDefinition()->getLabel();
    $output .= ':';
    return $output;
  }

  /**
   * @return string
   */
  public function getNodeFieldValue($entity, $node_field = NULL) {
    $field_value = $entity->get($node_field)->getValue();
    $output = $field_value[0]['value'];
    dpm($output);
    return $output;
  }

}
