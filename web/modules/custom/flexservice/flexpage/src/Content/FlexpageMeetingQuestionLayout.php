<?php

/**
 * @file
 */

namespace Drupal\flexpage\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\flexpage\Content\FlexpageBaseJson;
use Drupal\flexpage\Content\FlexpageEventLayout;
use Drupal\flexpage\Content\FlexpageJsonGenerator;
use Drupal\flexpage\Content\FlexpageSampleDataGenerator;

/**
 * An example controller.
 */
class FlexpageMeetingQuestionLayout extends ControllerBase {

  public $FlexpageBaseJson;
  public $FlexpageEventLayout;
  public $FlexpageJsonGenerator;
  public $FlexpageSampleDataGenerator;

  /**
   *
   */
  public function __construct() {
    $this->FlexpageBaseJson = new FlexpageBaseJson();
    $this->FlexpageEventLayout = new FlexpageEventLayout();
    $this->FlexpageJsonGenerator = new FlexpageJsonGenerator();
    $this->FlexpageSampleDataGenerator = new FlexpageSampleDataGenerator();
  }

  /**
   *
   */
  public function programPageLayout($entity_id = array(), $start = NULL, $end = NULL) {
    $output = NULL;

    $program_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($entity_id);

    $evaluationform_term = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetIdTermEntity($program_term, 'field_program_evaluationform');

    $meeting_nodes = \Drupal::getContainer()
      ->get('flexinfo.querynode.service')
      ->nodesByStandardByFieldValue('meeting', 'field_meeting_program', $entity_id);;

    if ($evaluationform_term) {
      if ($evaluationform_term && $evaluationform_term->getVocabularyId() == 'evaluationform') {
        $output .= '<div class="questions-page-layout-wrapper">';
          $output .= $this->meetingPageLayoutByEvaluationformTerm($meeting_nodes, $evaluationform_term);
        $output .= '</div>';
      }
    }

    return $output;
  }

  /**
   *
   */
  public function meetingPageLayout($entity_id = array(), $start = NULL, $end = NULL) {
    $output = NULL;

    $meeting_node = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);
    $evaluationform_tid = \Drupal::getContainer()
      ->get('flexinfo.node.service')
      ->getMeetingEvaluationformTid($meeting_node);

    if ($evaluationform_tid) {
      $evaluationform_term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->load($evaluationform_tid);

      if ($evaluationform_term && $evaluationform_term->getVocabularyId() == 'evaluationform') {
        $output .= '<div class="questions-page-layout-wrapper">';
          $output .= $this->meetingPageLayoutByEvaluationformTerm(array($meeting_node), $evaluationform_term);
        $output .= '</div>';
      }
    }

