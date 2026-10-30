<?php

/**
 * @file
 */

namespace Drupal\ngjson\Content;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Url;

use Drupal\dashpage\Content\DashTabSpeaker;
use Drupal\dashpage\Content\DashpageObjectContent;

use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 * An example controller.
 */
class NgjsonCaseContent {

  /**
   *
   */
  public function blockTabChartjs21($meeting_nodes = array()) {
    $output = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsMeetingsByMonthByEventType($meeting_nodes);

    $output['blockContent'][0]['tabTitle'] = "MONTH";
    $output['tabShow'] = "show";

    $output['blockContent'][1] = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsMeetingsByQuarterByEventType($meeting_nodes)['blockContent'][0];
    $output['blockContent'][1]['tabTitle'] = "QUARTER";

    return $output;
  }

  /**
   *
   */
  public function blockTabChartjs22($meeting_nodes = array()) {
    $output = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsMeetingsByProvince($meeting_nodes);

    $output['tabShow'] = "show";
    $output['blockContent'][0]['tabTitle'] = "PROVINCE";

    // init tab 2
    $output['blockContent'][1] = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsMeetingsByProvinceByEventType($meeting_nodes)['blockContent'][0];
    $output['blockContent'][1]['tabTitle'] = "EVENT TYPE";

    return $output;
  }

  /**
   *
   */
  public function blockTabChartjs31($meeting_nodes = array()) {
    $output = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsHcpReachByMonthByEventType($meeting_nodes);

    $output['tabShow'] = "show";
    $output['blockContent'][0]['tabTitle'] = "MONTH";

    // init tab 2
    $output['blockContent'][1] = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsHcpReachByQuarterByEventType($meeting_nodes)['blockContent'][0];
    $output['blockContent'][1]['tabTitle'] = "QUARTER";

    return $output;
  }

  /**
   *
   */
  public function blockTabChartjs32($meeting_nodes = array()) {
    $output = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsHcpReachByProvince($meeting_nodes);

    $output['tabShow'] = "show";
    $output['blockContent'][0]['tabTitle'] = "PROVINCE";

    // init tab 2
    $output['blockContent'][1] = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsHcpReachByProvinceByEventType($meeting_nodes)['blockContent'][0];
    $output['blockContent'][1]['tabTitle'] = "EVENT TYPE";

    return $output;
  }

  /**
   *
   */
  public function _programEngagementPageContent($meeting_nodes, $entity_id, $start, $end, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $output = $this->_tileSectionProgramEngagement($meeting_nodes);
    $output[] = \Drupal::service('ngdata.atomic.template')
      ->blockTableTemplate(
        "View Events",
        $this->_tableContentEngagement($meeting_nodes, "_tableDataByEventEngagement"),
        $color_box_palette, $bg_color_class);
    $output[] = \Drupal::service('ngdata.atomic.template')
      ->blockTableTemplate(
        "View Programs",
        $this->_tableContentEngagement($meeting_nodes, "_tableDataByTopProgramEngagement"),
        $color_box_palette, $bg_color_class);

    return $output;
  }

  /**
   *
    $tableData = $this->molecule->tableDataByTopProgram($meeting_nodes);
   */
  public function _tableContentEngagement($meeting_nodes = array(), $table_data_template_name = '_tableDataByTopProgramEngagement') {

    if ($table_data_template_name == '_tableDataByEventEngagement') {
      $tableData = $this->_tableDataByEventEngagement($meeting_nodes);
    }
    else {
      $tableData = $this->_tableDataByTopProgramEngagement($meeting_nodes);
    }

    $tableMiddleFields = \Drupal::service('ngdata.atomic.molecule')->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = \Drupal::service('ngdata.atomic.organism')->tableContentStandardTemplate($tableMiddleFields, $tableData);
    // $output['tSortField'] = "Program";

    return $output;
  }

