<?php

/**
 *
  from bidash run on lillydash
  require_once('/Applications/AMPPS/www/bidash/modules/custom/phpdebug/export_from_d7/export_d7_user.php');
  _load_user_print_info();
 */

function _load_user_print_info() {
  // get all user object
  $users = entity_load('user');
  $field_names = _user_method_collections();

  foreach ($users as $user) {
    if ($user->uid > 1 && $user->uid < 230000000000) {
      $output[$user->uid] = array(
        "name" => $user->name,
        "email" => $user->mail,
        "roles" => $user->roles,
      );

      foreach ($field_names as $field_name => $row) {
        $field_value = NULL;
        $field_info = field_info_field($row['d7_field_name']);

        if ($field_info['type'] == 'entityreference') {
          // check is user or term
          if ($field_info['settings']['target_type'] == 'user') {
            if (isset($user->{$row['d7_field_name']}['und'][0]['target_id'])) {
              $user = user_load($user->{$row['d7_field_name']}['und'][0]['target_id']);
              if (isset($user->name)) {
                $field_value = $user->name;
              }
            }
          }
          else {
            if (isset($user->{$row['d7_field_name']}['und'][0]['target_id'])) {
              foreach ($user->{$row['d7_field_name']}['und'] as $value) {
                $field_term = taxonomy_term_load($value['target_id']);
                if (isset($field_term->name)) {
                  $field_value[] = $field_term->name;
                }
              }
            }
          }
        }
        else {       // text, date
          if (isset($user->{$row['d7_field_name']}['und'][0]['value'])) {
            $field_value = $user->{$row['d7_field_name']}['und'][0]['value'];
          }
        }

        $output[$user->uid]['field'][$row['d8_field_name']] = $field_value;
      }
    }
  }

  $json_data = json_encode($output, JSON_UNESCAPED_UNICODE);
  dpm($json_data);
}

/**
 *
 */
function _user_method_collections() {
  $output = array(
    // array(
    //   'd7_field_name' => 'field_user_first_name',
    //   'd8_field_name' => 'field_user_firstname',
    // ),
    // array(
    //   'd7_field_name' => 'field_user_last_name',
    //   'd8_field_name' => 'field_user_lastname',
    // ),
    // array(
    //   'd7_field_name' => 'field_user_region',
    //   'd8_field_name' => 'field_user_region',
    // ),
    // array(
    //   'd7_field_name' => 'field_user_program_unit',
    //   'd8_field_name' => 'field_user_businessunit',
    // ),
    // array(
    //   'd7_field_name' => 'field_user_therapeutic_area',
    //   'd8_field_name' => 'field_user_theraparea',
    // ),
    // array(
    //   'd7_field_name' => 'field_user_disease_state',
    //   'd8_field_name' => 'field_user_diseasestate',
    // ),
  );

  return $output;
}
