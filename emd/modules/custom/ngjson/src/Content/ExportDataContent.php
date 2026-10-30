<?php

/**
 * @file
 */

namespace Drupal\ngjson\Content;

/**
 * An example controller.
 */
class ExportDataContent {

  /**
   * this is only Json output', no ngpage view
   */
  public function standardDataTemplate($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $output = $this->meetingGroup($section, $entity_id, $start, $end);

    return $output;
  }

  /**
   * this is only Json output, no ngpage view
   */
  public function meetingGroup($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $output = [];

    $meeting_nodes = \Drupal::service('flexinfo.querynode.service')
      ->nodesByBundle('meeting');

    foreach ($meeting_nodes as $meeting_node) {
      $program_entity = \Drupal::service('flexinfo.field.service')
        ->getFieldFirstTargetIdTermEntity($meeting_node, 'field_meeting_program');

      $output[] = $this->abbvieProgramReportTemplate(array($meeting_node), $program_entity);

      // $evaluation_nodes = \Drupal::service('flexinfo.querynode.service')
      //   ->nodesByStandardByFieldValue('evaluation', 'field_evaluation_meetingnid', $meeting_node->id());
      // if ($evaluation_nodes) {
      //   foreach ($evaluation_nodes as $key => $evaluation_node) {
      //     $output[] = $this->abbvieEvaluationReportTemplate($evaluation_node);
      //   }
      // }
    }

    return $output;
  }

  /**
   *
   */
  public function programGroup($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $output = [];

    $NgjsonObjectContent = new NgjsonObjectContent();

    $terms = \Drupal::service('flexinfo.term.service')
      ->getFullTermsFromVidName('program');

    foreach ($terms as $term) {
      $meeting_nodes = $NgjsonObjectContent->querySnapshotMeetingsNodes('program', $term->id());

      if (!$meeting_nodes) {
        continue;
      }

      $output[] = $this->abbvieProgramReportTemplate($meeting_nodes, $term);
    }

    return $output;
  }

  /**
   *
   */
  public function abbvieProgramReportTemplate($meeting_nodes, $term) {
    $output = [];

    $signature_total = array_sum(
      \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_signature')
    );
    $evaluation_nums = array_sum(
      \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_evaluationnum')
    );

    $pre_correct_number = \Drupal::service('ngdata.node.evaluation')
      ->getNumberOfEvaluationByQuestionCorrectAnswerByReferValue(
        $meeting_nodes,
        344,
        'refer_other',
        'Pre'
      );
    $post_correct_number = \Drupal::service('ngdata.node.evaluation')
      ->getNumberOfEvaluationByQuestionCorrectAnswerByReferValue(
        $meeting_nodes,
        344,
        'refer_other',
        'Post'
      );

    $pre_correct_percentage = \Drupal::service('flexinfo.calc.service')
      ->getPercentageDecimal($pre_correct_number, $evaluation_nums, 0);
    $post_correct_percentage = \Drupal::service('flexinfo.calc.service')
      ->getPercentageDecimal($post_correct_number, $evaluation_nums, 0);

    $output = [
      'Program ID#' => \Drupal::service('flexinfo.field.service')->getFieldFirstValue(current($meeting_nodes), 'field_meeting_programid'),
      'Program Name' => $term->getName(),
      'HCP Reach' => $signature_total,
      'Evaluations' => $evaluation_nums,
      'Response Rate' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal($evaluation_nums, $signature_total, 0) . '%',
      'Event Type' => \Drupal::service('flexinfo.field.service')
        ->getFieldFirstTargetIdTermName(current($meeting_nodes), 'field_meeting_eventtype'),
      //
      'Pre Score' => $pre_correct_percentage . '%',
      'Post Score' => $post_correct_percentage . '%',
      'Learning Outcome' => ($post_correct_percentage - $pre_correct_percentage) . '%',
      // dashboard tile
      'Overall Program Quality' => \Drupal::service('ngdata.term.question')
        ->getRaidoQuestionTidStatsAverage(120, $meeting_nodes),
      'Overall General Satisfaction' => \Drupal::service('ngdata.term.question')
        ->getRaidoQuestionTidsStatsAverage(array(130, 131, 132, 133), $meeting_nodes),
      'Speaker Rating' => \Drupal::service('ngdata.term.question')
        ->getRaidoQuestionTidStatsAverage(134, $meeting_nodes),
      'Hospitality' => \Drupal::service('ngdata.term.question')
        ->getRaidoQuestionTidsStatsAverage(array(130, 131, 132, 133), $meeting_nodes),
      // single question
      'I intend to change my clinical practice following what I learned in this program' => \Drupal::service('ngdata.term.question')->getRaidoQuestionTidStatsAverage(130, $meeting_nodes),
      'The content of the program was fair and balanced' => \Drupal::service('ngdata.term.question')->getRaidoQuestionTidStatsAverage(131, $meeting_nodes),
      'I would recommend this program to others' => \Drupal::service('ngdata.term.question')->getRaidoQuestionTidStatsAverage(132, $meeting_nodes),
      'What I learned has practical applications in my daily practice' => \Drupal::service('ngdata.term.question')->getRaidoQuestionTidStatsAverage(133, $meeting_nodes),
      'The communications and travel arrangements for this meeting met my expectations' => \Drupal::service('ngdata.term.question')->getRaidoQuestionTidStatsAverage(135, $meeting_nodes),
      'The quality of the venue is conducive to an educational program' => \Drupal::service('ngdata.term.question')->getRaidoQuestionTidStatsAverage(292, $meeting_nodes),
      'The catering/restaurant food was satisfactory and met my dietary needs' => \Drupal::service('ngdata.term.question')->getRaidoQuestionTidStatsAverage(137, $meeting_nodes),
    ];

    return $output;
  }

