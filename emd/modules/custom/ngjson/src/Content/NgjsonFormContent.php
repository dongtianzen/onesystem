<?php

/**
 * @file
 */
namespace Drupal\ngjson\Content;

/**
 * An example controller.
 */
class NgjsonFormContent {

  public function __construct() {
  }

  /**
   *
   */
  public function standardFormContentData($section, $entity_id) {
    $output = array();

    switch ($section) {
      case 'evaluation':
        $output = \Drupal::service('ngdata.form.page')->formNodeEvaluationAddByMeetingNid($entity_id);
        break;
      case 'hcpcomments':
        $output = \Drupal::service('ngdata.form.page')->formHcpCommentsPage();
        break;
      case 'hcpquestioncomments':
        $output = array(
          array(
            "fieldId" => "field_hcp",
            "fieldLabel" => "HCP Comments Single Question",
            "inputType" => "text",
            "displayType" => "customtext",
            "default" => [],
            "options" => [],
          ),
        );
        $output[] = \Drupal::service('ngdata.form.page')->getFieldHcpTextQuestions();
        break;
      case 'hcptherapareacomments':
        $output = array(
          array(
            "fieldId" => "field_hcp",
            "fieldLabel" => "HCP Comments By Question",
            "inputType" => "text",
            "displayType" => "customtext",
            "default" => [],
            "options" => [],
          ),
        );
        $output[] = \Drupal::service('ngdata.form.page')->getFieldHcpBusinessUnit();
        $output[] = \Drupal::service('ngdata.form.page')->getFieldHcpTherapArea();
        $output[] = \Drupal::service('ngdata.form.page')->getFieldHcpTextQuestions();
        break;

      default:
        break;
    }


    return $output;
  }


  /**
   *
    $output = $this->demoEvaluationForm();
   */
  public function demoEvaluationForm() {
    return array (
      0 => array (
        "fieldId" => "field_hcp",
        "fieldLabel" => "HCP PAGE",
        "inputType" => "text",
        "displayType" => "customtext",
        "default" => [],
        "options" => [],
      ),
      1 => array (
        'fieldId' => 'field_meeting_title',
        'fieldLabel' => 'Title',
        'inputType' => 'textfield',
        'displayType' => 'textfield',
        'default' => 'Evaluation for meeting 805',
        'data' => '',
      ),
    );
  }

  public function demoEvaluationForm2() {
    return array (
      0 => array (
        'fieldId' => 'field_evaluation_title',
        'fieldLabel' => 'Current IBD Management: Lost in Translation Unraveling the mysteries of IBD management toward better outcomes',
        'inputType' => 'text',
        'displayType' => 'customtext',
        'default' => 'Current IBD Management: Lost in Translation Unraveling the mysteries of IBD management toward better outcomes',
        'data' =>
        array (
        ),
      ),
      1 => array (
        'fieldId' => 'field_meeting_title',
        'fieldLabel' => 'Title',
        'inputType' => 'textfield',
        'displayType' => 'textfield',
        'default' => 'Evaluation for meeting 805',
        'data' => '',
      ),
      2 => array (
        'fieldId' => 'field_evaluation_user',
        'fieldLabel' => 'User',
        'inputType' => 'textfield',
        'displayType' => 'textfield',
        'default' => '',
        'data' => '',
      ),
      3 => array (
        'fieldId' => 'field_evaluation_date',
        'fieldLabel' => 'Event Date',
        'inputType' => 'calendar',
        'displayType' => 'calendar',
        'default' => '',
        'data' => '',
      ),
    );
  }
}
