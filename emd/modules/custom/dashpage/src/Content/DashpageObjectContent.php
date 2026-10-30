<?php

/**
 * @file
 * Contains \Drupal\dashpage\Content\DashpageObjectContent.
 */

namespace Drupal\dashpage\Content;

use Drupal\Core\Url;
use Drupal\Component\Utility\Unicode;

use Drupal\flexpage\Content\FlexpageBaseJson;
use Drupal\flexpage\Content\FlexpageEventLayout;
use Drupal\flexpage\Content\FlexpageJsonGenerator;
use Drupal\flexpage\Content\FlexpageSampleDataGenerator;

use Drupal\dashpage\Content\DashTabSpeaker;

use Drupal\ngjson\Content\NgjsonObjectContent;

/**
 *
 */
class DashpageGridContent {

  public $FlexpageBaseJson;
  public $FlexpageJsonGenerator;
  public $FlexpageSampleDataGenerator;

  /**
   *
   */
  public function __construct() {
    $this->FlexpageBaseJson = new FlexpageBaseJson();
    $this->FlexpageJsonGenerator = new FlexpageJsonGenerator();
    $this->FlexpageSampleDataGenerator = new FlexpageSampleDataGenerator();
  }

  /**
   *
   */
  public function meetingNodesByEventType($meeting_nodes = array()) {
    $output['labels'][] = 'OLA';
    $output['labels'][] = 'Accredited';
    $output['labels'][] = 'Speaker Tour';
    $output['labels'][] = 'Symposiums';

    $output['nodes'][] = \Drupal::service('flexinfo.querynode.service')
      ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_eventtype', array(2037), 'IN');   // ola
    $output['nodes'][] = \Drupal::service('flexinfo.querynode.service')
      ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_eventtype', array(2036), 'IN');   // Accredited
    $output['nodes'][] = \Drupal::service('flexinfo.querynode.service')
      ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_eventtype', array(2038), 'IN');   // Speaker Tour
    $output['nodes'][] = \Drupal::service('flexinfo.querynode.service')
      ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_eventtype', array(2039), 'IN');   // Symposiums

    return $output;
  }

  /**
   *
   */
  public function meetingNodesByProgramType($meeting_nodes = array()) {
    $output['labels'][] = 'Head Office';
    $output['labels'][] = 'Regional';

    $output['nodes'][] = \Drupal::service('flexinfo.querynode.service')
      ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_programtype', array(2043), 'IN');   // Head Office
    $output['nodes'][] = \Drupal::service('flexinfo.querynode.service')
      ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_programtype', array(2044), 'IN');   // Regional

    return $output;
  }

