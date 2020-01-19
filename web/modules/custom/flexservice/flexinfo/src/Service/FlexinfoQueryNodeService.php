<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoQueryNodeService.php.
 */

namespace Drupal\flexinfo\Service;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryFactory;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\TypedData\Plugin\DataType\Timestamp;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\Component\Utility\Timer;

/**
 * An example Service container.
   $FlexinfoQueryNodeService = new FlexinfoQueryNodeService();
   $FlexinfoQueryNodeService->runQueryWithGroup();
 *
   \Drupal::getContainer()->get('flexinfo.querynode.service')->runQueryWithGroup();
 */
class FlexinfoQueryNodeService extends ControllerBase {

  protected $entity_query;

  /**
   * {@inheritdoc}
   */
  public function __construct(QueryFactory $entity_query) {
    $this->entity_query = $entity_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /**
   * @return array, nids
   */
  public function nidsByBundle($node_bundle = NULL) {
    $query = \Drupal::entityQuery('node');

    $query->condition('status', 1);
    $query->condition('type', $node_bundle);

    $result = $query->execute();

    return array_values($result);
  }

  /**
   * @return array, nids
   */
  public function nodesByBundle($node_bundle = NULL) {
    $nodes = array();

    $nids = $this->nidsByBundle($node_bundle);

    if ($nids) {
      $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);
    }

    return $nodes;
  }

  /** - - - - - - query not run execute() - - - - - - - - - - - - - - - - -  */

  /**
   * @return
   */
  public function queryNidsByBundle($node_bundle = NULL) {
    $query = \Drupal::entityQuery('node');

    $query->condition('status', 1);
    $query->condition('type', $node_bundle);

    return $query;
  }

  /** - - - - - - execute - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return array, nids
   */
  public function runQueryWithGroup($query = NULL) {
    $result = $query->execute();

    return array_values($result);
  }

  /** - - - - - - Node Standard Group - - - - - - - - - - - - - - - - - - - - - -  */
  /**
   * @param $field_name like "field_meeting_province", containing
   * @see https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Entity!Query!QueryInterface.php/function/QueryInterface%3A%3Acondition/8.2.x
   */
  public function groupStandardByFieldValue($query = NULL, $field_name = NULL, $field_value = NULL, $operator = NULL, $langcode = NULL) {
    if ($operator == 'IN' || $operator == 'NOT IN') {
      if (is_array($field_value) && count($field_value) == 0) {
        // $field_value cannot be empty array
        $field_value = array(-1);
      }
    }

    $group = $query->andConditionGroup()
      ->condition($field_name, $field_value, $operator);

    return $group;
  }

  /**
   *
   * $query = $query_container->groupCondition($query, $group);
   */
  public function groupCondition($query, $group = NULL) {
    if ($group) {
      $query = $query->condition($group);
    }

    return $query;
  }

  /** - - - - - - Date Group - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * $group = $query_container->groupByMeetingDateTime($query, "2016-01-01T23:30:00", "2016-12-31T23:55:00");
   */
  public function groupByMeetingDateTime($query = NULL, $start_time = NULL, $end_time = NULL) {
    if ($end_time) {
      $group = $query->andConditionGroup()
        ->condition('field_meeting_date', $start_time, '>')
        ->condition('field_meeting_date', $end_time, '<');
    }
    else {
      $group = $query->andConditionGroup()
        ->condition('field_meeting_date', $start_time, '>');
    }

    return $group;
  }
  /**
   *
   */
  public function groupByMeetingTimestamp($query = NULL, $start_timestamp = NULL, $end_timestamp = NULL) {
    $start_time = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToHtmlDate($start_timestamp, 'html_datetime', $format = NULL, 'UTC');

    $end_time = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToHtmlDate($end_timestamp, 'html_datetime', $format = NULL, 'UTC');

    $group = $this->groupByMeetingDateTime($query, $start_time, $end_time);

    return $group;
  }

