<?php

namespace Drupal\ngdata\Entity\Node;

/**
 * Class NgdataNodeMeeting.
 \Drupal::service('ngdata.node.meeting')->demo();
 */
class NgdataNodeMeeting extends NgdataNode {

  /**
   * Constructs a new NgdataNodeMeeting object.
   */
  public function __construct() {

  }

  /**
   * @param $meeting_entity
   *

   */
  public function getMeetingEvaluationformTid($meeting_entity = NULL) {
    $evaluationform_tid = NULL;

    if ($meeting_entity && method_exists($meeting_entity, 'getType')) {
      if ($meeting_entity->getType() == 'meeting') {
        $meeting_evaluationform_tid = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstTargetId($meeting_entity, 'field_meeting_evaluationform');

        if ($meeting_evaluationform_tid) {
          $evaluationform_tid = $meeting_evaluationform_tid;
        }
        else {
          $program_tid = \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldFirstTargetId($meeting_entity, 'field_meeting_program');

          if ($program_tid) {
            $program_entity = \Drupal::entityTypeManager()
              ->getStorage('taxonomy_term')
              ->load($program_tid);

            $evaluationform_tid = \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstTargetId($program_entity, 'field_program_evaluationform');
          }
        }
      }
    }

    return $evaluationform_tid;
  }

  /**
   *
   */
  public function countMeetingNodesArray($meeting_nodes_array = array()) {
    $output = array();

    foreach ($meeting_nodes_array as $key => $value) {
      $output[$key] = count($value);
    }

    return $output;
  }

  /**
   *
   */
  public function meetingNodesByBU($meeting_nodes = array()) {
    $output = [];

    $bu_tids = \Drupal::service('ngdata.term')->getTermListByVocabulary('businessunit')['tid'];

    foreach ($bu_tids as $key => $bu_tid) {
      $theraparea_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')
        ->wrapperTermTidsByField('therapeuticarea', 'field_theraparea_businessunit', array($bu_tid), 'IN');

      $output[] = \Drupal::getContainer()
        ->get('flexinfo.querynode.service')
        ->meetingNodesByTheraparea($meeting_nodes, $theraparea_tids);
    }

    return $output;
  }

  /**
   *
   */
  public function meetingNodesByEventType($meeting_nodes = array()) {
    $output = array();

    $eventTypeList = \Drupal::service('ngdata.term')->getTermListByVocabulary('eventtype');

    foreach ($eventTypeList['tid'] as $key => $value) {
      $output[$value] = \Drupal::getContainer()
        ->get('flexinfo.querynode.service')
        ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_eventtype', array($value), 'IN');
    }

    return $output;
  }

  /**
   *
   */
  public function meetingNodesByTherapeuticArea($meeting_nodes = array(), $entity_id = NULL) {
    $output = [];

    $therap_tids = \Drupal::service('ngdata.term')
      ->getTermTherapeuticAreaListByBu($entity_id)['tid'];
    foreach ($therap_tids as $key => $therap_tid) {
      $output[] = \Drupal::getContainer()
        ->get('flexinfo.querynode.service')
        ->meetingNodesByTheraparea($meeting_nodes, array($therap_tid));
    }

    return $output;
  }

}
