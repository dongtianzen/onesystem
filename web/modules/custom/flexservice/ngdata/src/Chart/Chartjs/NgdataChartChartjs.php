<?php

namespace Drupal\ngdata\Chart\Chartjs;

use Drupal\ngdata\Chart\NgdataChart;

use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 * Class NgdataChartChartjs.
  \Drupal::service('ngdata.chart.chartjs')->basic();
 */
class NgdataChartChartjs extends NgdataChart {

  /**
   * Constructs a new NgdataChartChartjs object.
   *
   */
  public function __construct() {

  }

  /**
   *
   */
  public function chartBarOption($datasets_data = array()) {
    $output = [
      "legend" => [
        "display" => FALSE
      ],
      "plugins" => [
        "labels" => [
          "render" => "value",
          "fontColor" => "#000000",
          "fontSize" => 14
        ]
      ],
      "layout" => [
        "padding" => [
          "top" => 30
        ]
      ],
      "responsive" => FALSE,
      "maintainAspectRatio" => FALSE,
      "scales" => [
        "xAxes" => [[
          "stacked" => TRUE,
          "gridLines" => [
            "color" => "#f1f1f1"
          ],
          "ticks" => [
            "fontSize" => 14,
          ]
        ]],
        "yAxes" => [[
          "stacked" => TRUE,
          "gridLines" => [
            "color" => "#f1f1f1"
          ],
          "ticks" => [
            "fontSize" => 14,
            // "max" => 100,
          ]
        ]]
      ]
    ];

    if ($datasets_data && is_array($datasets_data)) {
      $max_value = max($datasets_data);

      $yaxes_max = 10;
      if ($max_value > 999) {
        $yaxes_max = round($max_value, -2) + 100;

        if (((round($max_value, -2) / 100) % 2) == 0) {
          $yaxes_max = $yaxes_max + 100;
        }
      }
      elseif ($max_value > 99) {
        $yaxes_max = round($max_value, -2) + 100;
      }
      elseif ($max_value > 9) {
        $yaxes_max = round(($max_value * 1.1), -1) + 10;
      }

      $output["scales"]["yAxes"][0]["ticks"]["max"] = $yaxes_max;
    }

    return $output;
  }

  /**
   *
   */
  public function chartStackBarOption($stackbar_datasets_data = array()) {
    $datasets_data = array();
    $data_array = array();

    if ($stackbar_datasets_data && is_array($stackbar_datasets_data)) {
      foreach ($stackbar_datasets_data as $key => $row) {
        $data_array[] = $row['data'];
      }

      if (isset($data_array) && is_array($data_array) && isset($data_array[0])) {
        foreach ($data_array[0] as $subkey => $subrow) {
          $datasets_data[$subkey] = array_sum(array_column($data_array, $subkey));
        }
      }
    }

    $output = $this->chartBarOption($datasets_data);
    $output["plugins"] = [
      "labels" => [
        // "render" => "value",
        "fontColor" => "#ffffff",
        "fontSize" => 0
      ]
    ];

    return $output;
  }

  /**
   *
   */
  public function chartStackBarHorizontalBarOption($stackbar_datasets_data = array()) {
    $datasets_data = array();
    $data_array = array();

    if ($stackbar_datasets_data && is_array($stackbar_datasets_data)) {
      foreach ($stackbar_datasets_data as $key => $row) {
        $data_array[] = $row['data'];
      }

      if (isset($data_array) && is_array($data_array) && isset($data_array[0])) {
        foreach ($data_array[0] as $subkey => $subrow) {
          $datasets_data[$subkey] = array_sum(array_column($data_array, $subkey));
        }
      }
    }


    $output = $this->chartBarOption($datasets_data);
    $output["plugins"] = [
      "labels" => [
        "fontColor" => "#ffffff",
        "fontSize" => 0
      ],
      // "stacked100" => [
      //   "enable" => true,
      // ]
    ];
    // $output["scales"] = [
    //   "xAxes" => [
    //     "stacked" => true,
    //   ],
    //   "yAxes" => [
    //     "stacked" => true,
    //   ]
    // ];

    return $output;
  }

