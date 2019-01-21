<?php
/**
 * @file
 * Contains Drupal\cfi_export_word\Service\CfiExportWordService.php
 */

namespace Drupal\cfi_export_word\Service;

/**
 * CfiExportWord Service container.
 * \Drupal::getContainer()->get('cfi_export_word.node.service')->exportWordPageFromEntityId();
 */
class CfiExportWordService {

  /**
   *
   */
  public function exportWordPageFromEntityId($entity_id, $language = 'en') {
    $entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);

    return $this->exportWordPageFromEntity($entity, $language);
  }

  /**
   *
   */
  public function exportWordPageFromEntity($entity = NULL, $language = 'en') {
    if ($language && $language !== 'en') {
      $entity = $entity->getTranslation($language);
    }

    if ($entity) {
      $this->saveWordDocument($entity);
    }

    return;
  }

  /**
   *
   */
  public function saveWordDocument($entity = NULL, $doc_file_name = 'cfi.docx') {
    $phpWord = $this->generateWordDocument($entity);

    // Saving the document as OOXML file...
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment;filename="' . $doc_file_name . '"');
    header("Cache-Control: max-age=0");

    // Saving the document as OOXML file...
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save('php://output');

    return;
  }

  /**
   *
   */
  public function generateWordDocument($entity = NULL) {
    require_once \Drupal::moduleHandler()->getModule('cfi_export_word')->getPath() .'/vendor/autoload.php';

    // Creating the new document...
    $phpWord = new \PhpOffice\PhpWord\PhpWord();

    // set word default font
    $phpWord->setDefaultFontName($this->getFontFamily());
    $phpWord->setDefaultFontSize($this->getFontSize());

    $phpWord->addTitleStyle(1, array('name' => 'HelveticaNeueLT Std Med', 'size' => 32, 'color' => '000000')); //h1 styling

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
        if ($this->getNodeFieldValue($entity, $node_field)) {
          $section->addTextBreak();

          $fieldLabelElement = $section->addText($this->getNodeFieldLabel($entity, $node_field));
          $fieldLabelElement->setFontStyle($this->getFontStyleFieldLabel());

          $fieldValueElement = \PhpOffice\PhpWord\Shared\Html::addHtml(
            $section,
            $this->getNodeFieldValue($entity, $node_field),
            false,
            false
          );
        }
      }
    }

    return $phpWord;
  }

  /**
   *
   */
  public function getPhpWordStyleFont() {
    return new \PhpOffice\PhpWord\Style\Font();
  }

  /**
   * @section Adding Text element to the Section having font style for Node Title
   */
  public function getFontStyleNodeTitle() {
    $fontStyle = $this->getPhpWordStyleFont();
    $fontStyle->setBold(true);
    $fontStyle->setName($this->getFontFamily());
    $fontStyle->setSize(18);

    return $fontStyle;
  }

  /**
   * @section Adding Text element to the Section having font style for Field Label
   */
  public function getFontStyleFieldLabel() {
    $fontStyle = $this->getPhpWordStyleFont();
    $fontStyle->setBold(true);
    $fontStyle->setName($this->getFontFamily());
    $fontStyle->setSize(14);

    return $fontStyle;
  }

  /**
   * @section Adding Text element with font customized using explicitly created font style object for Node Text Field
   */
  public function getFontStyleFieldValue() {
    $fontStyle = $this->getPhpWordStyleFont();
    $fontStyle->setBold(false);
    $fontStyle->setName($this->getFontFamily());
    $fontStyle->setSize($this->getFontSize());

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
  public function getFontSize() {
    $output = 12;
    return $output;
  }

  /**
   * @return array, word file output fields
   */
  public function getNodeFieldList() {
    $output = array(
      'field_article_text',
      'field_article_long_text',
    );

    return $output;
  }

  /**
   *
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
    $output = NULL;

    $field_value = $entity->get($node_field)->getValue();
    if (isset($field_value[0]['value'])) {
      $output = $field_value[0]['value'];

      $output = str_replace("&nbsp;", " ", $output);

      // It Convert some not standard characters to HTML entities
      $output = htmlentities($output);
      // It convert HTML entities back to standard characters
      $output = html_entity_decode($output);
    }

    return $output;
  }

}