  /**
   * @return array
    $output[] = array(
      'Program' => $program_html,
      'Events' => count($meeting_nodes_by_current_term),
      'Reach' => $signature_total,
      'Responses' => $evaluation_nums,
    );
   */
  public function _tableDataByTopProgramEngagement($meeting_nodes = array(), $limit_row = NULL) {
    $output = array();

    $program_trees = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')->loadTree('program', 0);

    if (is_array($program_trees)) {
      // first loop get top 10 Program
      $top_program_trees = array();
      foreach ($program_trees as $key => $term) {
        $meeting_nodes_by_current_term = \Drupal::service('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        if (count($meeting_nodes_by_current_term) > 0) {
          $num_meeting_nodes = count($meeting_nodes_by_current_term);

          $top_program_trees[] = array(
            'term' => $term,
            'num_meeting_nodes' => $num_meeting_nodes,
          );

          // for sort order condition criteria
          $sort_value[] = $num_meeting_nodes;
        }
      }

      // cut table off to specify number
      if ($limit_row) {
        if (count($top_program_trees) > $limit_row) {
          $top_program_trees = array_slice($top_program_trees, 0, $limit_row);
        }
      }

      /** - - - - - - second loop for top 10 - - - - - - - -  - - - - - - - - - - - - - - -  */
      foreach ($top_program_trees as $key => $top_program) {
        $term = $top_program['term'];

        $meeting_nodes_by_current_term = \Drupal::service('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        if (count($meeting_nodes_by_current_term) > 0) {
          $signature_total = array_sum(
            \Drupal::service('flexinfo.field.service')
              ->getFieldFirstValueCollection($meeting_nodes_by_current_term, 'field_meeting_signature')
          );

          $viewed_75 = \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_term, 'field_meeting_viewed75');
          $viewed_75 = \Drupal::service('flexinfo.calc.service')
              ->getPercentageDecimal(array_sum($viewed_75), count($viewed_75), 0) / 100;
          $viewed_75 = $viewed_75 > 0 ? ($viewed_75 . '%') : '';

          $viewed_100 = \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_term, 'field_meeting_viewed100');
          $viewed_100 = \Drupal::service('flexinfo.calc.service')
              ->getPercentageDecimal(array_sum($viewed_100), count($viewed_100), 0) / 100;
          $viewed_100 = $viewed_100 > 0 ? ($viewed_100 . '%') : '';

          $internal_url = \Drupal\Core\Url::fromUserInput('/ngpage/program/page/' . $term->tid, array('attributes' => array('class' => array('text-primary'))));
          $program_short_name = \Drupal\Component\Utility\Unicode::truncate($term->name, 36, $wordsafe = TRUE, $add_ellipsis = TRUE);
          $program_html = '<div class="html-tooltip-wrapper">';
            $program_html .= '<span class="html-tooltip-text-wrapper">';
              $program_html .= \Drupal::l($program_short_name, $internal_url);
            $program_html .= '</span>';
            $program_html .= '<span class="html-tooltip-hover-wrapper visibility-hidden color-000 min-width-120 bg-c6c6c6 text-align-center border-radius-6 padding-5 position-absolute z-index-1">';
              $program_html .= $term->name;
            $program_html .= '</span>';
          $program_html .= '</div>';

          $result_row = array(
            'Program' => $term->name,
            'Reach' => $signature_total,
            '% Attended 75%' => $viewed_75,
            '% Attended 100%' => $viewed_100,
          );

          $row = $result_row;
          $row['tableBodyData'] = $result_row;
          $row['Program'] = $term->name;

          $output[] = $row;
        }
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function _tableDataByEventEngagement($meeting_nodes = array(), $limit_row = NULL) {
    $output = array();

    if (is_array($meeting_nodes)) {
      foreach ($meeting_nodes as $node) {
        $program_entity = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstTargetIdTermEntity($node, 'field_meeting_program');

        $internal_url = \Drupal\Core\Url::fromUserInput('/htmlpage/meeting/page/' . $node->id(), array('attributes' => array('class' => array('text-primary'))));

        $date = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValueDateFormat($node, 'field_meeting_date');
        $program_name = $program_entity ? $program_entity->getName() : '';
        $program_short_name = \Drupal\Component\Utility\Unicode::truncate($program_name, 36, $wordsafe = TRUE, $add_ellipsis = TRUE);

        $program_html = '<div class="html-tooltip-wrapper">';
          $program_html .= '<span class="html-tooltip-text-wrapper">';
            $program_html .= \Drupal::l($program_short_name, $internal_url);
          $program_html .= '</span>';
          $program_html .= '<span class="html-tooltip-hover-wrapper visibility-hidden color-000 min-width-120 bg-c6c6c6 text-align-center border-radius-6 padding-5 position-absolute z-index-1">';
            $program_html .= $program_name;
          $program_html .= '</span>';
        $program_html .= '</div>';
        $speaker = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstTargetIdUserName($node, 'field_meeting_speaker');
        $reach = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($node, 'field_meeting_signature');


        $viewed_75 = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($node, 'field_meeting_viewed75');
        $viewed_100 = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($node, 'field_meeting_viewed100');

        $result_row = array(
          'Date' => $date,
          'Program' => $program_name,
          'Speaker' => $speaker,
          'Reach' => $reach,
          '% Attended 75%' => $viewed_75 > 0 ? ($viewed_75 . '%') : '',
          '% Attended 100%' => $viewed_100 > 0 ? ($viewed_100 . '%') : '',
        );

        $row = $result_row;
        $row['tableBodyData'] = $result_row;
        $row['Program'] = $program_name;

        $output[] = $row;
      }
    }

    return $output;
  }

  /**
   *
   */
  public function _tileSectionProgramEngagement($meeting_nodes = array()) {
    $output = array();

    $signature_total = array_sum(
      \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_signature')
    );
    $viewed_75 = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_viewed75');
    $viewed_100 = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_viewed100');

    $tile_array[] = array(
      'name'  => '% of Attendees who viewed 75% of the program',
      'value' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal(array_sum($viewed_75), count($viewed_75), 0) / 100 . '%',
    );
    $tile_array[] = array(
      'name'  => '% of Attendees who viewed 100% of the program',
      'value' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal(array_sum($viewed_100), count($viewed_100), 0) / 100 . '%',
    );
    $tile_array[] = array(
      'name'  => 'Total Events11',
      'value' => count($meeting_nodes),
    );
    $tile_array[] = array(
      'name'  => 'HCP Reached',
      'value' => $signature_total,
    );

    foreach ($tile_array as $key => $row) {
      $output[] = \Drupal::service('ngdata.atomic.organism')
        ->tileSection(
          $row['value'],
          $row['name'],
          ' bg-' . \Drupal::service('baseinfo.setting.service')->colorPlateForTile($key + 1, FALSE)
        );
    }

    return $output;
  }

  /**
   *
   */
  public function _tileSectionGroupProgramPage($meeting_nodes = array()) {
    $output = array();

    $signature_total = array_sum(
      \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_signature')
    );
    $evaluation_nums = array_sum(
      \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_evaluationnum')
    );
    $viewed_75 = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_viewed75');
    $viewed_100 = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_viewed100');

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
      'value' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal($evaluation_nums, $signature_total, 0) . '%',
    );
    $tile_array[] = array(
      'name'  => '% of Attendees who viewed 75% of the program',
      'value' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal(array_sum($viewed_75), count($viewed_75), 0) / 100 . '%',
    );
    $tile_array[] = array(
      'name'  => '% of Attendees who viewed 100% of the program',
      'value' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal(array_sum($viewed_100), count($viewed_100), 0) / 100 . '%',
    );

    foreach ($tile_array as $key => $row) {
      $output[] = \Drupal::service('ngdata.atomic.organism')
        ->tileSection(
          $row['value'],
          $row['name'],
          ' bg-' . \Drupal::service('baseinfo.setting.service')->colorPlateForTile($key + 1, FALSE)
        );
    }

    return $output;
  }

}

