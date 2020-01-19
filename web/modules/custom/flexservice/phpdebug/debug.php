<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug.php');
  _printMeetingNidsWhenHaveTwoSpeakers();
 */

// use Drupal\Core\Controller\ControllerBase;
// use Drupal\Core\Entity\EntityManagerInterface;
// use Drupal\Core\Entity\Query\QueryFactory;

// use Symfony\Component\DependencyInjection\ContainerInterface;


function _printMeetingNidsWhenHaveTwoSpeakers() {
  $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
  $query = $query_container->queryNidsByBundle('meeting');
  $meeting_nids = $query_container->runQueryWithGroup($query);

  $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($meeting_nids);

  foreach ($nodes as $node) {
    $speaker_uids = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIds($node, 'field_meeting_speaker');
    if (count($speaker_uids) > 1) {
      dpm($node->id() . ' -- ' . count($speaker_uids));
    }
  }

}

function _getValue() {
  $entity = \Drupal::entityTypeManager()->getStorage('node')->load(41659);
  $reactset = $entity->get('field_evaluation_reactset')->getValue();
  dpm($reactset);  // something
}

function _getUidByUserName($user_name = NULL) {
  $output = NULL;

  if ($user_name) {
    $user = user_load_by_name($user_name);
dpm($user);
    if (count($user) > 0) {
      // dpm($user->getUsername());
      // dpm($user->getAccountName());
      // dpm($user->getDisplayName());
      // dpm($user->id());
      // ksm($user);

      // dpm($user->get('uid')->value);

      // $user = \Drupal\user\Entity\User::load(746);
      // dpm($user->get('uid')->value);
      ksm($user);

      $output = $user->get('uid')->value;
    }
  }

  return $output;
}

function _get_json_file() {
  global $base_url;
  $feed_url = $base_url . '/terminfojson/basiccollection/meetingformat';

  $response = \Drupal::httpClient()->get($feed_url, array('headers' => array('Accept' => 'text/plain')));
  // $data = $response->getBody();

  // $term_json = json_decode($data, TRUE);
}

/**
 *
 require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug.php');
 _get_taxonomy_term_tree();
 */
function _get_taxonomy_term_tree() {
  $tids =  \Drupal::getContainer()->get('flexinfo.term.service')->getTidsFromVidName('therapeuticarea');

  $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadMultiple($tids);

  foreach ($terms as $key => $term) {
    // $bu_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($term, 'field_program_theraparea');

    if ($term->id() != 2109 && $term->id() != 2110 && $term->id() != 2111) {
      $filter_tids[] = $term->id();
    }
  }
dpm($filter_tids);
  // delete a vocabulary terms programmatically in Drupal 8
  if (21 > 50) {
    if (is_array($filter_tids)) {
      // entity_delete_multiple($entity_type = 'taxonomy_term', $filter_tids);
    }
  }
}

function _get_term_tid($tid = NULL) {
  $term  = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
  dpm($term->get('name')->value);
  dpm($term->get('tid')->value);
}

function _state_value($key = NULL) {
  $val = \Drupal::state()->get($key);
  dpm($key . ' is :');
  // dpm($val);
}

/**
 *
 require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug.php');
 _service_keyvalue(3);
 */
function _service_keyvalue($key = NULL) {
  $KeyValueFactory = \Drupal::keyValue('dino_variable');
  $val = $KeyValueFactory->get('roar_3');

  dpm($val);

  $val = $KeyValueFactory->delete('roar_3');



  $val = $KeyValueFactory->get('roar_3');
  dpm($val);
}

function _storage_load_node() {
  $nid = 5;
  $node  = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

  $methods = get_class_methods($node);
  // dpm($methods);
  dpm($node->get('title')->value);
  dpm($node->get('body')->value);
  dpm($node->get('field_tags')->value);
}

// use entityTypeManager;
function _create_node() {
  $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
  $field_array = array(
    'type' => 'article',
    'title' => 'The title3',
    'langcode' => $language,
    'uid' => 1,
    'status' => 1,
    'body' => array('The body text'),
    'field_date' => array("2010-01-30"),
    //'field_fields' => array('Custom values'), // Add your custom field values like this
    // 'field_image' => array(
    //   'target_id' => $fileID,
    //   'alt' => "My alt",
    //   'title' => "My title",
    // ),
  );

  // create node object
  $node = \Drupal::entityTypeManager()->getStorage('node')->create($field_array);

  \Drupal::entityTypeManager()->getStorage('node')->save($node);
}

// use \Drupal\node\Entity\Node;
function _create_node2() {
  $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
  $node = \Drupal\node\Entity\Node::create(array(
    'type' => 'article',
    'title' => 'The title2',
    'langcode' => $language,
    'uid' => 1,
    'status' => 1,
    'body' => array('The body text'),
  ));

  $node->save();
}

function _edit_node($nid = NULL) {
  $node  = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
  if ($node) {
    // $node->set($field_name, $field_value);
    $node->set('title', 'new title');

    $node->save();
  }
}

/**
 *
   require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug.php');
  _get_node(3);
 */
function _get_node($nid = NULL) {
  $node  = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
  // Entity reference
  dpm('// tid value - ');
  dpm($node->get('field_page_tags')->target_id);

  dpm('// tids getValue() 0 - ');
  dpm($node->get('field_page_tags')->getValue()[0]['target_id']);

  dpm('// tids getValue() - ');
  dpm($node->get('field_page_tags')->getValue());

  dpm('<hr />');

  // Text (plain)
  dpm('// city value - ');
  dpm($node->get('field_page_city')->value);

  dpm('// city getValue() - ');
  dpm($node->get('field_page_city')->getValue());
}

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug.php');
  _check_cache_exist();
 */
use Drupal\dashpage\Content\DashpageCacheContent;
function _check_cache_exist() {
  $DashpageCacheContent = new DashpageCacheContent();
  $cache_id_group = $DashpageCacheContent->getSnapshotCacheIdGroup();
  $cache_id_regions = $DashpageCacheContent->getSnapshotCacheIdRegion();

  $num = 0;
  foreach ($cache_id_group as $value) {
    foreach ($cache_id_regions as $cache_id_region) {
      $cache_page_url_array[$num]['cache_id'] = $cache_id_region . $value['cache_id'];
      $cache_page_url_array[$num]['start'] = $value['start'];
      $cache_page_url_array[$num]['end'] = $value['end'];
      $cache_page_url_array[$num]['region'] = $cache_id_region;

      $num++;
    }
  }

  foreach ($cache_page_url_array as $row) {
    $cache_id = $row['cache_id'];

    $cache = \Drupal::cache()->get($cache_id);

    if ($cache) {
      dpm('Cache works - ' . $cache_id);
    }
    else {
      dpm('Cache not exist - ' . $cache_id);
    }
  }

}

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/debug.php');
  _run_cache_homepage();
 */
use Drupal\batchinfo\Controller\BatchinfoController;
function _run_cache_homepage() {

  $DashpageCacheContent = new DashpageCacheContent();
  $cache_page_url_array = $DashpageCacheContent->getPageCacheUrlArray();
dpm($cache_page_url_array);
  // $BatchinfoController = new BatchinfoController();
  // $BatchinfoController->runGenerateCacheHomePage();
}
