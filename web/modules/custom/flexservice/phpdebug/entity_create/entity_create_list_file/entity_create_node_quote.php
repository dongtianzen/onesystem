<?php

/**
 *
  field type list:
  boolean
  datetime
  decimal
  email
  entity_reference
  file
  float
  image
  integer
  link
  list_integer
  list_string
  telephone
  string         // Text (plain)
  string_long    // Text (plain, long)
  text_long      // Text (formatted, long)
  text_with_summary
 */
function _entity_create_node_repair_field() {
  // $fields[] = array(
  //   'field_name' => 'field_quote_repairnid',
  //   'type'       => 'entity_reference',
  //   'label'      => t('Repair NID'),
  // );
  // $fields[] = array(
  //   'field_name' => 'field_quote_company',
  //   'type'       => 'entity_reference',
  //   'label'      => t('Company Name'),
  // );
  $fields[] = array(
    'field_name' => 'field_quote_clientname',
    'type'       => 'string',
    'label'      => t('Client Name'),
  );
  $fields[] = array(
    'field_name' => 'field_quote_sumprice',
    'type'       => 'decimal',
    'label'      => t('Sum Price'),
  );
  $fields[] = array(
    'field_name' => ' field_quote_warrantyday',
    'type'       => 'integer',
    'label'      => t('Warranty Day'),
  );
  $fields[] = array(
    'field_name' => ' field_quote_date',
    'type'       => 'datetime',
    'label'      => t('Quote Date'),
  );
  $fields[] = array(
    'field_name' => ' field_quote_authorizestamp',
    'type'       => 'boolean',
    'label'      => t('Authorize Stamp'),
  );

  return $fields;
}
