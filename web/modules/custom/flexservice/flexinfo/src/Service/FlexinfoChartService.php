<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoChartService.php.
 */
namespace Drupal\flexinfo\Service;

/**
 * Service container.
 *
 */
class FlexinfoChartService {

  /**
   * @return php array, to prepare render table
   \Drupal::getContainer()->get('flexinfo.chart.service')->convertContentToTableArray();
   */
  public function convertContentToTableArray($source_array = array(), $tbody_key_index = FALSE) {
    $table_value = array();
    if (is_array($source_array)) {
      foreach ($source_array as $row) {
        if ($tbody_key_index) {
          $tbody_content[] = ($row);                // key => value
        }
        else {
          $tbody_content[] = array_values($row);    // only value not include key name
        }
      }

      $table_thead = array();
      if (is_array($source_array) && isset($source_array[0])) {
        $table_thead = array(array_keys($source_array[0]));

        if ($tbody_key_index) {
          foreach (array_keys($source_array[0]) as $key => $value) {
            $table_thead_array[$value] = $value;         // key => value
          }
          $table_thead = array($table_thead_array);
        }
      }

      $table_tbody = array();
      if (isset($tbody_content)) {
        $table_tbody = array_values($tbody_content);
      }

      $table_value = array(
        "thead" => $table_thead,
        "tbody" => $table_tbody,
      );
    }

    return $table_value;
  }

  /**
   *
   */
  public function generateChartLabelForRadioQuestion($pool_data = array()) {
    $output = NULL;

    $labels = array_keys($pool_data);
    foreach($labels as $label) {
      $output[] = $label + 1;
    }

    return $output;
  }

  /**
   * @param
   \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartBarDataSet();
   */
  public function renderChartBarDataSet($pool_data = array(), $chart_label = array()) {
    $chart_data = array();

    if ($pool_data && $chart_label) {
      $chart_data = array(
        "labels" => $chart_label,
        "datasets" => [
          [
            "fillColor" => "#00a9e0",    // #56bfb5
            "strokeColor" => "#ffffff",
            "pointColor" => "#05d23e",
            "pointStrokeColor" => "#fff",
            "data" => $pool_data,
          ],
        ]
      );
    }

    return $chart_data;
  }

  /**
   * @param
   */
  public function renderChartBarMultiGroupDataSet($group_data = array(), $chart_label = array()) {
    $chart_data = array();

    if ($group_data && $chart_label) {
      $color_key = 1;
      foreach ($group_data as $data_row) {
        $datasets[] =  array(
          "fillColor" => \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFour($color_key, TRUE),
          "strokeColor" => "#ffffff",
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => $data_row,
        );

        $color_key++;
      }

      $chart_data = array(
        "labels" => $chart_label,
        "datasets" => $datasets,
      );
    }

    return $chart_data;
  }

  /**
   * @param
   */
  public function renderChartBarMultiGroupDataSetColorPlateTwo($group_data = array(), $chart_label = array()) {
    $chart_data = array();

    if ($group_data && $chart_label) {
      $color_key = 1;
      foreach ($group_data as $data_row) {
        $datasets[] =  array(
          "fillColor" => \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateTwo($color_key + 1, TRUE),
          "strokeColor" => "#ffffff",
          "pointColor" => "#05d23e",
          "pointStrokeColor" => "#fff",
          "data" => $data_row,
        );

        $color_key++;
      }

      $chart_data = array(
        "labels" => $chart_label,
        "datasets" => $datasets,
      );
    }

    return $chart_data;
  }

  /**
   * @param
     Array (
      [0] => Array (
        [0] => 0
        [1] => 0
        [2] => 1
        [3] => 13
        [4] => 35
      ),
      [1] => Array (
        [0] => 0
        [1] => 3
        [2] => 55
        [3] => 223
        [4] => 123
      )
     )
   *
   */
  public function renderChartHorizontalStackedBarDataSet($pool_data = array(), $chart_label = array()) {
    $color_plate = 'colorPlateDoughnut';
    if (\Drupal::hasService('baseinfo.chart.service')) {
      if(method_exists(\Drupal::getContainer()->get('baseinfo.chart.service'), 'renderChartBarDataSetColorPlate')){
        $color_plate = \Drupal::getContainer()->get('baseinfo.chart.service')->renderChartBarDataSetColorPlate();
      }
    }

    $chart_data = array();
    if ($pool_data && $chart_label) {
      $datasets = array();

      if (is_array($pool_data)) {
        for ($i = 4; $i > -1; $i--) {
          $data = array();
          foreach ($pool_data as $key => $row) {
            if (isset($row[$i])) {
              $data[] = $row[$i];
            }
            else {
              $data[] = 0;
            }
          }
          $datasets[] = array(
            "fillColor" => \Drupal::getContainer()->get('flexinfo.setting.service')->{$color_plate}($i + 2, TRUE),
            "strokeColor" => "#ffffff",
            "pointColor" => "#05d23e",
            "pointStrokeColor" => "#fff",
            "data" => $data,
          );
        }
      }

      $chart_data = array(
        "labels" => $chart_label,
        // "labels" => array($chart_label),
        "datasets" => $datasets,
      );
    }

    return $chart_data;
  }