    return $output;
  }

  /**
   *
   */
  public function meetingPageLayoutByEvaluationformTerm($meeting_nodes = array(), $evaluationform_term = NULL) {
    $output = NULL;

    $question_tids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    $question_terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadMultiple($question_tids);

    foreach ($question_terms as $key => $question_term) {
      $field_type = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_fieldtype');

      if ($field_type == 'radios') {
        $output .= $this->getHtmlTableByQuestionTerm($meeting_nodes, $question_term);
      }
      elseif ($field_type == 'selectkey') {
        $output .= $this->getHtmlTableByQuestionTerm($meeting_nodes, $question_term, $select_key_answer = TRUE);
      }
      elseif ($field_type == 'textfield') {
        $output .= $this->getHtmlTextfieldByQuestionComment($meeting_nodes, $question_term);
      }

      $output .= '<br />';
      $output .= '<div>';
        $output .= '&nbsp;';
      $output .= '</div>';
    }

    return $output;
  }

  /**
   *
   */
  public function getQuestionAnswerAllDataByProvince($meeting_nodes = array(), $question_term = NULL) {
    $province_terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    if (is_array($province_terms)) {
      foreach ($province_terms as $key => $province_term) {
        $meeting_nodes_by_province_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', array($key));

        $pool_data = $this->FlexpageEventLayout->getQuestionAnswerAllData($meeting_nodes_by_province_term, $question_term->id());

        $output[$key] = array_count_values($pool_data);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getHtmlTableByQuestionTerm($meeting_nodes = array(), $question_term = NULL, $select_key_answer = FALSE) {
    $output = NULL;

    $province_terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    $pool_data = $this->FlexpageEventLayout->getQuestionAnswerAllData($meeting_nodes, $question_term->id());
    $pool_data_sum = count($pool_data);

    $pool_data_count = array_count_values($pool_data);

    $pool_data_count_by_province = $this->getQuestionAnswerAllDataByProvince($meeting_nodes, $question_term);

    if ($pool_data_sum) {
      $output .= '<div class="panel-body bg-ffffff font-size-12 margin-left-12">';
        $output .= '<table class="table table-hover">';
          $output .= '<thead class="font-bold">';
            $output .= '<tr>';
              $output .= '<th>';
                $output .= $question_term->getName();
              $output .= '</th>';
            $output .= '</tr>';
          $output .= '</thead>';
        $output .= '</table>';
        $output .= '<table class="table table-hover">';
          $output .= '<thead class="font-bold">';
            $output .= '<tr>';
              $output .= '<th>';
                $output .= 'Label';
              $output .= '</th>';
              $output .= '<th>';
                $output .= 'Number of Responses';
              $output .= '</th>';
              $output .= '<th>';
                $output .= 'Percentage';
              $output .= '</th>';
              foreach ($province_terms as $key => $province_term) {
                $output .= '<th>';
                  $output .= $province_term->getDescription();
                $output .= '</th>';
              }
            $output .= '</tr>';
          $output .= '</thead>';

          ksort($pool_data_count);
          foreach ($pool_data_count as $key => $row) {
            if ($select_key_answer) {
              $selectAnswerTerm = \Drupal::entityTypeManager()
                ->getStorage('taxonomy_term')
                ->load($key);
            }

            $answerPercentage = \Drupal::getContainer()
              ->get('flexinfo.calc.service')
              ->getPercentageDecimal($row, $pool_data_sum, 0) . '%';

            $output .= '<tbody>';
              $output .= '<tr>';
                $output .= '<th class="font-weight-normal">';
                  if ($select_key_answer) {
                    $output .= $selectAnswerTerm->getName();
                  }
                  else {
                    $output .= $key;
                  }
                $output .= '</th>';
                $output .= '<th class="font-weight-normal">';
                  $output .= $row;
                $output .= '</th>';
                $output .= '<th class="font-weight-normal">';
                  $output .= $answerPercentage;
                $output .= '</th>';
                foreach ($pool_data_count_by_province as $subkey => $subvalue) {
                  $output .= '<th class="font-weight-normal">';
                    $output .= isset($subvalue[$key]) ? $subvalue[$key] : 0;
                  $output .= '</th>';
                }
              $output .= '</tr>';
            $output .= '</tbody>';
          }

        $output .= '</table>';
      $output .= '</div>';
    }

    return $output;
  }

  /**
   *
   */
  public function getHtmlTextfieldByQuestionComment($meeting_nodes = array(), $question_term = NULL) {
    $output = NULL;

    $pool_data = $this->FlexpageEventLayout->getTextfieldQuestionAllData($meeting_nodes, $question_term->id());

    $output = NULL;
    if (isset($pool_data) && count($pool_data) > 0) {
      $output .= '<div class="panel-body bg-ffffff font-size-12 margin-left-12">';
        $output .= '<table class="table table-hover">';
          $output .= '<thead class="font-bold">';
            $output .= '<tr>';
              $output .= '<th>';
                $output .= $question_term->getName();
              $output .= '</th>';
            $output .= '</tr>';
          $output .= '</thead>';

          $output .= '<tbody>';
          foreach ($pool_data as $key => $row) {
            $output .= '<tr>';
              $output .= '<td>';
                $output .= $row;
              $output .= '</td>';
            $output .= '</tr>';
          }
          $output .= '</tbody>';
        $output .= '</table>';
      $output .= '</div>';
    }

    return $output;
  }

}
