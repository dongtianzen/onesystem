<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/entity_create_fields.php');
  _run_batch_entity_create_fields();
 */

function _run_batch_entity_create_fields() {
  $entity_info = array(
    'entity_type' => 'taxonomy_term',  // 'node', 'taxonomy_term', 'user'
    'bundle' => 'company',
  );

  $fields = _entity_fields_info();
  foreach ($fields as $field) {
    _entity_create_fields_save($entity_info, $field);
  }
}

/**
 *
 */
function _entity_fields_info() {
  $fields[] = array(
    'field_name' => 'field_company_bank',
    'type'       => 'string',
    'label'      => t('Bank Name'),
  );
  $fields[] = array(
    'field_name' => 'field_company_accountnum',
    'type'       => 'string',
    'label'      => t('Account Number'),
  );
  $fields[] = array(
    'field_name' => 'field_company_logo',
    'type'       => 'image',
    'label'      => t('Company Logo'),
  );
  $fields[] = array(
    'field_name' => 'field_company_address',
    'type'       => 'string',
    'label'      => t('Company Address'),
  );
  $fields[] = array(
    'field_name' => 'field_company_contacts',
    'type'       => 'string',
    'label'      => t('Contacts'),
  );
  $fields[] = array(
    'field_name' => 'field_company_phone',
    'type'       => 'string',
    'label'      => t('Phone'),
  );
  $fields[] = array(
    'field_name' => 'field_company_stamp',
    'type'       => 'image',
    'label'      => t('Stamp'),
  );

  return $fields;
}

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
function _entity_create_fields_save($entity_info, $field) {
  $field_storage = FieldStorageConfig::create(array(
    'field_name'  => $field['field_name'],
    'entity_type' => $entity_info['entity_type'],
    'type'  => $field['type'],
    'settings' => array(
      'target_type' => 'node',
    ),
  ));
  $field_storage->save();

  $field_config = FieldConfig::create([
    'field_name'  => $field['field_name'],
    'label'       => $field['label'],
    'entity_type' => $entity_info['entity_type'],
    'bundle'      => $entity_info['bundle'],
  ]);
  $field_config->save();

  entity_get_form_display($entity_info['entity_type'], $entity_info['bundle'], 'default')
    ->setComponent($field['field_name'], [
      'settings' => [
        'display' => TRUE,
      ],
    ])
    ->save();

  entity_get_display($entity_info['entity_type'], $entity_info['bundle'], 'default')
    ->setComponent($field['field_name'], [
      'settings' => [
        'display_summary' => TRUE,
      ],
    ])
    ->save();
}

/**
 *
 */
function _entity_create_field_template() {
  entity_create('field_storage_config', array(
    'field_name'  => 'field_page_large_text',
    'entity_type' => 'node',                 // 'taxonomy_term'
    'type'        => 'text_with_summary',    // 'entity_reference', 'image'
  ))->save();

  entity_create('field_config', array(
    'field_name'  => 'field_page_large_text',
    'label'       => 'Large Text',
    'entity_type' => 'node',                 // 'taxonomy_term'
    'bundle'      => 'page',
    // optional
    'required'    => FALSE,
    'description' => t('your description'),
  ))->save();

  entity_get_form_display('node', 'page', 'default')
    ->setComponent('field_page_large_text', [
      'settings' => [
        'display' => TRUE,
      ],
    ])
    ->save();

  entity_get_display('node', 'page', 'default')
    ->setComponent('field_page_large_text', [
      'label' => 'above',
      'weight' => 20,
      'settings' => [
        'display_summary' => TRUE,
      ],
      'type' => 'string',
    ])
    ->save();
}

/**
 * Use entity_create('field_entity', $definition)->save().
 */
function _entity_create_fields($entity_info, $field) {
  entity_create('field_storage_config', array(
    'field_name'  => $field['field_name'],
    'entity_type' => $entity_info['entity_type'],
    'type'  => $field['type'],
  ))->save();

  entity_create('field_config', array(
    'field_name'  => $field['field_name'],
    'label'       => $field['label'],
    'entity_type' => $entity_info['entity_type'],
    'bundle'      => $entity_info['bundle'],
    // optional
    // 'required'    => isset($field['required']) ? TRUE : FALSE,
    // 'description' => isset($field['description']) ? $field['description'] : NULL,
  ))->save();

  entity_get_form_display($entity_info['entity_type'], $entity_info['bundle'], 'default')
    ->setComponent($field['field_name'], [
      'settings' => [
        'display' => TRUE,
      ],
    ])
    ->save();

  entity_get_display($entity_info['entity_type'], $entity_info['bundle'], 'default')
    ->setComponent($field['field_name'], [
      'settings' => [
        'display_summary' => TRUE,
      ],
    ])
    ->save();
}
