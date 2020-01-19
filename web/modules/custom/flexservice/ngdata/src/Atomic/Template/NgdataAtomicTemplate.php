<?php

namespace Drupal\ngdata\Atomic\Template;

use Drupal\ngdata\Atomic\NgdataAtomic;

use Drupal\dashpage\Content\DashpageObjectContent;

/**
 * Class NgdataAtomicTemplate.
 \Drupal::service('ngdata.atomic.template')->demo()
 */
class NgdataAtomicTemplate extends NgdataAtomic {

  private $atom;
  private $molecule;
  private $organism;

  /**
   * Constructs a new NgdataAtomicTemplate object.
   */
  public function __construct() {
    $this->atom     = \Drupal::service('ngdata.atomic.atom');
    $this->molecule = \Drupal::service('ngdata.atomic.molecule');
    $this->organism = \Drupal::service('ngdata.atomic.organism');
  }

  /**
   *
   */
  public function blockChartCssSet() {
    $output['blockClass'] = "col-md-6 margin-top-24";
    $output['blockClassSub'] = "col-md-12 block-box-shadow padding-left-0 padding-right-0";

    return $output;
  }

  /**
   *
   */
  public function blockTableTemplate($tableHeader, $tableContent, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $tableId = uniqid(NULL, TRUE);
    $output = $this->organism->basicSection("table");

    $output['blockClass'] = "col-md-12 margin-top-24";
    $output['blockClassSub'] = $this->blockChartCssSet()['blockClassSub'];
    $output['blockHeader'] = $this->molecule->getBlockHeader($tableHeader, $color_box_palette, $bg_color_class);

    $output['blockContent'][0]['tabData']['tableId'] = 'tableId-' . $tableId;
    $output['blockContent'][0]['tabData']['middle'] = $tableContent;

    return $output;
  }

  /**
   *
   */
  public function blockHtmlClearBoth() {
    $output = $this->organism->basicSection("htmlSnippt");

    $output['tabShow'] = "hide";

    $output['blockClass'] = "block-html-clear-both-wrapper clear-both height-16 min-height-20";
    $output['blockClass'] = "block-html-clear-both-wrapper clear-both col-xs-12 margin-top-6";
    $output['blockClass'] = "block-html-clear-both-wrapper";

    $output['blockHeader'] = '<div style="clear:both; height:1px;">666</div>';
    $output['blockHeader'] = '<span style="height:1px;"></span>';

    return $output;
  }

  /**
   *
   */
  public function blockHtmlBootstrapModalTemplate() {
    $output = $this->organism->basicSection();
    $output['blockClass'] = "col-xs-12 margin-top-2 height-100 color-fff";
    $output['blockHeader'] = $this->blockHtmlBootstrapModalTemplateHeader();

    return $output;
  }

  /**
   *
   */
  public function blockHtmlBootstrapModalTemplateHeader() {
    $output = '<div id="bootstrap-modal-link" class="modal fade bootstrap-modal-wrapper">';
      $output .= '<div class="modal-dialog" role="document">';
        $output .= '<div class="modal-content">';

          $output .= '<div class="modal-header">';
            $output .= '<div class="row bg-673ab7 margin-top-n-24 padding-top-20">';
              $output .= '<h4 class="modal-title color-fff text-align-center modal-title-speaker-name">';
                // $output .= $user->getDisplayName();
              $output .= '</h4>';
            $output .= '</div>';
            $output .= $this->getModalContentBody($user);
          $output .= '</div>';

          // $output .= '<div class="modal-body">';
          //   $output .= 'Body content';
          // $output .= '</div>';

          // $output .= '<div class="modal-footer">';
          //   $output .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
          //   $output .= '<button type="button" class="btn btn-primary">Save changes</button>';
          // $output .= '</div>';

        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getModalContentBody() {
    $output = '';
    $output .= '<div class="row bg-673ab7">';
      $output .= '<ul class="nav nav-tabs">';
        $output .= '<li class="color-fff">';
          $output .= '<a class="color-fff" data-toggle="tab">YTD</a>';
        $output .= '</li>';
        $output .= '<li class="color-fff">';
          $output .= '<a class="color-fff" data-toggle="tab">ALL TIME</a>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</div>';

    $output .= '<div class="tab-content margin-top-n-4">';
      $output .= '<div class="tab-pane fade in active tab-header-html-content-ytd">';
      $output .= '</div>';

      $output .= '<div class="tab-pane fade tab-header-html-content-all">';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function blockHtmlProgramNameHeader($program_tid) {
    $output = $this->organism->basicSection();
    $output['blockClass'] = "col-xs-12 margin-top-6";
    $output['blockHeader'] = $this->molecule->getBlockHeaderForProgramName($program_tid);

    return $output;
  }

  /**
   *
   */
  public function blockHtmlTileMeetingHeader($meeting_entity) {
    $DashpageObjectContent = new DashpageObjectContent();

    $output = $this->organism->basicSection();
    $output['blockClass'] = "col-xs-12 margin-top-6";
    $output['blockHeader'] = $DashpageObjectContent->blockTileMeetingHtml($meeting_entity);

    return $output;
  }

  /**
   *
   */
  public function renderHtmlBasicTableByQuestion($meeting_nodes = array(), $question_tid = NULL, $title = '') {
    $thead_data = $this->atom->tableHeaderByBasicQuestion();
    $tbody_data = $this->atom->tableDataByByBasicQuestion($meeting_nodes, $question_tid);

    $thead = \Drupal::service('ngdata.atomic.molecule')->getTableTheadHtmlByField($thead_data);
    $tbody = \Drupal::service('ngdata.atomic.molecule')->getTableTbodyHtml($tbody_data);

    $table = $this->organism->htmlSectionBasicTableTemplate($title, $thead, $tbody);

    return $table;
  }

  /**
   *
   */
  public function renderHtmlBasicTableTopProgram($meeting_nodes = array(), $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $tableData = $this->molecule->tableDataByTopProgram($meeting_nodes, 10);

    $thead_data = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);
    $tbody_data = $tableData;

    $thead = \Drupal::service('ngdata.atomic.molecule')->getTableTheadHtmlByField($thead_data);
    $tbody = \Drupal::service('ngdata.atomic.molecule')->getTableTbodyHtml($tbody_data);

    $table = $this->organism->htmlSectionBasicTableTemplate('Top Programs', $thead, $tbody, $color_box_palette, $bg_color_class);

    return $table;
  }

  /**
   *
   */
  public function renderHtmlBasicTableTopSpeaker($meeting_nodes = array(), $limit_row = 10, $question_tid = NULL, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $tableData = $this->molecule->tableDataByTopSpeaker($meeting_nodes, $limit_row, $question_tid);

    $thead_data = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);
    $tbody_data = $tableData;

    $thead = \Drupal::service('ngdata.atomic.molecule')->getTableTheadHtmlByField($thead_data);
    $tbody = \Drupal::service('ngdata.atomic.molecule')->getTableTbodyHtml($tbody_data);

    $table = $this->organism->htmlSectionBasicTableTemplate('Top Speakers', $thead, $tbody, $color_box_palette, $bg_color_class);

    return $table;
  }

