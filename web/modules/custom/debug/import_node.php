<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/debug/import_node.php');
  _run_batch_entity_node_repair();
 */

function _run_batch_entity_node_repair() {
  $nodes_info = _entity_node_json_info();

  dpm('count --'  . count($nodes_info));
  if (is_array($nodes_info)) {
    foreach ($nodes_info as $key => $node_info) {
      if ($key > -1) {
        _entity_create_node_repair($node_info);
        dpm('node create -- ' . $key);
      }
    }
  }
}

function _entity_create_node_repair($node_info) {
  $bundle_type = 'article';
  $language = \Drupal::languageManager()->getCurrentLanguage()->getId();


  $body_value = NULL;
  if (isset($node_info['body']['und'][0]['safe_value'])) {
    $body_value = array(
      'value' => $node_info['body']['und'][0]['safe_value'],
      'format' => 'full_html',
    );
  }

  $path_value = NULL;
  if (isset($node_info['path']['alias'])) {
    $path_value['alias'] = '/' . $node_info['path']['alias'];
  }

  $node = \Drupal\node\Entity\Node::create(array(
    'type' => $bundle_type,
    'title' => $node_info['title'],
    'langcode' => $language,
    'uid' => 1,
    'status' => 1,
    'body' => $body_value,
    'path' => $path_value,
  ));

  $node->save();
}

function _entity_node_json_info() {
  $jsons = _fetchConvertJsonToArrayFromInternalPath('/sites/default/files/json/node_01.json');

  return $jsons;
}

function _fetchConvertJsonToArrayFromInternalPath($file_path = NULL) {
  global $base_url;
  $feed_url = $base_url . $file_path;

  $output = _fetchConvertJsonToArrayFromUrl($feed_url);

  return $output;
}

/**
 * @param $file_path
   $file_path = 'http://lillymedical.education/superexport/meeting/entitylist'
 */
function _fetchConvertJsonToArrayFromUrl($feed_url = NULL) {
  $response = \Drupal::httpClient()
    ->get(
      $feed_url,
      array(
        'headers' => array('Accept' => 'text/plain'),
        // 'auth' => ['admin', 'password']
      )
    );
  $data = $response->getBody();

  $output = json_decode($data, TRUE);

  if (!$output) {
    $this->jsonValidate($data);
  }

  return $output;
}
