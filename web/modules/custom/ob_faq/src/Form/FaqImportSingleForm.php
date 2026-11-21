<?php

declare(strict_types=1);

namespace Drupal\ob_faq\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Imports one FAQ from pasted JSON.
 */
final class FaqImportSingleForm extends FaqImportFormBase {

  private const EXAMPLE = <<<'JSON'
{
  "category": "技术",
  "status": "draft",
  "translations": {
    "zh-hans": {"question": "LiveU LU800 支持 SRT 吗？", "answer": "……"},
    "en": {"question": "Does the LiveU LU800 support SRT?", "answer": "……"}
  },
  "device_terms": ["LiveU LU800"]
}
JSON;

  public function getFormId(): string {
    return 'ob_faq_import_single';
  }

  protected function inputName(): string {
    return 'json';
  }

  protected function buildInput(array &$form, FormStateInterface $form_state): void {
    $form['json'] = [
      '#type' => 'textarea',
      '#title' => $this->t('FAQ JSON'),
      '#required' => TRUE,
      '#rows' => 16,
      '#attributes' => ['placeholder' => self::EXAMPLE, 'style' => 'font-family: monospace;'],
      '#description' => $this->t('One FAQ object. Add "nid" to update a specific FAQ. Use the Bulk tab for several FAQs.'),
    ];
  }

  protected function readItems(FormStateInterface $form_state): array {
    $items = $this->importer->parseString((string) $form_state->getValue('json'));
    if (count($items) !== 1) {
      throw new \InvalidArgumentException((string) $this->t('Found @n items. Paste exactly one, or use the Bulk tab.', ['@n' => count($items)]));
    }
    return $items;
  }

}
