<?php

/**
 * @file
 * Contains Drupal\baseinfo\Service\BaseinfoChartService.php.
 */
namespace Drupal\baseinfo\Service;

use Drupal\flexinfo\Service\FlexinfoChartService;

/**
 * Service container.
 * \Drupal::service('baseinfo.chart.service')->getChartLegendFromLegendTextField();
 *
 */
class BaseinfoChartService extends FlexinfoChartService {

  /**
   *
   */
  public function getChartLegendFromLegendTextField($question_term = NULL, $key = NULL) {
    $legend_text_term = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstTargetIdTermEntity($question_term, $field_name = 'field_queslibr_chartlegend');


    $question_scale = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_scale');
    $legends_key_start_from_one = range(1, $question_scale);

    if ($legend_text_term) {
      // default the index key is start from 0
      $legends = \Drupal::service('flexinfo.field.service')
        ->getFieldAllValues($legend_text_term, $field_name = 'field_chartlegend_text');

      // set index key start from 1 with using array_combine()
      if ($legends) {
        $legends_key_start_from_one = array_combine(range(1, count($legends)), array_values($legends));
      }
    }

    $output = $this->getChartLegendOutput($legends_key_start_from_one, $key);

    return $output;
  }

}
