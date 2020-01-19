<?php

namespace Drupal\ngdata\Form;

use Drupal\dashpage\Content\DashpageObjectContent;

/**
 *
  \Drupal::service('ngdata.form.page')->demo();
 */
class NgdataFormPage {

  /**
   *
   */
  public function getFieldHcpBusinessUnit() {
    $output = array(
      "fieldId" => "field_businessunit_list",
      "fieldLabel" => "Business Unit",
      "inputType" => "radio",
      "displayType" => "dropdown",
      "default" => 1,
      "child" => "Therapeutic Area",
      "childId" => "field_theraparea_list",
      "options" => [],
      "availableDataName" => "available" . "field_businessunit_list",
      "functionName" => "getFieldHcpBusinessUnit",
    );

    $options = array(
      array(
        "value" => 101,
        "label"=> "None",
      ),
    );

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('businessunit');
    if ($terms & is_array($terms)) {
      foreach ($terms as $key => $term) {
        $options[] = array(
          "value" => $term->id(),
          "label"=> $term->getName()
        );
      }
    }

    $output["options"] = $options;

    return $output;
  }

  /**
   *
   */
  public function getFieldHcpTherapArea() {
    $output = array(
      "fieldId" => "field_theraparea_list",
      "isChild" => TRUE,
      "fieldLabel" => "Therapeutic Area",
      "inputType" => "radio",
      "displayType" => "dropdown",
      "default" => NULL,
      "child" => "Program",
      "childId" => "field_program_list",
      "options" => [],
      "availableDataName" => "available" . "field_theraparea_list",
      "functionName" => "getFieldHcpTherapArea",
    );

    $options = array(
      array(
        "parent" => array(
          "parentName"=> "businessUnit",
          "parentId"=> 1
        ),
        "value" => 201,
        "label" => "None"
      ),
    );

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('therapeuticarea');
    if ($terms & is_array($terms)) {
      foreach ($terms as $key => $term) {
        $options[] = array(
          "parent" => array(
            "parentName"=> "businessUnit",
            "parentId"=> \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstTargetId($term, 'field_theraparea_businessunit'),
          ),
          "value" => $term->id(),
          "label"=> $term->getName()
        );
      }
    }

    $output["options"] = $options;

    return $output;
  }

  /**
   *
   */
  public function getFieldHcpProgram() {
    $output = array(
      "fieldId" => "field_program_list",
      "isChild" => TRUE,
      "fieldLabel" => "Program",
      "inputType" => "radio",
      "displayType" => "dropdown",
      "default" => NULL,
      "child" => "",
      "options" => [],
      "availableDataName" => "available" . "field_program_list",
      "functionName" => "getFieldHcpProgram",
    );

    $options = array(
      array(
        "parent" => array(
          "parentName"=> "businessUnit",
          "parentId"=> 1
        ),
        "value" => 301,
        "label" => "None"
      ),
    );

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('program');
    if ($terms & is_array($terms)) {
      foreach ($terms as $key => $term) {
        $options[] = array(
          "parent" => array(
            "parentName"=> "businessUnit",
            "parentId"=> \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstTargetId($term, 'field_program_theraparea'),
          ),
          "value" => $term->id(),
          "label"=> $term->getName()
        );
      }
    }

    $output["options"] = $options;

    return $output;
  }

  /**
   *
   */
  public function getFieldHcpTextQuestions() {
    $output = array(
      "fieldId" => "field_textquestion_list",
      "fieldLabel" => "Question",
      "inputType" => "radio",
      "displayType" => "dropdown",
      "default" => NULL,
      "child" => "",
      "options" => [],
      "availableDataName" => "available" . "field_textquestion_list",
      "functionName" => "getFieldHcpTextQuestions",
    );

    $options = array();

    $textfield_tid= \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getTidByTermName($term_name = 'textfield', $vocabulary_name = 'fieldtype');
    $terms = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')
      ->wrapperTermEntitysByField('questionlibrary', 'field_queslibr_fieldtype', $textfield_tid);

    if ($terms & is_array($terms)) {
      foreach ($terms as $key => $term) {
        $options[] = array(
          "value" => $term->id(),
          "label"=> $term->getName()
        );
      }
    }

    $output["options"] = $options;

    return $output;
  }

  /**
   *
   */
  public function formHcpCommentsPage() {
    $output = array(
      array(
        "fieldId" => "field_hcp",
        "fieldLabel" => "HCP Comments By Program",
        "inputType" => "text",
        "displayType" => "customtext",
        "default" => [],
        "options" => [],
      ),
    );
    $output[] = $this->getFieldHcpBusinessUnit();
    $output[] = $this->getFieldHcpTherapArea();
    $output[] = $this->getFieldHcpProgram();

    return $output;
  }

  /**
   *
   */
  public function formNodeEvaluationAddByMeetingNid($meeting_nid = NULL) {
    $form_elements = array();

    $meeting_node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->load($meeting_nid);
    if ($meeting_node) {
      $evaluationform_term = \Drupal::getContainer()
        ->get('flexinfo.node.service')
        ->getMeetingEvaluationformTerm($meeting_node);

      $question_terms = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldAllTargetIdsEntitys($evaluationform_term, 'field_evaluationform_questionset');

      $form_elements = $this->getFormFieldElelements($meeting_node, $question_terms);
    }

    return $form_elements;
  }

