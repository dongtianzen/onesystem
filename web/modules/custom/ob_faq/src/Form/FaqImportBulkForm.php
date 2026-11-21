<?php

declare(strict_types=1);

namespace Drupal\ob_faq\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Imports many FAQs from an uploaded JSON file.
 */
final class FaqImportBulkForm extends FaqImportFormBase {

  private const MAX_BYTES = 5 * 1024 * 1024;

  public function getFormId(): string {
    return 'ob_faq_import_bulk';
  }

  protected function inputName(): string {
    return 'json_file';
  }

  protected function buildInput(array &$form, FormStateInterface $form_state): void {
    $description = (string) $this->t('A JSON list of FAQs, or {"faqs": [...]}. Max 5 MB.');
    if ($loaded = $form_state->get('items')) {
      $description .= ' ' . $this->t('Loaded: @n items. Leave empty to import the loaded file.', ['@n' => count($loaded)]);
    }
    $form['json_file'] = [
      '#type' => 'file',
      '#title' => $this->t('JSON file'),
      '#description' => $description,
      '#attributes' => ['accept' => '.json,application/json'],
    ];
  }

  protected function readItems(FormStateInterface $form_state): array {
    $files = $this->getRequest()->files->get('files', []);
    $upload = $files['json_file'] ?? NULL;

    if ($upload instanceof UploadedFile) {
      if (!$upload->isValid()) {
        throw new \InvalidArgumentException($upload->getErrorMessage());
      }
      if (strtolower($upload->getClientOriginalExtension()) !== 'json') {
        throw new \InvalidArgumentException((string) $this->t('Please upload a .json file.'));
      }
      if ($upload->getSize() > self::MAX_BYTES) {
        throw new \InvalidArgumentException((string) $this->t('The file is larger than 5 MB.'));
      }
      return $this->importer->parseString((string) file_get_contents($upload->getRealPath()));
    }

    // No new upload: reuse the file loaded by the previous Preview.
    if ($items = $form_state->get('items')) {
      return $items;
    }
    throw new \InvalidArgumentException((string) $this->t('Please choose a JSON file.'));
  }

}
