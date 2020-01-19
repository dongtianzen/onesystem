<?php

namespace Drupal\ngdata\Form;

/**
 *
  \Drupal::service('ngdata.form.field')->demo();
 */
class NgdataFormField {

  /**
   *
   */
  public function getFieldBasicProperty($question_term = NULL, $fieldName, $fieldTitle) {
    $output = array(
      // 'fieldName' => $fieldName,
      'fieldId' => $fieldName,
      'fieldLabel' => $fieldTitle,
      'fieldClass' => array(),
      'fieldRequired' => FALSE,
      'defaultValue' => "",
      'default' => [],
      'returnValue' => [
        'refer_tid' => "",
        'refer_uid' => "",
        'refer_other' => "",
        'question_tid' => $question_term ? $question_term->id() : NULL,
      ],
    );

    return $output;
  }

  /**
   *
   */
  public function getCheckbox($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $output = $this->getFieldBasicProperty($question_term, $fieldName, $fieldTitle);

    $output['fieldType'] = "checkbox";
    $output['returnType'] = "value";
    $output['question_tid'] = "";
    $output['updateStatus'] = 0;

    $output = $this->setFieldProperty($output, $options);

    return $output;
  }

  /**
   *
   */
  public function getCheckboxForReactSet($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $output = $this->getCheckbox($question_term, $fieldName, $fieldTitle, $options);
    $output["isReactSet"] = TRUE;

    return $output;
  }

  /**
   * customhtml
   */
  public function getCustomhtml($fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $output = $this->getFieldBasicProperty(NULL, $fieldName, $fieldTitle);

    $output['fieldType'] = "customhtml";
    $output['inputType'] = "customhtml";
    $output['displayType'] = "customhtml";
    $output['fieldLabel'] = $fieldTitle;

    $output = $this->setFieldProperty($output, $options);

    return $output;
  }

  /**
   *
   */
  public function getCustomtext($fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $output = $this->getFieldBasicProperty(NULL, $fieldName, $fieldTitle);

    $output['fieldType'] = "customtext";
    $output['inputType'] = "customtext";
    $output['displayType'] = "customtext";
    $output['fieldLabel'] = $fieldTitle;

    $output = $this->setFieldProperty($output, $options);

    return $output;
  }

  /**
   *
   */
  public function getDateTime($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $startTime = strtotime('00:00:00');
    $timeInterval = array();

    for($i = 0; $i <= 95; ++$i) {
      $timeInterval[] = $startTime + ($i * 15 * 60);
    }

    foreach ($timeInterval as $key => $value) {
      $fieldLabel[] = array(
        "termTid" => (date('H:i', $value)),
        "termName" => (date('h:i A', $value)),
      );
    }

    $output = array(
      'fieldType' => "dateTime",
      'fieldStyle' => "dateTime",
      'returnType' => "value",
      'fieldLabel' => $fieldLabel,
      'fieldDate' => NULL,
      'fieldTime' => NULL,

      'updateStatus' => 0
    );

    $output = $this->setFieldProperty($output, $options);

    return $output;
  }

  /**
   * @param $fieldCategory can be hierarchyFather, specificAnswer, filterFather or filterChildren
   * @param $parentTid is needed for child to use filter
   */
  public function getSelect($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array(), $fieldType = NULL) {
    $output = $this->getFieldBasicProperty($question_term, $fieldName, $fieldTitle);

    if(!$fieldType) {
      $fieldType = 'select';
    }

    $output['fieldLabel'] = $fieldTitle;

    $output['inputType'] = "radio";
    $output['displayType'] = "dropdown";

    $output['options'] = array();

    $output['question_tid'] = "";
    $output['fieldShow'] = TRUE;
    $output['returnType'] = "target_id";

    $output = $this->setFieldProperty($output, $options);
    $output = $this->overrideParentTid($output);

    return $output;
  }

  /**
   *
   */
  public function getSelectForReactSet($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array(), $fieldType = NULL) {
    $output = $this->getSelect($question_term, $fieldName, $fieldTitle, $options, $fieldType);
    $output["isReactSet"] = TRUE;

    return $output;
  }

  /**
   *
   */
  public function getMultiSelect($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array(), $fieldType = 'multiSelect') {
    $output = $this->getSelect($question_term, $fieldName, $fieldTitle, $options, $fieldType);
    return $output;
  }

  /**
   *
   */
  public function getMultiSelectForReactSet($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array(), $fieldType = 'multiSelect') {
    $output = $this->getMultiSelect($question_term, $fieldName, $fieldTitle, $options, $fieldType);
    $output["isReactSet"] = TRUE;

    return $output;
  }

  /**
   *
   */
  public function getSlider($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $output = $this->getFieldBasicProperty($question_term, $fieldName, $fieldTitle);

    $output = array(
      'fieldType' => "slider",
      'question_tid' => "",
      'minimumStep' => NULL,
      'minimumValue' => NULL,
      'maximumValue' => NULL,

      'returnType' => "value",
      'updateStatus' => 0
    );

    $output = $this->setFieldProperty($output, $options);

    return $output;
  }

  /**
   *
   */
  public function getTextfield($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array()) {
    $output = $this->getFieldBasicProperty($question_term, $fieldName, $fieldTitle);

    $output['fieldType'] = "textfield";
    $output['inputType'] = "textfield";
    $output['displayType'] = "textfield";
    $output['fieldLabel'] = $fieldTitle;
    $output['question_tid'] = "";
    $output['returnType'] = "value";
    $output['updateStatus'] = 0;

    $output = $this->setFieldProperty($output, $options);

    return $output;
  }

  /**
   *
   */
  public function getTextfieldForReactSet($question_term, $fieldName = NULL, $fieldTitle = NULL, $options = array(), $fieldType = 'multiSelect') {
    $output = $this->getTextfield($question_term, $fieldName, $fieldTitle, $options, $fieldType);
    $output["isReactSet"] = TRUE;

    return $output;
  }

  /**
   *
   */
  public function setFieldProperty($output = array(), $options = array(), $child = FALSE) {
    if (is_array($options)) {
      foreach ($options as $key => $value) {

        if (array_key_exists($key, $output)) {
          if (is_array($value)) {
            $output[$key] = $this->setFieldProperty($output[$key], $value, TRUE);
          }
          else {
            $output[$key] = $value;
          }
        }
        else {
          if ($child) {
            $output[$key] = $value;
          }
        }
      }
    }

    return $output;
  }

  /**
   * other
   */

  /**
   *
   */
  public function storeParentList() {
    $parent_list = array(
      // node meeting
      // 'field_meeting_module' => array(
      //   'option_field' => 'field_module_program',
      //   'filter_field' => 'field_meeting_program',
      // ),
      // term program
      'field_program_diseasestate' => array(
        'option_field' => 'field_disease_theraparea',
        'filter_field' => 'field_program_theraparea',
      ),
      'field_program_theraparea' => array(
        'option_field' => 'field_theraparea_businessunit',
        'filter_field' => 'field_program_businessunit',
      ),
    );

    return $parent_list;
  }

  /**
   *
   */
  public function overrideParentTid($output = NULL) {
    $parent_list = $this->storeParentList();
    $parent_list_key_array = array_keys($parent_list);

    if (isset($output['fieldName'])) {
      if (in_array($output['fieldName'], $parent_list_key_array)) {
        $output['parentFieldName'] = $parent_list[$output['fieldName']]['filter_field'];
        $output['fieldLabelOptions'] = 'filteredChildren';
      }
    }

    return $output;
  }

}