  /**
   * @return Array data
   */
  public function pageTopFixedSectionData($meeting_nodes = array()) {
    $signature_total = array_sum(
      \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_signature')
    );
    $evaluation_nums = array_sum(
      \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_evaluationnum')
    );
    $viewed_75 = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_viewed75');
    $viewed_100 = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_viewed100');

    $tile_array[] = array(
      'name'  => 'Total Events',
      'value' => count($meeting_nodes),
    );
    $tile_array[] = array(
      'name'  => 'HCP Reached',
      'value' => $signature_total,
    );
    $tile_array[] = array(
      'name'  => 'Evaluations Received',
      'value' => $evaluation_nums,
    );
    $tile_array[] = array(
      'name'  => 'HCP Response',
      'value' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal($evaluation_nums, $signature_total, 0) . '%',
    );

    $tile_array[] = array(
      'name'  => '% of Attendees who viewed 75% of the program',
      'value' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal(array_sum($viewed_75), count($viewed_75), 0) / 100 . '%',
    );
    $tile_array[] = array(
      'name'  => '% of Attendees who viewed 100% of the program',
      'value' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal(array_sum($viewed_100), count($viewed_100), 0) / 100 . '%',
    );

    foreach ($tile_array as $key => $value) {
      $output[] = array(
        'bg-' . \Drupal::service('baseinfo.setting.service')->colorPlateOne($key + 1, FALSE) . ' color-fff',
        $value['name'],
        $value['value'],
      );
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableEventByProvinceByEventtype($meeting_nodes = array(), $limit_row = NULL, $sort_boolean = FALSE) {
    $output = array();

    $province_terms = \Drupal::service('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    if (is_array($province_terms)) {
      foreach ($province_terms as $key => $province_term) {
        $meeting_nodes_by_province_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', array($key));

        $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes_by_province_term)['nodes'];

        $output[] = array(
          'Province' => $province_term->getName(),
          'OLA' => count($meeting_nodes_by_event_type[0]),
          'Accredited' => count($meeting_nodes_by_event_type[1]),
          'Speaker Tour' => count($meeting_nodes_by_event_type[2]),
          'Symposiums' => count($meeting_nodes_by_event_type[3]),
        );
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableHcpByProvinceByEventtype($meeting_nodes = array(), $limit_row = NULL, $sort_boolean = FALSE) {
    $output = array();

    $province_terms = \Drupal::service('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    if (is_array($province_terms)) {
      foreach ($province_terms as $key => $province_term) {
        $meeting_nodes_by_province_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', array($key));

        $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes_by_province_term)['nodes'];

        $signature_total_by_event_type = array_sum(
          \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValueCollection($meeting_nodes_by_event_type[0], 'field_meeting_signature')
        );

        $output[] = array(
          'Province' => $province_term->getName(),
          'OLA' => array_sum(
            \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_event_type[0], 'field_meeting_signature')
          ),
          'Accredited' => array_sum(
            \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_event_type[1], 'field_meeting_signature')
          ),
          'Speaker Tour' => array_sum(
            \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_event_type[2], 'field_meeting_signature')
          ),
          'Symposiums' => array_sum(
            \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_event_type[3], 'field_meeting_signature')
          ),
        );
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableProgramTopRate($meeting_nodes = array(), $limit_row = NULL, $sort_boolean = FALSE) {
    $output = array();

    $program_trees = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('program', 0);
    if (is_array($program_trees)) {

      // first loop get top 10 Program
      $top_program_trees = array();
      foreach ($program_trees as $key => $term) {
        $meeting_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        if (count($meeting_nodes_by_current_term) > 0) {
          $num_meeting_nodes = count($meeting_nodes_by_current_term);

          $top_program_trees[] = array(
            'term' => $term,
            'num_meeting_nodes' => $num_meeting_nodes,
          );

          // for sort order condition criteria
          $sort_value[] = $num_meeting_nodes;
        }
      }

      // sort by set condition
      if (count($top_program_trees) > 0 && $sort_boolean) {
        array_multisort($sort_value, SORT_DESC, $top_program_trees);
      }

      // cut table off to specify number
      if ($limit_row) {
        if (count($top_program_trees) > $limit_row) {
          $top_program_trees = array_slice($top_program_trees, 0, $limit_row);
        }
      }

      /** - - - - - - second loop for top 10 - - - - - - - -  - - - - - - - - - - - - - - -  */
      foreach ($top_program_trees as $key => $top_program) {
        $term = $top_program['term'];

        $meeting_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        if (count($meeting_nodes_by_current_term) > 0) {

          $signature_total = array_sum(
            \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_term, 'field_meeting_signature')
          );
          $evaluation_nums = array_sum(
            \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_term, 'field_meeting_evaluationnum')
          );

          $internal_url = Url::fromUserInput('/ngpage/program/page/' . $term->tid);

          $output[] = array(
            'Program' => \Drupal::l($term->name, $internal_url),
            'Events' => count($meeting_nodes_by_current_term),
            'Reach' => $signature_total,
            'Responses' => $evaluation_nums,
          );
        }
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableSpeakerTopRate($meeting_nodes = array(), $limit_row = NULL, $sort_boolean = FALSE) {
    $output = array();

    $speaker_users = \Drupal::service('flexinfo.queryuser.service')->wrapperUsersByRoleName('speaker');

    if (is_array($speaker_users)) {

      // first loop get top 10 user
      $top_speaker_users = array();
      foreach ($speaker_users as $key => $user) {
        $meeting_nodes_by_current_user = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->meetingNodesBySpeakerUids($meeting_nodes, array($user->id()));

        $num_meeting_nodes = count($meeting_nodes_by_current_user);

        if ($num_meeting_nodes > 0) {
          $top_speaker_users[] = array(
            'num_meeting_nodes' => $num_meeting_nodes,
            'user' => $user,
            'nodes_by_current_user' => $meeting_nodes_by_current_user,
          );

          $sort_value[] = $num_meeting_nodes;
        }
      }

      // sort by set condition
      if (count($top_speaker_users) > 0 && $sort_boolean) {
        array_multisort($sort_value, SORT_DESC, $top_speaker_users);
      }

      // // cut table off to specify number
      // if ($limit_row) {
      //   if (count($top_speaker_users) > $limit_row) {
      //     $top_speaker_users = array_slice($top_speaker_users, 0, $limit_row);
      //   }
      // }

      /** - - - - - - second loop for top 10 - - - - - - - -  - - - - - - - - - - - - - - -  */
      $DashTabSpeaker = new DashTabSpeaker();

      foreach ($top_speaker_users as $user_array) {
        $user = $user_array['user'];

        $meeting_nodes_by_current_user = $user_array['nodes_by_current_user'];

        $num_meeting_nodes = $user_array['num_meeting_nodes'];

        if ($num_meeting_nodes > 0) {
          $signature_total = array_sum(
            \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_user, 'field_meeting_signature')
          );
          $evaluation_nums = array_sum(
            \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_user, 'field_meeting_evaluationnum')
          );

          $speaker_name_link = $DashTabSpeaker->getHtmlModalContent($user);

          $output[] = array(
            'Name' => $speaker_name_link,
            'Events' => $num_meeting_nodes,
            'Reach' => $signature_total,
            'Rating' => NULL,
            // 'Rating' => \Drupal::service('flexinfo.calc.service')->arrayAverageByMeetingNodes($meeting_nodes_by_current_user, 3006),
          );
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  // public function basicTableDataEventByFundingSource($meeting_nodes = array()) {
  //   $thead = array(
  //     'Province',
  //     'OLA',
  //     'Accredited',
  //     'Speaker Tour',
  //     'Symposiums',
  //   );

  //   $tbody = $this->tableEventByProvinceByEventtype($meeting_nodes);

  //   $output = array(
  //     'thead' => array($thead),
  //     'tbody' => $tbody,
  //   );

  //   return $output;
  // }

  /**
   *
   */
  public function basicTableDataHcpByProvinceByEventtype($meeting_nodes = array()) {
    $thead = array(
      'Province',
      'OLA',
      'Accredited',
      'Speaker Tour',
      'Symposiums',
    );

    $tbody = $this->tableHcpByProvinceByEventtype($meeting_nodes);

    $output = array(
      'thead' => array($thead),
      'tbody' => $tbody,
    );

    return $output;
  }

  /**
   *
   */
  public function basicTableDataTopProgram($meeting_nodes = array()) {
    $thead = array(
      'Program Name',
      '# Events',
      'Reach',
      'Responses',
    );

    $tbody = $this->tableProgramTopRate($meeting_nodes);

    foreach ($tbody as $key => $value) {
      $reach[$key] = $value['Reach'];
    }
    array_multisort($reach, SORT_DESC, $tbody);
    $tbody = array_slice($tbody, 0, 10);

    $output = array(
      'thead' => array($thead),
      'tbody' => $tbody,
    );

    return $output;
  }

  /**
   *
   */
  public function basicTableDataTopRateSpeakers($meeting_nodes = array()) {
    $thead = array(
      'Speaker',
      '# Events',
      'Reach',
      'Rating',
    );

    $tbody = $this->tableSpeakerTopRate($meeting_nodes);

    foreach ($tbody as $key => $value) {
      $reach[$key] = $value['Reach'];
    }
    array_multisort($reach, SORT_DESC, $tbody);
    $tbody = array_slice($tbody, 0, 10);

    $output = array(
      'thead' => array($thead),
      'tbody' => $tbody,
    );

    return $output;
  }

  public function chartPieDataTotalEventsPfizerDivision($meeting_nodes = array()) {
    $pih_bu_tids = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperTermTidsByField('businessunit', 'field_businessunit_division', array(2054));
    $peh_bu_tids = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperTermTidsByField('businessunit', 'field_businessunit_division', array(2055));

    $pih_theraparea_tids = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperTermTidsByField('therapeuticarea', 'field_theraparea_businessunit', $pih_bu_tids, 'IN');
    $peh_theraparea_tids = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperTermTidsByField('therapeuticarea', 'field_theraparea_businessunit', $peh_bu_tids, 'IN');

    $chartData = array(
      count(\Drupal::service('flexinfo.querynode.service')
        ->meetingNodesByTheraparea($meeting_nodes, $pih_theraparea_tids)),     // PIH
      count(\Drupal::service('flexinfo.querynode.service')
        ->meetingNodesByTheraparea($meeting_nodes, $peh_theraparea_tids))        // PEH
    );

    foreach ($chartData as $key => $value) {
      $output[] = array(
        "value" => $value,
        "color" => \Drupal::service('baseinfo.setting.service')->colorPlatePieChartOne($key + 1, TRUE),
        "title" => ""
      );
    }

    return $output;
  }

  public function chartPieDataHeadOfficeRegionalPrograms($meeting_nodes = array()) {
    $meeting_nodes_by_event_type = $this->meetingNodesByProgramType($meeting_nodes)['nodes'];

    foreach ($meeting_nodes_by_event_type as $key => $value) {
      $output[] = array(
        "value" => count($value),
        "color" => \Drupal::service('baseinfo.setting.service')->colorPlateDoughnutChartOne($key + 1, TRUE),
        "title" => ""
      );
    }

    return $output;
  }

  public function chartLineDataHcpReachByLocationByMonth($meeting_nodes = array()) {
    $terms = \Drupal::service('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    foreach ($terms as $term) {
      $labels[] = strip_tags($term->getDescription());
    }

    $output = array(
      "labels" => $labels,
      "datasets" => array(
        array(
          "fillColor" => ["#75d1e0","#75d1e0"],
          "strokeColor" => ["#75d1e0","#75d1e0"],
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => array(
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 10), 'field_meeting_signature')), // 'ab'
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 11), 'field_meeting_signature')), // 'bc'
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 12), 'field_meeting_signature')), // 'mb'
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 13), 'field_meeting_signature')), // 'nb'
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 14), 'field_meeting_signature')), // 'nl'
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 15), 'field_meeting_signature')), // 'ns'
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 16), 'field_meeting_signature')), // 'on'
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 17), 'field_meeting_signature')), // 'pei'
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 18), 'field_meeting_signature')), // 'qc'
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection(\Drupal::service('flexinfo.querynode.service')
                ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', 19), 'field_meeting_signature')), // 'sa'
          )
          // "data" => array(
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          // ),
        ),
      )
    );

    return $output;
  }

  public function chartLineDataHcpReachByEventTypeByMonth($meeting_nodes = array()) {
    $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes)['nodes'];

    foreach ($meeting_nodes_by_event_type as $key => $row) {
      $month_num = array();
      for ($j = 1; $j < 13; $j++) {
        $meeting_nodes_by_month = \Drupal::service('flexinfo.querynode.service')->meetingNodesByMonth($row, array($j));
        $month_num[] = array_sum(
          \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValueCollection($meeting_nodes_by_month, 'field_meeting_signature')
        );
      }
      $datasets[] = array(
        "fillColor" => "rgba(151, 187, 205, 0)",
        "strokeColor" => \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE),
        "pointColor" => \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE),
        "pointStrokeColor" => "#fff",
        "pointDotRadius" => 3,
        "data" => $month_num,
        // "data" => array(
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20)
        // )
      );
    }

    $output = array(
      "labels" => \Drupal::service('flexinfo.setting.service')->getMonthNameAbb(),
      "datasets" => $datasets
    );

    return $output;
  }

  public function chartLineDataHcpReachByEventTypeByQuarter($meeting_nodes = array()) {
    $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes)['nodes'];

    foreach ($meeting_nodes_by_event_type as $key => $row) {
      $month_num = array();
      for ($j = 1; $j < 13; $j += 3) {
        $meeting_nodes_by_quarter = \Drupal::service('flexinfo.querynode.service')->meetingNodesByMonth($row, array($j, $j + 1, $j + 2));
        $month_num[] = array_sum(
          \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValueCollection($meeting_nodes_by_quarter, 'field_meeting_signature')
        );
      }
      $datasets[] = array(
        "fillColor" => "rgba(151, 187, 205, 0)",
        "strokeColor" => \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE),
        "pointColor" => \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE),
        "pointStrokeColor" => "#fff",
        "pointDotRadius" => 3,
        "data" => $month_num,
      );
    }

    $output = array(
      "labels" => \Drupal::service('flexinfo.setting.service')->getQuarterNameAbb(),
      "datasets" => $datasets
    );

    return $output;
  }

  public function chartLineDataEventImplementationByMonth($meeting_nodes = array()) {
    $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes)['nodes'];

    foreach ($meeting_nodes_by_event_type as $key => $row) {
      $month_num = array();
      for ($j = 1; $j < 13; $j++) {
        $month_num[] = count(\Drupal::service('flexinfo.querynode.service')->meetingNodesByMonth($row, array($j)));
      }
      $datasets[] = array(
        "fillColor" => "rgba(151, 187, 205, 0)",
        "strokeColor" => \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE),
        "pointColor" => \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE),
        "pointStrokeColor" => "#fff",
        "pointDotRadius" => 3,
        "data" => $month_num,
        // "data" => array(
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20),
        //   rand(1, 20)
        // )
      );
    }

    $output = array(
      "labels" => \Drupal::service('flexinfo.setting.service')->getMonthNameAbb(),
      "datasets" => $datasets
    );

    return $output;
  }

  public function chartLineDataEventImplementationByQuarter($meeting_nodes = array()) {
    $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes)['nodes'];

    foreach ($meeting_nodes_by_event_type as $key => $row) {
      $month_num = array();
      for ($j = 1; $j < 13; $j += 3) {
        $month_num[] = count(\Drupal::service('flexinfo.querynode.service')->meetingNodesByMonth($row, array($j, $j + 1, $j + 2)));
      }
      $datasets[] = array(
        "fillColor" => "rgba(151, 187, 205, 0)",
        "strokeColor" => \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE),
        "pointColor" => \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE),
        "pointStrokeColor" => "#fff",
        "pointDotRadius" => 3,
        "data" => $month_num,
      );
    }

    $output = array(
      "labels" => \Drupal::service('flexinfo.setting.service')->getQuarterNameAbb(),
      "datasets" => $datasets
    );

    return $output;
  }

  public function chartBarDataEventTypeTotals($meeting_nodes = array()) {
    $labels = $this->meetingNodesByEventType($meeting_nodes)['labels'];
    $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes)['nodes'];

    foreach ($meeting_nodes_by_event_type as $key => $row) {
      $chartData[] = count($row);
      $strokeColor[] = \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE);
    }

    $output = array(
      "labels" => $labels,
      "datasets" => array(
        array(
          "fillColor" => $strokeColor,
          "strokeColor" => $strokeColor,
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => $chartData
        ),
      )
    );

    return $output;
  }

  public function chartBarDataLocationEvents($meeting_nodes = array()) {
    $terms = \Drupal::service('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    foreach ($terms as $key => $term) {
      $labels[] = strip_tags($term->getDescription());
      $events_num[] = count(\Drupal::service('flexinfo.querynode.service')->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_province', $key));
    }

    $output = array(
      "labels" => $labels,
      "datasets" => array(
        array(
          "fillColor" => ["#75d1e0","#75d1e0"],
          "strokeColor" => ["#75d1e0","#75d1e0"],
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => $events_num,
          // "data" => array(
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          //   rand(1, 20),
          // ),
        ),
      )
    );

    return $output;
  }

  public function chartBarDataLocationEventType($meeting_nodes = array()) {
    $labels = $this->meetingNodesByEventType($meeting_nodes)['labels'];
    $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes)['nodes'];

    $output = array(
      "labels" => $labels,
      "datasets" => array(
        array(
          "fillColor" => ["#75d1e0","#75d1e0"],
          "strokeColor" => ["#75d1e0","#75d1e0"],
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => array(
            count($meeting_nodes_by_event_type[0]),
            count($meeting_nodes_by_event_type[1]),
            count($meeting_nodes_by_event_type[2]),
            count($meeting_nodes_by_event_type[3]),
          )
        ),
      )
    );

    return $output;
  }

  public function chartBarDataHcpReachEventType($meeting_nodes = array()) {
    $labels = $this->meetingNodesByEventType($meeting_nodes)['labels'];
    $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes)['nodes'];

    $output = array(
      "labels" => $labels,
      "datasets" => array(
        array(
          "fillColor" => ["#75d1e0","#75d1e0"],
          "strokeColor" => ["#75d1e0","#75d1e0"],
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => array(
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection($meeting_nodes_by_event_type[0], 'field_meeting_signature')),
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection($meeting_nodes_by_event_type[1], 'field_meeting_signature')),
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection($meeting_nodes_by_event_type[2], 'field_meeting_signature')),
            array_sum(\Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection($meeting_nodes_by_event_type[3], 'field_meeting_signature')),
          ),
        ),
      )
    );

    return $output;
  }

}

