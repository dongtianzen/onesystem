<?php

namespace Drupal\ngdata\Atomic\Molecule;

use Drupal\ngdata\Atomic\NgdataAtomic;

use Drupal\Core\Url;

/**
 * @deprecate
 */
use Drupal\flexpage\Content\FlexpageEventLayout;
use Drupal\modalpage\Content\ModalTabSpeaker;
use Drupal\ngjson\Content\EventStandardLayoutContent;

/**
 * Class NgdataAtomicMolecule.

  \Drupal::service('ngdata.atomic.molecule')->demo();
 */
class NgdataAtomicMolecule extends NgdataAtomic {

  private $atom;

  /**
   * Constructs a new NgdataAtomicMolecule object.
   */
  public function __construct() {
    $this->atom = \Drupal::service('ngdata.atomic.atom');
  }

  /**
   * @param $icon_position is css style, need include "float-right" and margin-top-12 margin-right-16
   */
  public function savePngIcon($icon_position = NULL, $save_block_id = NULL) {
    $output = "";
    $output .= '<div class="drop-down-icon-wrapper dropdown show ' . $icon_position . '">';
      $output .= '<a class="drop-down-icon-toggle dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $output .= '<i class="fa fa-angle-down color-fff"></i>';
      $output .= '</a>';

      $output .= '<div class="drop-down-icon-menu dropdown-menu padding-20 margin-left-n-86 text-align-center" aria-labelledby="dropdownMenuLink">';
        $output .= '<a onclick="saveHtmlToPng(\'' . $save_block_id . '\')" class="dropdown-item color-000 font-size-14" href="javascript:void(0);">SAVE PNG</a>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getBlockHeader($title = NULL, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $output = "";
    $output .= '<div class="' . $bg_color_class . ' color-fff padding-15 height-60">';
      if ($color_box_palette) {
        $output .= '<span class="float-left display-block height-32 width-32 border-1-eee margin-left-6 margin-right-12 ' . $color_box_palette . '">';
        $output .= '</span>';
      }

      $output .= '<span class="margin-top-6 line-height-1-2 margin-left-14 display-block">';
        $output .= $title;
      $output .= '</span>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getBlockMeetingHeader($title = NULL, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $output = '<div class="' . $bg_color_class . ' color-fff padding-15 height-60">';
      if ($color_box_palette) {
        $output .= '<span class="float-left display-block height-32 width-32 border-1-eee margin-left-6 margin-right-12 ' . $color_box_palette . '">';
        $output .= '</span>';
      }
      $output .= '<span class="margin-left-14 line-height-1-2 display-block">';
        $output .= $title;
      $output .= '</span>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function tileBlockHeader($num, $tile_name = NULL, $css_class = NULL) {
    $output = "";
    $output .= '<div class="line-height-1 height-100 padding-12' . $css_class . '">';
      $output .= '<div class="font-size-28 margin-top-10">';
        $output .= $num;
      $output .= "</div>";

      $output .= '<div class="font-size-16 margin-top-10">';
        $output .= $tile_name;
      $output .= "</div>";
    $output .= "</div>";

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionLegendText($question_term = NULL, $meeting_nodes = array()) {
    $output = [];

    $chartData = \Drupal::service('ngdata.node.evaluation')
      ->getRaidoQuestionData($question_term, $meeting_nodes);
    $chartLegend = $this->atom->getRaidoQuestionLegend($question_term);

    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $row_text = '';
        $row_text .= isset($chartLegend[$key]) ? $chartLegend[$key] : NULL;
        $row_text .= '(' . $chartData[$key] . ')';

        $output[] = $row_text;
      }
    }

