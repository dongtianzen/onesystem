<?php

/**
 * @file
 */

namespace Drupal\flexpage\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\flexpage\Content\FlexpageBaseJson;
use Drupal\flexpage\Content\FlexpageJsonGenerator;
use Drupal\flexpage\Content\FlexpageSampleDataGenerator;


/**
 * An example controller.
 $FlexpageEventLayout = new FlexpageEventLayout();
 $FlexpageEventLayout->sampleFunction();
 */
class FlexpageEventLayout extends ControllerBase {

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
  public function blockEventsSnapshot($meeting_nodes = array(), $evaluationform_tid = NULL, $page_view = NULL) {
    $output = array();

    if ($evaluationform_tid) {
      $evaluationform_term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->load($evaluationform_tid);

      if ($evaluationform_term && $evaluationform_term->getVocabularyId() == 'evaluationform') {
        $question_tids = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

        $output = $this->blockEventsSnapshotAuto($meeting_nodes, $question_tids, $page_view);

        $output = array_merge($output, $this->blockEventsSnapshotLearningObjectiveQuestions($meeting_nodes, $question_tids, $page_view));

        if ($page_view == 'meeting_view') {
          $output = array_merge($output, $this->blockEventsSnapshotMultipleQuestions($meeting_nodes, $evaluationform_term));
        }

        $output = array_merge($output, $this->blockEventsSnapshotSelectKeyQuestions($meeting_nodes, $evaluationform_term));
        $output = array_merge($output, $this->blockEventsSnapshotRankingQuestions($meeting_nodes, $evaluationform_term));
        $output = array_merge($output, $this->blockEventsSnapshotComments($meeting_nodes, $question_tids));
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockStandardLayout($meeting_nodes = array(), $question_tids = NULL, $page_view = NULL, $block_option_override = array()) {
    $output = array();

    if (is_array($question_tids) && count($question_tids) > 0) {

      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids);
      foreach ($question_terms as $question_term) {
        $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

        // $chart_type_method like "getChartDoughnut"
        $chart_type_method = \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTypeFunctionNameByQuestion($question_term);

        // $chart_render_method like "renderChartPieDataSet"
        $chart_render_method = \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTypeRenderFunctionByQuestion($question_term);

        $pool_data = array(
          "0" => $this->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), 1),
          "1" => $this->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), 2),
          "2" => $this->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), 3),
        );
        $pool_label = array(
          "0" => 1,
          "1" => 2,
          "2" => 3,
        );

        if ($question_scale > 3) {
          $pool_data = [];
          $pool_label = [];
          for ($i = 0; $i < $question_scale; $i++) {
            $pool_data[$i] = $this->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), ($i + 1));
            $pool_label[$i] = $i + 1;
          }
        }

        $color_plate = \Drupal::getContainer()
          ->get('flexinfo.setting.service')
          ->colorPlateOutputKeyPlusOneByPaletteName('EventPie5', $color_key = NULL, $pound_sign = FALSE, 'f6f6f6');
        if ($question_scale == 7) {
          $color_plate = \Drupal::getContainer()
            ->get('flexinfo.setting.service')
            ->colorPlateOutputKeyPlusOneByPaletteName('EventPie7', $color_key = NULL, $pound_sign = FALSE, 'f6f6f6');
        }
        elseif ($question_scale == 10) {
          $color_plate = \Drupal::getContainer()
            ->get('flexinfo.setting.service')
            ->colorPlateOutputKeyPlusOneByPaletteName('EventPie10', $color_key = NULL, $pound_sign = FALSE, 'f6f6f6');
        }

        $chart_data = \Drupal::getContainer()
          ->get('flexinfo.chart.service')
          ->{$chart_render_method}($pool_data, $pool_label, NULL, $question_term, $color_plate);

        $block_option  = $this->getBlockOption($pool_data, $question_term, $chart_type_method, $color_plate);
        $chart_options = $this->getChartOption($pool_data, $question_term, $chart_type_method);

        $block_option = $this->FlexpageBaseJson->setBlockProperty($block_option, $block_option_override);

        $chart_type_method = 'getChartNewJsPie';
        // output standard block or tab block
        $output[] = $this->FlexpageBaseJson->getBlockOne(
          $block_option,
          $this->FlexpageBaseJson->{$chart_type_method}($chart_options, $chart_data)
        );
      }

      // $num_question_tids = count($question_tids);
      // $remainder = fmod($num_question_tids, 2);
      // if ($remainder == 1) {
      //   // change last three as "col-md-4"
      //   for ($i = 0; $i < 3; $i++) {
      //     $output[$num_question_tids - $i - 1]['class'] = "col-md-4";
      //   }
      // }
      // if ($num_question_tids == 1) {
      //   $output[0]['class'] = "col-md-12";
      // }

    }

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotAuto($meeting_nodes = array(), $question_tids = array(), $page_view = NULL) {
    $output = array();

    $filter_tids = \Drupal::getContainer()
      ->get('baseinfo.queryterm.service')
      ->wrapperQuestionTidsByRadiosByLearningObjective($question_tids, FALSE);

    $sort_tids = array_intersect($question_tids, $filter_tids);

    $block_option_override = array(
      'top'  => array(
        'class' => 'height-72',
      ),
    );

    $output = $this->blockStandardLayout($meeting_nodes, $sort_tids, $page_view, $block_option_override);

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotLearningObjectiveQuestions($meeting_nodes = array(), $question_tids = array(), $page_view = NULL) {
    $output = array();

    $filter_tids = \Drupal::getContainer()
      ->get('baseinfo.queryterm.service')
      ->wrapperQuestionTidsByRadiosByLearningObjective($question_tids, TRUE);

    $sort_tids = array_intersect($question_tids, $filter_tids);

    $block_option_override = array(
      'top'  => array(
        'class' => 'bg-05d23e height-72',
      ),
    );

    $output = $this->blockStandardLayout($meeting_nodes, $sort_tids, $page_view, $block_option_override);

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotComments($meeting_nodes = array(), $question_tids = array()) {
    $textfield_tid= \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getTidByTermName($term_name = 'textfield', $vocabulary_name = 'fieldtype');

    $textfield_question_tids = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $textfield_tid);

    $sort_tids = array_intersect($question_tids, $textfield_question_tids);

    $output = $this->getTextfieldQuestionComments($sort_tids, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotMultipleQuestions($meeting_nodes = array(), $evaluationform_term = NULL) {
    $question_tids = \Drupal::getContainer()
      ->get('baseinfo.queryterm.service')
      ->wrapperMultipleQuestionTidsFromEvaluationform($evaluationform_term);

    $output = $this->getHtmlTableByMultipleQuestionsByReferUid($question_tids, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotRankingQuestions($meeting_nodes = array(), $evaluationform_term = NULL) {
    $question_tids = \Drupal::getContainer()
      ->get('baseinfo.queryterm.service')
      ->wrapperRankingQuestionTidsFromEvaluationform($evaluationform_term);

    $output = $this->getHtmlTableByMultipleQuestionsByReferTid($question_tids, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function blockEventsSnapshotSelectKeyQuestions($meeting_nodes = array(), $evaluationform_term = NULL) {
    $question_tids = \Drupal::getContainer()
      ->get('baseinfo.queryterm.service')
      ->wrapperFieldtypeQuestionTidsFromEvaluationform('selectkey', $evaluationform_term);

    // or $this->getCommonTableBySelectKeyAnswers()
    $output = $this->getHtmlTableBySelectKeyAnswers($question_tids, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function getBlockOption($pool_data, $question_term, $chart_type_method, $color_plate) {
    $grid_class = "col-md-6";

    $chart_block_title = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->getChartTitleByQuestion($question_term);
    $middle_bottom = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->renderChartBottomFooter($pool_data, $question_term, TRUE, TRUE);

    $block_option = array(
      'class' => $grid_class,
      'top'  => array(
        'value' => $chart_block_title,
      ),
    );

    $question_scale = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_scale');

    $legend_data = \Drupal::getContainer()->get('baseinfo.chart.service')->getChartLegendFromLegendTextField($question_term);
    if ($question_scale == 7) {
      $legend_data = \Drupal::getContainer()->get('baseinfo.chart.service')->getChartLegendPie7();
    }

    for ($i = $question_scale; $i > 0; $i--) {
      $legend_prefix = $i;
      if (isset($legend_data[$i])) {
        $legend_prefix = $legend_data[$i];
      }

      $legend_num = '';
      if (isset($pool_data[$i - 1])) {
        $legend_num = $pool_data[$i - 1];
      }

      $legend_text[$i] = $legend_prefix . '(' . $legend_num . ')';
    }

    $legends = [];
    if ($chart_type_method == 'getChartPie') {
      $legends = \Drupal::getContainer()
        ->get('flexinfo.chart.service')
        ->renderLegendSquare($legend_text, $color_plate);
    }
    elseif ($chart_type_method == 'getChartDoughnut') {
      $legends = \Drupal::getContainer()
        ->get('flexinfo.chart.service')
        ->renderChartDoughnutLegend($pool_data, $question_term);
    }

    $block_option = array(
      'class' => $grid_class,
      'middle' => array(
        'middleMiddle' => array(
          'middleMiddleMiddleClass' => "col-md-8",
          'middleMiddleRightClass' => "col-md-4",
          'middleMiddleRight' => $legends,
        ),
        "middleBottom" => $middle_bottom,
      ),
      'top'  => array(
        'value' => $chart_block_title,
        'class' => NULL,
      ),
    );

    if ($chart_type_method == 'getChartPie') {
      $block_option['middle']['middleMiddle']['gridColumn'] = "84";
    }

    return $block_option;
  }

  /**
   *
   */
  public function getChartOption($pool_data, $question_term, $chart_type_method) {
    $chart_option = array();

    if ($chart_type_method == 'getChartDoughnut') {
      $legends = array();
      $middle_text = NULL;

      if (isset($pool_data[1])) {
        $middle_text = "No Bias";

        $legends = \Drupal::getContainer()
          ->get('flexinfo.calc.service')
          ->getPercentageDecimal($pool_data[1], array_sum($pool_data), 0) . '%';
      }

      $chart_option = array(
        'chartOptions' => array(
          'crossText' => array(
            NULL,
            $middle_text,
            $legends,
          ),
        ),
      );

    }

    return $chart_option;
  }

  /**
   *
   */
  public function getCommonTableBySelectKeyAnswers($question_tids = array(), $meeting_nodes = array()) {
    $output = array();

    if (is_array($question_tids)) {
      $textfield_question_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {

        $pool_data = $this->getQuestionAnswerAllData($meeting_nodes, $question_term->id());

        $pool_data_sum = count($pool_data);
        $pool_data_count = array_count_values($pool_data);

        $tbody = NULL;
        if (isset($pool_data_count) && count($pool_data_count) > 0) {
          foreach ($pool_data_count as $key => $row) {
            $selectAnswerTerm = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($key);

            $tbody[] = array(
              $selectAnswerTerm->getName(),
              $row,
              \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($row, $pool_data_sum, 0) . '%'
            );
          }

          $table_data = array(
            "thead" => [
              [
                "Name",
                "Number of Responses",
                "Percentage"
              ]
            ],
            "tbody" => $tbody
          );

          $output[] = $this->FlexpageBaseJson->getBlockOne(
            array(
              'class' => "col-md-12",
              'type' => "commonTable",
              'blockClasses' => "height-400 overflow-visible"
            ),
            $this->FlexpageBaseJson->getCommonTable(NUll, $table_data)
          );
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getHtmlTableByMultipleQuestionsByReferTid($question_tids = array(), $meeting_nodes = array()) {
    $output = array();

    if (is_array($question_tids) && $question_tids) {
      $question_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {

        $pool_data = $this->getQuestionAnswerAllDataWithReferTid($meeting_nodes, $question_term->id());
        $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

        $table = NULL;
        if ($pool_data && count($pool_data) > 1) {
          $table .= '<div class="panel-body bg-ffffff font-size-12 margin-left-12">';
            $table .= '<table class="table table-hover">';
              $table .= '<thead class="font-bold">';
                $table .= '<tr>';
                  $table .= '<th>';
                    $table .= 'Name';
                  $table .= '</th>';
                  $table .= '<th>';
                    $table .= 'Total';
                  $table .= '</th>';
                  for ($i = $question_scale; $i > 0; $i--) {
                    $table .= '<th>';
                      $table .= $i;
                    $table .= '</th>';
                  }
                $table .= '</tr>';
              $table .= '</thead>';

              foreach ($pool_data as $key => $row) {
                if ($key) {
                  $term = \Drupal::entityTypeManager()
                    ->getStorage('taxonomy_term')
                    ->load($key);

                  if ($term) {
                    $count_values = array_count_values($row);

                    $table .= '<tbody>';
                      $table .= '<tr>';
                        $table .= '<th class="font-weight-normal">';
                          $table .= $term->getName();
                        $table .= '</th>';
                        $table .= '<th class="font-weight-normal">';
                          $table .= count($row);
                        $table .= '</th>';
                        for ($i = $question_scale; $i > 0; $i--) {
                          $cell_value = isset($count_values[$i]) ? $count_values[$i] : 0;

                          $table .= '<th class="font-weight-normal">';
                            $table .= $cell_value;
                            $table .= ' (' . \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($cell_value, count($row), 0) . '%)';
                          $table .= '</th>';
                        }
                      $table .= '</tr>';
                    $table .= '</tbody>';
                  }
                }
              }

            $table .= '</table>';
          $table .= '</div">';

          $block_option = array(
            'class' => "col-md-12",
            'top'  => array(
              'value' => $question_term->getName(),
            ),
          );

          $output[] = $this->FlexpageBaseJson->getBlockHtmlSnippet($block_option, $table);
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getHtmlTableByMultipleQuestionsByReferUid($question_tids = array(), $meeting_nodes = array()) {
    $output = array();

    if (is_array($question_tids) && $question_tids) {
      $question_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {

        $pool_data = $this->getQuestionAnswerAllDataWithReferUid($meeting_nodes, $question_term->id());

        $table = NULL;
        if ($pool_data && count($pool_data) > 1) {
          $table .= '<div class="panel-body bg-ffffff font-size-12 margin-left-12">';
            $table .= '<table class="table table-hover">';
              $table .= '<thead class="font-bold">';
                $table .= '<tr>';
                  $table .= '<th>';
                    $table .= 'Name';
                  $table .= '</th>';
                  $table .= '<th>';
                    $table .= 'Total';
                  $table .= '</th>';
                  for ($i = 5; $i > 0; $i--) {
                    $table .= '<th>';
                      $table .= $i;
                    $table .= '</th>';
                  }
                $table .= '</tr>';
              $table .= '</thead>';

              foreach ($pool_data as $key => $row) {
                if ($key) {
                  $user = \Drupal::entityTypeManager()
                    ->getStorage('user')
                    ->load($key);

                  if ($user) {
                    $count_values = array_count_values($row);

                    $table .= '<tbody>';
                      $table .= '<tr>';
                        $table .= '<th class="font-weight-normal">';
                          $table .= $user->getDisplayName();
                        $table .= '</th>';
                        $table .= '<th class="font-weight-normal">';
                          $table .= count($row);
                        $table .= '</th>';
                        for ($i = 5; $i > 0; $i--) {
                          $cell_value = isset($count_values[$i]) ? $count_values[$i] : 0;

                          $table .= '<th class="font-weight-normal">';
                            $table .= $cell_value;
                            $table .= ' (' . \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($cell_value, count($row), 0) . '%)';
                          $table .= '</th>';
                        }
                      $table .= '</tr>';
                    $table .= '</tbody>';
                  }
                }
              }

            $table .= '</table>';
          $table .= '</div">';

          $block_option = array(
            'class' => "col-md-12",
            'top'  => array(
              'value' => $question_term->getName(),
            ),
          );

          $output[] = $this->FlexpageBaseJson->getBlockHtmlSnippet($block_option, $table);
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getHtmlTableBySelectKeyAnswers($question_tids = array(), $meeting_nodes = array()) {
    $output = array();

    if (is_array($question_tids) && $question_tids) {
      $question_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {

        $pool_data = $this->getQuestionAnswerAllData($meeting_nodes, $question_term->id());
        $pool_data_sum = count($pool_data);

        $pool_data_count = array_count_values($pool_data);

        $table = NULL;
        if ($pool_data_sum) {
          $table .= '<div class="panel-body bg-ffffff font-size-12 margin-left-12">';
            $table .= '<table class="table table-hover">';
              $table .= '<thead class="font-bold">';
                $table .= '<tr>';
                  $table .= '<th>';
                    $table .= 'Name';
                  $table .= '</th>';
                  $table .= '<th>';
                    $table .= 'Number of Responses';
                  $table .= '</th>';
                  $table .= '<th>';
                    $table .= 'Percentage';
                  $table .= '</th>';
                $table .= '</tr>';
              $table .= '</thead>';

              foreach ($pool_data_count as $key => $row) {
                $selectAnswerTerm = \Drupal::entityTypeManager()
                  ->getStorage('taxonomy_term')
                  ->load($key);

                $selectAnswerTermPercentage = \Drupal::getContainer()
                  ->get('flexinfo.calc.service')
                  ->getPercentageDecimal($row, $pool_data_sum, 0) . '%';

                $table .= '<tbody>';
                  $table .= '<tr>';
                    $table .= '<th class="font-weight-normal">';
                      $table .= $selectAnswerTerm->label();
                    $table .= '</th>';
                    $table .= '<th class="font-weight-normal">';
                      $table .= $row;
                    $table .= '</th>';
                    $table .= '<th class="font-weight-normal">';
                      $table .= $selectAnswerTermPercentage;
                    $table .= '</th>';
                  $table .= '</tr>';
                $table .= '</tbody>';
              }

            $table .= '</table>';
          $table .= '</div">';

          $block_option = array(
            'class' => "col-md-12",
            'top'  => array(
              'value' => $question_term->getName(),
            ),
          );

          $output[] = $this->FlexpageBaseJson->getBlockHtmlSnippet($block_option, $table);
        }
      }
    }

    return $output;
  }

  /**
   * @before the name is filterQuestionData()
   */
  public function getQuestionAnswerByQuestionTid($meeting_nodes = array(), $question_tid = NULL, $question_answer = NULL) {
    $evaluation_nodes = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = 0;
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer'] == $question_answer) {
            $output++;

            // disable "Break" for mulitple user answer
            // break;
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getQuestionAnswerAllData($meeting_nodes = array(), $question_tid = NULL) {
    $evaluation_nodes = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = array();
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $output[] = $row['question_answer'];
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getQuestionAnswerAllDataWithReferUid($meeting_nodes = array(), $question_tid = NULL) {
    $evaluation_nodes = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = array();
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $output[$row['refer_uid']][] = $row['question_answer'];
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getQuestionAnswerAllDataWithReferTid($meeting_nodes = array(), $question_tid = NULL) {
    $evaluation_nodes = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = array();
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $output[$row['refer_tid']][] = $row['question_answer'];
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getTextfieldQuestionComments($textfield_question_tids = array(), $meeting_nodes = array()) {
    $output = array();

    if (is_array($textfield_question_tids)) {
      $textfield_question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($textfield_question_tids);

      foreach ($textfield_question_terms as $textfield_question_term) {

        $pool_data = $this->getTextfieldQuestionAllData($meeting_nodes, $textfield_question_term->id());

        $question_comments = NULL;
        if (isset($pool_data) && count($pool_data) > 0) {
          $question_comments .= '<div class="panel-body bg-ffffff font-size-12 margin-left-12">';
            foreach ($pool_data as $key => $row) {
              $question_comments .= '<li>' . $row . '</li>';
            }
          $question_comments .= '</div">';

          $block_option = array(
            'class' => "col-md-12",
            'top'  => array(
              'value' => $textfield_question_term->getName(),
            ),
          );

          $output[] = $this->FlexpageBaseJson->getBlockHtmlSnippet($block_option, $question_comments);
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getTextfieldQuestionAllData($meeting_nodes = array(), $question_tid = NULL) {
    $evaluation_nodes = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = array();
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $output[] = $row['question_answer'];

            break;
          }
        }
      }
    }

    return $output;
  }

}