  /**
   * @return
    $output[] = array(
      "backgroundColor" => "#f24b99",
      "borderColor" => "#f24b99",
      "pointColor" => "#f24b99",
      "data" => array(15, 7, 2, 0 , 0, 0),
    );
   */
  public function chartBarDataByEvaluationByPrePostByQuestions($meeting_nodes = array(), $question_term = NULL) {
    $output = [];

    $question_relatedtype = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllValues($question_term, 'field_queslibr_relatedtype');

    $question_scale = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_scale');

    if ($question_relatedtype) {
      $color = [];
      for ($i = 0; $i < $question_scale; $i++) {
        $color[] = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateStackedBarChart($i + 1, TRUE);
      }

      $color = array_reverse(array_slice($color, 0, $question_scale));

      for ($i = 0; $i < $question_scale; $i++) {
        $data = [];
        foreach ($question_relatedtype as $row) {
          $data[] = \Drupal::service('ngdata.node.evaluation')
            ->getQuestionAnswerByQuestionTidByReferValue($meeting_nodes, $question_term->id(), ($i + 1), 'refer_other', $row);
        }

        if ($question_scale > 8) {
          $color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateStackedBarChartByScale10($i + 1, TRUE);
        }

        $output[] = array(
          "backgroundColor" => $color[$i],
          "borderColor" => $color[$i],
          "pointColor" => $color[$i],
          "data" => $data,
        );
      }
    }

    return $output;
  }

  /**
   * $by_event = True, FALSE is by HCP Reach
   */
  public function chartBarDataByEventsByMonthByEventType($meeting_nodes = array(), $by_event = TRUE, $step = 1) {
    $output = [];

    $meeting_nodes_by_event_type = array_values(\Drupal::service('ngdata.node.meeting')->meetingNodesByEventType($meeting_nodes));

    foreach ($meeting_nodes_by_event_type as $key => $row) {
      $month_num = array();
      for ($j = 1; $j < 13; $j += $step) {
        $months = array($j);
        if ($step == 3) {
          $months = array($j, $j + 1, $j + 2);
        }

        $meeting_nodes_by_month = \Drupal::getContainer()->get('flexinfo.querynode.service')->meetingNodesByMonth($row, $months);

        if ($by_event) {
          $month_num[] = count($meeting_nodes_by_month);
        }
        else {
          $month_num[] = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_month, 'field_meeting_signature')
          );
        }
      }

      $color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE);
      $output[] = array(
        "backgroundColor" => $color,
        "borderColor" => $color,
        "pointColor" => $color,
        "data" => $month_num,
      );
    }

    return $output;
  }

  /**
   * $by_event = True, FALSE is by HCP Reach
   */
  public function chartBarDataByEventsByProvinceByEventType($meeting_nodes = array(), $by_event = TRUE) {
    $output = [];

    $meeting_nodes_by_event_type = array_values(\Drupal::service('ngdata.node.meeting')->meetingNodesByEventType($meeting_nodes));

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('province');
    foreach ($meeting_nodes_by_event_type as $key => $row) {
      $num = array();
      foreach ($terms as $term) {
        $nodes_by_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue(
            $row, 'field_meeting_province', $term->id()
          );

        if ($by_event) {
          $num[] = count($nodes_by_term);
        }
        else {
          $num[] = array_sum(
            \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstValueCollection($nodes_by_term, 'field_meeting_signature')
          );
        }
      }

      $color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE);
      $output[] = array(
        "backgroundColor" => $color,
        "borderColor" => $color,
        "pointColor" => $color,
        "data" => $num,
      );
    }

    return $output;
  }

  /**
   * $by_event = True, FALSE is by HCP Reach
   */
  public function chartBarDataByEventsByFundingsourceByEventType($meeting_nodes = array(), $by_event = TRUE) {
    $output = [];

    $meeting_nodes_by_event_type = array_values(\Drupal::service('ngdata.node.meeting')->meetingNodesByEventType($meeting_nodes));
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    foreach ($meeting_nodes_by_event_type as $key => $row) {
      $nums = array();
      foreach ($terms as $term) {
        $nodes_by_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue(
            $row, 'field_meeting_fundingsource', $term->id()
          );

        if ($by_event) {
          $nums[] = count($nodes_by_term);
        }
        else {
          $nums[] = array_sum(
            \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstValueCollection($nodes_by_term, 'field_meeting_signature')
          );
        }
      }

      $color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne($key + 1, TRUE);
      $output[] = array(
        "backgroundColor" => $color,
        "borderColor" => $color,
        "pointColor" => $color,
        "data" => $nums,
      );
    }

    return $output;
  }

  /**
   * $by_event = True, FALSE is by HCP Reach
   */
  public function chartLineDataByProvince($meeting_nodes = array(), $by_event = TRUE) {
    $output = [];

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    foreach ($terms as $key => $term) {
      $nodes_by_province = \Drupal::getContainer()
        ->get('flexinfo.querynode.service')
        ->wrapperMeetingNodesByFieldValue(
          $meeting_nodes, 'field_meeting_province', $term->id()
        );

      if ($by_event) {
        $output[] = count($nodes_by_province);
      }
      else {
        $output[] = array_sum(
          \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($nodes_by_province, 'field_meeting_signature')
        );
      }
    }

    return $output;
  }

}
