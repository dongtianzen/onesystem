<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/entity_export/export_term.php');
  drupal_set_time_limit(0);
  _run_entity_export_terms();
 */

/**
 *
 */

use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

use Symfony\Component\HttpFoundation\JsonResponse;

function _run_entity_export_terms($vid = 'BusinessUnit') {
  $output = _get_entity_terms($vid);

  // dpm(count(debug_backtrace());

  $json = json_encode($output, JSON_UNESCAPED_UNICODE);
  dpm($json);
}

function _get_entity_terms($vid = 'BusinessUnit') {
  $output = [];

  $terms = \Drupal::getContainer()->get('flexinfo.term.service')->getFullTermsFromVidName($vid);

  if (is_array($terms)) {
    foreach ($terms as $term) {
      $value = [];

      $value = [
        'name' => $term->getName(),
        'field' => _get_term_field_value($term, $vid),
      ];

      $output[$term->id()] = $value;
    }
  }

  return $output;
}

function _get_term_field_value($entity, $vid) {
  $output = [];

  $entity_type = 'taxonomy_term';

  $fields = _get_vocabulary_fields();
  if ($fields && is_array($fields)) {
    foreach ($fields as $key => $field_name) {
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
        $output[$field_name]['value'] = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldAllValues($entity, $field_name);
      }
      $output[$field_name]['vid'] = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getReferenceVidByFieldName($field_name, $vid, $entity_type);
    }
  }

  return $output;
}

function _get_vocabulary_fields($vid = NULL) {
  $output = [];

  $output = [
    // 'field_theraparea_businessunit',
    'field_queslabel_title',
  ];

  return $output;
}
