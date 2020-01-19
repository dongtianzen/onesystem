<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/entity_export/export_user.php');
  drupal_set_time_limit(0);
  _run_entity_export_meeting();
 */

_run_entity_export_meeting();
function _run_entity_export_meeting() {
  $output = _get_entity_nodes();

  $json = json_encode($output, JSON_UNESCAPED_UNICODE);
  dpm($json);
}

function _get_entity_nodes() {
  $output = array();

  $field_names = _node_meeting_method_collections();

  $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
  $meeting_nodes = $query_container->nodesByBundle('meeting');
  dpm(count($meeting_nodes));
  $entity_type = 'node';

  foreach ($meeting_nodes as $key => $entity) {
    if ($key < 4510) {
      foreach ($field_names as $field_name) {
        $field = \Drupal\field\Entity\FieldStorageConfig::loadByName($entity_type, $field_name);

        if (($field->getType() == 'entity_reference')) {
          if ($field_info['settings']['target_type'] == 'user') {
            $output[$entity->id()][$field_name]['value'] = \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldAllTargetIdsUserNames($entity, $field_name);
          }
          else {
            $output[$entity->id()][$field_name]['value'] = \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldAllTargetIdsTermNames($entity, $field_name);
          }

        }
        else {
          $output[$entity->id()][$field_name]['value'] = \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldAllValues($entity, $field_name);
        }


      }
    }
  }

  return $output;
}

function _node_meeting_method_collections() {
  $output = array(
    'field_meeting_address',
    'field_meeting_city',
    'field_meeting_province',
    'field_meeting_catering',
    'field_meeting_date',
    'field_meeting_evaluationform',
    'field_meeting_evaluationnum',
    'field_meeting_foodcost',
    'field_meeting_honorarium',
    'field_meeting_latitude',
    'field_meeting_location',
    'field_meeting_longitude',
    'field_meeting_meetingformat',
    'field_meeting_received',
    'field_meeting_module',
    'field_meeting_multitherapeutic',
    'field_meeting_postalcode',
    'field_meeting_program',
    'field_meeting_programclass',
    'field_meeting_representative',
    'field_meeting_signature',
    'field_meeting_speaker',
    'field_meeting_summaryevaluation',
    'field_meeting_usergroup',
    'field_meeting_venuename',
  );

  return $output;
}
