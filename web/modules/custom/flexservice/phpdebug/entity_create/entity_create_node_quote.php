<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/entity_create_node_function.php');
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/entity_create_node_quote.php');
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/ignore/entity_create_node_quote_json.php');
  _run_batch_entity_node_quote();
 */

function _run_batch_entity_node_quote() {
  $nodes_info = json_decode(_entity_node_json_info(), true);
  dpm('count --'  . count($nodes_info));
  if (is_array($nodes_info)) {
    foreach ($nodes_info as $key => $node_info) {
      if ($key > -1) {
        _entity_create_node_quote($node_info);
        dpm('node create -- ' . $key);
      }
    }
  }
}

function _entity_create_node_quote($node_info) {
  $bundle_type = 'quote';
  $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
dpm(_timestamp_convert($node_info['field_quote_create_date']));
  $node = \Drupal\node\Entity\Node::create(array(
    'type' => $bundle_type,
    'title' => 'D7-' . 'Quote-' . $node_info['field_quote_client_name'] . '-' . _timestamp_convert($node_info['field_quote_create_date']),
    'langcode' => $language,
    'uid' => 1,
    'status' => 1,

    // field
    'field_quote_authorizestamp' => $node_info['field_quote_authorize_stamp'],
    'field_quote_clientname'   => $node_info['field_quote_client_name'],
    'field_quote_company'    => _load_terms($node_info['field_quote_company_name']),
    'field_quote_date'   => _timestamp_convert($node_info['field_quote_create_date']),
    'field_repair_temp_repairnid' => $node_info['field_quote_repair_nid'],
    'field_quote_sumprice' => $node_info['field_quote_sum_price'],
    'field_quote_warrantyday'   => $node_info['field_quote_warranty_day'],
  ));

  $node->save();
}

function _entity_node_json_info() {
  $jsons = _entity_d7_node_quote_json();
  return $jsons;
}
