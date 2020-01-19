<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/debug_query.php');
  _query_pool_nodes();
 */
use Drupal\Component\Utility\Timer;

function _query_meeting_nodes() {
  $name = 'time_two';
  Timer::start($name);

  $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
  $query = $query_container->queryNidsByBundle('meeting');

  $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_province', 31);
  $query->condition($group);

  $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_city', 5045);
  $query->condition($group);

  $group = $query_container->groupByMeetingDateTime($query, "2016-01-01T23:30:00", "2016-12-31T23:55:00");
  $query->condition($group);

  $meeting_nids = $query_container->runQueryWithGroup($query);

  $meeting_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($meeting_nids);

  $signature_total = array_sum(
    \Drupal::getContainer()->get('flexinfo.field.service')
    ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_signature')
  );
  $evaluation_nums = array_sum(
    \Drupal::getContainer()->get('flexinfo.field.service')
    ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_evaluationnum')
  );

  dpm($signature_total);
  dpm($evaluation_nums);

dpm($meeting_nids);
  if (\Drupal::currentUser()->id() == 1) {
    Timer::stop($name);
    dpm('time_two ' . Timer::read($name) . 'ms');
  }
}

function _query_pool_nodes() {
  $meeting_nid = 5575;
  $question_tid = 2599;

  $referuser_uid = NULL;
  $referterm_tid = NULL;

  $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
  $query = $query_container->queryNidsByBundle('pool');

  $group = $query_container->groupStandardByFieldValue($query, 'field_pool_meetingnid', $meeting_nid);
  $query->condition($group);

  $group = $query_container->groupStandardByFieldValue($query, 'field_pool_questiontid', $question_tid);
  $query->condition($group);

  if ($referuser_uid) {
    // drupal_set_message('referuser_uid ' . $referuser_uid . ' on lilly_pool_nid ' . $lilly_entity_nid);
    $group = $query_container->groupStandardByFieldValue($query, 'field_pool_referuser', $referuser_uid);
    $query->condition($group);
  }

  if ($referterm_tid) {
    $group = $query_container->groupStandardByFieldValue($query, 'field_pool_referterm', $referterm_tid);
    $query->condition($group);
  }

  $pool_nids = $query_container->runQueryWithGroup($query);
dpm($pool_nids);
  return $pool_nids;
}
