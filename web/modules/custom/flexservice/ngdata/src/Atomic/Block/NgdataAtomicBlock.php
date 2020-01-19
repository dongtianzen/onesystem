<?php

namespace Drupal\ngdata\Atomic\Block;

use Drupal\ngdata\Atomic\NgdataAtomic;

use Drupal\ngjson\Content\NgjsonObjectContent;

/**
 * Class NgdataAtomicBlock.
 \Drupal::service('ngdata.atomic.block')->demo()
 */
class NgdataAtomicBlock extends NgdataAtomic {

  private $atom;
  private $molecule;
  private $organism;
  private $template;

  // private $entityService;

  /**
   * Constructs a new NgdataAtomicBlock object.
   */
  public function __construct() {
    $this->atom     = \Drupal::service('ngdata.atomic.atom');
    $this->molecule = \Drupal::service('ngdata.atomic.molecule');
    $this->organism = \Drupal::service('ngdata.atomic.organism');
    $this->template = \Drupal::service('ngdata.atomic.template');

    // $this->entityService = \Drupal::service('ngdata.entity');
  }

  /**
   *
   */
  public function blockChartjs($chart_type = "pie", $middle_class = "col-md-6", $right_class = "col-md-6", $bg_color_class = 'bg-0f69af') {
    $output = $this->organism->basicSection("chart");

    $output['isChartjs'] = TRUE;

    $output['blockClass'] = "col-xs-12 col-sm-6 col-md-4 margin-top-12";
    $output['blockContent'][0]['tabData']['class'] = "padding-left-0 padding-right-0";
    $output['blockContent'][0]['tabData']['top']['styleClass'] = "color-fff height-60 padding-15 padding-left-14 " . $bg_color_class;

    $output['blockContent'][0]['tabData']['middle'] = \Drupal::service('ngdata.atomic.organism')
      ->basicMiddleChart($chart_type, $middle_class, $right_class);

    return $output;
  }

