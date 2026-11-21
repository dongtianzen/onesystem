<?php

declare(strict_types=1);

namespace Drupal\ob_faq\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\ob_faq\FaqImporter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Shared Preview / Import flow for the FAQ import forms.
 */
abstract class FaqImportFormBase extends FormBase {

  public function __construct(protected FaqImporter $importer) {}

  public static function create(ContainerInterface $container): static {
    return new static($container->get('ob_faq.importer'));
  }

  /**
   * Adds the input element(s).
   */
  abstract protected function buildInput(array &$form, FormStateInterface $form_state): void;

  /**
   * Returns decoded FAQ items from the submitted input.
   *
   * @throws \InvalidArgumentException
   */
  abstract protected function readItems(FormStateInterface $form_state): array;

  /**
   * Name of the element that validation errors attach to.
   */
  abstract protected function inputName(): string;

  public function buildForm(array $form, FormStateInterface $form_state): array {
    if ($report = $form_state->get('report')) {
      $form['report'] = $this->buildReport($report, (bool) $form_state->get('report_preview'));
    }

    $this->buildInput($form, $form_state);

    $form['update'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Update FAQs that already exist'),
      '#description' => $this->t('An FAQ exists when "nid" is given, or when a FAQ with the same Chinese question exists. Unchecked: existing FAQs are skipped.'),
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['preview'] = [
      '#type' => 'submit',
      '#value' => $this->t('Preview'),
      '#submit' => ['::previewSubmit'],
    ];
    $form['actions']['import'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
      '#button_type' => 'primary',
      '#submit' => ['::importSubmit'],
    ];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    try {
      $items = $this->readItems($form_state);
      if (!$items) {
        throw new \InvalidArgumentException((string) $this->t('No FAQ items found.'));
      }
      $form_state->set('items', $items);
    }
    catch (\Throwable $e) {
      $form_state->setErrorByName($this->inputName(), $e->getMessage());
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Handled by previewSubmit() and importSubmit().
  }

  public function previewSubmit(array &$form, FormStateInterface $form_state): void {
    $this->run($form_state, TRUE);
  }

  public function importSubmit(array &$form, FormStateInterface $form_state): void {
    $this->run($form_state, FALSE);
  }

  /**
   * Runs the importer and keeps the report for the rebuilt form.
   */
  protected function run(FormStateInterface $form_state, bool $preview): void {
    $items = $form_state->get('items') ?? [];
    $report = $this->importer->import($items, [
      'preview' => $preview,
      'update' => (bool) $form_state->getValue('update'),
      'text-format' => 'basic_html',
      'uid' => (int) $this->currentUser()->id(),
    ]);

    foreach ($report['warnings'] as $warning) {
      $this->messenger()->addWarning($warning);
    }
    $args = ['@n' => count($items), '@e' => $report['errors']];
    if ($preview) {
      $this->messenger()->addStatus($this->t('Preview: @n items, @e errors. Nothing was saved.', $args));
    }
    elseif ($report['errors']) {
      $this->messenger()->addError($this->t('Imported with errors: @n items, @e errors.', $args));
    }
    else {
      $this->messenger()->addStatus($this->t('Imported @n items.', $args));
    }

    $form_state->set('report', $report);
    $form_state->set('report_preview', $preview);
    $form_state->setRebuild();
  }

  /**
   * Builds the result table; FAQ nids link to their edit form.
   */
  protected function buildReport(array $report, bool $preview): array {
    $rows = [];
    foreach ($report['rows'] as [$label, $result, $nid, $devices]) {
      $rows[] = [
        $label,
        $result,
        is_numeric($nid) ? Link::createFromRoute((string) $nid, 'entity.node.edit_form', ['node' => $nid]) : $nid,
        $devices,
      ];
    }
    return [
      '#type' => 'table',
      '#caption' => $preview ? $this->t('Preview result') : $this->t('Import result'),
      '#header' => [$this->t('Item'), $this->t('Result'), $this->t('FAQ nid'), $this->t('Device term IDs')],
      '#rows' => $rows,
      '#weight' => -100,
    ];
  }

}