/**
 * An example controller.
 */
class NgjsonObjectContent extends NgjsonCaseContent {

  public $atomic;

  /**
   * Constructs a new NgdataAtomicPage object.
   */
  public function __construct() {
    $this->atomic = \Drupal::service('ngdata.atomic');
  }

  /**
   * @return php object, not JSON
   */
  public function getFilterProgramTids() {
    $filter_program_tids = array();

    $user_default_theraparea_tid = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_theraparea');
    if ($user_default_theraparea_tid) {
      $filter_program_tids = \Drupal::service('flexinfo.queryterm.service')
        ->programTidsByTheraparea(array($user_default_theraparea_tid));
    }
    else{
      $user_default_businessunit_tid = \Drupal::service('user.data')
        ->get('navinfo', \Drupal::currentUser()->id(), 'default_businessunit');
      if ($user_default_businessunit_tid) {
        $theraparea_tids_by_businessunit_tids = \Drupal::service('flexinfo.queryterm.service')
          ->wrapperTermTidsByField('therapeuticarea', 'field_theraparea_businessunit', array($user_default_businessunit_tid));

        $filter_program_tids = \Drupal::service('flexinfo.queryterm.service')
          ->programTidsByTheraparea($theraparea_tids_by_businessunit_tids);
      }
      else {
        // no filter, it means all

        $filter_program_tids = \Drupal::getContainer()
          ->get('flexinfo.term.service')
          ->getTidsFromVidName('program');
      }
    }

    return $filter_program_tids;
  }

