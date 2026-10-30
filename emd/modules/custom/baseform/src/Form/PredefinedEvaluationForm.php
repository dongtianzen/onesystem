<?php

namespace Drupal\baseform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

/**
 * Class PredefinedEvaluationForm.
 */
class PredefinedEvaluationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'predefined_evaluation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['form_title'] = [
      '#type' => 'item',
      '#title' => $this->t('Create New Evaluation Form'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['form_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Evaluation Form Name'),
      '#maxlength' => 1024,
      '#size' => 64,
      '#weight' => '0',
      '#required' => TRUE,
    ];

    $form['fieldset_classic'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Questions Group'),
    ];


    $question_tids = [
      128,
      123,
      122,
      591,
      127,
      498,
      NULL,
      NULL,
      1922,
      452,
      453,
      592,
    ];
    foreach ($question_tids as $key => $tid) {
      $default_term = NULL;
      $disabled = FALSE;
      if ($tid) {
        $default_term = Term::load($tid);
        $disabled = TRUE;
      }

      $field_id = 'question_' . ($key + 1);

      $form['fieldset_classic'][$field_id] = [
        '#type' => 'entity_autocomplete',
        '#target_type' => 'taxonomy_term',
        '#tags' => FALSE,
        '#disabled' => $disabled,
        '#default_value' => $default_term,
        '#selection_handler' => 'default',
        '#selection_settings' => [
          'target_bundles' => ['questionlibrary'],
        ],
      ];
    }

    // $multiple_terms = \Drupal\taxonomy\Entity\Term::loadMultiple([127, 520]);
    // $form['my_element'] = [
    //   '#type' => 'entity_autocomplete',
    //   '#target_type' => 'taxonomy_term',
    //   '#tags' => TRUE,   // Settings #tags to TRUE makes the the form accept multiple values.
    //   '#default_value' => $multiple_terms,
    //   '#selection_handler' => 'default',
    //   '#selection_settings' => [
    //     'target_bundles' => ['questionlibrary'],
    //   ],
    // ];

    // $form['save'] = [
    //   '#type' => 'submit',
    //   '#title' => $this->t('Save'),
    //   '#weight' => '0',
    // ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['#attributes'] = [
      'class' => [
        'margin-top-24',
        'margin-right-36',
        'margin-left-36',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $result_question_tids = [];

    foreach ($form_state->getValues() as $key => $value) {
      // \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));

      if ($key == 'form_name') {
        $form_name = $value;
      }
      elseif (strpos($key, 'question_') !== false) {
        $result_question_tids[] = $value;
      }
    }

    if (isset($form_name)) {
      $this->_createEvaluationForm($form_name, $result_question_tids);
    }

    $url_path = '/ngpage/standardterm/page/evaluationform';
    $url = Url::fromUserInput($url_path);
    $form_state->setRedirectUrl($url);
  }

  /**
   * {@inheritdoc}
   */
  public function _createEvaluationForm($form_name, $question_tids) {
    $term = Term::create([
      'name' => $form_name,
      'vid'  => 'evaluationform',
      'field_evaluationform_questionset' => $question_tids,
    ]);
    $term->enforceIsNew();
    $term->save();
  }

}
