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
  //   'field_name' => 'field_repair_client_name',
  //   'type'       => 'entity_reference',
  //   'label'      => t('客户名称'),
  // );
  $fields[] = array(
    'field_name' => 'field_repair_clientsubname',
    'type'       => 'string',
    'label'      => t('Client Sub Name'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_contactname',
    'type'       => 'string',
    'label'      => t('Contact Name'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_contactphone',
    'type'       => 'string',
    'label'      => t('Contact Phone'),
  );

  // 收取
  $fields[] = array(
    'field_name' => 'field_repair_serialnumber',
    'type'       => 'string',
    'label'      => t('Serial Number'),
  );
  // $fields[] = array(
  //   'field_name' => 'field_repair_device_type',
  //   'type'       => 'entity_reference',
  //   'label'      => t('设备类型'),
  // );
  $fields[] = array(
    'field_name' => 'field_repair_deviceformat',
    'type'       => 'string',
    'label'      => t('Device Format'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_receivenote',
    'type'       => 'string',
    'label'      => t('Receive Note'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_receivedate',
    'type'       => 'datetime',
    'label'      => t('Receive Date'),
  );

  // 初验
  $fields[] = array(
    'field_name' => 'field_repair_checknote',
    'type'       => 'string',
    'label'      => t('Check Note'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_checkissue',
    'type'       => 'string',
    'label'      => t('Check Issue'),
  );
  // $fields[] = array(
  //   'field_name' => 'field_repair_check_staff',
  //   'type'       => 'entity_reference',
  //   'label'      => t('初验人员'),
  // );
  $fields[] = array(
    'field_name' => 'field_repair_checkdate',
    'type'       => 'datetime',
    'label'      => t('Check Date'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_quoteamount',
    'type'       => 'decimal',
    'label'      => t('Quote Amount'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_quotestatus',
    'type'       => 'boolean',
    'label'      => t('Quote Status'),
  );

  // 维修
  $fields[] = array(
    'field_name' => 'field_repair_issuereason',
    'type'       => 'string',
    'label'      => t('Issue Reason'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_repairapproach',
    'type'       => 'string',
    'label'      => t('Repair Approach'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_repairamount',
    'type'       => 'decimal',
    'label'      => t('Quote Amount'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_repairdate',
    'type'       => 'datetime',
    'label'      => t('Repair Date'),
  );

  // 返回
  $fields[] = array(
    'field_name' => 'field_repair_returnamount',
    'type'       => 'decimal',
    'label'      => t('Return Amount'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_returnnote',
    'type'       => 'string',
    'label'      => t('Return Note'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_returndate',
    'type'       => 'datetime',
    'label'      => t('Return Date'),
  );
  $fields[] = array(
    'field_name' => 'field_repair_warrantyday',
    'type'       => 'integer',
    'label'      => t('Warranty Day'),
  );

  return $fields;
}
