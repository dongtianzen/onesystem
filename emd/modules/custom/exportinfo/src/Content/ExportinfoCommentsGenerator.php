<?php

/**
 * @file
 */

namespace Drupal\exportinfo\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 * An example controller.
 */
class ExportinfoCommentsGenerator extends ControllerBase {

  /**
   *
   $start_query_date = \Drupal::getContainer()
     ->get('flexinfo.setting.service')->convertTimeStampToHtmlDate($start, 'html_datetime');

   $end_query_date = \Drupal::getContainer()
     ->get('flexinfo.setting.service')->convertTimeStampToHtmlDate($end, 'html_datetime');
   */
  public function queryMeetingsNodesByProgramTids($program_tids = NULL, $start = NULL, $end = NULL) {
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('meeting');

    $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_program', $program_tids, 'IN');
    $query->condition($group);

    // condition date ranger
    if ($start && $end) {
      // ISOString "2016-01-01T23:30:00";
      $start_time = date('Y-m-d\TH:i:s', $start);
      $end_time = date('Y-m-d\TH:i:s', $end);

      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_date', $start_time, '>');
      $query->condition($group);
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_date', $end_time, '<');
      $query->condition($group);
    }

    $meeting_nids = $query_container->runQueryWithGroup($query);

    $meeting_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($meeting_nids);

    return $meeting_nodes;
  }

  /**
   *
   */
  public function getQuestionTidsByProgram($program_tid = NULL) {
    $output = array();

    $program_entity = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($program_tid);
    if ($program_entity) {
      if ($program_entity->getVocabularyId() == 'program') {
        $evaluationform_entity = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstTargetIdTermEntity($program_entity, 'field_program_evaluationform');

        $output = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldAllTargetIds($evaluationform_entity, 'field_evaluationform_questionset');
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getTextfieldQuestionTidsByProgram($program_tid = NULL) {
    $output = array();

    $question_tids = $this->getQuestionTidsByProgram($program_tid);

    $textfield_tid= \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getTidByTermName($term_name = 'textfield', $vocabulary_name = 'fieldtype');

    $textfield_question_tids = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $textfield_tid);

    $output = array_intersect($question_tids, $textfield_question_tids);

    return $output;
  }

  /**
   *
   */
  public function getTextfieldQuestionTidsByPrograms($program_tids = array()) {
    $output = array();

    if ($program_tids) {
      foreach ($program_tids as $program_tid) {
        $textfield_question_tids = $this->getTextfieldQuestionTidsByProgram($program_tid);

        $output = array_unique(array_merge($output, $textfield_question_tids));
      }
    }

    return $output;
  }

  /**
   *
   */
  public function exportProgramsCommentsData($program_tids = array(), $start = NULL, $end = NULL) {
    $output = array();

    $meeting_nodes = $this->queryMeetingsNodesByProgramTids($program_tids, $start, $end);
    $question_tids = $this->getTextfieldQuestionTidsByPrograms($program_tids);

    $FlexpageEventLayout = new FlexpageEventLayout();
    $output = $FlexpageEventLayout->blockEventsSnapshotComments($meeting_nodes, $question_tids);

    return $output;
  }

  /**
   * @param $program_tids could be empty array
   */
  public function exportOneQuestionCommentsDataByProgramTids($question_tid, $program_tids = array(), $start = NULL, $end = NULL) {
    $output = array();

    $meeting_nodes = $this->queryMeetingsNodesByProgramTids($program_tids, $start, $end);

    $output = $this->exportOneQuestionCommentsDataByMeetingNodes($question_tid, $meeting_nodes);

    return $output;
  }

  /**
   * @param
   */
  public function exportOneQuestionCommentsDataByMeetingNodes($question_tid, $meeting_nodes = array()) {
    $output = array();

    $FlexpageEventLayout = new FlexpageEventLayout();
    $output = $FlexpageEventLayout->getTextfieldQuestionComments(array($question_tid), $meeting_nodes);

    return $output;
  }

}
