<?php

/**
 *
  from bidash8 run on lillydash
  require_once('/Applications/AMPPS/www/bidash/modules/custom/phpdebug/export_from_d7/export_d7_term.php');
  _taxonomyGetTreeTidNames();
  _getEvaluationFormTreeTidNames();
 */

function _taxonomyGetTreeTidNames($vid = NULL) {
  $output = array();

  $term_method_collections = _term_method_collections($vid);
  if ($vid) {
    $method_name = '_term_method_collections' . '_' . $vid;

    if (function_exists($method_name)) {
      $term_method_collections = $method_name();
    }
  }

  $vid = $term_method_collections['vid'];
  $field_names = $term_method_collections['field_name'];

  if ($vid) {
    $terms = taxonomy_get_tree($vid);

    if (is_array($terms)) {
      foreach ($terms as $term) {
        $output[$term->tid]['name'] = $term->name;
        $term = taxonomy_term_load($term->tid);

        foreach ($field_names as $field_name => $row) {
          $field_value = NULL;
          $field_info = field_info_field($row['d7_field_name']);

          if ($field_info['type'] == 'entityreference') {
            if (isset($term->{$row['d7_field_name']}['und'][0]['target_id'])) {
              foreach ($term->{$row['d7_field_name']}['und'] as $value) {

                if ($field_info['settings']['target_type'] == 'user') {
                  $user = user_load($value['target_id']);
                  if (isset($user->name)) {
                    $field_value[] = $user->name;
                  }
                }
                else {
                  $field_term = taxonomy_term_load($value['target_id']);
                  if (isset($field_term->name)) {
                    $field_value[] = $field_term->name;
                  }
                }

              }
            }
          }
          else {       // text, date field
            if (isset($term->{$row['d7_field_name']}['und'][0]['value'])) {
              foreach ($term->{$row['d7_field_name']}['und'] as $value) {
                $field_value[] = $value['value'];
              }
            }
          }

          $output[$term->tid]['field'][$row['d8_field_name']] = $field_value;
        }
      }
    }
  }


  $json_data = json_encode($output, JSON_UNESCAPED_UNICODE);
  dpm($json_data);

  // $file_name = '/Applications/AMPPS/www/bidash/modules/custom/phpdebug/import_data/entity_create_term_json.json';

  // if ($json_data) {
  //   $file = file_save_data($json_data, $file_name, FILE_EXISTS_REPLACE);
  // }
  // else {
  //   // put empty content
  //   $file = file_save_data('', 'public://json/' . $file_name, FILE_EXISTS_REPLACE);
  // }

  return $output;
}

/**
 * special one
 */
function _getEvaluationFormTreeTidNames() {
  $output = array();

  $term_method_collections = _term_method_collections_6();
  $vid = $term_method_collections['vid'];
  $field_names = $term_method_collections['field_name'];

  if ($vid) {
    $terms = taxonomy_get_tree($vid);

    if (is_array($terms)) {
      foreach ($terms as $key => $term) {
        if (1 < 2) {
          $output[$term->tid]['name'] = $term->name;
          $term = taxonomy_term_load($term->tid);

          $field_value = NULL;

          if (isset($term->field_quesset_question['und'])) {
            // custom compound field
            foreach ($term->field_quesset_question['und'] as $value) {
              $field_term = taxonomy_term_load($value['quesset_question_tid']);
              if (isset($field_term->name)) {
                $field_value[] = $field_term->name;
              }
            }
          }

          $output[$term->tid]['field']['field_evaluationform_questionset'] = $field_value;
        }
      }
    }
  }

  $json_data = json_encode($output, JSON_UNESCAPED_UNICODE);
  dpm($json_data);
}

/**
 * vid 11, Business Unit, Program Unit,
 * vid 33, Continents,
 * vid 26, Event Region,
 * vid 9,  Form Field, Form Field Type
 * vid 16, Meeting Format,
 * vid 23, Meeting Location,
 * vid 20, Meeting Received,
 * vid 26, Module, Program Module,  should be below
 * vid 13, Program Class
 * vid 14, Region, Canada Region
 * vid 17, User Group
 */
function _term_method_collections($vid = NULL) {
  $output['vid'] = $vid;
  $output['field_name'] = array(
  );
  return $output;
}

/**
 * Chart Type
 */
function _term_method_collections_7() {
  $output['vid'] = 7;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_charttype_function_name',
      'd8_field_name' => 'field_charttype_functionname',
    ),
  );

  return $output;
}

/**
 * City
 */
function _term_method_collections_18() {
  $output['vid'] = 18;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_city_province',
      'd8_field_name' => 'field_city_province',
    ),
  );

  return $output;
}

/**
 * Country
 */
function _term_method_collections_32() {
  $output['vid'] = 32;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_country_continents',
      'd8_field_name' => 'field_country_continents',
    ),
  );

  return $output;
}

/**
 * Disease State
 */
function _term_method_collections_31() {
  $output['vid'] = 31;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_disease_therapeutic_area',
      'd8_field_name' => 'field_disease_theraparea',
    ),
  );

  return $output;
}

/**
 * Division
 */
function _term_method_collections_30() {
  $output['vid'] = 30;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_division_businessunit',
      'd8_field_name' => 'field_division_businessunit',
    ),
  );

  return $output;
}

