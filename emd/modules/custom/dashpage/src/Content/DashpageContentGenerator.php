<?php

/**
 * @file
 */
namespace Drupal\dashpage\Content;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ngjson\Content\NgjsonObjectContent;

/**
 * An example controller.
 $DashpageContentGenerator = new DashpageContentGenerator();
 $DashpageContentGenerator->angularPage();
 */
class DashpageContentGenerator extends ControllerBase {

  public function __construct() {
  }

  /**
   *
   */
  public function standardPageWrapper($content = NULL) {
    $output = '';
    $output .= '<div class="standard-page-wrapper margin-left-60 margin-top-16 margin-right-44">';
      $output .= $content;
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function standardMap() {
    $output = $this->standardPageWrapper($this->standardMapWrapper());
    return $output;
  }

  /**
   *
   */
  public function standardMapWrapper() {
    $output = '';
    $output .= '<div class="row">';
      $output .= '<div id="standard-google-map-wrapper">';
        $output .= '<div id="map-canvas">';
          $output .= 'Google Map';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function standardMapJson($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $output = [];

    $start = \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTime();
    $end = \Drupal::getContainer()->get('flexinfo.setting.service')->userEndTime();

    $NgjsonObjectContent = new NgjsonObjectContent();
    $meeting_nodes = $NgjsonObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    if ($meeting_nodes && is_array($meeting_nodes)) {
      foreach ($meeting_nodes as $key => $node) {
        $program_entity = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($node, 'field_meeting_program');
        $lat = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_latitude');
        $lng = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_longitude');

        if ($lat && $lng) {
          $output[] = [
            "lat" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_latitude'),
            "lng" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_longitude'),
            "subtitle" => $program_entity->getName(),
            "workingField" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($program_entity, 'field_program_theraparea'),
            "date" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateFormat($node, 'field_meeting_date'),
            "city" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($node, 'field_meeting_city'),
            "eventType" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($node, 'field_meeting_eventtype'),
            "speakerName" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdUserName($node, 'field_meeting_speaker'),
            "venue" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_venuename'),
            "attendNum" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_signature'),
            "responseNum" => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_evaluationnum'),
          ];
        }
      }
    }

    return $output;
  }


}