  /**
   *
   */
  public function bootstrapSliderHtmlTemplate($meeting_nodes = array(), $answer_mean = NULL, $answer_mean_percentage = NULL) {
    $output = NULL;
    $output .= '<div class="slider slider-horizontal" id="bootstrap-slider-id">';
      $output .= '<div class="slider-track">';
        $output .= '<div class="slider-track-low" style="left:0px; width:0%;">';
        $output .= '</div>';
        $output .= '<div class="slider-selection" style="left:0%; width:' . $answer_mean_percentage . '%;">';
        $output .= '</div>';
        $output .= '<div class="slider-track-high" style="right:0px; width:' . (100 - $answer_mean_percentage) . '%;">';
        $output .= '</div>';
      $output .= '</div>';

      $output .= '<div class="tooltip tooltip-main top" role="presentation" style="left:' . $answer_mean_percentage . '%; opacity:0.9;">';
        $output .= '<div class="tooltip-arrow">';
        $output .= '</div>';
        $output .= '<div class="tooltip-inner" style="color:#fff;">';
          $output .= $answer_mean;
        $output .= '</div>';
      $output .= '</div>';

      // $output .= '<div class="tooltip tooltip-min top" role="presentation">';
      //   $output .= '<div class="tooltip-arrow">';
      //   $output .= '</div>';
      //   $output .= '<div class="tooltip-inner">';
      //   $output .= '</div>';
      // $output .= '</div>';

      // $output .= '<div class="tooltip tooltip-max top" role="presentation">';
      //   $output .= '<div class="tooltip-arrow">';
      //   $output .= '</div>';
      //   $output .= '<div class="tooltip-inner">';
      //   $output .= '</div>';
      // $output .= '</div>';

      $output .= '<div class="slider-handle min-slider-handle round" role="slider" aria-valuemin="0" aria-valuemax="10" style="left:' . $answer_mean_percentage . '%;">';
      $output .= '</div>';
      $output .= '<div class="slider-handle max-slider-handle round hide" role="slider" aria-valuemin="0" aria-valuemax="10">';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function dataBootstrapSliderByQuestion($meeting_nodes = array(), $question_tid = NULL) {
    $question_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($question_tid);

    $answer_mean = \Drupal::service('ngdata.term.question')
      ->getRaidoQuestionTermStatsAverage($question_term, $meeting_nodes);
    $answer_mean_percentage = $answer_mean * 10;

    $output = NULL;
    $output .= $this->molecule->getBlockMeetingHeader($question_term->getName());
    $output .= '<div class="bootstrap-slider-wrapper text-align-center">';
      $output .= '<span class="margin-right-6">0</span>';
        $output .= $this->bootstrapSliderHtmlTemplate($meeting_nodes, $answer_mean, $answer_mean_percentage);
      $output .= '<span class="margin-left-6">10</span>';
    $output .= '</div>';

    $output .= '<div class="text-align-center margin-bottom-24">';
      $output .= '<span class="margin-right-6 h5">';
        $output .= 'Average Response is ' . $answer_mean;
      $output .= '</span>';
    $output .= '</div>';

    return $output;
  }

}