/**
 *
 */
class DashpageLegendContent extends DashpageGridContent {

  /**
   *
   */
  public function getGuestShareLink($meeting_nid = NULL) {
    $output = NULL;

    $start = \Drupal::service('flexinfo.setting.service')->userStartTime();
    $end = \Drupal::service('flexinfo.setting.service')->userEndTime();

    $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;
    $site_path = 'sites';

    if (file_exists($app_root . '/' . $site_path . '/default/settings.local.php')) {
      $output .= \Drupal::request()->getHttpHost() . base_path();
    }
    else {
      $output .= 'https://' . \Drupal::request()->getHost() . '/';
    }
    $output .= 'ngguest/meeting/page/' . $meeting_nid . '/' . $start . '/' . $end;

    return $output;
  }

  /**
   *
   */
  public function blockTileMeetingShareLink($meeting_nid = NULL) {
    $start = \Drupal::service('flexinfo.setting.service')->userStartTime();
    $end = \Drupal::service('flexinfo.setting.service')->userEndTime();

    $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;
    $host = \Drupal::request()->getHost();
    $site_path = 'sites';

    $pdf_link = NULL;
    if (file_exists($app_root . '/' . $site_path . '/default/settings.local.php')) {
      $pdf_link .= base_path();
    }
    else {
      $pdf_link .= 'https://';
      $pdf_link .= \Drupal::request()->getHost() . '/';
    }
    $pdf_link .= 'genpdf/meeting/page/' . $meeting_nid;

    $output = '';
    $output .= '<div class="dropdown float-right">';
      $output .= '<button class="btn btn-default bg-009ddf color-fff dropdown-toggle min-width-20 padding-10" type="button" id="tile-meeting-share-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
        $output .= '<span class="margin-12">SHARE</span>';
        $output .= '<span class="caret"></span>';
      $output .= '</button>';
      $output .= '<ul class="dropdown-menu" aria-labelledby="tile-meeting-share-link" class="color-00a9e0 bg-ffffff">';
        $output .= '<li>';
          $output .= '<span class="margin-left-12">';
            $output .= '<a href="mailto:?subject=Share Link&amp;body=' . $this->getGuestShareLink($meeting_nid) . '" class="color-000 text-decoration-none">';
              $output .= 'Share Link';
            $output .= '</a>';
          $output .= '</span>';
        $output .= '</li>';
        $output .= '<li>';
          $output .= '<span class="margin-left-12">';
            $output .= '<a href="' . $pdf_link . '" class="color-000 text-decoration-none">';
              $output .= 'Download PDF';
            $output .= '</a>';
          $output .= '</span>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @return Array data
   */
  public function legendHeadOfficeRegionalPrograms($meeting_nodes = NULL, $topValue = NULL, $col = 6, $legends = array()) {
    $legend = array(0, 0);

    $chartData = $this->chartPieDataHeadOfficeRegionalPrograms($meeting_nodes);
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        if (isset($row['value'])) {
          $legend[$key] = $row['value'];
        }
      }
    }
    $legend_text = array(
      'Head Office (' . $legend[0] . ')',
      'Regional (' . $legend[1] . ')',
    );