  /**
   *
   */
  public function getFormFieldElelements($meeting_node = NULL, $question_terms = array()) {
    $form_elements = $this->formNodeEvaluationFieldsBasicElements($meeting_node);

    if (is_array($question_terms) && !empty($question_terms)) {
      foreach ($question_terms as $question_term) {
        $boolean_duplicate_question = FALSE;

        $related_fields = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldAllValues($question_term, 'field_queslibr_relatedfield');

        $fieldtype_tid = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstTargetId($question_term, 'field_queslibr_fieldtype');

        if ($related_fields) {
          foreach ($related_fields as $related_field) {

            if ($related_field == 'field_meeting_speaker') {
              $speaker_users = \Drupal::getContainer()
                ->get('flexinfo.field.service')
                ->getFieldAllTargetIdsEntitys($meeting_node, 'field_meeting_speaker', 'user');

              if ($speaker_users && count($speaker_users) > 1) {
                $boolean_duplicate_question = TRUE;

                foreach ($speaker_users as $speaker_user) {
                  $form_elements[] = \Drupal::service('ngdata.form.template')->formNodeEvaluationQuestionElements(
                    $question_term,
                    $meeting_node,
                    $question_term->getName() . ' - ' . $speaker_user->getUsername(),
                    array(
                      'returnValue' => array(
                        'refer_uid' => $speaker_user->id(),
                      )
                    )
                  );
                }
              }
            }
            elseif ($related_field == 'field_queslibr_relatedtype') {
              $relatedtypes = \Drupal::getContainer()
                ->get('flexinfo.field.service')
                ->getFieldAllValues($question_term, 'field_queslibr_relatedtype');

              if ($relatedtypes && count($relatedtypes) > 1) {
                $boolean_duplicate_question = TRUE;

                foreach ($relatedtypes as $relatedtype) {
                  $form_elements[] = \Drupal::service('ngdata.form.template')->formNodeEvaluationQuestionElements(
                    $question_term,
                    $meeting_node,
                    $question_term->getName() . ' - ' . $relatedtype,
                    array(
                      'refer_other' => $relatedtype,
                    )
                  );
                }
              }

            }
          }
        }

        // ranking is one of 'fieldtype' value
        // $ranking_tid = \Drupal::getContainer()
        //   ->get('flexinfo.term.service')
        //   ->getTidByTermName($term_name = 'ranking', $vocabulary_name = 'fieldtype');
        // if ($ranking_tid && $fieldtype_tid == $ranking_tid) {
        //   $boolean_duplicate_question = TRUE;

        //   $ranking_answer_terms = \Drupal::getContainer()
        //     ->get('flexinfo.field.service')
        //     ->getFieldAllTargetIdsEntitys($question_term, 'field_queslibr_rankinganswer');

        //   if ($ranking_answer_terms) {
        //     foreach ($ranking_answer_terms as $ranking_answer_term) {
        //       $form_elements[] = \Drupal::service('ngdata.form.template')->formNodeEvaluationQuestionElements(
        //         $question_term,
        //         $meeting_node,
        //         $question_term->getName() . ' - ' . $ranking_answer_term->getName(),
        //         array(
        //           'refer_tid' => $ranking_answer_term->id(),
        //         )
        //       );
        //     }
        //   }
        // }

        if (!$boolean_duplicate_question) {
          $form_elements[] = \Drupal::service('ngdata.form.template')->formNodeEvaluationQuestionElements(
            $question_term,
            $meeting_node,
            $question_term->getName(),
            $options = array()
          );
        }
      }
    }

    return $form_elements;
  }

  /**
   *
   */
  public function formNodeEvaluationFieldsBasicElements($meeting_node = NULL) {
    $form_elements = [];

    // temporary return
    $DashpageObjectContent = new DashpageObjectContent();
    $meeting_tile_html = $DashpageObjectContent->blockTileMeetingHtml($meeting_node);
    $meeting_tile_html .= "<div>.</div><hr />";
    $form_elements[] = \Drupal::service('ngdata.form.field')->getCustomhtml("meeting_tile", $meeting_tile_html);

    $form_elements[] = \Drupal::service('ngdata.atomic.template')->blockHtmlClearBoth();
    return $form_elements;

    // add node name for "title" field
    $title_placeholder = 'Evaluation for meeting ' . $meeting_node->id();
    $form_elements[] = $this->getTextfield('title', 'Title', array('defaultValue' => $title_placeholder));

    $form_elements[] = $this->getTextfield(
      'field_evaluation_user',
      t('User'),
      array('defaultValue' => NULL)
    );

    $list_options[] = array(
      "termTid" => '253',
      "termName" => 'Pre'
    );
    $list_options[] = array(
      "termTid" => '254',
      "termName" => 'Post'
    );

    $form_elements[] = $this->getSelect('field_evaluation_type', t('Type'), array('fieldLabel' => $list_options));

    return $form_elements;
  }

}
