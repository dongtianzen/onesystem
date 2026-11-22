<?php

declare(strict_types=1);

namespace Drupal\ob_faq\Form;

use Drupal\Component\Utility\Html;
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
    "zh-hans": {
      "question": "LiveU LU800 支持 4K HDR 吗？",
      "answer": "支持。LiveU LU800 最高可传输 4Kp60 10-bit HDR 视频。"
    },
    "en": {
      "question": "Does the LiveU LU800 support 4K HDR?",
      "answer": "Yes. The LiveU LU800 transmits up to 4Kp60 10-bit HDR video."
    }
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

    $form['sample'] = [
      '#type' => 'details',
      '#title' => $this->t('Sample JSON (click to select, then copy)'),
      '#open' => TRUE,
      'code' => [
        '#type' => 'html_tag',
        '#tag' => 'pre',
        '#value' => Html::escape(self::EXAMPLE),
        '#attributes' => [
          'class' => ['ob-faq-sample'],
          'style' => 'user-select: all; white-space: pre-wrap; font-family: monospace; background: #f3f4f9; padding: 1em; border-radius: 4px;',
        ],
      ],
      'fields' => [
        '#theme' => 'item_list',
        '#title' => $this->t('Fields'),
        '#items' => [
          $this->t('<code>category</code>: FAQ category name, e.g. 产品, 技术 or 售后. Optional.'),
          $this->t('<code>status</code>: <code>draft</code> (unpublished, default) or <code>published</code>.'),
          $this->t('<code>translations</code>: <code>zh-hans</code> is required, <code>en</code> is optional. Each language needs <code>question</code> and <code>answer</code>. Answers can be plain text; separate paragraphs with a blank line.'),
          $this->t('<code>device_terms</code>: device term IDs or names, one or more, e.g. <code>[152]</code> or <code>["LiveU LU800"]</code>. Use the term ID when several devices share a name.'),
          $this->t('<code>products</code>: optional list of product page node IDs, e.g. <code>[250]</code>. If <code>device_terms</code> is omitted, the devices of these pages are used.'),
          $this->t('<code>nid</code>: optional. Updates that FAQ instead of creating a new one (tick "Update FAQs that already exist").'),
        ],
      ],
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
