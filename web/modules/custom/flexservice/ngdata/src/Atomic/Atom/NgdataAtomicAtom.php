<?php

namespace Drupal\ngdata\Atomic\Atom;

use Drupal\ngdata\Atomic\NgdataAtomic;

/**
 * @deprecate
 */
use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 * Class NgdataAtomicAtom.
 \Drupal::service('ngdata.atomic.atom')->demo();
 */
class NgdataAtomicAtom extends NgdataAtomic {

  /**
   * Constructs a new NgdataAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   * @param $bottom_value is '&nbsp;' or 'RESPONSES'
   */
  public function getBottomHtmlCell($top_value = NULL, $bottom_value = '&nbsp;') {
    if (!$bottom_value) {
      $bottom_value = '&nbsp;';
    }

    $output = '';
    $output .= '<div class="display-inline-block width-pt-50 height-66 border-1-eee border-bottom-none">';
      $output .= '<div class="font-bold height-32 padding-top-6">';
        $output .= $top_value;
      $output .= '</div>';
      $output .= '<div class="font-size-10 text-center">';
        $output .= $bottom_value;
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * Horizontal is float
   */
  public function renderLegendSquareHorizontal($legend_text = array(), $legend_color = array(), $max_length = NULL) {
    $legends = '<div class="legend-square-wrapper margin-left-12 margin-top-24 font-size-12 width-pt-100">';

    foreach ($legend_text as $key => $value) {
      $bg_color_class = NULL;
      if (isset($legend_color[$key])) {
        $bg_color_class = 'bg-' . $legend_color[$key];
      }

      $legends .= '<div class="height-32 text-center float-left">';
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
   *
   */
  public function renderChartBottomFooterAveragePercentage($question_term = NULL, $meeting_nodes = array()) {
    $FlexpageEventLayout = new FlexpageEventLayout();
    $chartAllData = $FlexpageEventLayout->getQuestionAnswerAllData($meeting_nodes, $question_term->id());

    $meanByPercentage = \Drupal::getContainer()
      ->get('flexinfo.calc.service')
      ->getPercentageDecimal(array_sum($chartAllData), count($chartAllData), 0);

    $output = $meanByPercentage / 100;

    return $output;
  }

  /**
   *
   */
  public function renderChartBottomFooterByKeyValuePercentage($question_term = NULL, $meeting_nodes = array(), $key = 'Last') {
    $output = 0;

    $key_value = $this->renderChartBottomFooterBySpecifyKeyValuePercentage($question_term, $meeting_nodes, $key);

    $FlexpageEventLayout = new FlexpageEventLayout();
    $sum_data = count($FlexpageEventLayout->getQuestionAnswerAllData($meeting_nodes, $question_term->id()));

    $output = \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($key_value, $sum_data, 0);
    $output .= '%';

    return $output;
  }

  /**
   *
   */
  public function renderChartBottomFooterBySpecifyKeyValuePercentage($question_term = NULL, $meeting_nodes = array(), $key = 'Last') {
    $output = 0;

    $FlexpageEventLayout = new FlexpageEventLayout();
    switch ($key) {
      case 'First':
        $question_answer = 1;
        break;
      case 'Last':
        $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');
        $question_answer = $question_scale;
        break;

      // when $key is number, instead of string
      default:
        $question_answer = $key;
        break;
    }

    $output = $FlexpageEventLayout->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), $question_answer);

    return $output;
  }

  /**
   *
   */
  public function renderChartBottomFooterAnswerValue($question_term = NULL, $meeting_nodes = array()) {
    $footeranswer = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_footeranswer');

    if ($footeranswer) {
      $output = $this->renderChartBottomFooterByKeyValuePercentage($question_term, $meeting_nodes, $footeranswer);
    }
    else {
      $output = $this->renderChartBottomFooterAveragePercentage($question_term, $meeting_nodes);
    }

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionLegend($question_term = NULL) {
    $output = \Drupal::getContainer()
      ->get('baseinfo.chart.service')
      ->getChartLegendFromLegendTextField($question_term);
    $output = array_reverse($output);

    return $output;
  }

  /**
   *
   */
  public function getSelectkeyQuestionLabel($question_term = NULL) {
    $output = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIdsTermNames($question_term, 'field_queslibr_selectkeyanswer');

    return $output;
  }

  /**
   *
   */
  public function tableHeaderByBasicQuestion() {
    $output = array(
      array(
        'field' => 'Name',
        'header' => 'Name',
      ),
      array(
        'field' => 'Number of Responses',
        'header' => 'Number of Responses',
      ),
      array(
        'field' => 'Percentage',
        'header' => 'Percentage',
      ),
    );

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByByBasicQuestion($meeting_nodes = array(), $question_tid = NULL) {
    $output = array();

    $FlexpageEventLayout = new FlexpageEventLayout();

    $question_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($question_tid);
    if ($question_term) {
      $all_answer_terms = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldAllTargetIdsEntitys($question_term, 'field_queslibr_selectkeyanswer');

      $result = $FlexpageEventLayout->getQuestionAnswerAllData($meeting_nodes, $question_term->id());
      $answer_data_sum = count($result);
      $answer_data_count = array_count_values($result);

      foreach ($all_answer_terms as $key => $answer_term) {
        $num_answer = isset($answer_data_count[$key]) ? $answer_data_count[$key] : 0;
        $selectAnswerTermPercentage = \Drupal::getContainer()
          ->get('flexinfo.calc.service')
          ->getPercentageDecimal($num_answer, $answer_data_sum, 0) . '%';

        $output[] = array(
          'Name' => $answer_term->getName(),
          'Number of Responses' => $num_answer,
          'Percentage' => $selectAnswerTermPercentage,
        );
      }
    }

    return $output;
  }

  /**
   * @return font awesome icon
   */
  public function getMeetingStatusIconHtml($node = NULL) {
    $output = '';

    $output = '<div class="html-tooltip-wrapper color-a5d13f">';
      $output .= '<span class="html-tooltip-text-wrapper">';
        $output .= '<i class=" fa ' . \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingStatusIcon($node) . ' fa-lg color-';
        $output .= \Drupal::getContainer()->get('flexinfo.node.service')
          ->getMeetingStatusColorCode($node);
        $output .= '" aria-hidden="true">';
        $output .= '</i>';
      $output .= '</span>';
      $output .= '<span class="html-tooltip-hover-wrapper visibility-hidden color-000 min-width-120 bg-c6c6c6 text-align-center border-radius-20 padding-5 position-absolute z-index-1">';
        $output .= '<span>' . \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingStatus($node) . '</span>';
      $output .= '</span>';
    $output .= '</div>';

    return $output;
  }

}
