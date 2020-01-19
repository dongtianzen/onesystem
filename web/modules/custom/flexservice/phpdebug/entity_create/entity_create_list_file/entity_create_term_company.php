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
function _entity_create_term_company_field() {
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
