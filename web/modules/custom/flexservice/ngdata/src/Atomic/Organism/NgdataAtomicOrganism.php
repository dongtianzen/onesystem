<?php

namespace Drupal\ngdata\Atomic\Organism;

use Drupal\ngdata\Atomic\NgdataAtomic;

use Drupal\flexpage\Content\FlexpageEventLayout;

use Drupal\ngjson\Content\EventStandardLayoutContent;


/**
 * Class NgdataAtomicOrganism.

  \Drupal::service('ngdata.atomic.organism')->demo();
 */
class NgdataAtomicOrganism extends NgdataAtomic {

  private $atom;
  private $molecule;

  /**
   * Constructs a new NgdataAtomicOrganism object.
   */
  public function __construct() {
    $this->atom     = \Drupal::service('ngdata.atomic.atom');
    $this->molecule = \Drupal::service('ngdata.atomic.molecule');
  }

  /**
   * @todo Without Save Png
   */
  public function basicSectionPure($type = "htmlSnippt") {
    $blockId = uniqid(rand());

    $output = array(
      "tabShow" => "hide",
      "blockHeader" => "",
      "blockClass" => "col-md-12 margin-top-24",
      "blockClassSub" => NULL,
      'blockId' => $blockId,
      "blockContent" => array($this->basicTab($type)),
    );

    return $output;
  }

  /**
   *
   */
  public function basicSection($type = "htmlSnippt", $save_png_icon_style = "float-right margin-top-12 margin-right-16") {
    $blockId = uniqid(rand());

    $output = array(
      "tabShow" => "hide",
      "blockHeader" => "",
      "blockClass" => "col-md-12 margin-top-24",
      "blockClassSub" => NULL,
      'blockId' => $blockId,
      'blockIcon' => $this->molecule->savePngIcon($save_png_icon_style, $blockId),
      "blockContent" => array($this->basicTab($type)),
    );
    return $output;
  }

  /**
   *
   */
  public function basicTab($type) {
    $output = array(
      "tabTitle" => "",
      "tabData" => array(
        "class" => "",
        "type" => $type,
        "top" => array(
          "value" => ""
        ),
        "bottom" => array(
          "value" => ""
        ),
      ),
    );

    return $output;
  }

  /**
   *
   */
  public function basicMiddleChart($chart_type = "pie", $middle_class = "col-md-6", $right_class = "col-md-6") {
    $output = array(
      "class" => "min-height-340",
      "middleLeft" => [
        "value" => ""
      ],
      "middleMiddle" => [
        "value" => "",
        "type" => $chart_type,
        "styleClass" => $middle_class,
        "data" => [
          "labels" => [],
          "datasets" => [
            "label" => "",
            "data" => [],
            "backgroundColor" => []
          ]
        ],
        "options" => [
          "legend" => [
            "display" => FALSE
          ],
          "plugins" => [
            "labels" => [
              // "render" => "value",
              "fontColor" => "#fff",
              "fontSize" => 13,
              "position" => "border"
            ]
          ],
        ]
      ],
      "middleRight" => [
        "styleClass" => $right_class,
        "value" => ""
      ]
    );

    return $output;
  }

  /**
   *
   */
  public function emptyRowSection() {
    $output = $this->basicSection();
    $output['blockClass'] = "col-xs-12";
    $output['blockHeader'] = '<div style="clear:both;"></div>';

    return $output;
  }

  /**
   *
   */
  public function tileSection($num, $tile_name, $css_class) {
    $output = $this->basicSection();
    $output['blockClass'] = "col-xs-12 col-sm-6 col-md-3 margin-top-24 height-100 color-fff";
    $output['blockHeader'] = $this->molecule->tileBlockHeader($num, $tile_name, $css_class, TRUE);

    return $output;
  }

  /**
   *
   */
  public function blockHeaderHtmlQuestionTitle($question_term = NULL) {
    $output = '<div class="block-comment-header-wrapper clear-both">';
      $output .= '<div class="panel-header block-header bg-0f69af color-fff line-height-42 padding-left-18 padding-right-24 margin-top-12">';
        $output .= '<span>';
          $output .= \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($question_term);
        $output .= '</span>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionLegendHorizontal($question_term = NULL, $meeting_nodes = array()) {
    $output = $this->atom->renderLegendSquareHorizontal(
      $this->molecule->getRaidoQuestionLegendText($question_term, $meeting_nodes),
      \Drupal::service('ngdata.term.question')->getRaidoQuestionColors($question_term)
    );

    return $output;
  }

