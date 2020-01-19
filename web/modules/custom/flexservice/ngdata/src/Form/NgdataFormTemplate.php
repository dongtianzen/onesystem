<?php

namespace Drupal\ngdata\Form;

/**
 *
  \Drupal::service('ngdata.form.template')->demo();
 */
class NgdataFormTemplate {

  private $form_field;
  private $form_option;

  /**
   * Constructs a new object.
   */
  public function __construct() {
    $this->form_field  = \Drupal::service('ngdata.form.field');
    $this->form_option = \Drupal::service('ngdata.form.option');
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElements($question_term = NULL, $meeting_node = NULL, $question_title = NULL, $options = array()) {
    $question_elements = array();

    $field_name = 'field_evaluation_reactset' . '_' .mt_rand();

    $fieldtype = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_fieldtype');

    // check field type
    switch ($fieldtype) {
      case 'checkbox':  // is checkbox
        $question_elements = $this->formNodeEvaluationQuestionElementsCheckbox($field_name, $question_title, $question_term, $options);
        break;

      case 'customtext':  // is Custom Text
        $question_elements = $this->formNodeEvaluationQuestionElementsCustomtext($field_name, $question_title, $question_term, $options);
        break;

      case 'radios':  // is radios single select
        $question_elements = $this->formNodeEvaluationQuestionElementsRadios($field_name, $question_title, $question_term, $options);
        break;

      case 'selectkey':  // is single selectkey
        $question_elements = $this->formNodeEvaluationQuestionElementsSelectkey($field_name, $question_title, $question_term, $options);
        break;

      case 'textfield':  // is textfield
        $question_elements = $this->formNodeEvaluationQuestionElementsTextfield($field_name, $question_title, $question_term, $options);
        break;

      case 'ranking':  // is ranking
        // $question_elements = $this->formNodeEvaluationQuestionElementsRadios($field_name, $question_title, $question_term, $options);
        break;

      default:
        break;
    }

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElementsCheckbox($field_name, $question_title, $question_term, $options) {
    $question_elements = $this->form_field->getCheckboxForReactSet(
      $question_term,
      $field_name,
      $question_title,
      array(
        'question_tid' => $question_term->id(),
      )
    );

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElementsCustomtext($field_name, $question_title, $question_term, $options) {
    $question_elements = $this->form_field->getCustomtext(
      $question_term,
      $field_name,
      $question_title,
      array(
        'question_tid' => $question_term->id(),
      )
    );

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElementsRadios($field_name, $question_title, $question_term, $options) {
    $question_options = array(
      // 'fieldLabel' => $this->form_option->getSelectOptions($question_term),
      'options' => $this->form_option->getSelectOptions($question_term),
      'question_tid' => $question_term->id(),
    );

    if ($options) {
      foreach ($options as $key => $value) {
        // 'refer_uid' => 366,
        $question_options[$key] = $value;
      }
    }

    $question_elements = $this->form_field->getSelectForReactSet(
      $question_term,
      $field_name,
      $question_title,
      $question_options
    );

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElementsSelectkey($field_name, $question_title, $question_term, $options) {
    $queslibr_label_term = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetIdTermEntity($question_term, 'field_queslibr_label');

    $question_elements = $this->form_field->getMultiSelectForReactSet(
      $question_term,
      $field_name,
      $question_title,
      array(
        'options' => $this->form_option->getSelectOptionsForSelectkyeAnswerOptions($question_term),
        'question_tid' => $question_term->id(),
      )
    );

    return $question_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationQuestionElementsTextfield($field_name, $question_title, $question_term, $options) {
    $question_options = array(
      'question_tid' => $question_term->id(),
    );
    if ($options) {
      foreach ($options as $key => $value) {
        // 'refer_uid' => 366,
        $question_options[$key] = $value;
      }
    }

    $question_elements = $this->form_field->getTextfieldForReactSet(
      $question_term,
      $field_name,
      $question_title,
      $question_options
    );

    return $question_elements;
  }

}