    $legend_color = \Drupal::service('baseinfo.setting.service')->colorPlateDoughnutChartOne(NULL, FALSE);
    $legends = \Drupal::service('flexinfo.chart.service')->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color);

    return $legends;
  }

  /**
   * @return Array data
   */
  public function legendTotalEventsPfizerDivision($meeting_nodes = NULL) {
    $legend = array(0, 0);

    $chartData = $this->chartPieDataTotalEventsPfizerDivision($meeting_nodes);
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        if (isset($row['value'])) {
          $legend[$key] = $row['value'];
        }
      }
    }
    // Division
    $legend_text = array(
      'PIH (' . $legend[0] . ')',
      'PEH (' . $legend[1] . ')',
    );
    $legend_color = \Drupal::service('baseinfo.setting.service')->colorPlatePieChartOne(NULL, FALSE);
    $legends = \Drupal::service('flexinfo.chart.service')->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color);

    return $legends;
  }

  /**
   * @return Array data
   */
  public function legendEventImplementation($meeting_nodes = NULL) {
    $legend = array(0, 0, 0, 0);

    $chartData = $this->meetingNodesByEventType($meeting_nodes)['nodes'];

    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend[$key] = count($row);
      }
    }

    $legend_text = array(
      'OLA (' . $legend[0] . ')',
      'Accredited (' . $legend[1] . ')',
      'Speaker Tour (' . $legend[2] . ')',
      'Symposiums (' . $legend[3] . ')',
    );
    $legend_color = \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne(NULL, FALSE);
    $legends = \Drupal::service('flexinfo.chart.service')->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color);

    return $legends;
  }

  /**
   * @return Array data
   */
  public function legendHcpReachByEventType($meeting_nodes = NULL) {
    $legend = array(0, 0, 0, 0, 0);

    $meeting_nodes_by_event_type = $this->meetingNodesByEventType($meeting_nodes)['nodes'];

    if ($meeting_nodes_by_event_type && is_array($meeting_nodes_by_event_type)) {
      foreach ($meeting_nodes_by_event_type as $key => $row) {
        $legend[$key] = array_sum(
          \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValueCollection($row, 'field_meeting_signature'));
      }
    }

    $legend_text = array(
      'OLA (' . $legend[0] . ')',
      'Accredited (' . $legend[1] . ')',
      'Speaker Tour (' . $legend[2] . ')',
      'Symposiums (' . $legend[3] . ')',
    );
    $legend_color = \Drupal::service('baseinfo.setting.service')->colorPlateLineChartOne(NULL, FALSE);
    $legends = \Drupal::service('flexinfo.chart.service')->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color);

    return $legends;
  }

}