  /**
   *
   */
  public function querySnapshotMeetingsNodes($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    // query container
    $query_container = \Drupal::service('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('meeting');

    // condition date ranger
    if ($start && $end) {
      $group = $query_container->groupByMeetingTimestamp($query, $start, $end);
      $query->condition($group);
    }

    // condition event type, only work on basic map
    $user_default_eventtype = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_eventtype');
    if ($user_default_eventtype) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_eventtype', $user_default_eventtype);
      $query->condition($group);
    }

    // condition province
    $user_default_provinces = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_province');
    if ($user_default_provinces) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_province', $user_default_provinces, 'IN');
      $query->condition($group);
    }

    // top filter condition
    $filter_program_tids = $this->getFilterProgramTids();
    $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_program', $filter_program_tids, 'IN');
    $query->condition($group);

    // condition group
    if ($section == 'program' || $section == 'programEngagement') {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_program', $entity_id);
      $query->condition($group);
    }

    $meeting_nids = $query_container->runQueryWithGroup($query);

    // shuffle and slice
    // shuffle($meeting_nids);
    // $meeting_nids = array_slice($meeting_nids, 0, 10);

    $meeting_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($meeting_nids);

    return $meeting_nodes;
  }

  /**
   *
    $output[] = $this->atomic->getBlock->blockChartjsWithoutLegendMetricQuestion($meeting_nodes, 120, $chart_type = 'bar', FALSE, 'bg-69d0ff');
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSelectkeySwitch($meeting_nodes, 188, 'pie', FALSE, 'bg-f7d417');
   */
  public function dashboard1PageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output = $this->atomic->getOrganism->tileSectionGroup($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsTotalEventsByBusinessunit($meeting_nodes);
    $output[] = $this->atomic->getBlock->blockChartTotalEventsByEventType($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->blockTabChartjs31($meeting_nodes);
    $output[] = $this->blockTabChartjs32($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->blockTabChartjs21($meeting_nodes);
    $output[] = $this->blockTabChartjs22($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableTopProgram($meeting_nodes);
    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableTopSpeaker($meeting_nodes);

    return $this->getComponentPrimengChartjs($output);
  }

  /**
   *
   */
  public function dashboardPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output = $this->atomic->getOrganism->tileSectionGroup($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsTotalEventsByBusinessunit($meeting_nodes);
    $output[] = $this->atomic->getBlock->blockChartTotalEventsByEventType($meeting_nodes);

    return $this->getComponentPrimengChartjs($output);
  }

  /**
   *
   */
  public function dashboardlazy2PageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output = [];
    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->blockTabChartjs31($meeting_nodes);
    $output[] = $this->blockTabChartjs32($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->blockTabChartjs21($meeting_nodes);
    $output[] = $this->blockTabChartjs22($meeting_nodes);

    return $this->getComponentPrimengChartjs($output);
  }

  /**
   *
   */
  public function dashboardlazy3PageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output = [];
    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableTopProgram($meeting_nodes);
    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableTopSpeaker($meeting_nodes);

    return $this->getComponentPrimengChartjs($output);
  }

  /**
   * this is only Json output, no ngpage view
   */
  public function exportdataPageContent($section, $entity_id, $start, $end) {
    $output = [];

    // $ExportDataContent = new ExportDataContent();
    // $tbody_data = $ExportDataContent->standardDataTemplate($section, $entity_id, $start, $end);
    // return json
    // return $tbody_data;

    // Html table
    // $thead = \Drupal::service('ngdata.atomic.molecule')
    //   ->getTableTheadHtml(array_keys(current($tbody_data)));
    // $tbody = \Drupal::service('ngdata.atomic.molecule')
    //   ->getTableTbodyHtml($tbody_data);

    // $table = \Drupal::service('ngdata.atomic.organism')
    //   ->htmlSectionBasicTableTemplate(NULL, $thead, $tbody, $color_box_palette = FALSE, $bg_color_class = 'bg-ffffff margin-top-n-66');

    // $download_url = Url::fromUserInput(
    //   '/ngpage/export_table_data/excel',
    //   ['attributes' => ['class' => ['color-fff']]]
    // );

    // $html_snippet = '';
    // $html_snippet .= '<div class="btn btn-primary margin-left-12 margin-top-n-36 clear-both color-fff">';
    //   $html_snippet .= \Drupal::l(t('Export Excel'), $download_url);
    // $html_snippet .= '</div>';
    // $html_snippet .= $table;

    // $output[] = \Drupal::service('ngdata.atomic.block')
    //   ->getBlockHtmlSnippet($html_snippet, $type = "htmlSnippt", $save_png_icon_style = "display-none");

    return $output;
  }

  /**
   *
    $output[] = $this->atomic->getBlock->blockChartjsWithoutLegendMetricQuestion($meeting_nodes, 120, $chart_type = 'bar');
    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableByQuestion($meeting_nodes, 188, 'Hcp Reach By Specialty');
    $output[] = $this->blockBootstrapSliderByQuestion($meeting_nodes, 120);
   */
  public function metricPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output = [];

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 128);
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 123);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 122);
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 591);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 520);
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 498);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $question_term_453 = \Drupal::entityManager()->getStorage('taxonomy_term')->load(453);
    $output[] = $this->atomic->getBlock->getBlockCommentByQuestion($meeting_nodes, $question_term_453);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $question_term_452 = \Drupal::entityManager()->getStorage('taxonomy_term')->load(452);
    $output[] = $this->atomic->getBlock->getBlockCommentByQuestion($meeting_nodes, $question_term_452);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $program_term = \Drupal::entityManager()->getStorage('taxonomy_term')->load(748);
    $output[] = $this->atomic->getBlock->getBlockHtmlTableBySelectKeyAnswerQuestion($program_term, $meeting_nodes);

    return $this->getComponentPrimengChartjs($output);
  }

  /**
   *
   */
  public function meetingPageContent($section, $entity_id, $start, $end) {
    $content = \Drupal::service('ngdata.atomic.page')
      ->meetingPageContent($section, $entity_id, $start, $end);

    return $this->getComponentPrimengChartjs($content);
  }

  /**
   *
   */
  public function programPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $content = \Drupal::service('ngdata.atomic.page')->programPageContentWithSharePdfLink($meeting_nodes, $entity_id, $start, $end);

    return $this->getComponentPrimengChartjs($content);
  }

  /**
   * View Programs Table List
   */
  public function eventlistPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $table = \Drupal::service('ngdata.atomic.page')
      ->eventlistPageContent($meeting_nodes, $entity_id, $start, $end);

    return $this->getComponentPrimengTable($table);
  }

  /**
   * View Programs Table List
   */
  public function programEngagementPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $table = $this->_programEngagementPageContent($meeting_nodes, $entity_id, $start, $end);

    return $this->getComponentPrimengTable($table);
  }

  /**
   * View Programs Table List
   */
  public function programlistPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $table = \Drupal::service('ngdata.atomic.page')
      ->programlistPageContent($meeting_nodes, $entity_id, $start, $end);

    return $this->getComponentPrimengTable($table);
  }

  /**
   *
   */
  public function speakerlistPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $table = \Drupal::service('ngdata.atomic.page')
      ->speakerlistPageContent($meeting_nodes, $entity_id, $start, $end);

    return $this->getComponentPrimengTable($table);
  }

  /**
   *
   */
  public function standardnodePageContent($section, $entity_id, $start, $end) {
    if ($entity_id == 'meeting') {
      $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);
      $table = \Drupal::service('ngdata.atomic.page')
        ->customNodeMeetingPageContent($meeting_nodes, $entity_id, $start, $end);
    }
    else {
      $table = \Drupal::service('ngdata.atomic.page')
        ->standardnodePageContent($entity_id, $start, $end);
    }

    return $this->getComponentPrimengTable($table);
  }

  /**
   *
   */
  public function standardtermPageContent($section, $entity_id, $start, $end) {
    if ($entity_id == 'questionevalform') {
      $table = \Drupal::service('ngdata.atomic.page')
        ->customTermQuestionEvaluationFormPageContentShowSeparatPages($entity_id, $start, $end);
    }
    else {
      $table = \Drupal::service('ngdata.atomic.page')
      ->standardtermPageContent($entity_id, $start, $end);
    }

    if ($entity_id == 'evaluationform') {
      $additonal_create_term_link = \Drupal::l(
        'Copy Evaluation Form from Existing Evaluation Form',
        Url::fromUserInput('/baseform/form/predefined_evaluation')
      );

      $table = \Drupal::service('ngdata.atomic.page')
        ->customTermEvaluationFormPageContent($entity_id, $start, $end);
      $table[0]['blockHeader'] .= '<span class="margin-left-12">+' . $additonal_create_term_link . '</span>';
    }

    return $this->getComponentPrimengTable($table);
  }

  /**
   *
   */
  public function standarduserPageContent($section, $entity_id, $start, $end) {
    $table = \Drupal::service('ngdata.atomic.page')
      ->standarduserPageContent($entity_id, $start, $end);

    return $this->getComponentPrimengTable($table);
  }

  /**
   *
   */
  public function getComponentPrimengChartjs($content) {
    $output = [];
    $output[0]["componentname"] = "primengchartjs";
    $output[0]["primengcontentdata"] = $content;

    return $output;
  }

  /**
   *
   */
  public function getComponentPrimengTable($content) {
    $output = [];
    $output[0]["componentname"] = "primengtable";
    $output[0]["primengcontentdata"] = $content;

    return $output;
  }

  /**
   * For custom question evaluationforms List
   */
  public function questionEvaluationformsPageContent($question_tid) {
    $evaluation_terms = \Drupal::service('flexinfo.term.service')
      ->getFullTermsFromVidName($vid = 'evaluationform');

    $question_term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($question_tid);

    $evaluationform_terms = '';
    $evaluationform_terms .= '<div class="font-weight-460 font-size-20">';
      $evaluationform_terms .= 'Question Name: ';
    $evaluationform_terms .= '</div>';
    $evaluationform_terms .= '<div class="font-size-16">';
      $evaluationform_terms .= $question_term->getName();
    $evaluationform_terms .= '</div>';
    $evaluationform_terms .= '<div class="font-weight-460 font-size-20">';
      $evaluationform_terms .= 'Evaluation Forms: ';
    $evaluationform_terms .= '</div>';
    $evaluationform_terms .= '<ol>';
      foreach ($evaluation_terms as $key => $evaluation_term) {
        $question_tids = \Drupal::service('flexinfo.field.service')
          ->getFieldAllTargetIds($evaluation_term, 'field_evaluationform_questionset');
        if (in_array($question_tid, $question_tids)) {
          $evaluationform_terms .= '<li>';
            $evaluationform_terms .= '<a href= ' . base_path() . 'taxonomy/term/' . $evaluation_term->id() . '/edit' . '>';
              $evaluationform_terms .= $evaluation_term->getName();
            $evaluationform_terms .= '</a>';
          $evaluationform_terms .= '</li>';
        }
      }
    $evaluationform_terms .= '</ol>';

    return $evaluationform_terms;
  }

}