  /**
   * field_evaluation_type like Pre, Post
   */
  public function getBlockChartByEvaluationTypeQuestionForBar($question_term = NULL, $meeting_nodes = array(), $chart_type = "bar", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $data[] = $this->getRaidoQuestionCorrectAnswerData($question_term, $meeting_nodes, 253);
    $data[] = 0;
    $data[] = $this->getRaidoQuestionCorrectAnswerData($question_term, $meeting_nodes, 254);
    $labels[] = 'Pre';
    $labels[] = '';
    $labels[] = 'Post';

    $output = $this->blockChartjs($chart_type);

    $output['blockClass'] = "col-md-6 margin-top-24";
    $output['blockClassSub'] = "col-md-12 block-box-shadow padding-left-0 padding-right-0";
    $output['blockHeader'] = $this->molecule->getBlockMeetingHeader(\Drupal::getContainer()
        ->get('flexinfo.chart.service')
        ->getChartTitleByQuestion($question_term), $color_box_palette, $bg_color_class);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-12 margin-top-12 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = $labels;
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => $data,
      "backgroundColor" => \Drupal::service('ngdata.term.question')
        ->getRaidoQuestionColors($question_term, TRUE)
    ]];

    //add chartjs tooltip and label
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["renderLabel"] = 'Percentage';
    $output['blockContent'][0]["chartjsPluginsOptions"] = [
      "calculateTooltip" => TRUE,
      "calculateLabel" => FALSE,
    ];

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-5 margin-top-12";

    $output['blockContent'][0]['tabData']['bottom']["value"] = $this->molecule->getRaidoQuestionBottom($question_term, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function getBlockChartByPrePostQuestionForStackedBarMultipleHorizontal($question_term = NULL, $meeting_nodes = array(), $chart_type = "horizontalBar", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {

    $question_relatedtype = \Drupal::service('flexinfo.field.service')
      ->getFieldAllValues($question_term, 'field_queslibr_relatedtype');

    $output = $this->blockChartjs($chart_type);
    $datasets_data = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEvaluationByPrePostByQuestions($meeting_nodes, $question_term);

    $output['blockClass'] = "col-md-6 margin-top-24";
    $output['blockClassSub'] = "col-md-12 block-box-shadow padding-left-0 padding-right-0";
    $output['blockHeader'] = $this->molecule->getBlockMeetingHeader(\Drupal::getContainer()
        ->get('flexinfo.chart.service')
        ->getChartTitleByQuestion($question_term), $color_box_palette, $bg_color_class);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-12 margin-top-12 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"] = [
      "labels" => $question_relatedtype,
      "datasets" => $datasets_data,
    ];

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')
      ->chartStackBarHorizontalBarOption($datasets_data);

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-5 margin-top-12";

    $output['blockContent'][0]['tabData']['bottom']["value"] = $this->molecule->getRaidoQuestionBottom($question_term, $meeting_nodes);

    $output['blockContent'][0]['tabData']['top']["styleClass"] = "col-xs-12";
    $output['blockContent'][0]['tabData']['top']["value"] = $this->organism->getRaidoQuestionLegendHorizontal($question_term, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function getBlockChartByPrePostQuestionWithAnswerForBar($question_term = NULL, $meeting_nodes = array(), $chart_type = "bar", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $question_relatedtype = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllValues($question_term, 'field_queslibr_relatedtype');

    $data = [];
    if ($question_relatedtype) {
      $correct_answer = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_answer');
      foreach ($question_relatedtype as $row) {
        $data[] = \Drupal::service('ngdata.node.evaluation')
          ->getQuestionAnswerByQuestionTidByReferValue($meeting_nodes, $question_term->id(), $correct_answer, 'refer_other', $row);
      }
    }

    $output = $this->blockChartjs($chart_type);

    $output['blockClass'] = "col-md-6 margin-top-24";
    $output['blockClassSub'] = "col-md-12 block-box-shadow padding-left-0 padding-right-0";
    $output['blockHeader'] = $this->molecule->getBlockMeetingHeader(\Drupal::getContainer()
        ->get('flexinfo.chart.service')
        ->getChartTitleByQuestion($question_term), $color_box_palette, $bg_color_class);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-12 margin-top-12 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = $question_relatedtype;
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => $data,
      "backgroundColor" => \Drupal::service('ngdata.term.question')
        ->getRaidoQuestionColors($question_term, TRUE)
    ]];

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-5 margin-top-12";

    $output['blockContent'][0]['tabData']['bottom']["value"] = $this->molecule->getRaidoQuestionBottom($question_term, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function getBlockChartByRadioQuestion($question_term = NULL, $meeting_nodes = array()) {
    $output = array();

    $charttype_name = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_charttype');

    switch ($charttype_name) {
      case 'Bar Chart':
        $output = $this->getBlockChartByRadioQuestionForBar($question_term, $meeting_nodes);
        break;

      case 'Bar Chart Correct Answer':
        $output = $this->getBlockChartByPrePostQuestionWithAnswerForBar($question_term, $meeting_nodes);
        break;

      case 'Stacked Bar Chart Multiple':
        break;

      case 'Stacked Bar Chart Multiple Horizontal':
        $output = $this->getBlockChartByPrePostQuestionForStackedBarMultipleHorizontal($question_term, $meeting_nodes);
        break;


      default:
        $output = $this->getBlockChartByRadioQuestionForPie($question_term, $meeting_nodes);
        break;
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockChartByRadioQuestionForBar($question_term = NULL, $meeting_nodes = array(), $chart_type = "bar", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $output = $this->blockChartjs($chart_type = "bar", $middle_class = "col-md-12 margin-top-24");

    $output['blockClass'] = $this->template->blockChartCssSet()['blockClass'];
    $output['blockClassSub'] = $this->template->blockChartCssSet()['blockClassSub'];
    $output['blockHeader'] = $this->molecule->getBlockMeetingHeader(\Drupal::getContainer()
        ->get('flexinfo.chart.service')
        ->getChartTitleByQuestion($question_term), $color_box_palette, $bg_color_class);

    $datasets_data = \Drupal::service('ngdata.node.evaluation')
      ->getRaidoQuestionData($question_term, $meeting_nodes);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["renderLabel"] = 'Percentage';
    $output['blockContent'][0]["chartjsPluginsOptions"] = [
      "calculateTooltip" => TRUE,
      "calculateLabel" => FALSE,
    ];

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = $this->atom->getRaidoQuestionLegend($question_term);
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => $datasets_data,
      "backgroundColor" => array_values(\Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne(NULL, TRUE)),
      "borderColor" => array_values(\Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne(NULL, TRUE)),
      "borderWidth" => 1
    ]];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarOption($datasets_data);

    $output['blockContent'][0]['tabData']['bottom']["value"] = $this->molecule->getRaidoQuestionBottom($question_term, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function getBlockChartByRadioQuestionForPieFromQuestionTid($question_tid = NULL, $meeting_nodes = array(), $chart_type = "pie", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $output = [];

    $question_term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($question_tid);

    $output = $this->getBlockChartByRadioQuestionForPie($question_term, $meeting_nodes, $chart_type, $color_box_palette, $bg_color_class);

    return $output;
  }

  /**
   *
   */
  public function getBlockChartByRadioQuestionForPie($question_term = NULL, $meeting_nodes = array(), $chart_type = "pie", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $output = [];

    if (\Drupal::service('ngdata.term.question')
      ->getChartLegendSortOrderValueByQuestionTerm($question_term) == 'Ascend') {
      $output = $this->getBlockChartByRadioQuestionForPieAscendOrder($question_term, $meeting_nodes, $chart_type, $color_box_palette, $bg_color_class);
    }
    else {
      $output = $this->getBlockChartByRadioQuestionForPieDescendOrder($question_term, $meeting_nodes, $chart_type, $color_box_palette, $bg_color_class);
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockChartByRadioQuestionForPieDescendOrder($question_term = NULL, $meeting_nodes = array(), $chart_type = "pie", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $output = $this->getBlockChartByRadioQuestionForPieTemplate();

    $output['blockHeader'] = $this->molecule->getBlockMeetingHeader(\Drupal::getContainer()
        ->get('flexinfo.chart.service')
        ->getChartTitleByQuestion($question_term), $color_box_palette, $bg_color_class);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = $this->atom->getRaidoQuestionLegend($question_term);
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => \Drupal::service('ngdata.node.evaluation')
        ->getRaidoQuestionData($question_term, $meeting_nodes),
      "backgroundColor" => \Drupal::service('ngdata.term.question')
        ->getRaidoQuestionColors($question_term, TRUE)
    ]];

    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->molecule->getRaidoQuestionHtmlLegend($question_term, $meeting_nodes);

    $output['blockContent'][0]['tabData']['bottom']["value"] = $this->molecule->getRaidoQuestionBottom($question_term, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function getBlockChartByRadioQuestionForPieAscendOrder($question_term = NULL, $meeting_nodes = array(), $chart_type = "pie", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $output = $this->getBlockChartByRadioQuestionForPieDescendOrder($question_term, $meeting_nodes);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = array_reverse($output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"]);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => array_reverse(\Drupal::service('ngdata.node.evaluation')
        ->getRaidoQuestionData($question_term, $meeting_nodes)),
      "backgroundColor" => array_reverse(\Drupal::service('ngdata.term.question')
        ->getRaidoQuestionColors($question_term, TRUE))
    ]];

    return $output;
  }

  /**
   *
   */
  public function getBlockChartByRadioQuestionForPieTemplate($chart_type = "pie") {
    $output = $this->blockChartjs($chart_type);

    $output['blockClass'] = "col-md-6 margin-top-24";
    $output['blockClassSub'] = "col-md-12 block-box-shadow padding-left-0 padding-right-0";

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-7 margin-top-12 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["renderLabel"] = 'Percentage';
    $output['blockContent'][0]["chartjsPluginsOptions"] = [
      "calculateTooltip" => TRUE,
      "calculateLabel" => TRUE,
    ];
    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-5 margin-top-12";

    /**
     * interface placeholder
     */
    $output['blockHeader'] = '';

    /**
     * example
      $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = [
        "Strongly Agree",
        "Agree",
        "Neutral",
        "Disagree",
        "Strongly Disagree"
      ];
     */
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = [];

    /**
     * example
      $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
        "data" => [
          2,
          7,
          1,
          1,
          0
        ],
        "backgroundColor" => [
          "#2fa9e0",
          "#05d23e",
          "#c6c6c6",
          "#f7d417",
          "#f24b99"
        ]
      ]];
     */
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => [],
      "backgroundColor" => []
    ]];

    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = '';
    $output['blockContent'][0]['tabData']['bottom']["value"] = '';

    return $output;
  }

  /**
   *
   */
  public function getBlockChartBySelectkeyQuestionForPie($question_term = NULL, $meeting_nodes = array(), $chart_type = "pie", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $output = $this->getBlockChartByRadioQuestionForPieTemplate();

    $output['blockHeader'] = $this->molecule->getBlockMeetingHeader(\Drupal::getContainer()
        ->get('flexinfo.chart.service')
        ->getChartTitleByQuestion($question_term), $color_box_palette, $bg_color_class);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = $this->atom->getSelectkeyQuestionLabel($question_term);
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => array_values(\Drupal::service('ngdata.term.question')
        ->getSelectkeyQuestionData($question_term, $meeting_nodes)
      ),
      "backgroundColor" => \Drupal::service('ngdata.term.question')
        ->getRaidoQuestionColors($question_term, TRUE)
    ]];

    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->molecule->getSelectkeyQuestionLegend($question_term, $meeting_nodes);

    $output['blockContent'][0]['tabData']['bottom']["value"] = $this->molecule->getSelectkeyQuestionBottom($question_term, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function getBlockCommentByQuestion($meeting_nodes = array(), $textfield_question_term = NULL) {
    $output = array();

    if ($textfield_question_term) {
      $question_answers = \Drupal::service('ngdata.term.question')
        ->getTextfieldQuestionAllData($meeting_nodes, $textfield_question_term->id());

      $comments = '<div class="block-comment-wrapper clear-both margin-0 margin-top-12">';
        $comments .= $this->organism->blockHeaderHtmlQuestionTitle($textfield_question_term);
        if (isset($question_answers) && count($question_answers) > 0) {
          $comments .= '<div class="panel-body bg-ffffff font-size-12 margin-left-12">';
            foreach ($question_answers as $key => $row) {
              $comments .= '<li>' . $row . '</li>';
            }
          $comments .= '</div">';
        }
      $comments .= '</div">';

      $output = $this->organism->basicSection("htmlSnippt", "float-right margin-top-24 margin-right-16");
      $output['blockClass'] = "col-xs-12 margin-top-12 margin-bottom-12 block-comment-wrapper";
      $output['blockHeader'] = $comments;
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockHtmlTableByRadioQuestionMultipleByReferUid($question_term = NULL, $meeting_nodes = array()) {
    $output = array();

    $output = $this->organism->basicSection("htmlSnippt", "float-right margin-bottom-n-24 margin-right-16");
    $output['blockClass'] = "col-xs-12 margin-top-12 min-height-100 margin-bottom-12";
    $output['blockHeader'] = $this->organism->blockHeaderHtmlQuestionTitle($question_term);
    $output['blockContent'][0]['tabData']['top']['value'] = $this->organism->getHtmlTableByMultipleQuestionByReferUid($question_term, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function getBlockHtmlTableBySelectKeyAnswerQuestion($question_term = NULL, $meeting_nodes = array()) {
    $output = array();

    $output = $this->organism->basicSection("htmlSnippt", "float-right margin-bottom-n-24 margin-right-16");
    $output['blockClass'] = "col-xs-12 margin-top-12 min-height-100 margin-bottom-12";
    $output['blockHeader'] = $this->organism->blockHeaderHtmlQuestionTitle($question_term);
    $output['blockContent'][0]['tabData']['top']['value'] = $this->organism->getHtmlTableBySelectKeyAnswerQuestion($question_term, $meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function blockChartjsMetricQuestionSwitch($meeting_nodes = array(), $question_tid = NULL, $chart_type = "pie", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $output = array();

    $question_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($question_tid);

    if ($question_term) {
      switch ($chart_type) {
        case 'bar':
          $output = $this->getBlockChartByRadioQuestionForBar($question_term, $meeting_nodes, $chart_type, $color_box_palette, $bg_color_class);
          break;

        default:
          $output = $this->getBlockChartByRadioQuestionForPie($question_term, $meeting_nodes, $chart_type, $color_box_palette, $bg_color_class);
          break;
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockChartjsMetricQuestionSelectkeySwitch($meeting_nodes = array(), $question_tid = NULL, $chart_type = "pie", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $question_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($question_tid);

    $output = $this->getBlockChartBySelectkeyQuestionForPie($question_term, $meeting_nodes, $chart_type, $color_box_palette, $bg_color_class);

    return $output;
  }

  /**
   *
   */
  public function blockChartjsTotalEventsByBusinessunit($meeting_nodes = array(), $bg_color_class = 'bg-149b5f') {
    $output = $this->blockChartjs("pie");

    $output['blockClass'] = $this->template->blockChartCssSet()['blockClass'];
    $output['blockClassSub'] = $this->template->blockChartCssSet()['blockClassSub'];
    $output['blockHeader'] = $this->molecule->getBlockHeader("Total Events by Business Unit", FALSE, $bg_color_class);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-7 margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = \Drupal::service('ngdata.term')->getTermListByVocabulary('businessunit')['label'];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => \Drupal::service('ngdata.node.meeting')
        ->countMeetingNodesArray(\Drupal::service('ngdata.node.meeting')
          ->meetingNodesByBU($meeting_nodes)
      ),
      "backgroundColor" => array_values(\Drupal::service('baseinfo.setting.service')
        ->colorPlatePieChartOne(NULL, TRUE))
    ]];

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-5 margin-top-12";
    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->organism->getLegendTotalEventsByBU($meeting_nodes);

    return $output;
  }

  /**
   * bar chart
   */
  public function blockChartTotalEventsByEventType($meeting_nodes = array(), $bg_color_class = 'bg-149b5f') {
    $output = $this->blockChartjs("bar", $middle_class = "col-md-12 margin-top-24");

    $output['blockClass'] = $this->template->blockChartCssSet()['blockClass'];
    $output['blockClassSub'] = $this->template->blockChartCssSet()['blockClassSub'];
    $output['blockHeader'] = $this->molecule->getBlockHeader("Total Events By Type", FALSE, $bg_color_class);

    $datasets_data = array_values(\Drupal::service('ngdata.node.meeting')
      ->countMeetingNodesArray(\Drupal::service('ngdata.node.meeting')
        ->meetingNodesByEventType($meeting_nodes))
    );

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = \Drupal::service('ngdata.term')
      ->getTermListByVocabulary('eventtype')['label'];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => $datasets_data,
      "backgroundColor" => array_values(\Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne(NULL, TRUE)),
      "borderColor" => array_values(\Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne(NULL, TRUE)),
      "borderWidth" => 1
    ]];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarOption($datasets_data);

    return $output;
  }

  /**
   * @param $entity_id is current businessunit tid
   */
  public function blockChartTotalEventsByTherapeuticArea($meeting_nodes = array(), $entity_id = NULL) {
    $output = $this->blockChartjs("pie");

    $output['blockClass'] = $this->template->blockChartCssSet()['blockClass'];
    $output['blockClassSub'] = $this->template->blockChartCssSet()['blockClassSub'];
    $output['blockHeader'] = $this->molecule->getBlockHeader("Total Events by Therapeutic Area");

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-7 margin-top-6";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["labels"] = \Drupal::service('ngdata.term')->getTermTherapeuticAreaListByBu($entity_id)['label'];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"]["datasets"] = [[
      "data" => \Drupal::service('ngdata.node.meeting')
        ->countMeetingNodesArray(\Drupal::service('ngdata.node.meeting')
          ->meetingNodesByTherapeuticArea($meeting_nodes, $entity_id)
      ),
      "backgroundColor" => array_values(\Drupal::getContainer()->get('baseinfo.setting.service')->colorPlatePieChartOne(NULL, TRUE))
    ]];

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-5 margin-top-12";
    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->organism->getLegendTotalEventsByTherapeuticArea($meeting_nodes, $entity_id);

    return $output;
  }

  /**
   * @internal stackbar chart X-axis is Month,
   */
  public function blockChartjsMeetingsByMonthByEventType($meeting_nodes = array(), $bg_color_class = 'bg-ffc832') {
    $output = $this->blockChartjs("bar");

    $output['blockClass'] = $this->template->blockChartCssSet()['blockClass'];
    $output['blockClassSub'] = $this->template->blockChartCssSet()['blockClassSub'];
    $output['blockHeader'] = $this->molecule->getBlockHeader("Event Implementation", FALSE, $bg_color_class);

    $datasets_data = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEventsByMonthByEventType($meeting_nodes);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-8 margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"] = [
      "labels" => \Drupal::getContainer()->get('flexinfo.setting.service')->getMonthNameAbb(),
      "datasets" => $datasets_data,
    ];

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')->chartStackBarOption($datasets_data);

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-4 margin-top-12";
    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->organism->legendTotalEventsByEventType($meeting_nodes);

    return $output;
  }

  /**
   * @internal stackbar chart X-axis is Quarter,
   */
  public function blockChartjsMeetingsByQuarterByEventType($meeting_nodes = array()) {
    $output = [];
    $output = $this->blockChartjs("bar");

    // init tab 2
    $output['blockContent'][0] = \Drupal::service('ngdata.atomic.organism')
      ->basicTab('chart');
    $output['blockContent'][0]['tabData']['middle'] = \Drupal::service('ngdata.atomic.organism')
      ->basicMiddleChart($chart_type = 'bar');

    $datasets_data = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEventsByMonthByEventType($meeting_nodes, TRUE, $step = 3);

    $output['blockContent'][0]['tabData']['top']['styleClass'] = "bg-0f69af color-fff height-60 padding-15 padding-left-14";

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-8 margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"] = [
      "labels" => \Drupal::getContainer()->get('flexinfo.setting.service')->getQuarterNameAbb(),
      "datasets" => $datasets_data,
    ];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')->chartStackBarOption($datasets_data);

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-4 margin-top-12";
    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->organism->legendTotalEventsByEventType($meeting_nodes);

    return $output;
  }

  /**
   * @internal stackbar chart X-axis is Month,
   */
  public function blockChartjsHcpReachByMonthByEventType($meeting_nodes = array(), $bg_color_class = 'bg-e61e50') {
    $output = $this->blockChartjs("bar");

    $output['blockClass'] = $this->template->blockChartCssSet()['blockClass'];
    $output['blockClassSub'] = $this->template->blockChartCssSet()['blockClassSub'];
    $output['blockHeader'] = $this->molecule->getBlockHeader("HCP Reach By Event Type", FALSE, $bg_color_class);

    $datasets_data_0 = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEventsByMonthByEventType($meeting_nodes, FALSE);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-8 margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"] = [
      "labels" => \Drupal::getContainer()->get('flexinfo.setting.service')->getMonthNameAbb(),
      "datasets" => $datasets_data_0,
    ];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')->chartStackBarOption($datasets_data_0);

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-4 margin-top-12";
    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->organism->legendTotalEventsByEventType($meeting_nodes, FALSE);

    return $output;
  }

  /**
   *
   */
  public function blockChartjsHcpReachByQuarterByEventType($meeting_nodes = array()) {
    $output = $this->blockChartjs("bar");

    $output['blockContent'][0] = \Drupal::service('ngdata.atomic.organism')
      ->basicTab('chart');
    $output['blockContent'][0]['tabData']['middle'] = \Drupal::service('ngdata.atomic.organism')
      ->basicMiddleChart($chart_type = 'bar');

    $datasets_data_1 = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEventsByMonthByEventType($meeting_nodes, FALSE, $step = 3);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-8 margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"] = [
      "labels" => \Drupal::getContainer()->get('flexinfo.setting.service')->getQuarterNameAbb(),
      "datasets" => $datasets_data_1,
    ];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')->chartStackBarOption($datasets_data_1);

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-4 margin-top-12";
    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->organism->legendTotalEventsByEventType($meeting_nodes, FALSE);

    return $output;
  }

  /**
   * @internal bar chart, bar chart is array($data) of stackbar chart
   */
  public function blockChartjsMeetingsByProvince($meeting_nodes = array(), $bg_color_class = 'bg-ffc832') {
    $output = $this->blockChartjs("bar");

    $output['blockClass'] = $this->template->blockChartCssSet()['blockClass'];
    $output['blockClassSub'] = $this->template->blockChartCssSet()['blockClassSub'];
    $output['blockHeader'] = $this->molecule->getBlockHeader("Location of Events", FALSE, $bg_color_class);

    $datasets_data_0 = \Drupal::service('ngdata.chart.chartjs')
      ->chartLineDataByProvince($meeting_nodes, TRUE);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-12 margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"] = [
      "labels" => \Drupal::getContainer()->get('flexinfo.setting.service')->getProvinceDescriptions(),
      "datasets" => array(
        array(
          "data" => $datasets_data_0,
          "backgroundColor" => "#0f69af",
          "borderColor" => "#0f69af",
          "borderWidth" => 1
        )
      )
    ];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')->chartBarOption($datasets_data_0);

    return $output;
  }

  /**
   * @internal bar chart, bar chart is array($data) of stackbar chart
   */
  public function blockChartjsHcpReachByProvince($meeting_nodes = array(), $bg_color_class = 'bg-e61e50') {
    $output = $this->blockChartjs("bar");

    $output['blockClass'] = $this->template->blockChartCssSet()['blockClass'];
    $output['blockClassSub'] = $this->template->blockChartCssSet()['blockClassSub'];
    $output['blockHeader'] = $this->molecule->getBlockHeader("HCP Reach By Location", FALSE, $bg_color_class);

    $datasets_data_0 = \Drupal::service('ngdata.chart.chartjs')
      ->chartLineDataByProvince($meeting_nodes, FALSE);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-12 margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"] = [
      "labels" => \Drupal::getContainer()->get('flexinfo.setting.service')->getProvinceDescriptions(),
      "datasets" => array(
        array(
          "data" => $datasets_data_0,
          "backgroundColor" => "#0f69af",
          "borderColor" => "#0f69af",
          "borderWidth" => 1
        )
      )
    ];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')->chartBarOption($datasets_data_0);

    return $output;
  }

  /**
   *
   */
  public function blockChartjsMeetingsByProvinceByEventType($meeting_nodes = array()) {
    $output = $this->blockChartjs("bar");

    $output['blockContent'][0] = \Drupal::service('ngdata.atomic.organism')
      ->basicTab('chart');
    $output['blockContent'][0]['tabData']['middle'] = \Drupal::service('ngdata.atomic.organism')
      ->basicMiddleChart($chart_type = 'bar');

    $datasets_data_1 = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEventsByProvinceByEventType($meeting_nodes, TRUE);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-8 margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"] = [
      "labels" => \Drupal::getContainer()->get('flexinfo.setting.service')->getProvinceDescriptions(),
      "datasets" => $datasets_data_1,
    ];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')->chartStackBarOption($datasets_data_1);

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-4 margin-top-12";
    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->organism->legendTotalEventsByEventType($meeting_nodes, TRUE);

    return $output;
  }

  /**
   *
   */
  public function blockChartjsHcpReachByProvinceByEventType($meeting_nodes = array()) {
    $output = $this->blockChartjs("bar");

    $output['blockContent'][0] = \Drupal::service('ngdata.atomic.organism')
      ->basicTab('chart');
    $output['blockContent'][0]['tabData']['middle'] = \Drupal::service('ngdata.atomic.organism')
      ->basicMiddleChart($chart_type = 'bar');

    $datasets_data_1 = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEventsByProvinceByEventType($meeting_nodes, FALSE);

    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-8 margin-top-24 margin-bottom-20";
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["data"] = [
      "labels" => \Drupal::getContainer()->get('flexinfo.setting.service')->getProvinceDescriptions(),
      "datasets" => $datasets_data_1,
    ];
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["options"] = \Drupal::service('ngdata.chart.chartjs')->chartStackBarOption($datasets_data_1);

    $output['blockContent'][0]['tabData']['middle']['middleRight']["styleClass"] = "col-sm-12 col-md-4 margin-top-12";
    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = $this->organism->legendTotalEventsByEventType($meeting_nodes, FALSE);

    return $output;
  }

  /**
   *
   */
  public function blockChartjsWithoutLegendMetricQuestion($meeting_nodes = array(), $question_tid = NULL, $chart_type = "bar", $color_box_palette = '', $bg_color_class = 'bg-0f69af') {
    $output = $this->blockChartjsMetricQuestionSwitch($meeting_nodes, $question_tid, $chart_type, $color_box_palette, $bg_color_class);
    $output['blockContent'][0]['tabData']['middle']['middleMiddle']["styleClass"] = "col-md-12 margin-top-24 margin-bottom-20";

    $output['blockContent'][0]['tabData']['middle']['middleRight']["value"] = [];

    return $output;
  }

  /**
   * @see Bootstrap Slider
   */
  public function blockBootstrapSliderByQuestion($meeting_nodes = array(), $question_tid = NULL) {
    $output = \Drupal::service('ngdata.atomic.organism')->basicSection();

    $output['blockClass'] = $this->template->blockChartCssSet()['blockClass'];
    $output['blockClassSub'] = "col-md-12 block-box-shadow padding-left-0 padding-right-0 padding-bottom-48";
    $output['blockHeader'] = $this->template->dataBootstrapSliderByQuestion($meeting_nodes, $question_tid);

    return $output;
  }

  /**
   *
   */
  public function getBlockHtmlBasicTableByQuestion($meeting_nodes = array(), $question_tid = NULL, $title = '') {
    $output = \Drupal::service('ngdata.atomic.organism')->basicSection();
    $output['blockClass'] = "col-xs-12 margin-top-24";
    $output['blockHeader'] = $this->template->renderHtmlBasicTableByQuestion($meeting_nodes, $question_tid, $title);

    return $output;
  }

  /**
   *
   */
  public function getBlockHtmlBasicTableTopProgram($meeting_nodes = array(), $block_class = "col-md-6", $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $output = \Drupal::service('ngdata.atomic.organism')->basicSection();
    $output['blockClass'] = "col-xs-12 col-sm-12 margin-top-24 " . $block_class;
    $output['blockHeader'] = $this->template->renderHtmlBasicTableTopProgram($meeting_nodes, $color_box_palette, $bg_color_class);

    return $output;
  }

  /**
   *
   */
  public function getBlockHtmlBasicTableTopSpeaker($meeting_nodes = array(), $limit_row = 10, $question_tid = NULL, $block_class = "col-md-6", $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $output = \Drupal::service('ngdata.atomic.organism')->basicSection();
    $output['blockClass'] = "col-xs-12 col-sm-12 margin-top-24 " . $block_class;
    $output['blockHeader'] = $this->template->renderHtmlBasicTableTopSpeaker($meeting_nodes, $limit_row, $question_tid, $color_box_palette, $bg_color_class);

    return $output;
  }

  /**
   * @todo with Save Png
   */
  public function getBlockHtmlSnippet($content = NULL, $type = "htmlSnippt", $save_png_icon_style = "float-right margin-top-12 margin-right-16") {
    $output = \Drupal::service('ngdata.atomic.organism')->basicSection($type, $save_png_icon_style);
    $output['blockClass'] = "col-xs-12 margin-top-24";
    $output['blockHeader'] = $content;

    return $output;
  }

  /**
   * @todo Without Save Png
   */
  public function getBlockHtmlSnippetPure($content = NULL) {
    $output = \Drupal::service('ngdata.atomic.organism')->basicSectionPure();
    $output['blockClass'] = "col-xs-12 margin-top-24";
    $output['blockHeader'] = $content;

    return $output;
  }

}