  /**
   *
   */
  public function groupByEntityCreateDateTime($query = NULL, $start_time = NULL, $end_time = NULL) {
    if ($end_time) {
      $group = $query->andConditionGroup()
        ->condition('created', $start_time, '>')
        ->condition('created', $end_time, '<');
    }
    else {
      $group = $query->andConditionGroup()
        ->condition('created', $start_time, '>');
    }

    return $group;
  }
  /**
   *
   */
  public function groupByEntityCreateTimestamp($query = NULL, $start_timestamp = NULL, $end_timestamp = NULL) {
    $start_time = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToHtmlDate($start_timestamp, 'html_datetime', $format = NULL, 'UTC');

    $end_time = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToHtmlDate($end_timestamp, 'html_datetime', $format = NULL, 'UTC');

    $group = $this->groupByEntityCreateDateTime($query, $start_time, $end_time);

    return $group;
  }

  /** - - - - - - get nids or nodes  - - - - - - - - - - - - - - - - - - - -  */

  /**
   *
   \Drupal::getContainer()->get('flexinfo.querynode.service')->nodeNidsByStandardByFieldValue('meeting', $field_name, $field_value);
   */
  public function nodeNidsByStandardByFieldValue($node_bundle, $field_name, $field_value, $operator = NULL, $langcode = NULL) {
    $query = $this->queryNidsByBundle($node_bundle);
    $group = $this->groupStandardByFieldValue($query, $field_name, $field_value, $operator);
    $query->condition($group);

    $nids = $this->runQueryWithGroup($query);

    return $nids;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.querynode.service')->nodeNidsByTwoStandardByFieldValue('meeting', $field_name, $field_value);
   */
  public function nodeNidsByTwoStandardByFieldValue($node_bundle, $first_field_name, $first_field_value, $first_operator = NULL, $two_field_name, $two_field_value, $two_operator = NULL, $langcode = NULL) {
    $query = $this->queryNidsByBundle($node_bundle);

    $group = $this->groupStandardByFieldValue($query, $first_field_name, $first_field_value, $first_operator);
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, $two_field_name, $two_field_value, $two_operator);
    $query->condition($group);

    $nids = $this->runQueryWithGroup($query);

    return $nids;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.querynode.service')->nodesByStandardByFieldValue('meeting', $field_name, $field_value);
   */
  public function nodesByStandardByFieldValue($node_bundle, $field_name, $field_value, $operator = NULL, $langcode = NULL) {
    $nids = $this->nodeNidsByStandardByFieldValue($node_bundle, $field_name, $field_value, $operator);
    $nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    return $nodes;
  }

  /** - - - - - - wrapper - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   *
   \Drupal::getContainer()->get('flexinfo.querynode.service')->nodeNidsByTwoStandardByFieldValue('meeting', $field_name, $field_value);
   */
  public function wrapperEvaluationNidsByTwoFieldValue($meeting_nid, $meeting_operator = NULL, $question_tid, $question_operator = NULL, $langcode = NULL) {
    $query = $this->queryNidsByBundle('evaluation');

    $group = $this->groupStandardByFieldValue($query, 'field_evaluation_meetingnid', $meeting_nid, $meeting_operator);
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, 'field_evaluation_reactset.question_tid', $question_tid, $question_operator);
    $query->condition($group);

    $nids = $this->runQueryWithGroup($query);

    return $nids;
  }

  /**
   *  @before the name is getEvaluationNids()
   */
  public function wrapperEvaluationNidsByQuestion($meeting_nodes = array(), $question_tid = NULL, $question_answer = NULL) {
    $meeting_nids = \Drupal::getContainer()->get('flexinfo.node.service')->getNidsFromNodes($meeting_nodes);

    // query container
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('evaluation');

    $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_meetingnid', $meeting_nids, 'IN');
    $query->condition($group);

    if ($question_tid) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_reactset.question_tid', $question_tid);
      $query->condition($group);
    }

