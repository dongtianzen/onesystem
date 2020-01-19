<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoNodeService.php.
 */
namespace Drupal\flexinfo\Service;

use Drupal\Component\Datetime;
use Drupal\Core\Url;

/**
 * An example Service container.
 */
class FlexinfoNodeService {

  /**
   * @deprecated by 2017 Nov
   * @see $this->entityCreateNode();
   */
  public function entitySaveNode($field_array = array()) {
    $this->entityCreateNode($field_array);
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.node.service')->entityCreateNode($field_array);
   */
  public function entityCreateNode($field_array = array()) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->create($field_array);

    \Drupal::entityTypeManager()->getStorage('node')->save($node);

    if (\Drupal::currentUser()->id() == 1) {
      if (isset($node->get('nid')->value)) {
        drupal_set_message('create node - nid - ' . $node->get('nid')->value);
      }
    }
  }

  /**
   *
   */
  public function entityUpdateNode($nid = NULL, $field_array = array()) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($node->id() == $nid) {
      foreach ($field_array as $key => $value) {
        $node->set($key, $value);  // $value is either string or array
      }
      $node->save();
    }

    // unset($node);
  }

  /**
   * @param array $nodes
   * The entitys
   *
   \Drupal::getContainer()->get('flexinfo.node.service')->getNidsFromNodes($nodes);
   */
  public function getNidsFromNodes($nodes = array()) {
    $nids = array();

    if (is_array($nodes)) {
      foreach ($nodes as $node) {
        $nids[] = $node->id();
      }
    }

    // array_keys looks slow than foreach
    // $nids = array_keys($nodes);

    return $nids;
  }

  /**
   * @param nid
   *
   \Drupal::getContainer()->get('flexinfo.node.service')->getNodeEditLink($nid);
   */
  public function getNodeEditLink($nid = NULL, $link_text = 'Edit') {
    $link = NULL;

    if ($nid) {
      $url = Url::fromUserInput('/node/' . $nid . '/edit');
      $link = \Drupal::l(t($link_text), $url);
    }

    return $link;
  }

  /**
   * @param nid
   */
  public function getNodeViewLink($nid = NULL, $view_text = 'View') {
    $link = NULL;

    if ($nid) {
      $url = Url::fromUserInput('/node/' . $nid);
      $link = \Drupal::l(t($view_text), $url);
    }

    return $link;
  }

  /** - - - - - - Evaluation - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   *
   \Drupal::getContainer()->get('flexinfo.node.service')->entityCreatePoolFromEvaluation($entity);
   */
  public function entityCreatePoolFromEvaluation($entity = NULL) {
    $meeting_nid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($entity, 'field_evaluation_meetingnid');

    if ($meeting_nid) {
      $reactset_value = $entity->get('field_evaluation_reactset')->getValue();

      if (is_array($reactset_value)) {
        foreach ($reactset_value as $row) {

          $question_tid = $row['question_tid'];
          if ($question_tid) {

            $question_type = NULL;
            $question_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($question_tid);
            if ($question_term) {
              if ($question_term->getVocabularyId() == 'questionlibrary') {
                $question_type = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_fieldtype');
              }
            }

            // get all evaluation for this $meeting_nid and this $question_tid
            $evaluation_nids = \Drupal::getContainer()
              ->get('flexinfo.querynode.service')
              ->wrapperEvaluationNidsByTwoFieldValue($meeting_nid, NULL, $question_tid);

            if (is_array($evaluation_nids)) {

              $evaluation_nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple($evaluation_nids);
              $refer_field_array = NULL;
              $refer_field = NULL;
              $refer_value = NULL;

              // check one evaluation_node include 'refer_uid' or 'refer_tid'
              if ($row['refer_uid']) {
                $refer_field_array['field_pool_referuser'] = $row['refer_uid'];

                $refer_field = 'refer_uid';
                $refer_value = $row['refer_uid'];
              }
              elseif ($row['refer_tid']) {
                $refer_field_array['field_pool_referterm'] = $row['refer_tid'];

                $refer_field = 'refer_tid';
                $refer_value = $row['refer_tid'];
              }

              $subfield_condition_array = array(
                array(
                  'subfield_name' => 'question_tid',
                  'subfield_value' => $question_tid,
                ),
              );

              if ($refer_field) {
                $subfield_condition_array[] = array(
                  'subfield_name' => $refer_field,
                  'subfield_value' => $refer_value,
                );
              }

              // multiselect tid is 2492, selectkey tid is 2494
              if ($question_type == 2492 || $question_type == 2494) {
                $evaluation_raw_answer = \Drupal::getContainer()
                  ->get('flexinfo.field.service')
                  ->getReactsetFieldAllValueCollectionWithSubfieldCondition($evaluation_nodes, 'field_evaluation_reactset', 'question_answer', $subfield_condition_array);
              }
              else {
                $evaluation_raw_answer = \Drupal::getContainer()
                  ->get('flexinfo.field.service')
                  ->getReactsetFieldFirstValueCollectionWithSubfieldCondition($evaluation_nodes, 'field_evaluation_reactset', 'question_answer', $subfield_condition_array);
              }

              $this->entityCreatePoolFromEvaluationAnswers($evaluation_raw_answer, $meeting_nid, $question_term, $question_type, $refer_field_array);
            }
          }
        }
      }
    }
  }
  /**
   *
   */
  public function entityCreatePoolFromEvaluationAnswers($evaluation_raw_answer = array(), $meeting_nid, $question_term, $question_type, $refer_field_array = NULL) {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $field_array = array();
    $refer_key = NULL;

    // plus refer_field_array
    if ($refer_field_array) {
      $refer_key = current(array_keys($refer_field_array));
      $field_array[$refer_key] = $refer_field_array[$refer_key];
    }

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('pool');

    $group = $query_container->groupStandardByFieldValue($query, 'field_pool_meetingnid', $meeting_nid);
    $query->condition($group);
    $group = $query_container->groupStandardByFieldValue($query, 'field_pool_questiontid', $question_term->id());
    $query->condition($group);

    if ($refer_field_array) {
      $group = $query_container->groupStandardByFieldValue($query, $refer_key, $refer_field_array[$refer_key]);
      $query->condition($group);
    }

    $pool_nids_result = \Drupal::getContainer()->get('flexinfo.querynode.service')->runQueryWithGroup($query);
    $pool_nid = reset($pool_nids_result);

    // checkbox tid is 2490
    if ($question_type == 2490) {
      $evaluation_group_answer = $this->groupEvaluationAnswers($evaluation_raw_answer, $question_term);
      $field_array['field_pool_answerint'] = $evaluation_group_answer;
    }
    // multiselect tid is 2492
    elseif ($question_type == 2492) {
      foreach ($evaluation_raw_answer as $answer_value) {
        $field_array['field_pool_answerterm'][] = $answer_value;
      }
    }
    // radios tid is 2493
    elseif ($question_type == 2493) {
      $evaluation_group_answer = $this->groupEvaluationAnswers($evaluation_raw_answer, $question_term);
      $field_array['field_pool_answerint'] = $evaluation_group_answer;
    }
    // selectkey tid is 2494
    elseif ($question_type == 2494) {
      foreach ($evaluation_raw_answer as $answer_value) {
        $field_array['field_pool_answerterm'][] = $answer_value;
      }
    }
    // textfield tid is 2496
    elseif ($question_type == 2496) {
      $field_array['field_pool_answertext'] = $evaluation_raw_answer;
    }

    // update or create
    if ($pool_nid) {
      $this->entityUpdateNode($pool_nid, $field_array);
    }
    else {
      // prepare new node field
      $field_array['type'] = 'pool';
      $field_array['title'] = 'Evaluation Pool - meeting -' . $meeting_nid . ' - question - ' . $question_term->id();
      if ($refer_key) {
        $field_array['title'] .=  ' - ' . $refer_key . ' - ' . $refer_field_array[$refer_key];
      }

      $field_array['langcode'] = $language;
      $field_array['uid'] = \Drupal::currentUser()->id();
      $field_array['status'] = 1;

      $field_array['field_pool_meetingnid']  = $meeting_nid;
      $field_array['field_pool_questiontid'] = $question_term->id();

      $this->entityCreateNode($field_array);
    }
  }

  /**
   *
   */
  public function groupEvaluationAnswers($evaluation_raw_answer = array(), $question_term = NULL) {
    $evaluation_group_answer = array();

    $evaluation_raw_answer = \Drupal::getContainer()->get('flexinfo.calc.service')->removeArrayEmptyElements($evaluation_raw_answer);
    $evaluation_group_answer = array_count_values($evaluation_raw_answer);

    $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

    // $max_key_value = 0;
    // if ($evaluation_group_answer) {
    //   $max_key_value = array_search(max($evaluation_group_answer), $evaluation_group_answer);
    // }

    for ($i = 1; $i < ($question_scale + 1); $i++) {
      if (!isset($evaluation_group_answer[$i])) {
        $evaluation_group_answer[$i] = 0;
      }
    }

    ksort($evaluation_group_answer);

    return $evaluation_group_answer;
  }

  /**
   *
   */
  public function updateMeetingForEvaluationNum($entity = NULL) {
    $meeting_nid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($entity, 'field_evaluation_meetingnid');

    if ($meeting_nid) {
      $meeting_node  = \Drupal::entityTypeManager()->getStorage('node')->load($meeting_nid);

      if ($meeting_node) {
        $evaluation_nids = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->nodesByStandardByFieldValue('evaluation', 'field_evaluation_meetingnid', $meeting_nid);

        $evaluation_num = count($evaluation_nids);

        $meeting_node->set('field_meeting_evaluationnum', $evaluation_num);
        $meeting_node->save();
      }
    }
  }

  /** - - - - - - meeting - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @param array $meeting_entity
   *
   \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingEvaluationformTid($meeting_entity);
   */
  public function getMeetingEvaluationformTid($meeting_entity = NULL) {
    $evaluationform_tid = NULL;

    if ($meeting_entity && method_exists($meeting_entity, 'getType')) {
      if ($meeting_entity->getType() == 'meeting') {
        $meeting_evaluationform_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($meeting_entity, 'field_meeting_evaluationform');

        if ($meeting_evaluationform_tid) {
          $evaluationform_tid = $meeting_evaluationform_tid;
        }
        else {
          $program_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($meeting_entity, 'field_meeting_program');
          $program_entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($program_tid);

          $evaluationform_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($program_entity, 'field_program_evaluationform');
        }
      }
    }

    return $evaluationform_tid;
  }
  /**
   *
   \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingEvaluationformTerm($meeting_entity);
   */
  public function getMeetingEvaluationformTerm($meeting_entity = NULL) {
    $evaluationform_term = NULL;   //  "empty" object

    $evaluationform_tid = $this->getMeetingEvaluationformTid($meeting_entity);

    if ($evaluationform_tid) {
      $evaluationform_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($evaluationform_tid);
    }

    return $evaluationform_term;
  }

  /**
   * @return Completed Meeting Nodes
   *  evaluated  -- after meeting time && meeting evaluation number > 0
   */
  public function booleanMeetingNodeCompleted($node = NULL) {
    $boolean = FALSE;

    if ($node) {
      $evaluation_num = \Drupal::getContainer()->get('flexinfo.field.service')
        ->getFieldFirstValue($node, 'field_meeting_evaluationnum');

      if ($evaluation_num > 0) {
        $boolean = TRUE;
      }
    }

    return $boolean;
  }

  /**
   * @return Completed Meeting Nodes
   *  evaluated  -- after meeting time && meeting evaluation number > 0
   \Drupal::getContainer()->get('flexinfo.node.service')->getCompletedMeetingNodes($nodes);
   */
  public function getCompletedMeetingNodes($nodes = array()) {
    $output = array();

    if ($nodes && is_array($nodes)) {
      foreach ($nodes as $node) {
        if ($this->booleanMeetingNodeCompleted($node)) {
          $output[] = $node;
        }
      }
    }

    return $output;
  }

  /**
   * @return $status
   *  upcoming   -- before meeting time
   *  evaluated  -- after meeting time && meeting evaluation number > 0
   *  overdue    -- meeting evaluation number = 0 && after meeting time > 2 days (not 48 hours, 2 whole days)
   *  in PROGRESS -- meeting evaluation number = 0 && after meeting time < 2 days (not 48 hours, 2 whole days)
   *
   \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingStatus($node);
   */
  public function getMeetingStatus($node = NULL) {
    $status = NULL;

    $eventStatus = array('Overdue', 'Upcoming', 'Evaluated', 'In Progress');
    $key = rand(0, 3);
    $status = $eventStatus[$key];

    $now_timestamp = strtotime("now");

    $meeting_timestamp = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateTimestamp($node, 'field_meeting_date');

    if ($now_timestamp <= $meeting_timestamp) {
      $status = t('Upcoming');
    }
    else {
      $status = t('Overdue');

      $evaluation_num = \Drupal::getContainer()->get('flexinfo.field.service')
        ->getFieldFirstValue($node, 'field_meeting_evaluationnum');

      if ($evaluation_num > 0) {
        $status = t('Evaluated');
      }
      else {
        $meeting_day_value = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateFormat($node, 'field_meeting_date', 'html_date');

        $now_timestamp_minus_2_day = $now_timestamp - (60 * 60 * 24 * 2);

        // current time is less than (meeting time - 2 whole day)
        if ($now_timestamp_minus_2_day < $meeting_timestamp) {
          $status = t('In Progress');
        }
      }
    }

    return $status;
  }

  /**
   * @deprecated by 2018 Jan
   * @see $this->getMeetingStatusColorCode();
   * @return Integer,
   */
  public function getMeetingStatusColor($node = NULL) {
    $color_code = $this->getMeetingStatusColorCode($node);

    $output = 'bg-' . $color_code;
    return $output;
  }

  /**
   * @return Integer, only RGB color code
   */
  public function getMeetingStatusColorCode($node = NULL) {
    $eventStatus = $this->getMeetingStatus($node);
    $output = NULL;

    switch ($eventStatus) {
      case 'Overdue':
        $output = 'f24b99';
        break;
      case 'Upcoming':
        $output = '00aade';
        break;
      case 'Evaluated':
        $output = 'a5d13f';
        break;
      case 'In Progress':
        $output = 'ffc400';
        break;

      default:
        $output = 'e6e6e6';
        break;
    }

    return $output;
  }

  /**
   * @return Integer, only RGB color code
   */
  public function getMeetingStatusIcon($node = NULL) {
    $eventStatus = $this->getMeetingStatus($node);
    $output = NULL;

    switch ($eventStatus) {
      case 'Overdue':
        $output = 'fa-exclamation-circle';
        break;
      case 'Upcoming':
        $output = ' fa-arrow-circle-right';
        break;
      case 'Evaluated':
        $output = ' fa-check-circle';
        break;
      case 'In Progress':
        $output = ' fa-play-circle';
        break;

      default:
        $output = 'fa-circle-o-notch';
        break;
    }

    return $output;
  }

  /**
   * @return $status
   *  upcoming   -- before meeting time
   *  evaluated  -- after meeting time && meeting evaluation number > 0
   *  overdue    -- meeting evaluation number = 0 && after meeting time > 2 days (not 48 hours, 2 whole days)
   *  in PROGRESS -- meeting evaluation number = 0 && after meeting time < 2 days (not 48 hours, 2 whole days)
   */
  public function getWebinarStatus($node = NULL) {
    $status = NULL;

    $now_timestamp = strtotime("now");

    $meeting_timestamp = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateTimestamp($node, 'field_webinar_date');

    if ($now_timestamp <= $meeting_timestamp) {
      $status = t('Upcoming');
    }
    else {
      $status = t('Completed');
    }

    return $status;
  }

}