/**
 * Evaluation Form
 */
function _term_method_collections_6() {
  $output['vid'] = 6;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_quesset_question',
      'd8_field_name' => 'field_evaluationform_questionset',
    ),
  );

  return $output;
}

/**
 * Event Country
 */
function _term_method_collections_28() {
  $output['vid'] = 28;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_event_country_hub',
      'd8_field_name' => 'field_eventcountry_hub',
    ),
  );

  return $output;
}

/**
 * Event Hub
 */
function _term_method_collections_27() {
  $output['vid'] = 27;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_event_country_flag_name',
      'd8_field_name' => 'field_eventcountry_flagname',
    ),
    array(
      'd7_field_name' => 'field_event_hub_region',
      'd8_field_name' => 'field_eventhub_region',
    ),
  );

  return $output;
}

/**
 * Module, Program Module
 */
function _term_method_collections_26() {
  $output['vid'] = 26;
  $output['field_name'] = array(
    // array(
    //   'd7_field_name' => 'field_module_program',
    //   'd8_field_name' => 'field_module_program',
    // ),
  );

  return $output;
}

/**
 * Program
 */
function _term_method_collections_2() {
  $output['vid'] = 2;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_program_programunit',
      'd8_field_name' => 'field_program_businessunit',
    ),
    array(
      'd7_field_name' => 'field_program_theraparea',
      'd8_field_name' => 'field_program_theraparea',
    ),
    // array(
    //   'd7_field_name' => 'field_program_brand',
    // ),
    array(
      'd7_field_name' => 'field_program_programtype',
      'd8_field_name' => 'field_program_delivertype',
    ),
    array(
      'd7_field_name' => 'field_program_programclass',
      'd8_field_name' => 'field_program_programclass',
    ),
    array(
      'd7_field_name' => 'field_program_division',
      'd8_field_name' => 'field_program_division',
    ),
    array(
      'd7_field_name' => 'field_program_evaluation_form',
      'd8_field_name' => 'field_program_evaluationform',
    ),
    // array(
    //   'd7_field_name' => 'field_program_region',
    //   'd8_field_name' => 'field_program_region',
    // ),
    // array(
    //   'd7_field_name' => 'field_program_disease_state',
    //   'd8_field_name' => 'field_program_diseasestate',
    // ),
  );

  return $output;
}

/**
 * Program Type, Deliverable Type
 */
function _term_method_collections_12() {
  $output['vid'] = 12;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_program_type_regions',
      'd8_field_name' => 'field_delivertype_region',
    ),
  );

  return $output;
}

/**
 * Province
 */
function _term_method_collections_8() {
  $output['vid'] = 8;
  $output['field_name'] = array(
    // d8_field_province_abbr_name is deprecated, now use $term->description__value
    // array(
    //   'd7_field_name' => 'field_field_province_abbr_name',
    //   'd8_field_name' => 'd8_field_province_abbr_name',
    // ),
    array(
      'd7_field_name' => 'field_province_region',
      'd8_field_name' => 'field_province_region',
    ),
  );

  return $output;
}

/**
 * Question Label
 */
function _term_method_collections_5() {
  $output['vid'] = 5;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_queslabel_title',
      'd8_field_name' => 'field_queslabel_title',
    ),
  );

  return $output;
}

/**
 * Question Library
 */
function _term_method_collections_4() {
  $output['vid'] = 4;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_queslibr_ques_field',
      'd8_field_name' => 'field_queslibr_fieldtype',
    ),
    array(
      'd7_field_name' => 'field_queslibr_label',
      'd8_field_name' => 'field_queslibr_label',
    ),
    array(
      'd7_field_name' => 'field_queslibr_length',
      'd8_field_name' => 'field_queslibr_scale',
    ),
    // array(
    //   'd7_field_name' => 'field_queslibr_legend',
    //   'd8_field_name' => 'field',
    // ),
    // array(
    //   'd7_field_name' => 'field_queslibr_chart_title',
    // ),
    array(
      'd7_field_name' => 'field_queslibr_question_type',
      'd8_field_name' => 'field_queslibr_questiontype',
    ),
    array(
      'd7_field_name' => 'field_queslibr_subtitle',
      'd8_field_name' => 'field_queslibr_subtitle',
    ),
    // array(
    //   'd7_field_name' => 'field_queslibr_selectkey_vid',
    // ),
    array(
      'd7_field_name' => 'field_queslibr_module',
      'd8_field_name' => 'field_queslibr_module',
    ),
  );

  return $output;
}

/**
 * Specialty, Specialties
 */
function _term_method_collections_35() {
  $output['vid'] = 35;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_specialty_businessunit',
      'd8_field_name' => 'field_specialty_businessunit',
    )
  );

  return $output;
}

/**
 * Therapeutic Area
 */
function _term_method_collections_15() {
  $output['vid'] = 15;
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_theraparea_unit',
      'd8_field_name' => 'field_theraparea_businessunit',
    ),
    // array(
    //   'd7_field_name' => 'field_theraparea_region',
    //   'd8_field_name' => 'field_theraparea_eventregion',
    //   'd8_vocabulary_name' => 'eventregion',
    // ),
  );

  return $output;
}