    if ($question_answer) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_reactset.question_answer', $question_answer);
      $query->condition($group);
    }

    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   * @param $field_value is like - $businessunit_tids, $program_tids
   \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperMeetingNodesByFieldValue($meeting_nodes, $field_name, $field_value);
   */
  public function wrapperMeetingNodesByFieldValue($meeting_nodes = array(), $field_name = NULL, $field_value = NULL, $operator = NULL, $langcode = NULL) {
    $meeting_nids = \Drupal::getContainer()->get('flexinfo.node.service')->getNidsFromNodes($meeting_nodes);

    $query = $this->queryNidsByBundle('meeting');
    $group = $this->groupStandardByFieldValue($query, $field_name, $field_value, $operator);
    $query->condition($group);

    // filter by meeting_nids nids
    $group = $this->groupStandardByFieldValue($query, 'nid', $meeting_nids, 'IN');
    $query->condition($group);

    $filter_meeting_nids = $this->runQueryWithGroup($query);
    $filter_nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($filter_meeting_nids);

    return $filter_nodes;
  }

  /**
   *
   */
  public function wrapperPoolAnswerTypeDataNodes($meeting_nodes = array(), $question_tid = NULL, $operator = NULL) {
    $pool_nodes = $this->nodesByStandardByFieldValue('pool', 'field_pool_questiontid', $question_tid, $operator);

    $meeting_nids = \Drupal::getContainer()->get('flexinfo.node.service')->getNidsFromNodes($meeting_nodes);
    $pool_nodes = \Drupal::getContainer()->get('flexinfo.querynode.service')->poolNodesByPoolMeetingNids($pool_nodes, $meeting_nids);

    return $pool_nodes;
  }

  /** - - - - - - wrapper - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  public function wrapperPoolAnswerIntDataByQuestionTid($meeting_nodes = array(), $question_tid = NULL, $operator = NULL) {
    $pool_answer_data = $this->wrapperPoolAnswerFieldDataByQuestionTid($meeting_nodes, $question_tid, $operator, 'field_pool_answerint');
    return $pool_answer_data;
  }

  public function wrapperPoolAnswerTermDataByQuestionTid($meeting_nodes = array(), $question_tid = NULL, $operator = NULL) {
    $pool_answer_data = $this->wrapperPoolAnswerFieldDataByQuestionTid($meeting_nodes, $question_tid, $operator, 'field_pool_answerterm');
    return $pool_answer_data;
  }

  public function wrapperPoolAnswerTextDataByQuestionTid($meeting_nodes = array(), $question_tid = NULL, $operator = NULL) {
    $pool_answer_data = $this->wrapperPoolAnswerFieldDataByQuestionTid($meeting_nodes, $question_tid, $operator, 'field_pool_answertext');
    return $pool_answer_data;
  }

  public function wrapperPoolAnswerFieldDataByQuestionTid($meeting_nodes = array(), $question_tid = NULL, $operator = NULL, $field_name = 'field_pool_answerint') {
    $pool_nodes = $this->wrapperPoolAnswerTypeDataNodes($meeting_nodes, $question_tid, $operator);

    switch ($field_name) {
      case 'field_pool_answerint':
        $pool_answer_data = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAnswerIntArray($pool_nodes, $field_name);
        break;
      case 'field_pool_answerterm':
        $pool_answer_data = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAnswerTermArray($pool_nodes, $field_name);
        break;
      case 'field_pool_answertext':
        $pool_answer_data = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAnswerTextArray($pool_nodes, $field_name);
        break;

      default:
        $pool_answer_data = array();
        break;
    }

    return $pool_answer_data;
  }

  public function wrapperPoolAnswerIntDataByQuestionTidByReferTid($meeting_nodes = array(), $question_tid = NULL, $refer_tid = NULL) {
    $pool_answer_data = array();

    if ($meeting_nodes) {
      $meeting_nids = \Drupal::getContainer()->get('flexinfo.node.service')->getNidsFromNodes($meeting_nodes);

      $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
      $query = $query_container->queryNidsByBundle('pool');

      $group = $query_container->groupStandardByFieldValue($query, 'field_pool_questiontid', $question_tid);
      $query->condition($group);
      $group = $query_container->groupStandardByFieldValue($query, 'field_pool_referterm', $refer_tid);
      $query->condition($group);
      $group = $query_container->groupStandardByFieldValue($query, 'field_pool_meetingnid', $meeting_nids, 'IN');
      $query->condition($group);

      $pool_nids = $query_container->runQueryWithGroup($query);

      $pool_nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($pool_nids);

      $pool_answer_data = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAnswerIntArray($pool_nodes, 'field_pool_answerint');
    }

    return $pool_answer_data;
  }

  public function wrapperPoolAnswerIntDataByQuestionTidByReferUid($meeting_nodes = array(), $question_tid = NULL, $refer_uid = NULL) {
    $meeting_nids = \Drupal::getContainer()->get('flexinfo.node.service')->getNidsFromNodes($meeting_nodes);

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('pool');

    $group = $query_container->groupStandardByFieldValue($query, 'field_pool_questiontid', $question_tid);
    $query->condition($group);
    $group = $query_container->groupStandardByFieldValue($query, 'field_pool_referuser', $refer_uid);
    $query->condition($group);
    $group = $query_container->groupStandardByFieldValue($query, 'field_pool_meetingnid', $meeting_nids);
    $query->condition($group);

    $pool_nids = $query_container->runQueryWithGroup($query);

    $pool_nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($pool_nids);

    $pool_answer_data = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAnswerIntArray($pool_nodes, 'field_pool_answerint');
    return $pool_answer_data;
  }

  /** - - - - - - other  - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @param same entity node type,
   * @return array,
   *  get intersect
   */
  public function matchNodesByNids($nodes = array(), $nids_array = array()) {
    $output = array();

    if (is_array($nodes)) {
      foreach($nodes as $node) {
        $nid = $node->id();
        if ($nid) {
          if (in_array($nid, $nids_array)) {
            $output[] = $node;
          }
        }
      }
    }

    return $output;
  }

  /** - - - - - - other test - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * Old version
   * @return array,
   */
  public function meetingNidsByTime($meeting_nodes = array(), $start_time = NULL, $end_time = NULL) {
    $output = NULL;

    if ($start_time || $end_time) {
      if (is_array($meeting_nodes)) {

        foreach($meeting_nodes as $node) {

          // 2016-04-25T23:30:00
          $date_time = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue('node', $node, 'field_meeting_date');
          $timestamp = date_format(date_create($date_time, timezone_open('America/Toronto')), "U");

          if ($start_time) {
            if ($timestamp > $start_time) {
              if ($end_time) {
                if ($timestamp < $end_time) {
                  $output[] = $node;
                }
              }
              else {
                $output[] = $node;
              }
            }
          }
          else {
            if ($end_time) {
              if ($timestamp < $end_time) {
                $output[] = $node;
              }
            }
          }

        }
      }
    }
    else {
      $output = $meeting_nodes;
    }

    return $output;
  }

  /**
   * @return array,
   * get each businessunit_tid of program to check in_array()
   */
  public function meetingNodesByBusinessunit($meeting_nodes = array(), $businessunit_tids = array()) {
    $program_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')->programTidsByBusinessunit($businessunit_tids);
    $output = $this->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', $program_tids, 'IN');

    return $output;
  }

  /**
   * @return array,
   * get each businessunit_tid of program to check in_array()
   */
  public function meetingNodesByDiseasestate($meeting_nodes = array(), $diseasestate_tids = array()) {
    $program_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')->programTidsByDiseasestate($diseasestate_tids);
    $output = $this->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', $program_tids, 'IN');

    return $output;
  }

  /**
   * get program tids for businessunit_tids to check in_array()
   */
  public function meetingNodesByTheraparea($meeting_nodes = array(), $theraparea_tids = array()) {
    $program_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')->programTidsByTheraparea($theraparea_tids);
    $output = $this->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', $program_tids, 'IN');

    return $output;
  }

  /**
   * @return array,
   *
   * use preg_match() or substr($date_time, 5, 2) get month number from string "2018-01-01T06:00:00"
   * not below way
   * $timestamp = date_format(date_create($date_time, timezone_open('America/Toronto')), "U");
   * $month_num = \Drupal::service('date.formatter')->format($timestamp, 'page_format_month');
   */
  public function meetingNodesByMonth($meeting_nodes = array(), $months = array()) {
    $output = $this->queryNodesByFieldByMonth($meeting_nodes, 'field_meeting_date', $months);

    return $output;
  }

  /**
   * @return array,
   */
  public function meetingNodesBySpeakerUids($meeting_nodes = array(), $uids = array()) {
    // return $this->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_speaker', $uids, 'IN');

    // wrapperMeetingNodesByFieldValue is slow than below
    $output = array();

    if (is_array($meeting_nodes)) {
      foreach($meeting_nodes as $node) {

        $speaker_uids = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIds($node, 'field_meeting_speaker');

        if (is_array($speaker_uids) && count($speaker_uids) > 0) {
          if (array_intersect($speaker_uids, $uids)) {
            $output[] = $node;
          }
        }
      }
    }

    return $output;
  }

  /**
   * use preg_match() or substr($date_time, 5, 2) get month number from string "2018-01-01T06:00:00"
   * not below way
   * $timestamp = date_format(date_create($date_time, timezone_open('America/Toronto')), "U");
   * $month_num = \Drupal::service('date.formatter')->format($timestamp, 'page_format_month');
   */
  public function queryNodesByFieldByMonth($nodes = array(), $field_name = NULL, $months = array()) {
    $output = array();

    if (is_array($nodes)) {
      foreach($nodes as $node) {

        $date_time = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, $field_name);
        if ($date_time) {

          preg_match("/^20\d\d\-(\d\d)/i", $date_time, $matches);
          if (isset($matches[1])) {

            $month_num = $matches[1];
            if (in_array($month_num, $months)) {
              $output[] = $node;
            }
          }
        }
      }
    }

    return $output;
  }

  /**
   * @return array,
   */
  public function poolNidByMeetingNidByQuestionTid($meeting_nid = NULL, $question_tid = NULL, $referuser_uid = NULL, $referterm_tid = NULL) {
    $query = $this->queryNidsByBundle('pool');

    $group = $this->groupStandardByFieldValue($query, 'field_pool_meetingnid', $meeting_nid);
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, 'field_pool_questiontid', $question_tid);
    $query->condition($group);

    if ($referuser_uid) {
      $group = $this->groupStandardByFieldValue($query, 'field_pool_referuser', $referuser_uid);
      $query->condition($group);
    }

    if ($referterm_tid) {
      $group = $this->groupStandardByFieldValue($query, 'field_pool_referterm', $referterm_tid);
      $query->condition($group);
    }

    $pool_nids = $this->runQueryWithGroup($query);

    $pool_nid = reset($pool_nids);

    return $pool_nid;
  }

  /**
   * @return array,
   */
  public function poolNodesByPoolMeetingNids($pool_nodes = array(), $param_meeting_nids = array()) {
    $output = array();

    if (is_array($pool_nodes)) {
      foreach($pool_nodes as $node) {

        $meeting_nid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($node, 'field_pool_meetingnid');
        if ($meeting_nid) {
          if (in_array($meeting_nid, $param_meeting_nids)) {
            $output[] = $node;
          }
        }
      }
    }

    return $output;
  }

  /**
   * @return array,
   */
  public function poolNodesByPoolMeetingNids2($pool_nids = array(), $param_meeting_nids = array()) {
    $query = $this->queryNidsByBundle('pool');
    $group = $this->groupStandardByFieldValue($query, 'nid', $pool_nids, 'IN');
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, 'field_pool_meetingnid', $param_meeting_nids, 'IN');
    $query->condition($group);


    $pool_nids = $this->runQueryWithGroup($query);
    // $pool_nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($pool_nids);

    return $pool_nids;
  }

}
