<?php

/**
 * @file
 * Contains \Drupal\flexpage\Controller\FlexpageSampleDataGenerator.
 */

namespace Drupal\flexpage\Content;

use Drupal\Core\Controller\ControllerBase;

class FlexpageSampleDataGenerator {

  /**
   *
   */
  public function generateBarChartData() {
    $output = array(
      "labels" => array(
        "Family Medicine",
        "Respirology",
        "Rheumatology",
        "Pathology",
        "Radiology",
      ),
      "datasets" => array(
        array(
          "fillColor" => ["#56bfb5","#f24b99","#344a5f","#bfbfbf","#e6e6e6"],
          "strokeColor" => ["#56bfb5","#f24b99","#344a5f","#bfbfbf","#e6e6e6"],
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => array(
            50,
            30,
            14,
            12,
            9,
          ),
        ),
      )
    );

    return $output;
  }

  /**
   *
   */
  public function generateBarChartData2() {
    $output = array(
      "labels" => array(
        "Baseline scores",
        "Most recent scores",
        "Last session scores (CHE Evaluations)",
      ),
      "datasets" => array(
        array(
          "fillColor" => ["#56bfb5","#f24b99","#344a5f","#bfbfbf","#e6e6e6"],
          "strokeColor" => ["#56bfb5","#f24b99","#344a5f","#bfbfbf","#e6e6e6"],
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => array(
            5,
            8,
            6,
          ),
        ),
      )
    );

    return $output;
  }

  public function generateBarChartDataWithSameColor() {
    $output = array(
      "labels" => array(
        "Family Medicine",
        "Respirology",
        "Rheumatology",
        "Pathology",
        "Radiology",
      ),
      "datasets" => array(
        array(
          "fillColor" => ["#56bfb5","#56bfb5","#56bfb5","#56bfb5","#56bfb5"],
          "strokeColor" => ["#56bfb5","#56bfb5","#56bfb5","#56bfb5","#56bfb5"],
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => array(
            50,
            30,
            14,
            12,
            9,
          ),
        ),
      )
    );

    return $output;
  }

  /**
   *
   */
  public function generateBarChartGroupData() {
    $output = array(
      "labels" => array(
        "1",
        "2",
        "3",
        "4",
        "5"
      ),
      "datasets" => array(
        array(
          "fillColor" => ["#56bfb5","#f24b99","#344a5f","#bfbfbf","#e6e6e6"],
          "strokeColor" => ["#56bfb5","#f24b99","#344a5f","#bfbfbf","#e6e6e6"],
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => array(
            500,
            140,
            14,
            12
          ),
        ),
        array(
          "fillColor" => "#f24b99",
          "strokeColor" => "#ffffff",
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => array(
            35,
            15,
            15,
            19
          ),
        )
      )
    );

    return $output;
  }

  public function generateDoughnutChartData() {
    $output = array(
      array(
        "value" => 5,
        "color" => "#f3f3f3",
        "title" => "Yes"
      ),
      array(
        "value" => 25,
        "color" => "#a5d23e",
        "title" => "No"
      )
    );

    return $output;
  }

  public function generateDoughnutChartData2() {
    $output = array(
      array(
        "value" => 5,
        "color" => "#f3f3f3",
        "title" => "Yes"
      ),
      array(
        "value" => 25,
        "color" => "#009ddf",
        "title" => "No"
      )
    );

    return $output;
  }

  public function generateDoughnutChartData3() {
    $output = array(
      array(
        "value" => 5,
        "color" => "#f3f3f3",
        "title" => "Yes"
      ),
      array(
        "value" => 25,
        "color" => "#ec247f",
        "title" => "No"
      )
    );

    return $output;
  }

  public function generateDoughnutChartDataEmptyValue() {
    $output = array(
      array(
        "value" => NULL,
        "color" => NULL,
        "title" => NULL
      )
    );

    return $output;
  }

  public function generateLineChartData() {
    $output = array(
      "labels" => array(
        "JAN",
        "FEB",
        "MAR",
        "APR",
        "MAY",
        "JUN",
        "JUL",
        "AUG",
        "SEP",
        "OCT",
        "NOV",
        "DEC"
      ),
      "datasets" => array(
        array(
          "fillColor" => "rgba(151,187,205,0)",
          "strokeColor" => "#f24b99",
          "pointColor" => "#f24b99",
          "pointStrokeColor" => "#fff",
          "data" => array(
            6,
            8,
            2,
            9,
            19,
            1,
            15,
            15,
            4,
            6,
            9,
            13
          )
        ),
        array(
          "fillColor"=> "#00a9e0",
          "strokeColor"=> "#00a9e0",
          "pointColor"=> "#00a9e0",
          "pointStrokeColor"=> "#fff",
          "data"=> array(
            12,
            13,
            3,
            7,
            13,
            16,
            17,
            11,
            18,
            4,
            23,
            26
          )
        )
      )
    );

    return $output;
  }

