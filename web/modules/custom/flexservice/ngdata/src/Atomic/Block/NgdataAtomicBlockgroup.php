<?php

namespace Drupal\ngdata\Atomic\Block;

use Drupal\ngdata\Atomic\NgdataAtomic;

use Drupal\ngjson\Content\EventStandardLayoutContent;

/**
 * Class NgdataAtomicBlockgroup.
 \Drupal::service('ngdata.atomic.blockgroup')->basic();
 */
class NgdataAtomicBlockgroup extends NgdataAtomic {

  private $atom;
  private $molecule;
  private $organism;
  private $template;
  private $block;

  /**
   * Constructs a new NgdataAtomicBlockgroup object.
   */
  public function __construct() {
    $this->atom     = \Drupal::service('ngdata.atomic.atom');
    $this->molecule = \Drupal::service('ngdata.atomic.molecule');
    $this->organism = \Drupal::service('ngdata.atomic.organism');
    $this->template = \Drupal::service('ngdata.atomic.template');
    $this->block    = \Drupal::service('ngdata.atomic.block');
  }

  /**
   *
   */
  public function getBlockGroupByRadioQuestion($meeting_nodes = array(), $question_tids = array(), $row_break_num = 0) {
    $output = array();

    if (is_array($question_tids)) {
      $question_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {
        $output[] = $this->block->getBlockChartByRadioQuestion($question_term, $meeting_nodes);

        if ( ($row_break_num + 1) % 2 === 0 ) {
          $output[] = $this->template->blockHtmlClearBoth();
        }

        $row_break_num++;
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockGroupChartBySelectKeyQuestion($meeting_nodes = array(), $question_tids = array()) {
    $output = array();

    if (is_array($question_tids)) {
      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {
        // $output[] = $this->getBlockChartBySelectkeyQuestionForPie($question_term, $meeting_nodes);
        $output[] = $this->block->getBlockHtmlTableBySelectKeyAnswerQuestion($question_term, $meeting_nodes);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockGroupByCommentQuestion($meeting_nodes = array(), $textfield_question_tids = array()) {
    $output = array();

    if (is_array($textfield_question_tids)) {
      $textfield_question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($textfield_question_tids);

      foreach ($textfield_question_terms as $textfield_question_term) {
        $output[] = $this->block->getBlockCommentByQuestion($meeting_nodes, $textfield_question_term);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockGroupByRadioQuestionMultipleByReferUid($question_tids = array(), $meeting_nodes = array()) {
    $output = array();

    if (is_array($question_tids) && $question_tids) {
      $question_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {
        $question_relatedfield = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstValue($question_term, 'field_queslibr_relatedfield');
        if ($question_relatedfield == 'field_meeting_speaker') {
          $output[] = $this->block->getBlockHtmlTableByRadioQuestionMultipleByReferUid($question_term, $meeting_nodes);
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockGroupForProgramSnapshot($entity_id, $meeting_nodes) {
    $program_entity = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($entity_id);
    $evaluationform_tid = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetId($program_entity, 'field_program_evaluationform');

    $EventStandardLayoutContent = new EventStandardLayoutContent();
    $output = $EventStandardLayoutContent->blockEventsSnapshot($meeting_nodes, $evaluationform_tid, 'program_view');

    return $output;
  }

}