    if (\Drupal::service('ngdata.term.question')
      ->getChartLegendSortOrderValueByQuestionTerm($question_term) == 'Ascend') {
      krsort($output);
    }

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionBottom($question_term = NULL, $meeting_nodes = array()) {
    $output = '';

    $FlexpageEventLayout = new FlexpageEventLayout();

    $output .= '<div class="text-center bottom-n-1 padding-0 block-box-shadow">';
      $output .= $this->atom->getBottomHtmlCell(
        count($FlexpageEventLayout->getQuestionAnswerAllData($meeting_nodes, $question_term->id())),
        'RESPONSES'
      );
      $output .= $this->atom->getBottomHtmlCell(
        $this->atom->renderChartBottomFooterAnswerValue($question_term, $meeting_nodes),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_chartfooter')
      );
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getSelectkeyQuestionBottom($question_term = NULL, $meeting_nodes = array()) {
    $output = '';
    $output .= '<div class="text-center bottom-n-1 padding-0 block-box-shadow">';
      $output .= $this->atom->getBottomHtmlCell(
        array_sum(\Drupal::service('ngdata.term.question')
          ->getSelectkeyQuestionData($question_term, $meeting_nodes)
        ),
        'RESPONSES'
      );
      $output .= $this->atom->getBottomHtmlCell(
        $this->atom->renderChartBottomFooterAnswerValue($question_term, $meeting_nodes),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_chartfooter')
      );
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionHtmlLegend($question_term = NULL, $meeting_nodes = array()) {
    $output = \Drupal::service('flexinfo.chart.service')
      ->renderLegendSquare(
        $this->getRaidoQuestionLegendText($question_term, $meeting_nodes),
        \Drupal::service('ngdata.term.question')
          ->getRaidoQuestionColors($question_term)
      );

    return $output;
  }

  /**
   *
   */
  public function getSelectkeyQuestionLegend($question_term = NULL, $meeting_nodes = array()) {
    $chartData = array_values(\Drupal::service('ngdata.term.question')
      ->getSelectkeyQuestionData($question_term, $meeting_nodes));
    $chartLabel = $this->atom->getSelectkeyQuestionLabel($question_term);

    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . $chartData[$key] . ')';
      }
    }

    $legend_color = \Drupal::service('ngdata.term.question')
      ->getRaidoQuestionColors($question_term);
    $output = \Drupal::getContainer()->get('flexinfo.chart.service')->renderLegendSquare($legend_text, $legend_color);

    return $output;
  }

  /**
   *
   */
  public function getBlockHeaderForProgramName($program_tid = NULL) {
    $program_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($program_tid);

    $output = '';
    if ($program_term) {
      $output .= '<span class="col-md-10 color-00a9e0 font-size-20 font-weight-300">';
        $output .= $program_term->getName();
      $output .= '</span>';
    }

    return $output;
  }

  /**
   *
   */
  public function getTableTheadHtml($thead_data = array()) {
    $thead_html = NULL;
    foreach ($thead_data as $row) {
      $thead_html .= '<th>';
        $thead_html .= $row;
      $thead_html .= '</th>';
    }

    return $thead_html;
  }

  /**
   * @todo use $row['field'] as table head
   */
  public function getTableTheadHtmlByField($thead_data = array()) {
    $thead_html = NULL;
    foreach ($thead_data as $row) {
      $thead_html .= '<th>';
        $thead_html .= $row['field'];
      $thead_html .= '</th>';
    }

    return $thead_html;
  }

