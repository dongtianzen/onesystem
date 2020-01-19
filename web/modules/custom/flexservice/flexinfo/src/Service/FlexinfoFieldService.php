<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoFieldService.php.
 */
namespace Drupal\flexinfo\Service;

use Drupal\field\Entity\FieldConfig;

/**
 * An example Service container.
 *
 */
class FlexinfoFieldService {

  /**
   * @return boolean
   */
  public function checkBundleHasField($entity_type_id = NULL, $bundle = NULL, $field_name = NULL) {
    $bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_id, $bundle);
    if (isset($bundle_fields[$field_name])) {
      $boolean = TRUE;
    }
    else {
      $boolean = FALSE;
      if (\Drupal::currentUser()->id() == 1) {
        dpm('no found this field name for this entity bundle - ' . $bundle . ' for this field - ' . $field_name);
      }
    }

    return $boolean;
  }

  /**
   * @return boolean
   */
  public function checkEntityHasField($entity = NULL, $field_name = NULL) {
    $boolean = $entity->hasField($field_name);

    $entity_id = NULL;
    if (method_exists($entity, 'id')) {
      $entity_id = $entity->id();
    }

    if ($boolean) {
    }
    else {
      if (\Drupal::currentUser()->id() == 1) {
        dpm('no found this field name for this entity - ' . $field_name . ' - for - ' . $entity_id);
      }
    }

    return $boolean;
  }

  /**
   * @return field single for any type
     \Drupal::getContainer()->get('flexinfo.field.service')->getFieldSingleValue();
   */
  public function getFieldSingleValue($entity_type = NULL, $entity = NULL, $field_name = NULL) {
    $output = '';

    $field = \Drupal\field\Entity\FieldStorageConfig::loadByName($entity_type, $field_name);

    if ($field) {
      $field_standard_type = $this->getFieldStandardType();

      // Standard Type use value directly
      if (in_array($field->getType(), $field_standard_type)) {
        $output = $this->getFieldFirstValue($entity, $field_name);
      }
      elseif ($field->getType() == 'datetime') {
        // $output = $this->getFieldFirstValue($entity, $field_name);    // 2016-02-24T17:00:00
        $output = $this->getFieldFirstValueDateFormat($entity, $field_name);
      }
      elseif ($field->getType() == 'entity_reference') {
        $target_id = $this->getFieldFirstTargetId($entity, $field_name);

        if ($field->getSetting('target_type') == 'taxonomy_term') {
          $output = \Drupal::getContainer()->get('flexinfo.term.service')->getNameByTid($target_id);
        }
        elseif ($field->getSetting('target_type') == 'node') {
          $output = $target_id;
        }
        elseif ($field->getSetting('target_type') == 'user') {
          $output = \Drupal::getContainer()->get('flexinfo.user.service')->getUserNameByUid($target_id);
        }
      }
      else {
        if (\Drupal::currentUser()->id() == 1) {
          dpm('no found this field type - ' . $field->getType());
        }
      }
    }
    else {
      if (\Drupal::currentUser()->id() == 1) {
        dpm('not found field type - for this field - ' . $field_name);
      }
    }

    return $output;
  }

  /**
   * @return array result value together for some entity array
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAnswerIntArray($entitys, 'field_pool_answerint');
   */
  public function getFieldAnswerIntArray($entity_array = array(), $field_name = NULL) {
    $output = array();

    if (is_array($entity_array)) {
      foreach ($entity_array as $entity) {
        $row = $entity->get($field_name)->getValue();
        if ( is_array($row) && count($row) > 0 ) {
          foreach ($row as $key => $value) {
            if (isset($output[$key])) {
              $output[$key] += $value['value'];
            }
            else {
              $output[$key] = $value['value'];
            }
          }
        }
      }
    }

    return $output;
  }

  /**
   * @return array result value together for some entity array
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAnswerTermArray($entitys, 'field_pool_answerterm');
   */
  public function getFieldAnswerTermArray($entity_array = array(), $field_name = NULL) {
    $output = array();

    if (is_array($entity_array)) {
      foreach ($entity_array as $entity) {
        $output = array_merge($this->getFieldAllTargetIdsTermNames($entity, $field_name), $output);
      }
    }

    return $output;
  }

  /**
   * @return array result value together for some entity array
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAnswerIntArray($entitys, 'field_pool_answertext');
   */
  public function getFieldAnswerTextArray($entity_array = array(), $field_name = NULL) {
    $output = array();

    if (is_array($entity_array)) {
      foreach ($entity_array as $entity) {
        $output = array_merge($this->getFieldAllValues($entity, $field_name), $output);
      }
    }

    return $output;
  }

  /**
   * @return field array "target_id"
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIds($entity, $field_name);
   */
  public function getFieldAllTargetIds($entity = NULL, $field_name = NULL) {
    $output = array();

    if ($entity && is_object($entity)) {
      $field_value_array = $entity->get($field_name)->getValue();
      foreach ($field_value_array as $row) {
        $output[] = $row['target_id'];
      }
    }

    return $output;
  }

  /**
   * @return field array "target_id"
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIdsEntitys($entity, $field_name, 'taxonomy_term');
   */
  public function getFieldAllTargetIdsEntitys($entity = NULL, $field_name = NULL, $entity_type = 'taxonomy_term') {
    $entitys = array();

    if ($entity) {
      $target_ids = $this->getFieldAllTargetIds($entity, $field_name);

      $entitys = \Drupal::entityTypeManager()->getStorage($entity_type)->loadMultiple($target_ids);
    }

    return $entitys;
  }

  /**
   * @return
   */
  public function getFieldAllTargetIdsTermNames($entity = NULL, $field_name = NULL, $entity_type = 'taxonomy_term') {
    $output = array();

    $target_ids = $this->getFieldAllTargetIds($entity, $field_name);

    if ($target_ids && is_array($target_ids)) {
      $entitys = \Drupal::entityTypeManager()->getStorage($entity_type)->loadMultiple($target_ids);
      foreach ($entitys as $entity) {
        if ($entity) {
          $output[] = $entity->getName();
        }
      }
    }

    return $output;
  }

  /**
   * @return
   */
  public function getFieldAllTargetIdsUserNames($entity = NULL, $field_name = NULL, $entity_type = 'user') {
    $output = array();

    $target_ids = $this->getFieldAllTargetIds($entity, $field_name);

    if ($target_ids && is_array($target_ids)) {
      foreach ($target_ids as $target_id) {
        $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($target_id);
        $output[] = $entity->getUsername();
      }
    }

    return $output;
  }

  /**
   * @return field array values
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllValues();
   */
  public function getFieldAllValues($entity = NULL, $field_name = NULL) {
    $output = array();

    if ($entity && is_object($entity)) {
      $field_value_array = $entity->get($field_name)->getValue();
      foreach ($field_value_array as $row) {
        $output[] = $row['value'];
      }
    }

    return $output;
  }

  /**
   * @return field single value
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstBooleanValue();
   */
  public function getFieldFirstBooleanValue($entity = NULL, $field_name = NULL) {
    $output = NULL;

    if ($entity && is_object($entity)) {
      $output = $entity->get($field_name)->value;
      if ($output) {
        $output = TRUE;
      }
      else {
        $output = FALSE;
      }
    }

    return $output;
  }

  /**
   * @return field single "target_id"
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId();
   */
  public function getFieldFirstTargetId($entity = NULL, $field_name = NULL) {
    $output = NULL;

    if ($entity && is_object($entity)) {
      if ($this->checkEntityHasField($entity, $field_name)) {
        $output = $entity->get($field_name)->target_id;
      }
    }

    return $output;
  }

  /**
   * @return array result value together for some entity array
   */
  public function getFieldFirstTargetIdCollection($entity_array = array(), $field_name = NULL) {
    $output = array();

    if (is_array($entity_array)) {
      foreach ($entity_array as $entity) {
        $output[] = $this->getFieldFirstTargetId($entity, $field_name);
      }
    }

    return $output;
  }

  /**
   * @return Node Entity
   */
  public function getFieldFirstTargetIdToEntity($entity = NULL, $entity_type = NULL, $field_name = NULL) {
    $node = NULL;

    $target_id = $this->getFieldFirstTargetId($entity, $field_name);
    if ($target_id) {
      $node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($target_id);
    }

    return $node;
  }

  /**
   * @deprecated  by 2017 Nov
   * @see $this->getFieldFirstTargetIdToEntity()
   */
  public function getFieldFirstTargetIdNodeEntity($entity = NULL, $field_name = NULL) {
    $output = $this->getFieldFirstTargetIdToEntity($entity, 'node', $field_name);
    return $output;
  }

  /**
   * @deprecated  by 2017 Nov
   * @see $this->getFieldFirstTargetIdToEntity()
   */
  public function getFieldFirstTargetIdTermEntity($entity = NULL, $field_name = NULL) {
    $output = $this->getFieldFirstTargetIdToEntity($entity, 'taxonomy_term', $field_name);
    return $output;
  }

  /**
   * @return
   */
  public function getFieldFirstTargetIdTermName($entity = NULL, $field_name = NULL) {
    $target_id = $this->getFieldFirstTargetId($entity, $field_name);
    $output = \Drupal::getContainer()->get('flexinfo.term.service')->getNameByTid($target_id);

    return $output;
  }

  /**
   * @deprecated by 2017 Nov
   * @see $this->getFieldFirstTargetIdToEntity()
   */
  public function getFieldFirstTargetIdUserEntity($entity = NULL, $field_name = NULL) {
    $output = $this->getFieldFirstTargetIdToEntity($entity, 'user', $field_name);
    return $output;
  }

  /**
   * @return field single "target_id"
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId();
   */
  public function getFieldFirstTargetIdUserName($entity = NULL, $field_name = NULL) {
    $target_id = $this->getFieldFirstTargetId($entity, $field_name);
    $output = \Drupal::getContainer()->get('flexinfo.user.service')->getUserNameByUid($target_id);

    return $output;
  }

  /**
   * @return field single value
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue();
   */
  public function getFieldFirstValue($entity = NULL, $field_name = NULL) {
    $output = NULL;
    if ($entity && is_object($entity)) {
      $output = $entity->get($field_name)->value;
    }
    return $output;
  }

  /**
   * @return field single value
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue();
   */
  public function getFieldFirstValueDateFormat($entity = NULL, $field_name = NULL, $type = 'html_date') {
    $timestamp = $this->getFieldFirstValueDateTimestamp($entity, $field_name);

    $output = \Drupal::getContainer()->get('flexinfo.setting.service')->convertTimeStampToHtmlDate($timestamp, $type);

    return $output;
  }

  /**
   * @return array result value together for some entity array
   */
  public function getFieldFirstValueCollection($entity_array = array(), $field_name = NULL) {
    $output = array();

    if (is_array($entity_array)) {
      foreach ($entity_array as $entity) {
        $output[] = $this->getFieldFirstValue($entity, $field_name);
      }
    }

    return $output;
  }

  /**
   * @return timestamp
   */
  public function getFieldFirstValueDateTimestamp($entity = NULL, $field_name = NULL) {
    $field_value = $this->getFieldFirstValue($entity, $field_name);

    $timestamp = NULL;
    if ($field_value) {
      $timestamp = date_format(date_create($field_value, timezone_open('UTC')), "U");
    }

    return $timestamp;
  }

  /**
   $entity->get($field_name)->getValue()
   * @return basic original array
      Array(
        [0] => Array(
          [value] => 2018-01-01T06:00:00
        )
        [1] => Array(
          [value] => 2017-01-01T06:00:00
        )
      )
   */
  /**
   * @return field standard_type
     \Drupal::getContainer()->get('flexinfo.field.service')->getFieldStandardType();

   * other
   * 'datetime'
   * \Drupal::service('date.formatter')->format($row_value, 'page_daytime', $format = '', $timezone = 'UTC')
   *
   * 'entity_reference'
   * 'target_type' == 'taxonomy_term'
   * 'target_type' or 'user'
   */
  public function getFieldStandardType() {
    $field_standard_type = array(
      'boolean',
      'datetime',  // Date only need "2017-07-09" Date and Time need covert timestamp to "2017-07-09T16:15:30"
      'decimal',
      'email',
      'float',
      'integer',
      'list_string',
      'list_integer',
      'string',
      'string_long',
      'text_long',
    );

    return $field_standard_type;
  }

  /**
   * @param $bundle is Vid - Vocabulary Name
   * @return array, only include custom field
   \Drupal::getContainer()->get('flexinfo.field.service')->getFieldsCollectionByEntityBundle();
   */
  public function getFieldsCollectionByEntityBundle($entity_type = NULL, $bundle = NULL) {
    $output = array();

    $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type, $bundle);
    // class BaseFieldDefinition
    // https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Field%21BaseFieldDefinition.php/class/BaseFieldDefinition/8.2.x

    foreach ($field_definitions as $field_name => $field_definition) {
      // two approach to check is base field or custom field
      // if (!empty($field_definition->getTargetBundle())) {
      if($field_definition->getFieldStorageDefinition()->isBaseField() === FALSE) {
        $output[$field_name] = $field_definition;
      }
    }

    return $output;
  }

  /** - - - - - - Reactset Field - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return field array values
   */
  public function getReactsetFieldAllValue($entity = NULL, $field_name = 'field_evaluation_reactset', $subfield = NULL, $question_tid = NULL) {
    $output = array();
    if ($entity) {
      $result = $entity->get($field_name)->getValue();

      if (isset($result[0][$subfield])) {
        foreach ($result as $row) {

          // check $question_tid is match
          if ($question_tid) {
            if ($row['question_tid'] == $question_tid) {
              $output[] = $row[$subfield];

              // do not break
            }
          }
          else {
            $output[] = $row[$subfield];
          }
        }
      }
    }

    return $output;
  }

  /**
   * @return field array values
   */
  public function getReactsetFieldAllValueCollection($entity_array = array(), $field_name = 'field_evaluation_reactset', $subfield = NULL, $question_tid = NULL) {
    $output = array();

    if (is_array($entity_array)) {
      foreach ($entity_array as $entity) {
        $output = array_merge($output, $this->getReactsetFieldAllValue($entity, $field_name, $subfield, $question_tid));
      }
    }

    return $output;
  }

  /**
   * @param $subfield_condition_array
      $subfield_condition_array = array(
        array(
          'subfield_name' => 'question_tid',
          'subfield_value' => 3006,
        ),
        array(
          'subfield_name' => 'refer_tid',
          'subfield_value' => 2600,
        ),
      );
   *
   * @return field array values
   \Drupal::getContainer()->get('flexinfo.field.service')->getReactsetFieldFirstValueWithSubfieldCondition();
   */
  public function getReactsetFieldAllValueWithSubfieldCondition($entity = NULL, $field_name = 'field_evaluation_reactset', $subfield = NULL, $subfield_condition_array = array()) {
    $output = NULL;
    if ($entity) {
      $result = $entity->get($field_name)->getValue();

      if ($subfield_condition_array && is_array($subfield_condition_array)) {
        foreach ($result as $row) {

          // match all subfield condition
          foreach ($subfield_condition_array as $subfield_condition) {
            if ($row[$subfield_condition['subfield_name']] == $subfield_condition['subfield_value']) {

            }
            else {
              continue 2;
            }
          }

          if (isset($row[$subfield])) {
            $output[] = $row[$subfield];
          }
        }
      }
    }

    return $output;
  }

  /**
   * @return field array values
   \Drupal::getContainer()->get('flexinfo.field.service')->getReactsetFieldFirstValue();
   */
  public function getReactsetFieldFirstValue($entity = NULL, $field_name = 'field_evaluation_reactset', $subfield = NULL, $question_tid) {
    $output = NULL;
    if ($entity) {
      $result = $entity->get('field_evaluation_reactset')->getValue();

      foreach ($result as $row) {
        if ($row['question_tid'] == $question_tid) {
          $output = $row[$subfield];

          break;
        }
      }
    }

    return $output;
  }

  /**
   * @return field array values
   \Drupal::getContainer()->get('flexinfo.field.service')->getReactsetFieldFirstValueCollection();
   */
  public function getReactsetFieldFirstValueCollection($entity_array = array(), $field_name = 'field_evaluation_reactset', $subfield = NULL, $question_tid) {
    $output = array();

    if (is_array($entity_array)) {
      foreach ($entity_array as $entity) {
        $output[] = $this->getReactsetFieldFirstValue($entity, $field_name, $subfield, $question_tid);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getReactsetFieldAllValueCollectionWithSubfieldCondition($entity_array = array(), $field_name = 'field_evaluation_reactset', $subfield = NULL, $subfield_condition_array = array()) {
    $output = array();

    if (is_array($entity_array)) {
      foreach ($entity_array as $entity) {
        $output = array_merge($output, $this->getReactsetFieldAllValueWithSubfieldCondition($entity, $field_name, $subfield, $subfield_condition_array));
      }
    }

    return $output;
  }

  /**
   * @param $subfield_condition_array
      $subfield_condition_array = array(
        array(
          'subfield_name' => 'question_tid',
          'subfield_value' => 3006,
        ),
        array(
          'subfield_name' => 'refer_tid',
          'subfield_value' => 2600,
        ),
      );
   *
   * @return field array values
   \Drupal::getContainer()->get('flexinfo.field.service')->getReactsetFieldFirstValueWithSubfieldCondition();
   */
  public function getReactsetFieldFirstValueWithSubfieldCondition($entity = NULL, $field_name = 'field_evaluation_reactset', $subfield = NULL, $subfield_condition_array = array()) {
    $output = NULL;
    if ($entity) {
      $result = $entity->get($field_name)->getValue();

      if ($subfield_condition_array && is_array($subfield_condition_array)) {
        foreach ($result as $row) {

          // match all subfield condition
          foreach ($subfield_condition_array as $subfield_condition) {
            if ($row[$subfield_condition['subfield_name']] == $subfield_condition['subfield_value']) {

            }
            else {
              continue 2;
            }
          }

          $output = $row[$subfield];
        }
      }
    }

    return $output;
  }

  /**
   * @param $subfield_condition_array
      $subfield_condition_array = array(
        array(
          'subfield_name' => 'question_tid',
          'subfield_value' => 3006
        ),
        array(
          'subfield_name' => 'refer_tid',
          'subfield_value' => 2600
        ),
      );
   *
   * @return field array values
   \Drupal::getContainer()->get('flexinfo.field.service')->getReactsetFieldFirstValueWithSubfieldCondition();
   */
  public function getReactsetFieldFirstValueCollectionWithSubfieldCondition($entity_array = array(), $field_name = 'field_evaluation_reactset', $subfield = NULL, $subfield_condition_array = array()) {
    $output = array();

    if (is_array($entity_array)) {
      foreach ($entity_array as $entity) {
        $output[] = $this->getReactsetFieldFirstValueWithSubfieldCondition($entity, $field_name, $subfield, $subfield_condition_array);
      }
    }

    return $output;
  }

  /**
   * Entity reference
   * @param $bundle, user bundle is 'user'
   * @return vocabulary_name vid
   */
  public function getReferenceVidByFieldName($field_name = NULL, $bundle = NULL, $entity_type = 'taxonomy_term') {
    $vocabulary_name = NULL;

    if ($field_name && $bundle) {
      if ($this->checkBundleHasField($entity_type, $bundle, $field_name)) {
        $FieldConfig = FieldConfig::loadByName($entity_type, $bundle, $field_name);

        // @return target_bundles array, most of case, only one option
        if (isset($FieldConfig->getSettings()['handler_settings'])) {
          $target_bundles = $FieldConfig->getSettings()['handler_settings']['target_bundles'];

          if (is_array($target_bundles)) {
            $vocabulary_name = current($target_bundles);
          }
        }
      }
    }

    return $vocabulary_name;
  }

  /** - - - - - - Update - - - - - - - - - - - - - - - - - - - - - - - - - -  */
  /**
   * @param
   *  $entity_type = 'taxonomy_term'
   *  $field_name = 'field_page_city';

   \Drupal::getContainer()->get('flexinfo.field.service')->updateFieldValue('taxonomy_term', $entity, 'field_form_questionset', $values);
   \Drupal::getContainer()->get('flexinfo.field.service')->updateFieldValue('node', $entity, 'field_page_city', $values);
   */
  public function updateFieldValue($entity_type = 'node', $entity = NULL, $field_name = NULL, $new_field_values = array()) {
    $field = $entity->get($field_name);
    if ($field->getName() == $field_name) {
      $field->setValue($new_field_values);
      $result = $entity->save();

      if ($result = SAVED_UPDATED) {
        if (\Drupal::currentUser()->id() == 1) {
          dpm('successful update  - ' . $entity->id() . ' - updateFieldValue()');
        }
      }
      else {
        if (\Drupal::currentUser()->id() == 1) {
          dpm('fail to update  - ' . $entity->id() . ' - updateFieldValue()');
        }
      }
    }
  }

  /**
   * @param
   */
  public function updateAdditionalFieldValue($entity_type = 'node', $entity = NULL, $field_name = NULL, $new_field_values = array()) {
    $field = $entity->get($field_name);
    if ($field->getName() == $field_name) {
      $old_values = $field->getValue();

      $old_field_values = array();
      if (is_array($old_values)) {
        foreach ($old_values as $key => $value) {
          foreach ($value as $sub_value) {
           $old_field_values[] = $sub_value;
          }
        }
      }

      $merge_values = array_merge($old_field_values, $new_field_values);
      $unique_values = array_unique($merge_values);

      $field->setValue($unique_values);
      $result = $entity->save();

      if ($result = SAVED_UPDATED) {
        if (\Drupal::currentUser()->id() == 1) {
          dpm('successful update  - ' . $entity->id() . ' - updateFieldValue()');
        }
      }
      else {
        if (\Drupal::currentUser()->id() == 1) {
          dpm('fail to update  - ' . $entity->id() . ' - updateFieldValue()');
        }
      }
    }
  }

  /**
   * @param
   *  $entity_type = 'taxonomy_term'
   *  $field_name = 'field_page_city';

   \Drupal::getContainer()->get('flexinfo.field.service')->updateTermEvaluationFormQuestionSetValue('taxonomy_term');
   */
  public function updateTermEvaluationFormQuestionSetValue($entity_type = 'taxonomy_term', $field_name = 'field_evaluationform_questionset') {
    $vid = 'evaluationform';

    $search_question_tid = 5347; // 2730;
    $new_question_tid = 5621;    // replace $search_question_tid to $new_question_tid 3006
    $additional_question_tid = 5622;   // 2843

    $query_container = \Drupal::getContainer()->get('flexinfo.queryterm.service');
    $query = $query_container->queryTidsByBundle($vid);
    $group = $query_container->groupStandardByFieldValue($query, 'field_evaluationform_questionset', $search_question_tid);
    $query->condition($group);
    $evaluationform_tids = $query_container->runQueryWithGroup($query);

    foreach ($evaluationform_tids as $entity_id) {
      $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);

      $field = $entity->get($field_name);
      $field_value = $entity->get($field_name)->getValue();
      $new_field_values = $field_value;

      $FieldAllTargetIds = $this->getFieldAllTargetIds($entity, $field_name);

      if (in_array($search_question_tid, $FieldAllTargetIds)) {
        $this_question_key = array_search($search_question_tid, $FieldAllTargetIds);

        // update one, replace $search_question_tid to $new_question_tid
        $new_field_values[$this_question_key]['target_id'] = $new_question_tid;

        // insert new one
        if ($additional_question_tid) {
          $additional_question_key =($this_question_key + 1);
          array_splice($new_field_values, $additional_question_key, 0, $additional_question_tid);
          $new_field_values[$additional_question_key] = array(
            'target_id' => $additional_question_tid,
          );
        }

        \Drupal::getContainer()->get('flexinfo.field.service')->updateFieldValue('taxonomy_term', $entity, $field_name, $new_field_values);
      }
    }
  }

  /**
   * @todo
   * change evaluation question answer batch
   * copy field_evaluation_reactset one answer value to another one
   */
  public function copyNodeEvaluationQuestionAnswerValue($entity_type = 'node', $field_name = 'field_evaluation_reactset') {
    $meeting_nid = 54712;

    $search_question_tid = 2844;
    $new_question_tid = 3011;

    $new_question_answer = 2;    // replace $search_question_answer to $new_question_answer

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('evaluation');
    $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_meetingnid', $meeting_nid);
    $query->condition($group);
    $evaluation_nids = $query_container->runQueryWithGroup($query);

    $entitys = \Drupal::entityTypeManager()->getStorage($entity_type)->loadMultiple($evaluation_nids);
    foreach ($entitys as $entity) {

      $search_question_answer = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getReactsetFieldFirstValue($entity, 'field_evaluation_reactset', 'question_answer', $search_question_tid);

      $reasctset_values = $entity->get('field_evaluation_reactset')->getValue();

      // new
      $new_question_answer = array(
        'question_tid' => $new_question_tid,
        'question_answer' => $search_question_answer,
        'refer_uid' => 0,
        'refer_tid' => 0,
        'refer_other' => NULL,
      );
      $reasctset_values[] = $new_question_answer;

      \Drupal::getContainer()->get('flexinfo.field.service')->updateFieldValue($entity_type, $entity, $field_name, $reasctset_values);

      // dpm('Update evaluation to new answer - ' . $entity->id());

    }
  }

  /**
   * @todo
   * change evaluation question answer batch
   * update field_evaluation_reactset answer value
   \Drupal::getContainer()->get('flexinfo.field.service')->updateNodeEvaluationQuestionAnswerValue();
   */
  public function updateNodeEvaluationQuestionAnswerValue($entity_type = 'node', $field_name = 'field_evaluation_reactset') {
    $search_question_tid = 3013;
    $search_question_answer = 5;
    $new_question_answer = 2;    // replace $search_question_answer to $new_question_answer

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('evaluation');

    $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_meetingnid', 43985);
    $query->condition($group);
    $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_reactset.question_tid', $search_question_tid);
    $query->condition($group);

    $evaluation_nids = $query_container->runQueryWithGroup($query);

    drupal_set_message('totally have evaluation nid - ' . count($evaluation_nids));

    $entitys = \Drupal::entityTypeManager()->getStorage($entity_type)->loadMultiple($evaluation_nids);
    foreach ($entitys as $entity) {

      $get_evaluation_answer = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getReactsetFieldFirstValue($entity, 'field_evaluation_reactset', 'question_answer', $search_question_tid);

      if ($get_evaluation_answer == $search_question_answer) {
        $reasctset_values = $entity->get('field_evaluation_reactset')->getValue();

        $column_key = array_search($search_question_tid, array_column($reasctset_values, 'question_tid'));

        if ($column_key > -1) {
          if (isset($reasctset_values[$column_key]['question_tid'])) {
            if ($reasctset_values[$column_key]['question_tid'] == $search_question_tid) {
              $reasctset_values[$column_key]['question_answer'] = $new_question_answer;
              \Drupal::getContainer()->get('flexinfo.field.service')->updateFieldValue($entity_type, $entity, $field_name, $reasctset_values);

              dpm('Update evaluation to new answer - ' . $entity->id());
            }
          }
        }
      }
    }
  }

}
