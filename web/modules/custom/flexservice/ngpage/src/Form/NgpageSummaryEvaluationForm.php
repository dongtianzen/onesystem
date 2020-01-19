<?php
/**
 * @file
 * Contains \Drupal\ngpage\Form\NgpageSummaryEvaluationForm.php.
 */

namespace Drupal\ngpage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class NgpageSummaryEvaluationForm extends FormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'manageinfo_summary_evaluation_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_id = NULL) {
    $form['htmltext'] = [
      '#type' => 'label',
      '#title' => '<div class="clear-both"><h5>Summary Question Form</h5></div>'
    ];

    $form['meeting_nid'] = [
      '#type' => 'number',
      '#title' => 'Meeting Nid',
      '#value' => $entity_id,
      '#disabled' => TRUE,
    ];

    $form['#tree'] = TRUE; // When this is set to false, the submit method gets no results through getValues().

    $form['reactset'] = array(
      '#type' => 'container',
      '#title' => $this->t('Answer'),
      '#attributes' => array(
        'class' => array('reactset-answerset-warpper'),
      ),
      '#open' => TRUE,
    );

    $answer_set = [];
    $all_question_terms = $this->_getQuestionTermsByNid($entity_id);
    if ($all_question_terms) {
      foreach ($all_question_terms as $tid => $question_term) {
        $question_scale = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstValue($question_term, 'field_queslibr_scale');

        $question_fieldtype = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_fieldtype');

        $form['reactset']['question_item_' . $tid] = array(
          '#type' => 'label',
          '#title' => $question_term->getName(),
          '#prefix' => '<div class="clear-both h5 font-size-16">',
          '#suffix' => '</div>',
        );

        if ($question_fieldtype == 'radios') {
          for ($i = 1; $i < ($question_scale + 1); $i++) {
            $form['reactset']['question_answer_radios_'. $tid . '_' . $i] = array(
              '#type' => 'number',
              '#title' => $i,
            );

            $answer_set['radios'][$tid][] = $i;
          }
        }
        elseif ($question_fieldtype == 'selectkey') {
          $answer_terms = \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldAllTargetIdsEntitys($question_term, 'field_queslibr_selectkeyanswer');

          if ($answer_terms) {
            foreach ($answer_terms as $key => $answer_term) {
              $form['reactset']['question_answer_selectkey_'. $tid . '_' . $key] = array(
                '#type' => 'number',
                '#title' => $answer_term->getName(),
              );

              $answer_set['selectkey'][$tid][] = $key;
            }
          }
        }
        elseif ($question_fieldtype == 'textfield') {
          $form['reactset']['question_answer_textfield_'. $tid . '_' . 1] = array(
            '#type' => 'textarea',
            '#title' => NULL,
            '#description' => 'use "&&&" to break line',
          );

          $answer_set['textfield'][$tid][] = 1;
        }
      }
    }

    $form['show'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['#prefix'] = '<div class="container">';
    $form['#suffix'] = '</div>';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('email')) {
      if (strpos($form_state->getValue('email'), '.com') === FALSE) {
        $form_state->setErrorByName('email', $this->t('This is not a .com email address.'));
      }
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // drupal_set_message($this->t('Your email address is @email', ['@email' => $form_state->getValue('email')]));

    $this->_convertQuestionReactset($form_state);

    return;
  }

  /**
   *
   */
  public function _convertQuestionReactset($form_state = NULL) {
    $reactset_values = $form_state->getValue('reactset');

    $output = [];
    $answer_set = [];

    if ($reactset_values) {

      // $key is like 'question_answer_radios_2096_1'
      foreach ($reactset_values as $key => $row) {
        if ($row) {
          $result_array = explode("_", $key);
          if ($result_array && isset($result_array[3])) {
            if ($result_array[2] == 'radios' || $result_array[2] == 'selectkey') {
              for ($i = 0; $i < $row; $i++) {
                $answer_set[$result_array[3]][] = $result_array[4];
              }
            }
            elseif ($result_array[2] == 'textfield') {
              $textfield_row = explode("&&&", $row);
              if ($textfield_row) {
                foreach ($textfield_row as $comment) {
                  $answer_set[$result_array[3]][] = $comment;
                }
              }
            }
          }
        }
      }

      // get maxmium number
      $max_num = 0;
      if ($answer_set) {
        foreach ($answer_set as $key => $value) {
          $array_sum = count($value);

          if ($array_sum > $max_num) {
            $max_num = array_sum($value);
          }
        }
      }
    }

    for ($i = 0; $i < $max_num; $i++) {
      $output = NULL;
      foreach ($answer_set as $key => $value) {
        if (isset($value[$i])) {
          $output[] = [
            'question_tid' => $key,
            'question_answer' => $value[$i]
          ];
        }
      }

      if ($output) {
        $this->_entityCreateForm($form_state, $output);
      }
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function _entityCreateForm($form_state = NULL, $reactset = NULL) {
    $field_array = $this->_generateEvaluationFieldsValue($form_state, $reactset);
    \Drupal::getContainer()->get('flexinfo.node.service')->entityCreateNode($field_array);

    return;
  }

  /**
   *
   */
  public function _generateEvaluationFieldsValue($form_state = NULL, $reactset = NULL) {
    $entity_bundle = 'evaluation';

    $output = array(
      'type' => $entity_bundle,
      'title' => 'Summary Evaluation for meeting ' . $form_state->getValue('meeting_nid'),
      'langcode' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      'uid' => \Drupal::currentUser()->id(),
      'status' => 1,
    );

    $output['field_evaluation_meetingnid'] = array($form_state->getValue('meeting_nid'));
    $output['field_evaluation_reactset'] = $reactset;

    return $output;
  }

  /**
   *
   */
  public function _getQuestionTermsByNid($entity_id = NULL) {
    $output = [];

    $meeting_entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);
    if ($meeting_entity) {
      $evaluationform_term = \Drupal::getContainer()
        ->get('flexinfo.node.service')
        ->getMeetingEvaluationformTerm($meeting_entity);

      $output = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldAllTargetIdsEntitys($evaluationform_term, 'field_evaluationform_questionset');
    }

    return $output;
  }

}