  /**
   *
   */
  public function renderChartBarOneGroupDataSet($pool_data = array(), $chart_label = array()) {
    $color_plate = 'colorPlateThree';
    if (\Drupal::hasService('baseinfo.chart.service')) {
      if(method_exists(\Drupal::getContainer()->get('baseinfo.chart.service'), 'renderChartBarDataSetColorPlate')){
        $color_plate = \Drupal::getContainer()->get('baseinfo.chart.service')->renderChartBarDataSetColorPlate();
      }
    }

    $chart_data = array();

    if ($pool_data && $chart_label) {

      foreach ($pool_data as $key => $row) {
        $fillColor[] = \Drupal::getContainer()->get('flexinfo.setting.service')->{$color_plate}(5 - $key + 1, TRUE);

        $pool_data_convert[$key] = $pool_data[4 - $key];
        $chart_label_convert[$key] = $chart_label[4 - $key];
      }

      if (is_array($pool_data_convert)) {
        $chart_data = array(
          "labels" => $chart_label_convert,
          "datasets" => array(
            array(
              "fillColor" => $fillColor,
              "strokeColor" => "#ffffff",
              "pointColor" => "#05d23e",
              "pointColor" => "#05d23e",
              "pointStrokeColor" => "#fff",
              "data" => $pool_data_convert
            ),
          ),
        );
      }
    }

    return $chart_data;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartBottomLegendOnly();
   */
  public function renderChartBottomLegendOnly($chart_legend = array(), $max_length = NULL) {
    $color_plate = 'colorPlateThree';
    if (\Drupal::hasService('baseinfo.chart.service')) {
      if(method_exists(\Drupal::getContainer()->get('baseinfo.chart.service'), 'renderChartPieLegendColorPlate')){
        $color_plate = \Drupal::getContainer()->get('baseinfo.chart.service')->renderChartPieLegendColorPlate();
      }
    }

    $legends = '<div class="row">';
      $legends .= '<div class="text-center margin-top-12 margin-bottom-6">';
        $legends .= '<div class="display-inline-block font-size-12 margin-left-n-12">';

        if ($chart_legend && is_array($chart_legend)) {
          foreach ($chart_legend as $key => $value) {
            $legends .= '<span class="fa fa-circle color-' . \Drupal::getContainer()->get('flexinfo.setting.service')->{$color_plate}(5 - $key + 1) . ' margin-left-12">';
            $legends .= '</span>';
            $legends .= '<span class="legend-text margin-left-6">';
              $legends .= isset($chart_legend[$key]) ? $chart_legend[$key] : 0;
            $legends .= '</span>';

            if ($max_length) {
              if (($key + 2) > $max_length) {
                break;
              }
            }
          }
        }

        $legends .= '</div>';
      $legends .= '</div>';
    $legends .= '</div>';

    return $legends;
  }

  /**
   * @param
   \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartBottomLegend();
   */
  public function renderChartBottomLegend($pool_data = array(), $chart_label = array(), $max_length = NULL, $legend_text = NULL) {
    if (is_array($pool_data)) {
      krsort($pool_data);
    }

    $color_plate = 'colorPlateThree';
    if (\Drupal::hasService('baseinfo.chart.service')) {
      if(method_exists(\Drupal::getContainer()->get('baseinfo.chart.service'), 'renderChartPieLegendColorPlate')){
        $color_plate = \Drupal::getContainer()->get('baseinfo.chart.service')->renderChartPieLegendColorPlate();
      }
    }

    $legends = '<div class="row">';
      $legends .= '<div class="text-center margin-top-12 margin-bottom-6">';
        $legends .= '<div class="display-inline-block font-size-12 margin-left-n-12">';

        foreach ($pool_data as $key => $value) {
          $legends .= '<span class="fa fa-circle color-' . \Drupal::getContainer()->get('flexinfo.setting.service')->{$color_plate}($key + 2) . ' margin-left-12">';
          $legends .= '</span>';
          $legends .= '<span class="legend-text margin-left-6">';
            $legends .= ($legend_text) ? $legend_text[$key + 1] : ($key + 1);
            $legends .= ' (';
            $legends .= isset($pool_data[$key]) ? $pool_data[$key] : 0;
            $legends .= ')';
          $legends .= '</span>';

          if ($max_length) {
            if (($key + 2) > $max_length) {
              break;
            }
          }
        }

        $legends .= '</div>';
      $legends .= '</div>';
    $legends .= '</div>';

    return $legends;
  }

  /**
   * @param
   */
  public function renderChartBottomLegendBasic($chart_label = array(), $max_length = NULL) {
    $label = $this->getChartLegendTwo();

    // if (is_array($label)) {
    //   krsort($label);
    // }

    $legends = '<div class="row">';
      $legends .= '<div class="text-center margin-top-24 margin-left-12">';
        $legends .= '<div class="display-inline-block font-size-12">';

        foreach ($label as $key => $value) {
          $legends .= '<span class="legend-square bg-' . \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateThree($key + 1) . ' margin-left-12">';
          $legends .= '</span>';
          $legends .= '<span class="legend-text float-left">';
            $legends .= $value;
          $legends .= '</span>';

          if ($max_length) {
            if (($key + 1) > $max_length) {
              break;
            }
          }
        }

        $legends .= '</div>';
      $legends .= '</div>';
    $legends .= '</div>';

    return $legends;
  }

  /**
   * @param
   */
  public function renderChartBottomLegendFour($chart_label = array(), $max_length = NULL) {
    $legends = '<div class="row">';
      $legends .= '<div class="text-center margin-top-6 margin-left-12 margin-bottom-12">';
        $legends .= '<div class="display-inline-block font-size-12">';

        foreach ($chart_label as $key => $value) {
          $legends .= '<span class="legend-square bg-' . \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFour($key + 1) . ' margin-left-12">';
          $legends .= '</span>';
          $legends .= '<span class="legend-text float-left">';
            $legends .= $value;
          $legends .= '</span>';

          if ($max_length) {
            if (($key + 1) > $max_length) {
              break;
            }
          }
        }

        $legends .= '</div>';
      $legends .= '</div>';
    $legends .= '</div>';

    return $legends;
  }

  /**
   * \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartBottomFooter();
   */
  public function renderChartBottomFooter($pool_data = array(), $question_term = NULL, $position_absolute = FALSE, $bottom_border = FALSE) {
    $left_label = t('RESPONSES');
    $left_data = array_sum($pool_data);

    // right label
    $right_num = $this->renderChartBottomFooterValue($pool_data, $question_term);
    $right_footer = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_chartfooter');
    if ($right_footer) {
      $right_label = $right_footer;
    }
    else {
      $right_label = t('Very Good or Excellent');

      // check label
      $question_label_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_label');
    }

    $bottom_border_class = 'border-bottom-none';
    if (!$bottom_border) {
      $bottom_border_class = '';
    }

    // output
    $bottom_value = NULL;
    if ($position_absolute) {
      $bottom_value .= '<div class="col-md-12 text-center position-absolute bottom-n-1 padding-0">';
    }
    else {
      $bottom_value .= '<div class="col-md-12 text-center padding-0">';
    }
      $bottom_value .= '<div class="col-md-6 height-66 border-1-eee ' . $bottom_border_class . '">';
        $bottom_value .= '<div class="font-bold height-32 padding-top-6">';
          $bottom_value .= $left_data;
        $bottom_value .= '</div>';
        $bottom_value .= '<div class="font-size-10 text-center">';
          $bottom_value .= $left_label;
        $bottom_value .= '</div>';
      $bottom_value .= '</div>';
      $bottom_value .= '<div class="col-md-6 height-66 border-1-eee ' . $bottom_border_class . '">';
        if ($right_label != '&nbsp;') {
          $bottom_value .= '<div class="font-bold height-32 padding-top-6">';
            $bottom_value .= $right_num . '%';
          $bottom_value .= '</div>';
          $bottom_value .= '<div class="font-size-10 text-center">';
            $bottom_value .= $right_label;
          $bottom_value .= '</div>';
        }
      $bottom_value .= '</div>';
    $bottom_value .= '</div>';

    return $bottom_value;
  }

  /**
   * @deprecated by 2018-05-10 only keep for Lilly and BI 3.0
   */
  public function renderChartBottomFooterCombine5and4($pool_data = array(), $question_term = NULL, $position_absolute = FALSE, $bottom_border = FALSE) {
    // $question_tid == 2734 is How would you rate the overall quality of the Educational program?
    $question_tid = $question_term->id();
    if ($question_tid == 2734) {
      $left_label = t('NTS');
      $left_data = \Drupal::getContainer()->get('flexinfo.calc.service')->calcNTSScore($pool_data);
    }
    else {
      $left_label = t('RESPONSES');
      $left_data = array_sum($pool_data);
    }

    // right label
    $right_num = $this->renderChartBottomFooterValueCombine5and4($pool_data, $question_term);
    $right_footer = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_chartfooter');
    if ($right_footer) {
      $right_label = $right_footer;
    }
    else {
      $right_label = t('Very Good or Excellent');

      // check label
      $question_label_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_label');

      // lable tid 2453 is ABCDEFGHIJKLMNOPQRSTUVWXYZ
      if ($question_label_tid == 2453) {
        $right_label = '&nbsp;';
      }
    }

    $bottom_border_class = 'border-bottom-none';
    if (!$bottom_border) {
      $bottom_border_class = '';
    }

    // output
    $bottom_value = NULL;
    if ($position_absolute) {
      $bottom_value .= '<div class="col-md-12 text-center position-absolute bottom-n-1 padding-0">';
    }
    else {
      $bottom_value .= '<div class="col-md-12 text-center padding-0">';
    }
      $bottom_value .= '<div class="col-md-6 height-66 border-1-eee ' . $bottom_border_class . '">';
        $bottom_value .= '<div class="font-bold height-32 padding-top-6">';
          $bottom_value .= $left_data;
        $bottom_value .= '</div>';
        $bottom_value .= '<div class="font-size-10 text-center">';
          $bottom_value .= $left_label;
        $bottom_value .= '</div>';
      $bottom_value .= '</div>';
      $bottom_value .= '<div class="col-md-6 height-66 border-1-eee ' . $bottom_border_class . '">';
        if ($right_label != '&nbsp;') {
          $bottom_value .= '<div class="font-bold height-32 padding-top-6">';
            $bottom_value .= $right_num . '%';
          $bottom_value .= '</div>';
          $bottom_value .= '<div class="font-size-10 text-center">';
            $bottom_value .= $right_label;
          $bottom_value .= '</div>';
        }
      $bottom_value .= '</div>';
    $bottom_value .= '</div>';

    return $bottom_value;
  }

  /**
   *
   */
  public function renderChartBottomFooterPdfJson($pool_data = array(), $question_term = NULL, $position_absolute = FALSE, $bottom_border = FALSE) {
    $sum_data = array_sum($pool_data);

    $calc_data = 0;
    if (isset($pool_data[3])) {
      $calc_data += $pool_data[3];
    }
    if (isset($pool_data[4])) {
      $calc_data += $pool_data[4];
    }
    if ($question_term->id() == 3013 || $question_term->id() == 3014) {
      if (isset($pool_data[1])) {
        $calc_data = $pool_data[1];
      }
    }

    // $question_tid == 2734 is How would you rate the overall quality of the Educational program?
    $question_tid = $question_term->id();
    if ($question_tid == 2734) {
      $left_label = t('NTS');
      $left_data = \Drupal::getContainer()->get('flexinfo.calc.service')->calcNTSScore($pool_data);
    }
    else {
      $left_label = t('RESPONSES');
      $left_data = $sum_data;
    }

    // right label
    $right_num = \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($calc_data, $sum_data, 0);
    $right_footer = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_chartfooter');
    if ($right_footer) {
      $right_label = $right_footer;
    }
    else {
      $right_label = t('Very Good or Excellent');

      // check label
      $question_label_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_label');

      // lable tid 2453 is ABCDEFGHIJKLMNOPQRSTUVWXYZ
      if ($question_label_tid == 2453) {
        $right_label = '&nbsp;';
      }
    }

    // output
    $bottom_value = array(
      $left_data,
      $left_label,
      $right_num . '%',
      $right_label,
    );

    $bottom_value = implode(",", $bottom_value);

    return $bottom_value;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartBottomFooter();
   */
  public function renderChartBottomFooterValue($pool_data = array(), $question_term = NULL) {
    $sum_data = array_sum($pool_data);

    $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

    $calc_data = 0;
    // set as last value
    if (is_array($pool_data) && $question_scale) {
      end($pool_data);
      $last_key = key($pool_data);
      $calc_data = $pool_data[$last_key];

      // Yes No Notsure question
      if ($question_scale < 4) {
        if (isset($pool_data[0])) {
          $calc_data = $pool_data[0];
        }
      }
    }

    $right_num = \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($calc_data, $sum_data, 0);

    return $right_num;
  }

  /**
   *
   */
  public function renderChartBottomFooterValueCombine5and4($pool_data = array(), $question_term = NULL) {
    $sum_data = array_sum($pool_data);

    $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

    $calc_data = 0;
    // set as last value
    if (is_array($pool_data) && $question_scale) {
      if (isset($pool_data[$question_scale - 1])) {
        $calc_data = $pool_data[$question_scale - 1];
      }

      // Yes No Notsure question
      if ($question_scale < 4) {
        if (isset($pool_data[0])) {
          $calc_data = $pool_data[0];
        }
      }
      elseif ($question_scale == 5) {
        if (isset($pool_data[3])) {
          $calc_data += $pool_data[3];
        }
      }

      if ($question_term->id() == 3013 || $question_term->id() == 3014) {
        if (isset($pool_data[1])) {
          $calc_data = $pool_data[1];
        }
      }
    }

    $right_num = \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($calc_data, $sum_data, 0);

    return $right_num;
  }

  /**
   *
   */
  public function renderChartDoughnutDataSet($pool_data = array(), $chart_label = array(), $max_length = NULL) {
    $color_plate = 'colorPlateDoughnut';
    if (\Drupal::hasService('baseinfo.chart.service')) {
      if(method_exists(\Drupal::getContainer()->get('baseinfo.chart.service'), 'renderChartDoughnutDataSetColorPlate')){
        $color_plate = \Drupal::getContainer()->get('baseinfo.chart.service')->renderChartDoughnutDataSetColorPlate();
      }
    }

    $chart_data = array();

    foreach ($pool_data as $key => $value) {
      $chart_data[] = array(
        "value" => $value,
        "color" => \Drupal::getContainer()->get('flexinfo.setting.service')->{$color_plate}($key + 1, TRUE),
        "title" => "1(12)",
      );

      if ($max_length) {
        if (($key + 2) > $max_length) {
          break;
        }
      }
    }

    return $chart_data;
  }

  /**
   * @param, $data
      $data = array(
        [0] => array(
          [value] => 5,
          [color] => #f3f3f3,
          [title] => Yes,
        )

        [1] => array(
          [value] => 25,
          [color] => #ec247f,
          [title] => No,
        )
      );
   */
  public function renderChartDoughnutDataSetWithColor($data = array()) {
    $chart_data = array();

    foreach ($data as $row) {
      $chart_data[] = array(
        "value" => $row['value'],
        "color" => isset($row['color']) ? $row['color'] : '#f3f3f3',
        "title" => isset($row['title']) ? $row['title'] : NULL,
      );
    }

    return $chart_data;
  }

  /**
   * @deprecated  by 2017 Dec
   * @see $this->renderLegendSquare()
   */
  public function renderChartDoughnutLegend($pool_data = array(), $question_term = NULL, $max_length = NULL) {
    $question_label_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_label');

    $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');
    if (empty($max_length)) {
      $max_length = $question_scale;
    }

    $letter_label_key = array(
      'Yes',
      'No',
      'Not Sure',
    );

    $legends = '<div class="padding-top-64 margin-left-12 width-pt-100">';

    for ($i = 0; $i < $max_length; $i++) {
      $legends .= '<div class="clear-both height-32 text-center">';
        $legends .= '<span class="legend-square bg-' . \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateThree(6 - $i) . '">';
        $legends .= '</span>';
        $legends .= '<span class="float-left legend-text">';
          $legends .= $letter_label_key[$i];
          $legends .= ' (';
          $legends .= isset($pool_data[$i]) ? $pool_data[$i] : 0;
          $legends .= ')';
        $legends .= '</span>';
      $legends .= '</div>';
    }

    $legends .= '</div>';

    return $legends;
  }

  /**
   * @param
   * @return
    $chart_data = array(
      array(
        "value" => 36,
        "color" => "#2fa9e0",
        "title" => "1(36)"
      ),
      array(
        "value" => 28,
        "color" => "#f24b99",
        "title" => "2(28)"
      ),
    );
   \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartPieDataSet();
   */
  public function renderChartPieDataSet($pool_data = array(), $chart_label = array(), $max_length = NULL, $question_term = NULL, $color_plate = array()) {
    $chart_data = array();
    if (is_array($pool_data)) {
      krsort($pool_data);
    }

    if (!$color_plate) {
      $color_plate_name = 'colorPlateFive';
      if (\Drupal::hasService('baseinfo.chart.service')) {
        if(method_exists(\Drupal::getContainer()->get('baseinfo.chart.service'), 'renderChartPieDataSetColorPlate')){
          $color_plate_name = \Drupal::getContainer()->get('baseinfo.chart.service')->renderChartPieDataSetColorPlate();
        }
      }

      $color_plate = \Drupal::getContainer()->get('flexinfo.setting.service')->{$color_plate_name}();
    }

    foreach ($pool_data as $key => $value) {
      $chart_data[] = array(
        "value" => $value,
        "color" => '#' . $color_plate[$key + 1],
        "title" => "1(12)",
      );

      if ($max_length) {
        if (($key + 2) > $max_length) {
          break;
        }
      }
    }

    if ($question_term) {
      $question_label_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_label');

      // lable tid 2453 is ABCDEFGHIJKLMNOPQRSTUVWXYZ
      // lable tid 2458 is Yes No
      if ($question_label_tid == 2453 || $question_label_tid == 2458) {

        $question_scale_max_length = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');
        if ($question_scale_max_length) {

          if ($question_scale_max_length == 2) {
            $max_length = 2;

            // to match chart legend color order
            $question_scale_max_length = 5;
          }

          // clean empty first
          $chart_data = array();

          // reverse the order of the legend to A-B-C-D
          ksort($pool_data);
          foreach ($pool_data as $key => $value) {
            $chart_data[] = array(
              "value" => $value,
              "color" => '#' . $color_plate[$question_scale_max_length - $key],
              "title" => "1(12)",
            );

            if ($max_length) {
              if (($key + 2) > $max_length) {
                break;
              }
            }
          }
        }
      }
    }

    return $chart_data;
  }

  public function renderChartNewPieDataSet($pool_data = array(), $chart_label = array(), $max_length = NULL, $question_term = NULL, $color_plate = array(), $data_legend = array()) {
    $chart_data = array();
    if (is_array($pool_data)) {
      krsort($pool_data);
    }

    if (!$color_plate) {
      $color_plate_name = 'colorPlateFive';
      if (\Drupal::hasService('baseinfo.chart.service')) {
        if(method_exists(\Drupal::getContainer()->get('baseinfo.chart.service'), 'renderChartPieDataSetColorPlate')){
          $color_plate_name = \Drupal::getContainer()->get('baseinfo.chart.service')->renderChartPieDataSetColorPlate();
        }
      }

      $color_plate = \Drupal::getContainer()->get('flexinfo.setting.service')->{$color_plate_name}();
    }

    foreach ($pool_data as $key => $value) {
      $chart_data[] = array(
        "value" => $value,
        "color" => '#' . $color_plate[$key + 1],
        "title" => "1(12)",
        "legend" => isset($data_legend[$key + 1]) ? $data_legend[$key + 1] : NULL,
      );

      if ($max_length) {
        if (($key + 2) > $max_length) {
          break;
        }
      }
    }

    return $chart_data;
  }

  /**
   * @param
   * @return $chart_data = array(
      array(
        "value" => 36,
        "color" => "#2fa9e0",
        "title" => "1(36)"
      ),
      array(
        "value" => 28,
        "color" => "#f24b99",
        "title" => "2(28)"
      ),
    );
   */
  public function renderChartPieDataSetColorFour($pool_data = array(), $chart_label = array(), $max_length = NULL) {
    $chart_data = array();
    if (is_array($pool_data)) {
      krsort($pool_data);
    }

    foreach ($pool_data as $key => $value) {
      $chart_data[] = array(
        "value" => $value,
        "color" => \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFive($key + 1, TRUE),
        "title" => "1(12)",
      );

      if ($max_length) {
        if (($key + 2) > $max_length) {
          break;
        }
      }
    }

    return $chart_data;
  }

  /**
   * @param
   * @return
    $chart_data = array(
      array(
        "value" => 36,
        "color" => "#2fa9e0",
        "title" => "1(36)"
      ),
      array(
        "value" => 28,
        "color" => "#f24b99",
        "title" => "2(28)"
      ),
    );
   */
  public function renderChartPieDataSetFour($pool_data = array(), $chart_label = array(), $max_length = NULL) {
    $chart_data = array();
    if (is_array($pool_data)) {
      krsort($pool_data);
    }

    foreach ($pool_data as $key => $value) {
      $chart_data[] = array(
        "value" => $value,
        "color" => \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFive($key + 1, TRUE),
        "title" => "1(12)",
      );

      if ($max_length) {
        if (($key + 2) > $max_length) {
          break;
        }
      }
    }

    return $chart_data;
  }

  /**
   * @param
   * @return
    $chart_data = array(
      array(
        "value" => 36,
        "color" => "#2fa9e0",
        "title" => "1(36)"
      ),
      array(
        "value" => 28,
        "color" => "#f24b99",
        "title" => "2(28)"
      ),
    );
   */
  public function renderChartPieDataSetAccredited($pool_data = array(), $chart_label = array(), $max_length = NULL) {
    $chart_data = array();
    if (is_array($pool_data)) {
      krsort($pool_data);
    }

    foreach ($pool_data as $key => $value) {
      $chart_data[] = array(
        "value" => $value,
        "color" => \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFour($key + 1, TRUE),
        "title" => "1(12)",
      );

      if ($max_length) {
        if (($key + 2) > $max_length) {
          break;
        }
      }
    }

    return $chart_data;
  }

  /**
   * @param
   \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartTopLegendOne();
   */
  public function renderChartTopLegendOne($top_text = array()) {
    $output = '<div class="margin-top-12 margin-left-18 font-size-12 text-center">';
      $output .= $top_text;
    $output .= '</div>';

    return $output;
  }

  /**
   * @deprecated  by 2017 Dec
   * @see $this->renderLegendSquare()
   */
  public function renderChartPieLegend($pool_data = array(), $question_term = NULL, $max_length = NULL) {
    $question_label_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_label');

    $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');
    if (empty($max_length)) {
      $max_length = $question_scale;
    }

    // lable tid 2453 is ABCDEFGHIJKLMNOPQRSTUVWXYZ
    if (is_array($pool_data)) {
      if ($question_label_tid != 2453 || $question_label_tid != 2458) {
        krsort($pool_data);
      }
    }

    // lable tid 2458 is Yes No
    if ($question_label_tid == 2458) {
      $letter_label_key = array(
        'Yes',
        'No',
        'Not Sure',
      );

      $letter_label_key = array_slice($letter_label_key, 0, $max_length);
      krsort($letter_label_key);
      $letter_label_key = array_values($letter_label_key);
    }
    // lable tid 2453 is ABCDEFGHIJKLMNOPQRSTUVWXYZ
    elseif ($question_label_tid == 2453) {
      // question_scale_max_length
      $max_length = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

      $letter_label_key = array(
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
      );

      $letter_label_key = array_slice($letter_label_key, 0, $max_length);
      krsort($letter_label_key);
      $letter_label_key = array_values($letter_label_key);
    }

    $color_plate = 'colorPlateThree';
    if (\Drupal::hasService('baseinfo.chart.service')) {
      if(method_exists(\Drupal::getContainer()->get('baseinfo.chart.service'), 'renderChartPieLegendColorPlate')){
        $color_plate = \Drupal::getContainer()->get('baseinfo.chart.service')->renderChartPieLegendColorPlate();
      }
    }

    $legends = '<div class="padding-top-64 margin-left-12 width-pt-100">';

    for ($i = $max_length - 1; $i >= 0; $i--) {
      $legends .= '<div class="clear-both height-32 text-center">';
        $legends .= '<span class="legend-square bg-' . \Drupal::getContainer()->get('flexinfo.setting.service')->{$color_plate}($i + 2) . '">';
        $legends .= '</span>';
        $legends .= '<span class="float-left legend-text">';

          if ($question_label_tid == 2453 || $question_label_tid == 2458) {
            $legends .= $letter_label_key[$i];
          }
          else {
            $legends .= $i + 1;
          }

          $legends .= ' (';
            if ($question_label_tid == 2453 || $question_label_tid == 2458) {
              $legends .= isset($pool_data[$max_length - $i - 1]) ? $pool_data[$max_length - $i - 1] : 0;
            }
            else {
              $legends .= isset($pool_data[$i]) ? $pool_data[$i] : 0;
            }
          $legends .= ')';
        $legends .= '</span>';
      $legends .= '</div>';
    }
    $legends .= '</div>';

    return $legends;
  }

  /**
   * @deprecated  by 2017 Dec
   * @see $this->renderLegendSquare()
   */
  public function renderChartPieLegendByDataByColorByLegend($pool_data = array(), $color_plate = array(), $legend_data = array(), $max_length = NULL) {
    if (!$color_plate) {
      $color_plate = \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateThree();
    }
    if (!$max_length) {
      $max_length = 4;
    }

    if (!$legend_data) {
      $legend_data = array(
        5 => 'Strongly Agree',
        4 => 'Agree',
        3 => 'Netural',
        2 => 'Disagree',
        1 => 'Strongly Disagree',
      );
    }

    $legends = '<div class="padding-top-64 margin-top-24 margin-left-12 width-pt-100 font-size-12">';

    for ($key = $max_length; $key > -1 ; $key--) {
      $legends .= '<div class="clear-both height-32 text-center">';
        $legends .= '<span class="legend-square bg-' . $color_plate[$key + 2] . '">';
        $legends .= '</span>';
        $legends .= '<span class="float-left legend-text">';
          $legends .= $legend_data[$key + 1] . '(' . $pool_data[$key] . ')';
        $legends .= '</span>';
      $legends .= '</div>';
    }
    $legends .= '</div>';

    return $legends;
  }

  /**
   * @deprecated  by 2017 Dec
   * @see $this->renderLegendSquare()
   */
  public function renderChartPieLegendByDataByColorByLegendAscend($pool_data = array(), $color_plate = array(), $legend_data = array(), $max_length = NULL) {
    if (!$color_plate) {
      $color_plate = \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateThree();
    }

    if (!$legend_data) {
      $legend_data = array(
        1 => 'Yes',
        2 => 'No',
        3 => 'Not Sure',
      );
    }

    $legends = '<div class="padding-top-64 margin-top-24 margin-left-12 width-pt-100 font-size-12">';

    for ($key = 0; $key < 3 ; $key++) {
      $legends .= '<div class="clear-both height-32 text-center">';
        $legends .= '<span class="legend-square bg-' . $color_plate[$key + 2] . '">';
        $legends .= '</span>';
        $legends .= '<span class="float-left legend-text">';
          $legends .= $legend_data[$key + 1] . '(' . $pool_data[$key] . ')';
        $legends .= '</span>';
      $legends .= '</div>';
    }
    $legends .= '</div>';

    return $legends;
  }

  /**
   * @deprecated  by 2017 Dec
   * @see $this->renderLegendSquare()
   */
  public function renderChartPieLegendCustom($legend_data = array(), $max_length = NULL) {
    $legends = '<div class="padding-top-64 margin-top-24 margin-left-12 width-pt-100 font-size-12">';

    foreach ($legend_data as $key => $value) {
      $legends .= '<div class="clear-both height-32 text-center">';
        $legends .= '<span class="legend-square bg-' . \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFive($key) . '">';
        $legends .= '</span>';
        $legends .= '<span class="float-left legend-text">';
          $legends .= $value;
        $legends .= '</span>';
      $legends .= '</div>';
    }
    $legends .= '</div>';

    return $legends;
  }

  /**
   * @deprecated  by 2017 Dec
   * @see $this->renderLegendSquare()
   */
  public function renderChartPieLegendAccredited($legend_data = array(), $max_length = NULL) {
    $legends = '<div class="padding-top-64 margin-top-24 margin-left-12 font-size-12 width-pt-100">';

    $color_key = 1;
    foreach ($legend_data as $value) {
      $legends .= '<div class="clear-both height-32 text-center">';
        $legends .= '<span class="legend-square bg-' . \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFour($color_key) . '">';
        $legends .= '</span>';
        $legends .= '<span class="float-left legend-text">';
          $legends .= $value;
        $legends .= '</span>';
      $legends .= '</div>';

      $color_key++;
    }
    $legends .= '</div>';

    return $legends;
  }

  /**
   * @param
   * @return
   \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartLineDataSet();
   */
  public function renderChartLineDataSet($pool_data = array(), $chart_label = array()) {
    $chart_data = array();

    if ($pool_data && $chart_label) {
      $chart_data = array(
        "labels" => $chart_label,
        "datasets" => [
          [
            "fillColor" => "#2fa9e0",
            "strokeColor" => "#56bfb5",  // #56bfb5
            "pointColor" => "#56bfb5",
            "pointStrokeColor" => "#fff",
            "data" => $pool_data,
          ],
        ]
      );
    }

    return $chart_data;
  }

  /**
   * @param $pool_data_array is array of $pool_data
   */
  public function renderChartMultiLineDataSet($pool_data_array = array(), $chart_label = array()) {
    $chart_data = array();
    if (is_array($pool_data_array)) {

      $color_key = 1;
      foreach ($pool_data_array as $region_name => $pool_data) {
        $datasets[] = array(
          "fillColor" => "#2fa9e0",
          "strokeColor" => \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFour($color_key, TRUE),
          "pointColor" => \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFour($color_key, TRUE),
          "pointStrokeColor" => "#fff",
          "data" => $pool_data,
        );
        $color_key++;
      }
    }

    if ($pool_data && $chart_label) {
      $chart_data = array(
        "labels" => $chart_label,
        "datasets" => $datasets,
      );
    }

    return $chart_data;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.chart.service')->getChartLegendOne();
   */
  public function getChartLegendOne($key = NULL) {
    $legends = array(
      5 => 'Excellent',
      4 => 'Very Good',
      3 => 'Good',
      2 => 'Fair',
      1 => 'Poor',
    );

    $output = $this->getChartLegendOutput($legends, $key);

    return $output;
  }

  /**
   *
   */
  public function getChartLegendTwo($key = NULL) {
    $legends = array(
      5 => 'Strongly Agree',
      4 => 'Agree',
      3 => 'Netural',
      2 => 'Disagree',
      1 => 'Strongly Disagree',
    );

    $output = $this->getChartLegendOutput($legends, $key);

    return $output;
  }

  /**
   *
   */
  public function getChartLegendThree($key = NULL) {
    $legends = array(
      5 => 'Very Likely',
      4 => 'Likely',
      3 => 'Netural',
      2 => 'Unlikely',
      1 => 'Very Unlikely',
    );

    $output = $this->getChartLegendOutput($legends, $key);

    return $output;
  }

  /**
   *
   */
  public function getChartLegendFour($key = NULL) {
    $legends = array(
      'Accredited',
      "OLA's",
      'Symposia',
    );

    $output = $this->getChartLegendOutput($legends, $key);

    return $output;
  }

  /**
   *
   */
  public function getChartLegendFive($key = NULL) {
    $legends = array(
      'Strong Agree',
      'Agree',
    );

    $output = $this->getChartLegendOutput($legends, $key);

    return $output;
  }

  /**
   *
   */
  public function getChartLegendSix($key = NULL) {
    $legends = array(
      5 => 'Best',
      4 => 'Good',
      3 => 'Same',
      2 => 'Fair',
      1 => 'Worst',
    );

    $output = $this->getChartLegendOutput($legends, $key);

    return $output;
  }

  /**
   *
   */
  public function getChartLegendOutput($legend_array = array(), $legend_key = NULL) {
    $output = NULL;

    if ($legend_key || $legend_key === 0) {
      $output = $legend_array[$legend_key];
    }
    else {
      $output = $legend_array;
    }

    return $output;
  }

  /**
   *
   */
  public function getChartTitleByLayoutTerm($layout_term = NULL) {
    $term_block_title = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($layout_term, 'field_layout_blocktitle');

    if (empty($term_block_title)) {
      $term_question_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($layout_term, 'field_layout_question');

      $question_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term_question_tid);
      if ($question_term) {
        $term_block_title = $question_term->getName();
      }
    }

    return $term_block_title;
  }

  /**
   \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($question_term);
   */
  public function getChartTitleByQuestion($question_term = NULL) {
    $chart_title = NULL;

    $set_title = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_charttitle');
    if ($set_title) {
      $chart_title = $set_title;
    }
    else {
      if ($question_term->getName()) {
        $chart_title = $question_term->getName();
      }
    }

    return $chart_title;
  }

  /**
   * @return chart_type function_name, like - getChartPie, getChartDoughnut
   \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTypeByQuestion($question_term);
   */
  public function getChartTypeFunctionNameByQuestion($question_term = NULL) {
    $function_name = 'getChartPie';

    $chart_type_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_charttype');
    if ($chart_type_tid) {
      $chart_type_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($chart_type_tid);

      if ($chart_type_term) {
        $function_name = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($chart_type_term, 'field_charttype_functionname');
      }
    }

    return $function_name;
  }

  /**
   * @return renderChartPieDataSet, renderChartDoughnutDataSet
   \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTypeRenderFunctionByQuestion($question_term);
   */
  public function getChartTypeRenderFunctionByQuestion($question_term = NULL) {
    $render_function = 'renderChartPieDataSet';

    $chart_type_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_charttype');
    if ($chart_type_tid) {
      $chart_type_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($chart_type_tid);

      if ($chart_type_term) {
        $render_function = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($chart_type_term, 'field_charttype_renderfunction');
      }
    }

    return $render_function;
  }

  /**
   * @param
   */
  public function renderLegendSquare($legend_text = array(), $legend_color = array(), $max_length = NULL, $legends_class = 'font-size-12') {
    $legend_amount = count($legend_text);
    $legend_margin_top = 12;

    if ($legend_amount < 10) {
      $legend_margin_top = 12 + 14 * (10 - $legend_amount);
    }

    $legends = '<div style="margin-top:'. $legend_margin_top .'px;" class="legend-square-wrapper margin-left-12 width-pt-100 ' . $legends_class . '">';

    foreach ($legend_text as $key => $value) {
      $bg_color_class = NULL;
      if (isset($legend_color[$key])) {
        $bg_color_class = 'bg-' . $legend_color[$key];
      }

      $legends .= '<div class="clear-both height-32 text-center fn-render-legend-square">';
        $legends .= '<span class="legend-square ' . $bg_color_class . '">';
        $legends .= '</span>';
        $legends .= '<span class="float-left legend-text">';
          $legends .= $value;
        $legends .= '</span>';
      $legends .= '</div>';
    }
    $legends .= '</div>';

    return $legends;
  }

  /**
   * @param
   */
  public function renderLegendSquareColorKeyPlusOne($legend_text = array(), $legend_color = array(), $max_length = NULL, $legends_class = 'font-size-12') {
    $legends = '<div class="legends-square-wrapper padding-top-24 margin-top-24 margin-left-12 width-pt-100 ' . $legends_class . '">';

    foreach ($legend_text as $key => $value) {
      $bg_color_class = NULL;
      if (isset($legend_color[$key + 1])) {
        $bg_color_class = 'bg-' . $legend_color[$key + 1];
      }

      $legends .= '<div class="clear-both height-32 text-center">';
        $legends .= '<span class="legend-square ' . $bg_color_class . '">';
        $legends .= '</span>';
        $legends .= '<span class="float-left legend-text">';
          $legends .= $value;
        $legends .= '</span>';
      $legends .= '</div>';
    }
    $legends .= '</div>';

    return $legends;
  }

}
