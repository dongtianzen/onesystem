<?php
/**
 * @file
 * Contains \Drupal\cfi_export_word\Form\ExportWordSubmitForm.
 */
namespace Drupal\cfi_export_word\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class ExportWordSubmitForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'export_word_submit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['page_language'] = array(
      '#type' => 'hidden',
      '#value' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
    );

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node && ($node instanceof \Drupal\node\NodeInterface)) {
      if ($node->getType() == 'facility') {
        $form['page_nid'] = array(
          '#type' => 'hidden',
          '#value' => $node->id(),
        );

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Export Word'),
          '#button_type' => 'primary',
        );
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $language = $form_state->getValue('page_language');
    $entity_id = $form_state->getValue('page_nid');
    if ($language && $entity_id) {
      \Drupal::getContainer()->get('cfi_export_word.node.service')->exportWordPage($entity_id, $language);
    }

    drupal_set_message(
      $this->t('Your language is - @language_name - @nid ', array('@language_name' => $language, '@nid' => $entity_id))
    );
  }

}
