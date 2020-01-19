<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoCalcService.php.
 */

namespace Drupal\flexinfo\Service;

/**
 * An example Service container.
 */
class FlexinfoCalcService {

  /**
   * @param
    Array (
      [0] => 7
      [1] => 78
      [2] => 1094
      [3] => 3678
      [4] => 2410
    )
   *
   \Drupal::getContainer()->get('flexinfo.calc.service')->arrayAverageByCount($pool_data);
   */
  public function arrayAverageByCount($pool_data = array(), $decimals = 2) {
    $output = 0;

    $sum = 0;
    if (is_array($pool_data) && count($pool_data) > 0) {
      foreach ($pool_data as $key => $value) {
        $sum += ($key + 1) * $value;
      }

      if (array_sum($pool_data) > 0) {
        $result = $sum / array_sum($pool_data);
        $output = number_format((float)$result, $decimals, '.', '');
      }
    }

    return $output;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.calc.service')->arrayAverageByMeetingNodes($meeting_nodes, $question_tid);
   */
  public function arrayAverageByMeetingNodes($meeting_nodes = array(), $question_tid = NULL, $decimals = 2) {
    $pool_data = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperPoolAnswerIntDataByQuestionTid($meeting_nodes, $question_tid);
    $output = $this->arrayAverageByCount($pool_data, $decimals);

    return $output;
  }

  /**
   * @return NPS/NTS-- Net Promoter Score
   * NPS formula
   *
   * For First Question of Evaluation
   * Promoters  =  number of (score 5)
   * Passives   =  number of (score 3-4)
   * Detractors =  number of (score 1-2)
   * Total = Promoters + Passives + Detractors;
   * NPS =  (Promoters - Detractors) / Total;
   *
   \Drupal::getContainer()->get('flexinfo.calc.service')->calcNtsScoreByMeetingNodes();
   */
  public function calcNtsScoreByMeetingNodes($meeting_nodes = array(), $plus_sign = TRUE) {
    // How would you rate the overall quality of the Educational program
    $pool_data_2734 = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperPoolAnswerIntDataByQuestionTid($meeting_nodes, 2734);

    $nps = $this->calcNTSScore($pool_data_2734);
    return $nps;
  }

  /**
   * @return NPS/NTS-- Net Promoter Score
   * NPS formula
   *
   * For First Question of Evaluation
   * Promoters  =  number of (score 5)
   * Passives   =  number of (score 3-4)
   * Detractors =  number of (score 1-2)
   * Total = Promoters + Passives + Detractors;
   * NPS =  (Promoters - Detractors) / Total;
   *
   \Drupal::getContainer()->get('flexinfo.calc.service')->calcNTSScore($pool_data);
   */
  public function calcNTSScore($pool_data = array(), $plus_sign = TRUE) {
    foreach (range(0, 4) as $key => $value) {
      if (!isset($pool_data[$value])) {
        $pool_data[$value] = 0;
      }
    }

    $promoters  =  $pool_data[4];
    $passives   =  $pool_data[3] + $pool_data[2];
    $detractors =  $pool_data[1] + $pool_data[0];

    $total = array_sum($pool_data);

    $nps = 0;
    if ($total != 0) {
      $nps = ($promoters - $detractors) / $total;
    }

    // format NPS score
    if (is_numeric($nps)) {
      $nps = round($nps, 2) * 100;
      if ($plus_sign) {
        if ($nps > 0) {
          // add "+" sign before Positive integer number
          $nps = '+' . $nps;
        }
      }
    }

    return $nps;
  }

  /**
   * avoid number 2 is zero or null
   \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentage();
   */
  public function getPercentage($num1 = NULL, $num2 = NULL) {
    $output = 0;

    if ($num2 > 0) {
      $output = ($num1 / $num2) * 100;
    }

    return $output;
  }

  /**
   * avoid number 2 is zero or null
   \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal()
   */
  public function getPercentageDecimal($num1 = NULL, $num2 = NULL, $decimals = 2) {
    $result = $this->getPercentage($num1, $num2);
    $output = number_format((float)$result, $decimals, '.', '');

    return $output;
  }

  /**
   * @return
   \Drupal::getContainer()->get('flexinfo.calc.service')->getSumFromNodes();
   * count nodes number or count sum for specify field total
   */
  public function getSumFromNodes($entity_array = array(), $count_field = NULL) {
    $output = '';

    if (!$count_field) {
      $output = count($entity_array);
    }
    else {
      $output = array_sum(
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueCollection($entity_array, $count_field)
      );
    }

    return $output;
  }

  /**
   * @return
   */
  public function getSumValue($entity_array = array(), $entity = NULL) {
    $output = '';

    return $output;
  }

  /**
   * @return pool data by percentage instead of value
   */
  public function percentagePoolDataByAnswerInt($pool_data = array(), $decimals = 2) {
    $output = array();
    if ($pool_data && is_array($pool_data)) {
      $sum = array_sum($pool_data);
      foreach ($pool_data as $key => $value) {
        $output[$key] = $this->getPercentageDecimal($value, $sum, $decimals);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function percentage5or4ByAnswerInt($pool_data = array(), $decimals = 2) {
    $output = $this->getPercentageDecimal(
      \Drupal::getContainer() ->get('flexinfo.calc.service')->sum5or4ByAnswerInt($pool_data),
      array_sum($pool_data),
      $decimals
    );

    return $output;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.calc.service')->percentage5or4ByMeetingNodes($meeting_nodes, $question_tid);
   */
  public function percentage5or4ByMeetingNodes($meeting_nodes = array(), $question_tid = NULL, $decimals = 2) {
    $pool_data = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperPoolAnswerIntDataByQuestionTid($meeting_nodes, $question_tid);

    $output = $this->percentage5or4ByAnswerInt($pool_data, $decimals);
    return $output;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.calc.service')->removeArrayEmptyElements();
   */
  public function removeArrayEmptyElements($param_array) {
    $output = array_filter($param_array);

    return $output;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.calc.service')->sum5or4ByAnswerInt();
   */
  public function sum5or4ByAnswerInt($pool_data = array()) {
    $sum = 0;
    if (isset($pool_data[3])) {
      $sum += $pool_data[3];
    }
    if (isset($pool_data[4])) {
      $sum += $pool_data[4];
    }

    return $sum;
  }

  /**
   * @todo Descending
   */
  public function uasortArrayDescending($param_array = array()) {
    uasort($param_array, function($a, $b) {
      return $b - $a;
    });

    return $param_array;
  }

  /**
   * @todo Ascending
   */
  public function uasortArrayAscending($param_array = array()) {
    uasort($param_array, function($a, $b) {
      return $a - $b;
    });

    return $param_array;
  }

}
