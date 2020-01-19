<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/import_data/entity_create_term.php');
  drupal_set_time_limit(0);
  _run_batch_entity_create_terms();
 */

/**
 *
 * only check city term
  $check_term_exist = \Drupal::getContainer()
    ->get('flexinfo.term.service')
    ->getTidByCityNameAndProvinceName($row['name'], $vocabulary['vid'], $row['field']['field_city_province'][0]);

 * only check questionlibrary term
  $check_term_exist = \Drupal::getContainer()
    ->get('flexinfo.term.service')
    ->getTidByQuestionNameAndFieldTypeName($row['name'], $vocabulary['vid'], $row['field']['field_queslibr_fieldtype'][0]);
 */

use Drupal\Component\Utility\Timer;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

function _run_batch_entity_create_terms() {
  $vocabulary = array(
    'vid'  => strtolower('city'),              // vid must be small case /lower case
  );

  $name = 'time_one';
  Timer::start($name);

  // only allow these specific terms to import
  $allow_term_tids = array(
    3386,
    3391,
    3394,
    3395,
    3402,
    3408,
  );

  $terms_array = \Drupal::getContainer()
    ->get('flexinfo.json.service')
    ->fetchConvertJsonToArrayFromInternalPath('/modules/custom/flexrepo/phpdebug/import_data/entity_create_term_json.json');
  // dpm(count($terms_array));

  if (is_array($terms_array)) {
    foreach ($terms_array as $tid => $row) {
      $check_term_exist = \Drupal::getContainer()->get('flexinfo.term.service')->getTidByTermName($row['name'], $vocabulary['vid']);

      if ($check_term_exist) {
        continue;
      }

      dpm('import term - ' . $row['name']);
      // if (in_array($tid, $allow_term_tids)) {
      if (1 < 97) {
        // _entity_create_terms($row, $vocabulary);
      }
    }
  }

  Timer::stop($name);
  dpm(Timer::read($name) . 'ms');
}

function _entity_create_terms($row = array(), $vocabulary) {
  $term_value = [
    'name' => $row['name'],
    'vid'  => $vocabulary['vid'],
  ];

  if (isset($row['field']) && is_array($row['field'])) {
    foreach ($row['field'] as $field_name => $subrow) {

      $field = \Drupal\field\Entity\FieldStorageConfig::loadByName('taxonomy_term', $field_name);

      if ($field) {
        $field_standard_type = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldStandardType();

        if (is_array($subrow['value'])) {
          if (in_array($field->getType(), $field_standard_type)) {
            $term_value[$field_name] = $subrow['value'];
          }
          // get target_tid from name
          elseif ($field->getType() == 'entity_reference') {
            foreach ($subrow['value'] as $row_value) {
              if ($field->getSetting('target_type') == 'taxonomy_term') {
                // or
                // $vocabulary_name = $subrow['vid'];
                $vocabulary_name =  \Drupal::getContainer()->get('flexinfo.field.service')->getReferenceVidByFieldName($field_name, $vocabulary['vid']);

                $term_tid= \Drupal::getContainer()->get('flexinfo.term.service')->getTidByTermName($row_value, $vocabulary_name);
                $term_value[$field_name][] = $term_tid;
              }
              else{
                $term_value[$field_name][] = \Drupal::getContainer()->get('flexinfo.user.service')->getUserNameByUid($row_value);
              }
            }
          }
          else {
            dpm('no found this field type - ' . $field->getType());
          }
        }
      }
      else {
        dpm('not found field type - for this field - ' . $field_name);
      }

    }
  }

  $term = Term::create($term_value);
  $term->save();

  if (isset($term->get('tid')->value)) {
    dpm('create entity term - ' . $term->get('name')->value . ' - tid - ' . $term->get('tid')->value);
  }
}
