<?php

/**
 * @file
 */

namespace Drupal\ngjson\Content;

use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 *
 */
class EventStandardLayoutContent {

  /**
   * @param $type_tid is 'pre' 'post'
   */
  public function getQuestionAnswerByQuestionTidWithEvaluationType($meeting_nodes = array(), $question_tid = NULL, $question_answer = NULL, $type_tid = NULL) {
    $evaluation_nodes = \Drupal::service('baseinfo.querynode.service')
      ->wrapperEvaluationNodesFromMeetingNodesAndType($meeting_nodes, $type_tid);

    $output = 0;
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer'] == $question_answer) {
            $output++;

            break;
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshot($meeting_nodes = array(), $evaluationform_tid = NULL, $view_type = 'meeting_view', $entity_id = NULL) {
    $output = [];

    $evaluation_layout_tids = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperTermTidsByField('evaluationlayout', 'field_evallayout_form', $evaluationform_tid);

    $evaluation_layout_enable = FALSE;
    if ($evaluation_layout_tids) {
      $evaluation_layout_term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')->load($evaluation_layout_tids[0]);
      if ($evaluation_layout_term) {
        $evaluation_layout_enable = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($evaluation_layout_term, 'field_evallayout_enable');
      }
    }

    if ($evaluation_layout_enable) {
      $output = $this->blockEventsSnapshotByEvaluationLayout($meeting_nodes, $evaluationform_tid, $view_type , $entity_id, $evaluation_layout_term);
    }
    else {
      $output = $this->blockEventsSnapshotDefault($meeting_nodes, $evaluationform_tid, $view_type , $entity_id);
    }

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotByEvaluationLayout($meeting_nodes = array(), $evaluationform_tid = NULL, $view_type = 'meeting_view', $entity_id = NULL, $evaluation_layout_term = NULL) {
    $output = [];

    $question_terms = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsEntitys($evaluation_layout_term, 'field_evallayout_questionset');

    foreach ($question_terms as $key => $question_term) {
      $field_type = \Drupal::service('flexinfo.field.service')
        ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_fieldtype');

      if ($field_type == 'customtext') {
      }
      elseif ($field_type == 'radios') {
        $output = array_merge($output, \Drupal::service('ngdata.atomic.blockgroup')
      ->getBlockGroupByRadioQuestion($meeting_nodes, [$question_term->id()]));
      }
      elseif ($field_type == 'selectkey') {
        $output = array_merge($output, \Drupal::service('ngdata.atomic.blockgroup')
          ->getBlockGroupChartBySelectKeyQuestion($meeting_nodes, [$question_term->id()]));
      }
      elseif ($field_type == 'textfield') {
        $output = array_merge($output, \Drupal::service('ngdata.atomic.blockgroup')
          ->getBlockGroupByCommentQuestion($meeting_nodes, [$question_term->id()]));
      }
      elseif ($field_type == 'titlesection') {
        $output = array_merge($output, \Drupal::service('ngdata.atomic.blockgroup')
          ->getBlockGroupByCommentQuestion($meeting_nodes, [$question_term->id()]));
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotDefault($meeting_nodes = array(), $evaluationform_tid = NULL, $view_type = 'meeting_view', $entity_id = NULL) {
    $output = [];

    if ($evaluationform_tid) {
      $evaluationform_term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->load($evaluationform_tid);

      if ($evaluationform_term && $evaluationform_term->getVocabularyId() == 'evaluationform') {
        $question_tids = \Drupal::service('flexinfo.field.service')
          ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

        // manually loop QuestionType for Radios
        // $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Overall Program Satisfaction'));
        // $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'General Satisfaction'));
        // $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Speakers'));
        // $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'General'));
        // $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Hospitality'));
        // $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Impact'));
        // $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Learning Objective'));
        // $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'PrePost'));

        // PrePost is one of Radio Question.
        $output = array_merge($output, $this->blockGroupOnlyRadioQuestion($meeting_nodes, $question_tids));

        // SelectKey
        $output = array_merge($output, $this->blockGroupForSelectKeyQuestion($meeting_nodes, $question_tids, $evaluationform_term));

        // Radios table for Multiple Speaker
        if ($view_type == 'meeting_view') {
          $output = array_merge($output, $this->blockGroupRadioQuestionTableWithSpeaker($meeting_nodes, $evaluationform_term));
        }
        elseif ($view_type == 'program_view') {
          $output = array_merge($output, $this->blockGroupRadioQuestionTableWithMeetingField($meeting_nodes, $evaluationform_term, $entity_id));
        }

        // $output = array_merge($output, $this->blockEventsSnapshotRankingQuestions($meeting_nodes, $evaluationform_term));

        $output = array_merge($output, $this->blockGroupForTextfieldComment($meeting_nodes, $question_tids));
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockGroupOnlyRadioQuestion($meeting_nodes = array(), $question_tids = array()) {
    $output = array();

    $radio_tids = \Drupal::service('baseinfo.queryterm.service')
      ->wrapperQuestionTidsOnlyRadios($question_tids);

    $sort_tids = array_intersect($question_tids, $radio_tids);

    $output = \Drupal::service('ngdata.atomic.blockgroup')
      ->getBlockGroupByRadioQuestion($meeting_nodes, $sort_tids);

    return $output;
  }

  /**
   *
   */
  public function blockGroupRadioQuestionByQuestionType($meeting_nodes = array(), $question_tids = array(), $questiontype = 'General') {
    $output = array();

    $filter_tids = \Drupal::service('baseinfo.queryterm.service')
      ->wrapperQuestionTidsByRadiosByQuestiontype($question_tids, $questiontype);

    $sort_tids = array_intersect($question_tids, $filter_tids);

    $output = \Drupal::service('ngdata.atomic.blockgroup')
      ->getBlockGroupByRadioQuestion($meeting_nodes, $sort_tids);

    return $output;
  }

  /**
   *
   */
  public function blockGroupForSelectKeyQuestion($meeting_nodes = array(), $question_tids = array(), $evaluationform_term = NULL) {
    $output = array();

    $filter_tids = \Drupal::service('baseinfo.queryterm.service')
      ->wrapperFieldtypeQuestionTidsFromEvaluationform('selectkey', $evaluationform_term);

    $sort_tids = array_intersect($question_tids, $filter_tids);

    $output = \Drupal::service('ngdata.atomic.blockgroup')
      ->getBlockGroupChartBySelectKeyQuestion($meeting_nodes, $sort_tids);

    return $output;
  }

  /**
   * Radios table for Multiple Speaker.
   */
  public function blockGroupRadioQuestionTableWithSpeaker($meeting_nodes = array(), $evaluationform_term = NULL) {
    $output = array();

    $speaker_uids = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIds(current($meeting_nodes), 'field_meeting_speaker');

    if ($speaker_uids && count($speaker_uids) > 1) {
      $question_tids = \Drupal::service('baseinfo.queryterm.service')
        ->wrapperMultipleQuestionTidsFromEvaluationform($evaluationform_term);

      $output = \Drupal::service('ngdata.atomic.blockgroup')
        ->getBlockGroupByRadioQuestionMultipleByReferUid($question_tids, $meeting_nodes);
    }

    return $output;
  }

  /**
   * Breakdown table for specify quesitons on the program term.
   */
  public function blockGroupRadioQuestionTableWithMeetingField($meeting_nodes = array(), $evaluationform_term = NULL, $entity_id = NULL) {
    $output = array();

    $breakdown_values = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_module');

    $program_term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($entity_id);

    if ($breakdown_values && count($breakdown_values) > 1) {
      $question_terms = \Drupal::service('flexinfo.field.service')
        ->getFieldAllTargetIdsEntitys($program_term, 'field_program_breakdown_question');

      foreach ($question_terms as $question_term) {
        $output[] = \Drupal::service('ngdata.atomic.block')->getBlockHtmlTableByRadioQuestionMultipleByProgramBreakDownField($question_term, $meeting_nodes, $breakdown_values);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockGroupForTextfieldComment($meeting_nodes = array(), $question_tids = array()) {
    $textfield_tid= \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = 'textfield', $vocabulary_name = 'fieldtype');

    $textfield_question_tids = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $textfield_tid);

    $sort_tids = array_intersect($question_tids, $textfield_question_tids);

    $output = \Drupal::service('ngdata.atomic.blockgroup')
      ->getBlockGroupByCommentQuestion($meeting_nodes, $sort_tids);

    return $output;
  }

}