  public function generateOneLineChartData() {
    $output = array(
      "labels" => array(
        "JAN",
        "FEB",
        "MAR",
        "APR",
        "MAY",
        "JUN",
        "JUL",
        "AUG",
        "SEP",
        "OCT",
        "NOV",
        "DEC"
      ),
      "datasets" => array(
        array(
          "fillColor" => "rgba(151,187,205,0)",
          "strokeColor" => "#f24b99",
          "pointColor" => "#f24b99",
          "pointStrokeColor" => "#fff",
          "data" => array(
            6,
            8,
            2,
            9,
            19,
            1,
            15,
            15,
            4,
            6,
            9,
            13
          )
        )
      )
    );

    return $output;
  }

  public function generatePieChartData() {
    $output = array(
      array(
        "value" => 45,
        "color" => "#2fa9e0",
        "title" => "1(12)"
      ),
      array(
        "value" => 12,
        "color" => "#f24b99",
        "title" => "2(28)"
      ),
      array(
        "value" => 32,
        "color" => "#37d8b3",
        "title" => "3(9)"
      ),
      array(
        "value" => 15,
        "color" => "#bfbfbf",
        "title" => "4(5)"
      )
    );

    return $output;
  }

  public function generateTableData() {

    $output = array(
      "thead" => [
        [
          "NAME",
          "DETAILS",
          "DATE",
          "TIME(EST)",
          "STATUS",
          "ACTION"
        ]
      ],
      "tbody" => [
        [
          "How do you diagnose IPF?",
          "<a href=\"#\">View</a>",
          "Sept 7,2017",
          "10:00AM",
          "<span class='color-009ddf'>Upcoming</span>",
          "<i class='fa fa-calendar color-009ddf  margin-left-20' aria-hidden='true'></i>"
        ],
        [
          "Who should get a lung biopsy?",
          "<a href=''s>View</a>",
          "Sept 21,2017",
          "10:00AM",
          "<span class='color-009ddf'>Upcoming</span>",
          "<i class='fa fa-calendar color-009ddf margin-left-20' aria-hidden='true'></i>"
        ],
        [
          "What is a 'UIP pattern'?",
          "<a href='#'>View</a>",
          "Oct 5,2017",
          "10:00AM",
          "<span class='color-009ddf'>Upcoming</span>",
          "<i class='fa fa-calendar color-009ddf margin-left-20' aria-hidden='true'></i>"
        ],
        [
          "What is an 'inconsistent with UIP pattern'?",
          "<a href='#'>View</a>",
          "Oct 19,2017",
          "10:00AM",
          "<span class='color-009ddf'>Upcoming</span>",
          "<i class='fa fa-calendar color-009ddf margin-left-20' aria-hidden='true'></i>"
        ],
        [
          "What is the evidence for Pirfenidone in the treatment of IPF?",
          "<a href='#'>View</a>",
          "Nov 4,2017",
          "10:00AM",
          "<span class='color-009ddf'>Upcoming</span>",
          "<i class='fa fa-calendar color-009ddf margin-left-20' aria-hidden='true'></i>"
        ]
      ]
    );

    return $output;
  }

  public function legendsValue($colorOfLegend, $numberOfLegend = NULL) {
    $legends = NULL;
    $legends .= '<div class="margin-top-150">';
    // for ($i = 0 ; $i < $numberOfLegend; $i ++){
    //   $legends .= '<span class="'. $colorOfLegend[$i] . ' legend-square"></span>' ;
    //     $legends .= $i;
    //   $legends .= '<br />';
    // if ($numberOfLegend) {
    //   if ($key == $numberOfLegend) {
    //     break;
    //   }
    // }
    // }

    // dpm($colorOfLegend);
    foreach ($colorOfLegend as $key => $value) {
      $legends .= '<span class="'. $colorOfLegend[$key] . ' legend-square"></span>' ;
        $legends .= $key + 1;
      $legends .= '<br />';

      if ($numberOfLegend) {
        if ($key == $numberOfLegend) {
          break;
        }
      }
    }
    $legends .= '</div>';

    return $legends;
  }

  public function legendsColors() {
    $output = array(
      'doughnutChart' => array(
        'colorOfLegends' => array('bg-f3f3f3', 'bg-99dc3b')
      ),
      'pieChart' => array(
        'colorOfLegends' => array('bg-009fe3','bg-e72682', 'bg-56bfb5', 'bg-bfbfbf')
      ),
      'lineChart' => array(
        'colorOfLegends' => array('bg-009fe3', 'bg-e72682')
      ),
    );
    return $output;
  }

}
