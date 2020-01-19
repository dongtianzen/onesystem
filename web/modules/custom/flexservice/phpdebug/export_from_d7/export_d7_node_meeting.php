<?php

/**
 *
  from bidash run on lillydash
  require_once('/Applications/AMPPS/www/bidash/modules/custom/phpdebug/export_from_d7/export_d7_node_meeting.php');

  drupal_set_time_limit(0);
  _export_d7_node_meeting();
 */

function _export_d7_node_meeting() {
  $output = array();

  $field_method_collections = _node_meeting_method_collections();
  $field_names = $field_method_collections['field_name'];

  $NodeQuery = new NodeQuery();
  $meeting_nids = $NodeQuery->meetingNids();

  $meeting_nodes = node_load_multiple($meeting_nids);

  foreach ($meeting_nodes as $key => $node) {
    if ($key < 30000000) {

      $MeetingInfo = new MeetingInfo($node->nid);
      $TermProgramInfo = new TermProgramInfo($MeetingInfo->programTid());

      // ARHCC Grand Rounds
      // Revue de l'annee en diabete
      // 2017 Let's Have a Heart to Heart: CV Considerations in Type 2 DM
      if ($MeetingInfo->programTid() == 2895 || $MeetingInfo->programTid() == 3030 || $MeetingInfo->programTid() == 2988) {

      }
      else {
        continue;
      }
      // if ($TermProgramInfo->therapAreaTid() == 83) {     // Alliance
      //   continue;
      // }
      // if ($TermProgramInfo->programUnitTid() == 72) {     // Metabolic
      //   continue;
      // }

      // if ($node->created < 1489536000) {  // (your time zone): 3/14/2017, 8:00:00 PM
      //   continue;
      // }

      foreach ($field_names as $field_name => $row) {
        $field_value = NULL;
        $field_info = field_info_field($row['d7_field_name']);

        if ($field_info['type'] == 'entityreference') {
          if (isset($node->{$row['d7_field_name']}['und'][0]['target_id'])) {
            foreach ($node->{$row['d7_field_name']}['und'] as $value) {

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
          if (isset($node->{$row['d7_field_name']}['und'][0]['value'])) {
            foreach ($node->{$row['d7_field_name']}['und'] as $value) {
              $field_value[] = $value['value'];
            }
          }
        }

        $output[$node->nid]['field'][$row['d8_field_name']] = $field_value;
      }
    }
  }

  $json_data = json_encode($output, JSON_UNESCAPED_UNICODE);
  dpm($json_data);

}

/**
 *
 */
function _node_meeting_method_collections() {
  $output['field_name'] = array(
    array(
      'd7_field_name' => 'field_meeting_event_region',
      'd8_field_name' => 'field_meeting_eventregion',
    ),
    array(
      'd7_field_name' => 'field_meeting_event_hub',
      'd8_field_name' => 'field_meeting_eventhub',
    ),
    array(
      'd7_field_name' => 'field_meeting_program',
      'd8_field_name' => 'field_meeting_program',
    ),
    array(
      'd7_field_name' => 'field_meeting_module',
      'd8_field_name' => 'field_meeting_module',
    ),
    array(
      'd7_field_name' => 'field_meeting_program_class',
      'd8_field_name' => 'field_meeting_programclass',
    ),
    array(
      'd7_field_name' => 'field_meeting_evaluation_form',
      'd8_field_name' => 'field_meeting_evaluationform',
    ),
    array(
      'd7_field_name' => 'field_meeting_multi_therape',
      'd8_field_name' => 'field_meeting_multitherapeutic',
    ),
    array(
      'd7_field_name' => 'field_meeting_meeting_format',
      'd8_field_name' => 'field_meeting_meetingformat',
    ),
    array(
      'd7_field_name' => 'field_meeting_user_group',
      'd8_field_name' => 'field_meeting_usergroup',
    ),
    array(
      'd7_field_name' => 'field_meeting_representative',
      'd8_field_name' => 'field_meeting_representative',
    ),
    array(
      'd7_field_name' => 'field_meeting_speaker',
      'd8_field_name' => 'field_meeting_speaker',
    ),
    array(
      'd7_field_name' => 'field_meeting_date',
      'd8_field_name' => 'field_meeting_date',
    ),
    array(
      'd7_field_name' => 'field_meeting_event_me',
      'd8_field_name' => 'field_meeting_eventme',
    ),
    array(
      'd7_field_name' => 'field_meeting_location',
      'd8_field_name' => 'field_meeting_location',
    ),
    array(
      'd7_field_name' => 'field_meeting_venue_name',
      'd8_field_name' => 'field_meeting_venuename',
    ),
    array(
      'd7_field_name' => 'field_meeting_address',
      'd8_field_name' => 'field_meeting_address',
    ),
    array(
      'd7_field_name' => 'field_meeting_province',
      'd8_field_name' => 'field_meeting_province',
    ),
    array(
      'd7_field_name' => 'field_meeting_city',
      'd8_field_name' => 'field_meeting_city',
    ),
    array(
      'd7_field_name' => 'field_meeting_global_city',
      'd8_field_name' => 'field_meeting_globalcity',
    ),
    array(
      'd7_field_name' => 'field_meeting_postal_code',
      'd8_field_name' => 'field_meeting_postalcode',
    ),
    array(
      'd7_field_name' => 'field_meeting_honorarium',
      'd8_field_name' => 'field_meeting_honorarium',
    ),
    array(
      'd7_field_name' => 'field_meeting_food_cost',
      'd8_field_name' => 'field_meeting_foodcost',
    ),
    array(
      'd7_field_name' => 'field_meeting_catering',
      'd8_field_name' => 'field_meeting_catering',
    ),
    array(
      'd7_field_name' => 'field_meeting_signatures',
      'd8_field_name' => 'field_meeting_signature',
    ),
    array(
      'd7_field_name' => 'field_meeting_received',
      'd8_field_name' => 'field_meeting_received',
    ),
    array(
      'd7_field_name' => 'field_meeting_latlon_lat',
      'd8_field_name' => 'field_meeting_latitude',
    ),
    array(
      'd7_field_name' => 'field_meeting_latlon_lon',
      'd8_field_name' => 'field_meeting_longitude',
    ),
    array(
      'd7_field_name' => 'field_meeting_summary_evaluation',
      'd8_field_name' => 'field_meeting_summaryevaluation',
    ),
    array(
      'd7_field_name' => 'field_meeting_evaluation_num',
      'd8_field_name' => 'field_meeting_evaluationnum',
    ),
  );

  return $output;
}