  /**
   *
   */
  public function getTableTbodyHtml($tbody_data = array()) {
    $tbody_html = NULL;
    foreach ($tbody_data as $row) {
      $tbody_html .= '<tr>';
      foreach ($row as $value) {
        $tbody_html .= '<td>';
          $tbody_html .= $value;
        $tbody_html .= '</td>';
      }
      $tbody_html .= '</tr>';
    }

    return $tbody_html;
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
  public function tableDataByTopProgram($meeting_nodes = array(), $limit_row = NULL) {
    $output = array();

    $program_trees = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('program', 0);
    if (is_array($program_trees)) {

      // first loop get top 10 Program
      $top_program_trees = array();
      foreach ($program_trees as $key => $term) {
        $meeting_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
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

        $meeting_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        if (count($meeting_nodes_by_current_term) > 0) {

          $signature_total = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_term, 'field_meeting_signature')
          );
          $evaluation_nums = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_term, 'field_meeting_evaluationnum')
          );

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

          $output[] = array(
            'Program' => $program_html,
            'Events' => count($meeting_nodes_by_current_term),
            'Reach' => $signature_total,
            'Responses' => $evaluation_nums,
          );
        }
      }
    }

    return $output;
  }

  /**
   * @return array
    $output[] = array(
      'Program' => $program_html,
      'BU' => 'BUBUBU',
      'Events' => count($meeting_nodes_by_current_term),
      'Reach' => $signature_total,
      'Responses' => $evaluation_nums,
    );
   */
  public function tableDataByTopProgramTemplate2($meeting_nodes = array(), $limit_row = NULL) {
    $output = array();

    $program_trees = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('program', 0);
    if (is_array($program_trees)) {

      // first loop get top 10 Program
      $top_program_trees = array();
      foreach ($program_trees as $key => $term) {
        $meeting_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
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

        $meeting_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        $bu_term = \Drupal::getContainer()
          ->get('flexinfo.term.service')
          ->getBuTermFromProgramTid($term->tid);

        if (count($meeting_nodes_by_current_term) > 0) {

          $signature_total = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_term, 'field_meeting_signature')
          );
          $evaluation_nums = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_term, 'field_meeting_evaluationnum')
          );

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

          $output[] = array(
            'Program' => $program_html,
            'BU' => $bu_term->getName(),
            'Events' => count($meeting_nodes_by_current_term),
            'Reach' => $signature_total,
            'Responses' => $evaluation_nums,
          );
        }
      }
    }

    return $output;
  }

  /**
   * @return array
    $meeting_nids = \Drupal::getContainer()
      ->get('flexinfo.querynode.service')
      ->queryNidsByBundle('meeting');
   */
  public function tableDataByTermQuestion($meeting_nodes = array(), $limit_row = NULL) {
    $output = array();

    $meeting_nodes = \Drupal::getContainer()
      ->get('flexinfo.querynode.service')
      ->nodesByBundle('meeting');

    // get all evaluation form tid array base on meeting nid
    $meeting_evaluationform_tids = [];
    foreach ($meeting_nodes as $meeting_node) {

      $meeting_evaluationform_tids[$meeting_node->id()] = array(
        'evaluation_num' => \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstValue($meeting_node, 'field_meeting_evaluationnum'),
        'form_tid' => \Drupal::getContainer()
        ->get('flexinfo.node.service')
        ->getMeetingEvaluationformTid($meeting_node)
      );
    }

    //
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('questionlibrary');

    // $start = rand(10, (count($terms) - 5));
    // $terms = array_slice($terms, $start, 5);

    if (is_array($terms)) {
      foreach ($terms as $term) {

        $evaluationform_tids_by_question = \Drupal::getContainer()
          ->get('flexinfo.queryterm.service')
          ->wrapperTermTidsByField('evaluationform', 'field_evaluationform_questionset', $term->id());

        $evaluation_result = 0;
        $answer_result = 0;
        if ($evaluationform_tids_by_question && is_array($evaluationform_tids_by_question)) {
          foreach ($evaluationform_tids_by_question as $row) {

            // get meeting node nid as multidimensional array search by value For multiple results
            $match_keys = array_keys(
              array_combine(
                array_keys($meeting_evaluationform_tids),
                array_column($meeting_evaluationform_tids, 'form_tid')
              ),
              $row
            );

            if ($match_keys && is_array($match_keys)) {
              foreach ($match_keys as $meeting_nid) {
                $evaluation_result += $meeting_evaluationform_tids[$meeting_nid]['evaluation_num'];
              }
            }
          }
        }

        // query Evaluation Answer
        $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
        $query = $query_container
          ->queryNidsByBundle('evaluation');
        $group = $query_container
          ->groupStandardByFieldValue($query, 'field_evaluation_reactset.question_tid', $term->id());
        $query->condition($group);
        $query_evaluation_nids = $query_container->runQueryWithGroup($query);
        if ($query_evaluation_nids) {
          $answer_result = count($query_evaluation_nids);
        }

        $output[] = array(
          'Name' => $term->getName(),
          'Evaluation' => $evaluation_result,
          'Answer' => $answer_result,
          'Percentage' => \Drupal::getContainer()
            ->get('flexinfo.calc.service')
            ->getPercentageDecimal($answer_result, $evaluation_result) . '%',
        );
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByTopSpeaker($meeting_nodes = array(), $limit_row = NULL, $question_tid = 134) {
    $output = array();

    $speaker_users = \Drupal::getContainer()
      ->get('flexinfo.queryuser.service')
      ->wrapperUsersByRoleName('speaker');

    if (is_array($speaker_users)) {

      // first loop get top 10 user
      $top_speaker_users = array();
      foreach ($speaker_users as $key => $user) {
        $meeting_nodes_by_current_user = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->meetingNodesBySpeakerUids($meeting_nodes, array($user->id()));

        $num_meeting_nodes = count($meeting_nodes_by_current_user);

        if ($num_meeting_nodes > 0) {
          $top_speaker_users[] = array(
            'num_meeting_nodes' => $num_meeting_nodes,
            'user' => $user,
            'nodes_by_current_user' => $meeting_nodes_by_current_user,
          );

          $sort_value[] = $num_meeting_nodes;
        }
      }

      // // cut table off to specify number
      if ($limit_row) {
        if (count($top_speaker_users) > $limit_row) {
          $top_speaker_users = array_slice($top_speaker_users, 0, $limit_row);
        }
      }

      /** - - - - - - second loop for top 10 - - - - - - - -  - - - - - - - - - - - - - - -  */
      $ModalTabSpeaker = new ModalTabSpeaker();

      foreach ($top_speaker_users as $user_array) {
        $user = $user_array['user'];

        $meeting_nodes_by_current_user = $user_array['nodes_by_current_user'];
        $num_meeting_nodes = $user_array['num_meeting_nodes'];

        if ($num_meeting_nodes > 0) {
          $signature_total = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_user, 'field_meeting_signature')
          );
          $evaluation_nums = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_user, 'field_meeting_evaluationnum')
          );

          // $speaker_name_link = $user->getDisplayName();
          // $speaker_name_link = '<span id="modal-page-anchor" data-useruid="' . $user->id() . '">';
          //   $speaker_name_link .= '<a href="#" class="">';
          //     $speaker_name_link .= $user->getDisplayName();
          //   $speaker_name_link .= '</a>';
          // $speaker_name_link .= '</span>';

          $speaker_name_link = $ModalTabSpeaker->getHtmlModalContent($user);

          $output[] = array(
            'Speaker' => $speaker_name_link,
            'Events' => $num_meeting_nodes,
            'Reach' => $signature_total,
            'Rating' => \Drupal::service('ngdata.term.question')
              ->getRaidoQuestionTidStatsAverage($question_tid, $meeting_nodes_by_current_user),
          );
        }
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByTopSpeakerTemplate2($meeting_nodes = array(), $limit_row = NULL, $question_tid = 134) {
    $output = array();

    $speaker_users = \Drupal::getContainer()
      ->get('flexinfo.queryuser.service')
      ->wrapperUsersByRoleName('speaker');

    if (is_array($speaker_users)) {

      // first loop get top 10 user
      $top_speaker_users = array();
      foreach ($speaker_users as $key => $user) {
        $meeting_nodes_by_current_user = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->meetingNodesBySpeakerUids($meeting_nodes, array($user->id()));

        $num_meeting_nodes = count($meeting_nodes_by_current_user);

        if ($num_meeting_nodes > 0) {
          $top_speaker_users[] = array(
            'num_meeting_nodes' => $num_meeting_nodes,
            'user' => $user,
            'nodes_by_current_user' => $meeting_nodes_by_current_user,
          );

          $sort_value[] = $num_meeting_nodes;
        }
      }

      // // cut table off to specify number
      if ($limit_row) {
        if (count($top_speaker_users) > $limit_row) {
          $top_speaker_users = array_slice($top_speaker_users, 0, $limit_row);
        }
      }

      /** - - - - - - second loop for top 10 - - - - - - - -  - - - - - - - - - - - - - - -  */
      $ModalTabSpeaker = new ModalTabSpeaker();

      foreach ($top_speaker_users as $user_array) {
        $user = $user_array['user'];

        $meeting_nodes_by_current_user = $user_array['nodes_by_current_user'];
        $num_meeting_nodes = $user_array['num_meeting_nodes'];

        if ($num_meeting_nodes > 0) {
          $signature_total = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_user, 'field_meeting_signature')
          );
          $evaluation_nums = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meeting_nodes_by_current_user, 'field_meeting_evaluationnum')
          );

          // $speaker_name_link = $user->getDisplayName();
          // $speaker_name_link = '<span id="modal-page-anchor" data-useruid="' . $user->id() . '">';
          //   $speaker_name_link .= '<a href="#" class="">';
          //     $speaker_name_link .= $user->getDisplayName();
          //   $speaker_name_link .= '</a>';
          // $speaker_name_link .= '</span>';

          $speaker_name_link = $ModalTabSpeaker->getHtmlModalContent($user);

          $output[] = array(
            'Speaker' => $speaker_name_link,
            'Events' => $num_meeting_nodes,
            'Reach' => $signature_total,
            'Responses' => $evaluation_nums,
            'Rating' => \Drupal::service('ngdata.term.question')
              ->getRaidoQuestionTidStatsAverage($question_tid, $meeting_nodes_by_current_user),
          );
        }
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByEventList($meeting_nodes = array(), $limit_row = NULL) {
    $output = array();

    $nodes = \Drupal::getContainer()->get('flexinfo.querynode.service')->nodesByBundle('meeting');

    foreach ($nodes as $node) {
      $program_entity = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstTargetIdTermEntity($node, 'field_meeting_program');

      $internal_url = \Drupal\Core\Url::fromUserInput('/ngpage/meeting/page/' . $node->id(), array('attributes' => array('class' => array('text-primary'))));

      $output[] = array(
        'Date' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateFormat($node, 'field_meeting_date'),
        'Program Name' => $program_entity ? $program_entity->getName() : '',
        'Province' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($node, 'field_meeting_province'),
        'Speaker' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdUserName($node, 'field_meeting_speaker'),
        'Reach' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_signature'),
        'Responses' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_evaluationnum'),
        'View' => \Drupal::l('View', $internal_url),
      );
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByEventListTemplate2($meeting_nodes = array(), $limit_row = NULL) {
    $output = array();

    $nodes = \Drupal::getContainer()->get('flexinfo.querynode.service')->nodesByBundle('meeting');

    foreach ($nodes as $node) {
      $program_entity = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstTargetIdTermEntity($node, 'field_meeting_program');

      $internal_url = \Drupal\Core\Url::fromUserInput('/ngpage/meeting/page/' . $node->id(), array('attributes' => array('class' => array('text-primary'))));

      $output[] = array(
        'Date' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateFormat($node, 'field_meeting_date'),
        'Program Name' => $program_entity ? $program_entity->getName() : '',
        'City' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($node, 'field_meeting_city'),
        'Speaker' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdUserName($node, 'field_meeting_speaker'),
        'HCP Reach' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_signature'),
        'Responses' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_evaluationnum'),
        'Status' => $this->atom->getMeetingStatusIconHtml($node),
        'View' => \Drupal::l('View', $internal_url),
      );
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByStandardnode($bundle, $start, $end) {
    $output = array();

    $nodes = \Drupal::getContainer()->get('flexinfo.querynode.service')->nodesByBundle($bundle);

    if (is_array($nodes)) {
      foreach ($nodes as $key => $node) {
        $output[] = array(
          'Name' => $node->getTitle(),
          'Edit' => \Drupal::l(t('Edit'), Url::fromUserInput("/node/" . $node->id() . "/edit")),
        );
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByStandardterm($entity_id, $start, $end) {
    $output = array();

    $program_terms = \Drupal::service('flexinfo.term.service')->getFullTermsFromVidName($entity_id);
    if (is_array($program_terms)) {
      foreach ($program_terms as $key => $term) {
        $output[] = array(
          'Name' => $term->getName(),
          'DESCRIPTION' => $term->getDescription(),
          'Edit' => \Drupal::l(t('Edit'), Url::fromUserInput("/taxonomy/term/" . $term->id() . "/edit")),
        );
      }
    }

    return $output;
  }

  /**
   * @param $header_array is array('Name', 'City', 'Edit')
   * @return array
      $output = array(
        array(
          'field' => 'Name',
          'header' => 'Name',
        ),
        array(
          'field' => 'City',
          'header' => 'City',
        ),
        array(
          'field' => 'Edit',
          'header' => 'Edit',
        ),
      );
   */
  public function tableHeaderGenerateFromArray($header_array = array()) {
    $output = array();

    foreach ($header_array as $key => $value) {
      $output[] = array(
        'field' => $value,
        'header' => $value,
      );
    }

    return $output;
  }

  /**
   *
   */
  public function tableHeaderGenerateFromTableDataArrayKeys($tableData = array()) {
    $output = array();

    if (isset($tableData[0])) {
      $array_keys = array_keys($tableData[0]);
      $output = $this->tableHeaderGenerateFromArray($array_keys);
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByCustomNodeByMeeting($entity_id, $start, $end) {
    $output = array();

    $nodes = \Drupal::getContainer()->get('flexinfo.querynode.service')->nodesByBundle('meeting');

    foreach ($nodes as $node) {
      $program_entity = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($node, 'field_meeting_program');

      $theraparea_entity = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($program_entity, 'field_program_theraparea');

      $output[] = array(
        'Name' => $program_entity ? $program_entity->getName() : '',
        'Date' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateFormat($node, 'field_meeting_date'),
        'Create' => \Drupal::service('date.formatter')->format($node->getCreatedTime(), 'html_date'),
        'BU' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($theraparea_entity, 'field_theraparea_businessunit'),
        'Province' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($node, 'field_meeting_province'),
        'City' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($node, 'field_meeting_city'),
        'Speaker' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdUserName($node, 'field_meeting_speaker'),
        'Num' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_evaluationnum'),
        // \Drupal::l('Add', Url::fromUserInput('/manageinfo/node/evaluation/add/form/' . $node->id())),
        // \Drupal::l('Summary', Url::fromUserInput('/manageinfo/summaryevaluation/form/' . $node->id())),
        'Edit' => \Drupal::getContainer()->get('flexinfo.node.service')->getNodeEditLink($node->id()),
        'Add' => \Drupal::l('Add', Url::fromUserInput('/ngpage/evaluation/form/' . $node->id())),
        'Summary' => \Drupal::l('Add', Url::fromUserInput('/ngpage/summaryevaluation/basicform/' . $node->id())),
      );
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByCustomUserForAdminSection() {
    $output = array();

    $uids = \Drupal::getContainer()
      ->get('flexinfo.queryuser.service')
      ->queryUidsByStatus(1);

    if ($uids && is_array($uids)) {
      $users = \Drupal::entityManager()->getStorage('user')->loadMultiple($uids);
      foreach ($users as $user) {

        if (in_array('administrator', $user->getRoles($exclude_locked_roles = True))) {
          continue;
        }

        $output[] = [
          'Name' => $user->getUsername(),
          'Role' => implode(", ", $user->getRoles($exclude_locked_roles = TRUE)),
          'Edit' => \Drupal::getContainer()->get('flexinfo.user.service')->getUserEditLinkByFlexform($user->id()),
        ];
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByCustomTermByProgram() {
    $output = array();

    $program_terms = \Drupal::service('flexinfo.term.service')->getFullTermsFromVidName('program');
    if (is_array($program_terms)) {
      foreach ($program_terms as $key => $term) {
        $theraparea_term = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($term, 'field_program_theraparea');

        $output[] = array(
          'Name' => $term->getName(),
          'BU' => \Drupal::getContainer()->get('flexinfo.field.service')
          ->getFieldFirstTargetIdTermName($theraparea_term, 'field_theraparea_businessunit'),
          'TA' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($theraparea_term, 'name'),
          'Edit' => \Drupal::l(t('Edit'), Url::fromUserInput("/taxonomy/term/" . $term->id() . "/edit")),
        );
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function tableDataByCustomTermByQestion() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('questionlibrary');

    $evaluation_terms = \Drupal::getContainer()->get('flexinfo.term.service')->getFullTermsFromVidName($vid = 'evaluationform');

    if (is_array($terms)) {

      foreach ($terms as $term) {

        $output[] = array(
          'NAME' => $term->getName(),
          'FieldType' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_queslibr_fieldtype'),
          'QuestionType' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_queslibr_questiontype'),
          'EDIT' => \Drupal::l(t('Edit'), Url::fromUserInput("/taxonomy/term/" . $term->id() . "/edit")),
        );
      }
    }

    return $output;
  }

  /**
   *
   */
  public function tableDataByCustomTermEvaluationForm() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('evaluationform');

    $program_terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('program');

    foreach ($terms as $term) {

      $program_num = 0;
      foreach ($program_terms as $program_term) {
        $evaluation_tids = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIds($program_term, 'field_program_evaluationform');
        if (in_array($term->id(), $evaluation_tids)) {
          $program_num ++;
        }

      }

      $output[] = array(
        'NAME' => $term->getName(),
        'DESCRIPTION' => $term->getDescription(),
        'PROGRAM NUM' => $program_num,
        'EDIT' => \Drupal::l(t('Edit'), Url::fromUserInput("/taxonomy/term/" . $term->id() . "/edit")),
      );
    }

    return $output;
  }

  /**
   *
   */
  public function tableDataByCustomTermBusinessunit() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('businessunit');

    foreach ($terms as $term) {
      $output[] = array(
        'NAME' => $term->getName(),
        'DESCRIPTION' => $term->getDescription(),
        'EDIT' => \Drupal::l(t('Edit'), Url::fromUserInput("/taxonomy/term/" . $term->id() . "/edit")),
      );
    }

    return $output;
  }

  /**
   *
   */
  public function tableDataByCustomTermTherapeuticarea() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('therapeuticarea');

    foreach ($terms as $term) {
      $output[] = array(
        'NAME' => $term->getName(),
        'DESCRIPTION' => $term->getDescription(),
        'BU' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_theraparea_businessunit'),
        'EDIT' => \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      );
    }

    return $output;
  }

  /**
   *
   */
  public function tableDataByCustomTermQuestionEvaluationForm() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('questionlibrary');

    $evaluation_terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName($vid = 'evaluationform');

    foreach ($terms as $term) {
      $evaluationform_num = 0;
      $evaluationform_terms = array();
      foreach ($evaluation_terms as $key => $evaluation_term) {
        $question_tids = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldAllTargetIds($evaluation_term, 'field_evaluationform_questionset');

        if (in_array($term->id(), $question_tids)) {
          $evaluationform_num++;
          $evaluationform_terms[] = $evaluation_term;

        }
      }

      $evaluationform_counter = 0;
      $evaluationform_data = '';
      foreach ($evaluationform_terms as $question_evaluationform_term) {
        $evaluationform_counter ++;
        $question_evaluationform_term_name = str_replace("'", '`', $question_evaluationform_term->getName());
        $question_evaluationform_term_name = str_replace('"', '`', $question_evaluationform_term_name);
        $evaluationform_data .= ' <ol> ' . ' ( ' . $evaluationform_counter . ' ) ' . '<a href= ' . base_path() . 'taxonomy/term/' . $question_evaluationform_term->id() . '/edit' . '>' . $question_evaluationform_term->getName() . ' </a></ol>';
      }

      $output[] = array(
        'NAME' => $term->getName(),
        'EvalNum' => $this->tablePopUpTemplate($evaluationform_num, $evaluationform_data, $term->id()),
        'EDIT' => \Drupal::l(t('Edit'), Url::fromUserInput("/taxonomy/term/" . $term->id() . "/edit")),
      );
    }

    return $output;
  }

  /**
   *
   */
  public function tablePopUpTemplate($evaluation_num, $popup_body, $popup_id) {
    $output = '<div data-toggle="modal" data-target="#modalContent' . $popup_id . '">';
      $output .= $evaluation_num;
    $output .= '</div>';

    $output .= '<div class="modal fade" id="modalContent' . $popup_id . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
      $output .= '<div class="modal-dialog" role="document">';
        $output .= '<div class="modal-content">';
          $output .= '<div class="modal-body margin-50">';
            $output .= $popup_body;
          $output .= '</div>';
          $output .= '<div class="modal-footer margin-auto">';
            $output .= '<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}
