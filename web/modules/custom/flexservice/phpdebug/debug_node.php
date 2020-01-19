<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug_node.php');
  _getPoolNidsByQuestionTid();
 */

/**
 * use Guzzle to get node
 */
function _getNodeByHttpClient($nid = 1507) {
  $internal_url = base_path() . 'node/' . $nid;
  $internal_url = 'http://localhost/mildder8/node/' . $nid;
  dpm($internal_url);

  $response = \Drupal::httpClient()
    ->get($internal_url . '?_format=hal_json', [
      'auth' => ['admin', 'flexia420$'],
    ]);

  $json_string = (string) $response->getBody();
  dpm($json_string);
}

function _postNodeByHttpClient($nid = 1507) {
  $internal_url = 'http://localhost/mildder8/node/' . $nid;
  dpm($internal_url);

  $serialized_entity = json_encode([
    'title' => [['value' => 'Example node title UPDATED']],
    'type' => [['target_id' => 'article']],
    '_links' => ['type' => [
      'href' => 'http://localhost/mildder8/rest/type/node/article'
    ]],
  ]);

  $response = \Drupal::httpClient()
    ->patch($internal_url . '?_format=hal_json', [
      'auth' => ['admin', 'flexia420$'],
      'body' => $serialized_entity,
      'headers' => [
        'Content-Type' => 'application/hal+json',
        'X-CSRF-Token' => 'wQoY04AKnJ5fpxUJ4MxNl9Un7Gqp1VmUnZd8kD8YuBU'
      ],
    ]);
}

/**
 *
 */
function _getNidsByTermField($tids = array()) {
  $program_tids = array(
    2923,
    2924,
    2939,
    2949,
    2966,
    2967,
    2976,
    2989,
    2990,
    2992,
    2993,
    2994,
    2995,
  );

  $nids = \Drupal::getContainer()
    ->get('flexinfo.querynode.service')
    ->nodeNidsByStandardByFieldValue('meeting', 'field_meeting_program', $program_tids, 'IN');

  dpm(count($nids));

  // delete a vocabulary terms programmatically in Drupal 8
  if (21 > 50) {
    if (is_array($nids)) {
      // entity_delete_multiple($entity_type = 'node', $nids);
    }
  }
}

/**
 *
 */
function _getSignatureByNode($nids = array(30)) {
  $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);
  $signatures = \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($nodes, 'field_meeting_signature');

  dpm($signatures);


  foreach ($nodes as $key => $node) {
    $signature = \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValue($node, 'field_meeting_signature');

    dpm($node->id() . ' - - ' . $signature);
  }

}

/**
 *
 */
function _getPoolNidsByQuestionTid($tid = 3014) {
  $nids = \Drupal::getContainer()
    ->get('flexinfo.querynode.service')
    ->nodeNidsByStandardByFieldValue('pool', 'field_pool_questiontid', $tid);

  dpm(count($nids));
  dpm($nids);

}

/**
 *
 require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug_node.php');
 _getPoolNidsByMeetingNid();
 */
function _getPoolNidsByMeetingNid($nid) {
  $nids = \Drupal::getContainer()
    ->get('flexinfo.querynode.service')
    ->nodeNidsByStandardByFieldValue('pool', 'field_pool_meetingnid', $nid);

  $nids = \Drupal::getContainer()
    ->get('flexinfo.querynode.service')
    ->nidsByBundle($node_bundle = 'repair');

  dpm(count($nids));
  dpm($nids);

  if ($nids) {
    entity_delete_multiple($entity_type = 'node', $nids);
  }
}

/**
 *
 */
function _getEvaluationNidsByMeetingNid($nid) {
  $nids = \Drupal::getContainer()
    ->get('flexinfo.querynode.service')
    ->nodeNidsByStandardByFieldValue('evaluation', 'field_evaluation_meetingnid', $nid);

  dpm(count($nids));
  dpm($nids);
}


/**
 *
 */
function _updateMeetingNodesFieldValue() {
  /**
   *     deliverabletype       programclass
   * Accredited  5597                  2469
   * OLA         5596                  2470
   * Symposia    5595                  2471
   */

  $nids = \Drupal::getContainer()
    ->get('flexinfo.querynode.service')
    ->nodeNidsByStandardByFieldValue('meeting', 'field_meeting_programclass', 2471);
  dpm(count($nids));
  dpm(($nids));

  if (210 > 50) {
    if (is_array($nids)) {
      $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

      foreach ($nodes as $node) {
        if ($node->id() < 0) {
          // \Drupal::getContainer()->get('flexinfo.field.service')->updateFieldValue('node', $node, 'field_meeting_programclass', 2471);
        }
      }
    }
  }
}