  /**
   *
   */
  public function getHtmlTableBySelectKeyAnswerQuestion($question_term = NULL, $meeting_nodes = array()) {
    $FlexpageEventLayout = new FlexpageEventLayout();

    $pool_data = $FlexpageEventLayout->getQuestionAnswerAllData($meeting_nodes, $question_term->id());
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
    }

    return $table;
  }

  /**
   *
   */
  public function getHtmlTableByMultipleQuestionByReferUid($question_term = NULL, $meeting_nodes = array()) {
    $FlexpageEventLayout = new FlexpageEventLayout();
    $pool_data = $FlexpageEventLayout->getQuestionAnswerAllDataWithReferUid($meeting_nodes, $question_term->id());

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
    }

    return $table;
  }

  /**
   *
   */
  public function tileSectionGroup($meeting_nodes = array(), $eight_tile = TRUE) {
    $output = array();

    $signature_total = array_sum(
      \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_signature')
    );
    $evaluation_nums = array_sum(
      \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_evaluationnum')
    );

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
      'value' => \Drupal::getContainer()->get('flexinfo.calc.service')
        ->getPercentageDecimal($evaluation_nums, $signature_total, 0) . '%',
    );

    if ($eight_tile) {
      // $tile_array[] = array(
      //   'name'  => 'Overall program QUALITY',
      //   'value' => \Drupal::service('ngdata.term.question')
      //     ->getRaidoQuestionTidStatsAverage(120, $meeting_nodes),
      // );
      // $tile_array[] = array(
      //   'name'  => 'Overall General Satisfaction',
      //   'value' => \Drupal::service('ngdata.term.question')
      //     ->getRaidoQuestionTidsStatsAverage(array(130, 131, 132, 133), $meeting_nodes),
      // );
      // $tile_array[] = array(
      //   'name'  => ' Speaker Rating',
      //   'value' => \Drupal::service('ngdata.term.question')
      //     ->getRaidoQuestionTidStatsAverage(134, $meeting_nodes),
      // );
      // $tile_array[] = array(
      //   'name'  => 'Hospitality - Venue, Accommodation, Restoration',
      //   'value' => \Drupal::service('ngdata.term.question')
      //     ->getRaidoQuestionTidsStatsAverage(array(135, 136, 137), $meeting_nodes),
      // );
    }

    foreach ($tile_array as $key => $row) {
      $output[] = \Drupal::service('ngdata.atomic.organism')
        ->tileSection(
          $row['value'],
          $row['name'],
          ' bg-' . \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateForTile($key + 1, FALSE)
        );
    }

    return $output;
  }

  /**
   *
   */
  public function getLegendTotalEventsByBU($meeting_nodes = array()) {
    $chartData = array_values(\Drupal::service('ngdata.node.meeting')
      ->countMeetingNodesArray(\Drupal::service('ngdata.node.meeting')
        ->meetingNodesByBU($meeting_nodes)
      )
    );
    $chartLabel = \Drupal::service('ngdata.term')
      ->getTermListByVocabulary('businessunit')['label'];

    $legend_text = array();
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . $chartData[$key] . ')';
      }
    }

    $legend_color = \Drupal::service('flexinfo.setting.service')
      ->colorPlateOutputKeyByPaletteName('colorPlatePieChartOne', $color_key = NULL, $pound_sign = FALSE, 'f6f6f6');

    $output = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->renderLegendSquare($legend_text, $legend_color, $max_length = NULL, 'font-size-14');

    return $output;
  }

  /**
   *
   */
  public function getLegendTotalEventsByTherapeuticArea($meeting_nodes = array(), $businessunit_tid = NULL) {
    $legend_text = [];

    $chartData = \Drupal::service('ngdata.node.meeting')
      ->countMeetingNodesArray(\Drupal::service('ngdata.node.meeting')
        ->meetingNodesByTherapeuticArea($meeting_nodes, $businessunit_tid));
    $chartLabel = \Drupal::service('ngdata.term')
      ->getTermTherapeuticAreaListByBu($businessunit_tid)['label'];
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . $chartData[$key] . ')';
      }
    }

    $legend_color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlatePieChartOne(NULL, FALSE);
    $legends = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color, $max_length = NULL, 'font-size-14');

    return $legends;
  }

  /**
   *
   */
  public function legendTotalEventsByEventType($meeting_nodes = array(), $by_event = TRUE) {
    $chartData = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEventsByMonthByEventType($meeting_nodes, $by_event);
    $chartLabel = \Drupal::service('ngdata.term')->getTermListByVocabulary('eventtype')['label'];
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . array_sum($row['data']) . ')';
      }
    }

    $legend_color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne(NULL, FALSE);
    $legends = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color, $max_length = NULL, 'font-size-14');

    return $legends;
  }

  /**
   *
   */
  public function htmlSectionBasicTableTemplate($header = NULL, $thead = NULL, $tbody = NULL, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $table = $this->molecule->getBlockHeader($header, $color_box_palette, $bg_color_class);
    $table .= '<div class="html-basic-table-wrapper">';
      $table .= '<table class="table">';
        $table .= '<thead>';
          $table .= '<tr>';
            $table .= $thead;
          $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
          $table .= $tbody;
        $table .= '</tbody>';
      $table .= '</table>';
    $table .= '</div>';

    return $table;
  }

  /**
   *
    $tableData = $this->molecule->tableDataByEventList($meeting_nodes);
   */
  public function tableContentEventList($meeting_nodes = array(), $table_data_template_name = 'tableDataByEventList') {
    $tableData = $this->molecule->{$table_data_template_name}($meeting_nodes);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
    $tableData = $this->molecule->tableDataByTopProgram($meeting_nodes);
   */
  public function tableContentProgramList($meeting_nodes = array(), $table_data_template_name = 'tableDataByTopProgram') {
    $tableData = $this->molecule->{$table_data_template_name}($meeting_nodes);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);
    // $output['tSortField'] = "Program";

    return $output;
  }

  /**
   * @see $tableMiddle['tabledata']
      foreach ($speakers as $key => $value) {
        $tableMiddle['tabledata'][$key] = array(
          'Speaker' => $value,
          'Events' => rand(30, 100),
          'Reach' => rand(200, 300),
          'Rating' => rand(100, 200),
        );
      }

    $tableData = $this->molecule->tableDataByTopSpeaker($meeting_nodes, $limit_row, $question_tid);
   */
  public function tableContentSpeakerList($meeting_nodes = array(), $limit_row = NULL, $question_tid = NULL, $table_data_template_name = 'tableDataByTopSpeaker') {
    $tableData = $this->molecule->{$table_data_template_name}($meeting_nodes, $limit_row, $question_tid);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
    $tableData = $this->molecule->tableDataByTermQuestion($meeting_nodes);
   */
  public function tableContentQuestionList($meeting_nodes = array(), $table_data_template_name = 'tableDataByTermQuestion') {
    $tableData = $this->molecule->{$table_data_template_name}($meeting_nodes);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);
    // $output['tSortField'] = "Program";

    return $output;
  }

  /**
   *
   */
  public function tableContentStandardnode($entity_id, $start, $end) {
    $tableData = $this->molecule->tableDataByStandardnode($entity_id, $start, $end);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomNodeMeeting($entity_id, $start, $end) {
    $tableData = $this->molecule->tableDataByCustomNodeByMeeting($entity_id, $start, $end);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomUserForAdminSection() {
    $tableData = $this->molecule->tableDataByCustomUserForAdminSection();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermProgram() {
    $tableData = $this->molecule->tableDataByCustomTermByProgram();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermQuestion() {
    $tableData = $this->molecule->tableDataByCustomTermByQestion();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermEvaluationForm() {
    $tableData = $this->molecule->tableDataByCustomTermEvaluationForm();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermBusinessunit() {
    $tableData = $this->molecule->tableDataByCustomTermBusinessunit();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermTherapeuticarea() {
    $tableData = $this->molecule->tableDataByCustomTermTherapeuticarea();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermQuestionEvaluationForm() {
    $tableData = $this->molecule->tableDataByCustomTermQuestionEvaluationForm();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentStandardterm($entity_id, $start, $end) {
    $tableData = $this->molecule->tableDataByStandardterm($entity_id, $start, $end);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentStandardTemplate($tableMiddleFields, $tableData) {
    $output['tSortField'] = "";
    $output['tfields'] = $tableMiddleFields;
    $output['thead']   = $tableMiddleFields;

    $output['tabledata'] = $tableData;

    return $output;
  }

}
