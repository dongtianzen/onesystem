<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/import_data/entity_create_node_meeting.php');
  drupal_set_time_limit(0);
  _run_batch_entity_create_meeting();
 */

use Drupal\Component\Utility\Timer;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

function _run_batch_entity_create_meeting() {
  $entity_bundle = 'meeting';

  $name = 'time_one';
  Timer::start($name);

  $node_array = \Drupal::getContainer()
    ->get('flexinfo.json.service')
    ->fetchConvertJsonToArrayFromInternalPath('/modules/custom/flexrepo/phpdebug/import_data/entity_create_node_meeting_json.json');

  $cheat_sheet = array();
  if (is_array($node_array)) {
    foreach ($node_array as $meeting_nid => $row) {
      if ($meeting_nid > 0) {
        $result = _entity_create_meeting($row, $entity_bundle, $meeting_nid);

        if (!empty($result)) {
          $cheat_sheet[$result['from_meeting_nid']] = $result['new_meeting_nid'];
        }
      }
    }
  }

  // print $cheat_sheet
  foreach ($cheat_sheet as $key => $value) {
    dpm($key . ' => ' . $value . ",");
  }

  Timer::stop($name);
  dpm(Timer::read($name) . 'ms');
}

function _entity_create_meeting($row = array(), $entity_bundle, $meeting_nid) {
  $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
  $field_array = array(
    'type' => $entity_bundle,
    'title' => 'Migrate meeting',
    'langcode' => $language,
    'uid' => 1,
    'status' => 1,
  );

  if (is_array($row['field'])) {
    foreach ($row['field'] as $field_name => $value) {

      $field = \Drupal\field\Entity\FieldStorageConfig::loadByName('node', $field_name);
      $field_standard_type = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldStandardType();

      if ($field) {
        if (is_array($value)) {

          // multiple value for each field
          foreach ($value as $row_value) {
            if (in_array($field->getType(), $field_standard_type)) {
              $field_array[$field_name][] = $row_value;
            }
            elseif ($field->getType() == 'datetime') {
              $field_array[$field_name][] = \Drupal::service('date.formatter')->format($row_value, 'page_daytime', $format = '', $timezone = 'UTC');
            }
            elseif ($field->getType() == 'entity_reference') {
              if ($field->getSetting('target_type') == 'taxonomy_term') {

                // special one -- city with province
                if ($field_name == 'field_meeting_city') {
                  $term_tid= \Drupal::getContainer()->get('flexinfo.term.service')->getTidByCityNameAndProvinceName($row_value, $vocabulary = 'city', $province_name = $row['field']['field_meeting_province'][0]);

                  $field_array[$field_name][] = $term_tid;
                }
                else {
                  $vocabulary_name =  \Drupal::getContainer()->get('flexinfo.field.service')->getReferenceVidByFieldName($field_name, $entity_bundle, 'node');

                  $term_tid= \Drupal::getContainer()->get('flexinfo.term.service')->getTidByTermName($row_value, $vocabulary_name);
                  $field_array[$field_name][] = $term_tid;
                }
              }
              else{
                $field_array[$field_name][] = \Drupal::getContainer()->get('flexinfo.user.service')->getUidByUserName($row_value);
              }
            }
            else {
              dpm('no found this field type - ' . $field->getType());
            }
          }
        }
      }
      else {
        dpm('not found field type - nid - ' . $field_name);
      }
    }
  }

  $result = NULL;

  // create node object
  // $node = \Drupal::entityTypeManager()->getStorage('node')->create($field_array);

  // \Drupal::entityTypeManager()->getStorage('node')->save($node);

  // if (isset($node->get('nid')->value)) {
  //   dpm('create node - nid - ' . $node->get('nid')->value);
  //   $result['from_meeting_nid'] = $meeting_nid;
  //   $result['new_meeting_nid']  = $node->get('nid')->value;
  // }

  return $result;
}