/**
 *
 */
class DashpageBlockContent extends DashpageLegendContent {

  /**
   * @return Array data
   */
  public function blockChartNewJsPie($chartData = NULL, $topValue = NULL, $col = 6, $legends = array()) {
    $blockPara = $this->FlexpageJsonGenerator->getDemo()->chartblockPara($col);
    $blockPara['top'] = array(
      'enable' => true,
      'value' => $topValue
    );

    $blockPara['blockClasses'] = "height-320 overflow-visible";

    $blockPara['middle']['middleBottom'] = NULL;
    $blockPara['middle']['middleMiddle'] = array(
      'middleMiddleMiddleClass' => "col-md-6",
      'middleMiddleRightClass' => "col-md-6",
      'middleMiddleRight' => $legends
    );
    $blockPara['middle']['middleMiddle']['gridColumn'] = "75";

    $output = $this->FlexpageBaseJson->getBlockOne(
      $blockPara,
      $this->FlexpageBaseJson->getChartNewJsPie(NUll, $chartData)
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function blockChartNewJsDoughnut($chartData = NULL, $topValue = NULL, $col = 6, $legends = array()) {
    $blockPara = $this->FlexpageJsonGenerator->getDemo()->chartblockPara($col);
    $blockPara['top'] = array(
      'enable' => true,
      'value' => $topValue
    );
    $blockPara['blockClasses'] = "height-320 overflow-visible";


    $blockPara['middle']['middleBottom'] = NULL;
    $blockPara['middle']['middleMiddle'] = array(
      'middleMiddleMiddleClass' => "col-md-10 col-sm-8 col-xs-12",
      'middleMiddleRightClass' => "col-md-2 col-sm-4 col-xs-12",
      'middleMiddleRight' => $legends
    );
    $blockPara['middle']['middleMiddle']['gridColumn'] = "75";

    $chartPara['chartOptions']['crossText'] = ["", "", ""];
    $chartPara['chartOptions']['percentageInnerCutout'] = 70;
    $chartPara['chartOptions']['crossTextFontSize'] = [0,20,20];

    $output = $this->FlexpageBaseJson->getBlockOne(
      $blockPara,
      $this->FlexpageBaseJson->getChartNewJsDoughnut($chartPara, $chartData)
    );
    return $output;
  }

  /**
   * @return Array data
   */
  public function blockChartNewJsLine($chartData = NULL, $topValue = NULL, $col = 6, $legends = array(), $square_bg_color = 'd6006e') {
    $square_icon = $this->blockTopSquareIcon($topValue, $square_bg_color);

    $blockPara = $this->FlexpageJsonGenerator->getDemo()->chartblockPara($col);
    $blockPara['top'] = array(
      'enable' => true,
      'value'  => $square_icon
    );

    $blockPara['blockClasses'] = "height-320 overflow-visible";

    $blockPara['middle']['middleMiddle'] = array(
      'middleMiddleMiddleClass' => "col-md-8",
      'middleMiddleRightClass' => "col-md-4",
      'middleMiddleRight' => $legends
    );
    $chartPara["chartOptions"]["inGraphDataShow"] = FALSE;
    $chartPara["chartOptions"]["yAxisLabel"] = "";

    $output = $this->FlexpageBaseJson->getBlockOne(
      $blockPara,
      $this->FlexpageBaseJson->getChartNewJsLine($chartPara, $chartData)
    );
    return $output;
  }

  public function blockChartNewJsBar($chartData = NULL, $topValue = NULL, $col = 6, $legends = array(), $square_bg_color = 'd6006e') {
    if (empty($square_bg_color)) {
      $square_icon = $topValue;
    }
    else {
      $square_icon = $this->blockTopSquareIcon($topValue, $square_bg_color);
    }

    $blockPara = $this->FlexpageJsonGenerator->getDemo()->chartblockPara($col);
    $blockPara['top'] = array(
      'enable' => true,
      'value' => $square_icon
    );

    $blockPara['blockClasses'] = "height-320 overflow-visible";

    $ChartDisplayPara["chartOptions"]["barValueSpacing"] = 20;
    $ChartDisplayPara["chartType"] = "Bar";

    $output = $this->FlexpageBaseJson->getBlockOne(
      $blockPara,
      $this->FlexpageBaseJson->getChartNewJsBar($ChartDisplayPara, $chartData)
    );

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshot($meeting_nodes = array(), $evaluationform_tid = NULL, $page_view = NULL) {
    $FlexpageEventLayout = new FlexpageEventLayout();
    $output = $FlexpageEventLayout->blockEventsSnapshot($meeting_nodes, $evaluationform_tid, $page_view);

    return $output;
  }

  /**
   *
   */
  public function blockTileMeetingValue($meeting_node = NULL) {
    $speaker_names_array = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsUserNames($meeting_node, 'field_meeting_speaker');
    $speaker_names = implode(", ", $speaker_names_array);
    $speaker_names = Unicode::truncate($speaker_names, 300, $wordsafe = TRUE, $add_ellipsis = TRUE);

    $output = array(
      array(
        'value' => t('Date'),
        'value_one' => \Drupal::service('flexinfo.field.service')->getFieldFirstValueDateFormat($meeting_node, 'field_meeting_date'),
      ),
      array(
        'value' => t('City'),
        'value_one' => \Drupal::service('flexinfo.field.service')->getFieldFirstTargetIdTermName($meeting_node, 'field_meeting_city'),
      ),
      array(
        'value' => t('Venue'),
        'value_one' => \Drupal::service('flexinfo.field.service')->getFieldFirstValue($meeting_node, 'field_meeting_venuename'),
      ),
      array(
        'value' => t('Evaluation Received'),
        'value_one' => \Drupal::service('flexinfo.field.service')->getFieldFirstValue($meeting_node, 'field_meeting_evaluationnum'),
      ),
      array(
        'value' => t('Moderator'),
        'value_one' => \Drupal::service('flexinfo.field.service')->getFieldFirstTargetIdUserName($meeting_node, 'field_meeting_moderator'),
      ),
      array(
        'value' => t('Participants'),
        'value_one' => \Drupal::service('flexinfo.field.service')->getFieldFirstValue($meeting_node, 'field_meeting_signature'),
      ),
      array(
        'value' => '%' . t('Watched') . ' 75%',
        'value_one' => \Drupal::service('flexinfo.field.service')->getFieldFirstValue($meeting_node, 'field_meeting_viewed75') . '%',
      ),
      array(
        'value' => '%' . t('Watched') . ' 100%',
        'value_one' => \Drupal::service('flexinfo.field.service')->getFieldFirstValue($meeting_node, 'field_meeting_viewed100') . '%',
      ),
      array(
        'value' => t('Speaker'),
        'value_one' => $speaker_names,
        'pdf_col_length' => 192,
      ),
    );

    return $output;
  }

  /**
   *
   */
  public function blockTileMeetingHtml($meeting_node = NULL, $meeting_share_link = TRUE, $meeting_snapshot_link = FALSE) {
    $path_args = \Drupal::service('flexinfo.setting.service')->getCurrentPathArgs();

    $fixed_section_param = $this->blockTileMeetingValue($meeting_node);

    $output = '';
    $output .= '<div class="row margin-left-12 margin-bottom-12">';
      $output .= '<div class="col-md-9">';
        $output .= '<span class="color-00a9e0 font-size-20 font-weight-300">';
          $output .= \Drupal::service('flexinfo.field.service')->getFieldFirstTargetIdTermName($meeting_node, 'field_meeting_program');
        $output .= '</span>';
      $output .= '</div>';

      if ($meeting_share_link && \Drupal::currentUser()->isAuthenticated()) {
        $output .= '<div class="col-md-2 float-right">';
          $output .= $this->blockTileMeetingShareLink($meeting_node->id());
        $output .= '</div>';
      }

    $output .= '</div>';
    $output .= '<div class="margin-left-12 clear-both">';
      foreach ($fixed_section_param as $row) {
        if ($row['value'] == 'Speaker') {
          $html_span_tag_col = '<span class="col-md-12 col-sm-12 padding-top-12">';
        }
        else {
          $html_span_tag_col = '<span class="col-md-3 col-sm-6 padding-top-12">';
        }
        $output .= $html_span_tag_col;
          $output .= '<span class="dashpage-square-text">';
            $output .= $row['value'] . ':';
          $output .= '</span>';
          $output .= '<span class="dashpage-square-text padding-left-6">';
            $output .= $row['value_one'];
          $output .= '</span>';
        $output .= '</span>';
      }
    $output .= '</div>';

    if ($meeting_snapshot_link) {
      if (method_exists($meeting_node, 'id')) {
        $meeting_url = Url::fromUserInput('/ngpage/meeting/page/' . $meeting_node->id());

        $output .= '<div class="col-md-12 margin-top-12 margin-left-12 clear-both">';
          $output .= \Drupal::l(t('Meeting Snapshot'), $meeting_url);
        $output .= '</div>';
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockTileMeeting($meeting_node = NULL, $meeting_share_link = TRUE, $meeting_snapshot_link = FALSE) {
    $option['top'] = array(
      'enable' => FALSE,
    );

    $meeting_tile_html = $this->blockTileMeetingHtml($meeting_node, $meeting_share_link, $meeting_snapshot_link);

    $output = $this->FlexpageBaseJson->getBlockHtmlSnippet($option, $meeting_tile_html);

    return $output;
  }

  /**
   *
   */
  public function blockTopSquareIcon($topValue = NULL, $square_bg_color = 'd6006e') {
    $output = '';
    $output .= '<span class="float-left display-block height-32 width-32 bg-' . $square_bg_color . ' margin-top-4 margin-left-n-16 border-1-eee header-square-icon">';
      $output .= '&nbsp;';
    $output .= '</span>';
    $output .= '<span class="margin-left-18">';
      $output .= $topValue;
    $output .= '</span>';

    return $output;
  }

  /**
   * @param  $tableData is array
    $tableData = array(
      'thead' => $thead,
      'tbody' => $tbody,
    );
   */
  public function generateBasicTableData($tableData = array()) {
    $thead_data = $tableData['thead'];
    $tbody_data = $tableData['tbody'];

    $thead = NULL;
    foreach ($thead_data as $value) {
      $thead .= '<th>';
        $thead .= $value;
      $thead .= '</th>';
    }

    $tbody = NULL;
    foreach ($tbody_data as $row) {
      $tbody .= '<tr>';
      foreach ($row as $value) {
        $tbody .= '<td>';
          $tbody .= $value;
        $tbody .= '</td>';
      }
      $tbody .= '</tr>';
    }

    $output = '';
    $output .= '<div class="wrapper color-616365">';
      $output .= '<table id="Table1" class="table">';
        $output .= '<thead>';
          $output .= '<tr>';
            $output .= $thead;
          $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';
          $output .= $tbody;
        $output .= '</tbody>';
      $output .= '</table>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @return Array data
   */
  public function generateCommonTable($table_data = array(), $top = array()) {
    $output = $this->FlexpageBaseJson->getBlockOne(
      array(
        'class' => "col-md-12",
        'type' => "commonTable",
        'blockClasses' => "height-400 overflow-visible",
        'top' => $top,
      ),
      $this->FlexpageBaseJson->getCommonTable(NUll, $table_data)
    );

    return $output;
  }


  /**
   *
   */
  public function blockHtmlSnippetBasicTable($tableData = NULL, $topValue = NULL, $col_class = 'col-md-6') {
    $option['top'] = array(
      'enable' => TRUE,
      'value' => $topValue          // block top title value
    );

    $output = $this->FlexpageBaseJson->getBlockHtmlSnippet(
      $option,
      $tableData,
      'col-md-6'
    );
    return $output;
  }

}

/**
 * @return php object, not JSON
 */
class DashpageObjectContent extends DashpageBlockContent {

  /**
   * @return php object, not JSON
   */
  public function getFilterProgramTids() {
    $filter_program_tids = array();

    $user_default_diseasestate_tid = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_diseasestate');
    if ($user_default_diseasestate_tid) {
      $filter_program_tids = \Drupal::service('flexinfo.queryterm.service')
        ->programTidsByDiseasestate(array($user_default_diseasestate_tid));
    }
    else {
      $user_default_theraparea_tid = \Drupal::service('user.data')
        ->get('navinfo', \Drupal::currentUser()->id(), 'default_theraparea');
      if ($user_default_theraparea_tid) {
        $filter_program_tids = \Drupal::service('flexinfo.queryterm.service')
          ->programTidsByTheraparea(array($user_default_theraparea_tid));
      }
      else{
        $user_default_businessunit_tid = \Drupal::service('user.data')
          ->get('navinfo', \Drupal::currentUser()->id(), 'default_businessunit');
        if ($user_default_businessunit_tid) {
          $theraparea_tids_by_businessunit_tids = \Drupal::service('flexinfo.queryterm.service')
            ->wrapperTermTidsByField('therapeuticarea', 'field_theraparea_businessunit', array($user_default_businessunit_tid));

          $filter_program_tids = \Drupal::service('flexinfo.queryterm.service')
            ->programTidsByTheraparea($theraparea_tids_by_businessunit_tids);
        }
        else {
          // no filter, it means all

          $filter_program_tids = \Drupal::getContainer()
            ->get('flexinfo.term.service')
            ->getTidsFromVidName('program');
        }
      }
    }

    return $filter_program_tids;
  }

  /**
   * @return php object, not JSON
   */
  public function dashboardSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $NgjsonObjectContent = new NgjsonObjectContent();
    $meeting_nodes = $NgjsonObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output['fixedSection'] = $this->FlexpageBaseJson->generateTileStyleOne($this->pageTopFixedSectionData($meeting_nodes));

    // $output['contentSection'][] = $this->blockChartNewJsPie(
    //   $this->chartPieDataTotalEventsPfizerDivision($meeting_nodes),
    //   "Total Events by Pfizer Division",
    //   "4",
    //   $this->legendTotalEventsPfizerDivision($meeting_nodes)
    // );
    // $output['contentSection'][] = $this->blockChartNewJsBar(
    //   $this->chartBarDataEventTypeTotals($meeting_nodes),
    //   "Event Types of Total",
    //   "4",
    //   NULL,
    //   NULL
    // );
    // $output['contentSection'][] = $this->blockChartNewJsDoughnut(
    //   $this->chartPieDataHeadOfficeRegionalPrograms($meeting_nodes),
    //   "Head Office vs Regional Programs",
    //   "4",
    //   $this->legendHeadOfficeRegionalPrograms($meeting_nodes)
    // );

    //
    // $output['contentSection'][] = $this->FlexpageBaseJson->getBlockMultiTabs(
    //   array(
    //     'class' => "col-md-6",
    //     'top' =>  array(
    //       'enable' => true,
    //       'value' => $this->blockTopSquareIcon('Event Implementation'),
    //     ),
    //   ),
    //   array(
    //     $this->FlexpageBaseJson->getBlockTabContainer(
    //       array(
    //         'class' => "col-md-12",
    //         'title' => "Month",
    //         'type' => "multiContainer",
    //       ),
    //       array(
    //         $this->blockChartNewJsLine(
    //           $this->chartLineDataEventImplementationByMonth($meeting_nodes),
    //           "Event Implementation",
    //           "12",
    //           $this->legendEventImplementation($meeting_nodes)
    //         ),
    //       )
    //     ),
    //     $this->FlexpageBaseJson->getBlockTabContainer(
    //       array(
    //         'class' => "col-md-12",
    //         'title' => "Quarter",
    //         'type' => "multiContainer",
    //       ),
    //       array(
    //         $this->blockChartNewJsLine(
    //           $this->chartLineDataEventImplementationByQuarter($meeting_nodes),
    //           "Event Implementation",
    //           "12",
    //           $this->legendEventImplementation($meeting_nodes)
    //         ),
    //       )
    //     )
    //   )
    // );

    // //
    // $output['contentSection'][] = $this->FlexpageBaseJson->getBlockMultiTabs(
    //   array(
    //     'class' => "col-md-6",
    //     'top' =>  array(
    //       'enable' => true,
    //       'value' => $this->blockTopSquareIcon('Location of Events'),
    //     ),
    //   ),
    //   array(
    //     $this->FlexpageBaseJson->getBlockTabContainer(
    //       array(
    //         'class' => "col-md-12",
    //         'title' => "Province",
    //         'type' => "multiContainer",
    //       ),
    //       array(
    //         $this->blockChartNewJsBar(
    //           $this->chartBarDataLocationEvents($meeting_nodes),
    //           "Title Placeholder",
    //           "12"
    //         ),
    //       )
    //     ),
    //     $this->FlexpageBaseJson->getBlockTabContainer(
    //       array(
    //         'class' => "col-md-12",
    //         'title' => "Event Type",
    //         'type' => "multiContainer",
    //       ),
    //       array(
    //         $this->blockChartNewJsBar(
    //           $this->chartBarDataLocationEventType($meeting_nodes),
    //           "Title Placeholder",
    //           "12"
    //         ),
    //       )
    //     ),
    //   )
    // );

    // //
    // $output['contentSection'][] = $this->FlexpageBaseJson->getBlockMultiTabs(
    //   array(
    //     'class' => "col-md-6",
    //     'top' =>  array(
    //       'enable' => true,
    //       'value' => $this->blockTopSquareIcon('HCP Reach By Event Type', '7dba00'),
    //     ),
    //   ),
    //   array(
    //     $this->FlexpageBaseJson->getBlockTabContainer(
    //       array(
    //         'class' => "col-md-12",
    //         'title' => "Month",
    //         'type' => "multiContainer",
    //       ),
    //       array(
    //         $this->blockChartNewJsLine(
    //           $this->chartLineDataHcpReachByEventTypeByMonth($meeting_nodes),
    //           "Title Placeholder",
    //           "12",
    //           $this->legendHcpReachByEventType($meeting_nodes)
    //         ),
    //       )
    //     ),
    //     $this->FlexpageBaseJson->getBlockTabContainer(
    //       array(
    //         'class' => "col-md-12",
    //         'title' => "Quarter",
    //         'type' => "multiContainer",
    //       ),
    //       array(
    //         $this->blockChartNewJsLine(
    //           $this->chartLineDataHcpReachByEventTypeByQuarter($meeting_nodes),
    //           "Title Placeholder",
    //           "12",
    //           $this->legendHcpReachByEventType($meeting_nodes)
    //         ),
    //       )
    //     ),
    //   )
    // );

    // //
    // $output['contentSection'][] = $this->FlexpageBaseJson->getBlockMultiTabs(
    //   array(
    //     'class' => "col-md-6",
    //     'top' =>  array(
    //       'enable' => true,
    //       'value' => $this->blockTopSquareIcon('HCP Reach By Location', '7dba00'),
    //     ),
    //   ),
    //   array(
    //     $this->FlexpageBaseJson->getBlockTabContainer(
    //       array(
    //         'class' => "col-md-12",
    //         'title' => "Province",
    //         'type' => "multiContainer",
    //       ),
    //       array(
    //         $this->blockChartNewJsBar(
    //           $this->chartLineDataHcpReachByLocationByMonth($meeting_nodes),
    //           "Title Placeholder",
    //           "12"
    //         ),
    //       )
    //     ),
    //     $this->FlexpageBaseJson->getBlockTabContainer(
    //       array(
    //         'class' => "col-md-12",
    //         'title' => "Event Type",
    //         'type' => "multiContainer",
    //       ),
    //       array(
    //         $this->blockChartNewJsBar(
    //           $this->chartBarDataHcpReachEventType($meeting_nodes),
    //           "Title Placeholder",
    //           "12"
    //         ),
    //       )
    //     ),
    //   )
    // );

    // $output['contentSection'][] = $this->generateCommonTable($this->basicTableDataEventByFundingSource($meeting_nodes), array('value' => "Event By Province"));
    $output['contentSection'][] = $this->generateCommonTable($this->basicTableDataHcpByProvinceByEventtype($meeting_nodes), array('value' => "HCP By Province"));
    $output['contentSection'][] = $this->generateCommonTable($this->basicTableDataTopProgram($meeting_nodes), array('value' => "Top 10 Programs"));
    $output['contentSection'][] = $this->generateCommonTable($this->basicTableDataTopRateSpeakers($meeting_nodes), array('value' => "Top 10 Speakers"));

    return $output;
  }

  /**
   * @param $section is 'program' or 'meeting'
   */
  public function meetingSnapshotObjectContent($meeting_nodes = array(), $entity_id = NULL) {
    $evaluationform_tid = NULL;
    $meeting_entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);

    if ($meeting_entity) {
      if ($meeting_entity->getType() == 'meeting') {
        $evaluationform_tid = \Drupal::service('flexinfo.node.service')->getMeetingEvaluationformTid($meeting_entity);
      }
      else {
        \Drupal::service('flexinfo.setting.service')->throwExceptionPage(404);
      }
    }
    else {
      \Drupal::service('flexinfo.setting.service')->throwExceptionPage(404);
    }

    $output['contentSection'] = array_merge(
      array($this->blockTileMeeting($meeting_entity, TRUE)),
      $this->blockEventsSnapshot(array($meeting_entity), $evaluationform_tid, 'meeting_view')
    );

    return $output;
  }

  /**
   * @return php object, not JSON
   */
  public function programSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $NgjsonObjectContent = new NgjsonObjectContent();
    $meeting_nodes = $NgjsonObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $evaluationform_tid = NULL;
    $program_entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($entity_id);

    if ($program_entity->getVocabularyId() == 'program') {
      $evaluationform_tid = \Drupal::service('flexinfo.field.service')->getFieldFirstTargetId($program_entity, 'field_program_evaluationform');
    }
    else {
      \Drupal::service('flexinfo.setting.service')->throwExceptionPage(404);
    }

    $output['fixedSection'] = $this->FlexpageBaseJson->generateTileStyleOne($this->pageTopFixedSectionData($meeting_nodes));
    $output['contentSection'] = $this->blockEventsSnapshot($meeting_nodes, $evaluationform_tid, 'program_view');

    return $output;
  }

}