  /**
   *
   */
  public function abbvieEvaluationReportTemplate($evaluation_node) {
    $output = [
      'Program ID#' => NULL,
      'Program Name' => NULL,
      'HCP Reach' => NULL,
      'Evaluations' => NULL,
      'Response Rate' => NULL,
      'Event Type' => NULL,
      //
      'Pre Score' => NULL,
      'Post Score' => NULL,
      'Learning Outcome' => NULL,
      // dashboard tile
      'Overall Program Quality' => NULL,
      'Overall General Satisfaction' => NULL,
      'Speaker Rating' => NULL,
      'Hospitality' => NULL,
      // single question
      'I intend to change my clinical practice following what I learned in this program' => \Drupal::service('flexinfo.field.service')
        ->getReactsetFieldFirstValue($evaluation_node, 'field_evaluation_reactset', 'question_answer', 130),
      'The content of the program was fair and balanced' => \Drupal::service('flexinfo.field.service')
        ->getReactsetFieldFirstValue($evaluation_node, 'field_evaluation_reactset', 'question_answer', 131),
      'I would recommend this program to others' => \Drupal::service('flexinfo.field.service')
        ->getReactsetFieldFirstValue($evaluation_node, 'field_evaluation_reactset', 'question_answer', 132),
      'What I learned has practical applications in my daily practice' => \Drupal::service('flexinfo.field.service')
        ->getReactsetFieldFirstValue($evaluation_node, 'field_evaluation_reactset', 'question_answer', 133),
      'The communications and travel arrangements for this meeting met my expectations' => \Drupal::service('flexinfo.field.service')
        ->getReactsetFieldFirstValue($evaluation_node, 'field_evaluation_reactset', 'question_answer', 135),
      'The quality of the venue is conducive to an educational program' => \Drupal::service('flexinfo.field.service')
        ->getReactsetFieldFirstValue($evaluation_node, 'field_evaluation_reactset', 'question_answer', 292),
      'The catering/restaurant food was satisfactory and met my dietary needs' => \Drupal::service('flexinfo.field.service')
        ->getReactsetFieldFirstValue($evaluation_node, 'field_evaluation_reactset', 'question_answer', 137),
    ];

    return $output;
  }

}
