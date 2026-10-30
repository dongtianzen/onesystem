<?php

/**
 * @file
 * Contains \Drupal\dashtable\Content\DashtableObjectContent.
 */

namespace Drupal\dashtable\Content;

use Drupal\Core\Url;
use Drupal\Component\Utility\Unicode;

use Drupal\flexpage\Content\FlexpageBaseJson;
use Drupal\flexpage\Content\FlexpageJsonGenerator;
use Drupal\flexpage\Content\FlexpageSampleDataGenerator;

use Drupal\dashpage\Content\DashpageObjectContent;
use Drupal\dashpage\Content\DashTabSpeaker;

use Drupal\ngjson\Content\NgjsonObjectContent;
use Drupal\ngjson\Content\EventStandardLayoutContent;

/**
 *
 */
class DashtableGridData {

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
  public function basicTermData($vid = NULL) {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName($vid);

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        '<a href=' . base_path() . 'taxonomy/term/' . $term->id() . '/edit' . '>Edit</a>',
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "EDIT"
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  public function termProvince() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "ABB",
          "EDIT"
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  public function termQuestionlibrary() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('questionlibrary');

    $evaluation_terms = \Drupal::getContainer()->get('flexinfo.term.service')->getFullTermsFromVidName($vid = 'evaluationform');

    $tbody = [];
    foreach ($terms as $term) {

      $evaluationform_num = 0;
      $evaluationform_terms = array();
      foreach ($evaluation_terms as $key => $evaluation_term) {
        $question_tids = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIds($evaluation_term, 'field_evaluationform_questionset');

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
      $completed_evaluationform_data = $this->tablePopUpTemplate($evaluationform_data);

      $tbody[] = [
        $term->getName(),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_queslibr_fieldtype'),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_queslibr_questiontype'),
        '<a href="" ng-click="tablePopUp(\'' . $completed_evaluationform_data . '\')">' . $evaluationform_num . '</a>',
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "FieldType",
          "QuestionType",
          "EvalNum",
          "EDIT",
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  public function tablePopUpTemplate($popup_body) {
    $data_popup = "<div class=\'col-xs-12 team-card-header\'>";
      $data_popup .= "";
    $data_popup .= "</div>";
    $data_popup .= "<div class=\'col-xs-12 team-card-content\'>";
      $data_popup .= $popup_body;
    $data_popup .= "</div>";

    return $data_popup;
  }

  /**
   *
   */
  public function termBusinessunit() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('businessunit');

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_businessunit_division'),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "DV",
          "EDIT"
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  public function termDiseasestate() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('diseasestate');

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_disease_theraparea'),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "TA",
          "EDIT"
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  public function termProgram() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('program');

    foreach ($terms as $term) {
      $theraparea_term = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($term, 'field_program_theraparea');

      $tbody[] = [
        $term->getName(),
        \Drupal::getContainer()->get('flexinfo.field.service')
          ->getFieldFirstTargetIdTermName($theraparea_term, 'field_theraparea_businessunit'),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($theraparea_term, 'name'),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "BU",
          "TA",
          "EDIT"
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  public function termEvaluationForm() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('evaluationform');

    $program_terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('program');

    $tbody = [];
    foreach ($terms as $term) {
      $program_num = 0;
      foreach ($program_terms as $program_term) {
        $evaluation_tids = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIds($program_term, 'field_program_evaluationform');
        if (in_array($term->id(), $evaluation_tids)) {
          $program_num ++;
        }

      }

      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        $program_num,
        '<a href=' . base_path() . 'taxonomy/term/' . $term->id() . '/edit' . '>Edit</a>',
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "PROGRAM NUM",
          "EDIT"
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  public function termTherapeuticarea() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('therapeuticarea');

    foreach ($terms as $term) {
      $tbody[] = [
        $term->getName(),
        $term->getDescription(),
        \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($term, 'field_theraparea_businessunit'),
        \Drupal::getContainer()->get('flexinfo.term.service')->getTermEditLink($term->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "NAME",
          "DESCRIPTION",
          "BU",
          "EDIT"
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   * @deprecated
   */
  public function dataMeeting() {
    return [];
  }

  /**
   *
   */
  public function dataUser() {
    $users = \Drupal::getContainer()
      ->get('flexinfo.queryuser.service')
      ->wrapperUsersByRoleNames(array('client', 'speaker', 'siteadmin'));

    foreach ($users as $user) {
      $tbody[] = [
        $user->getUsername(),
        implode(", ", $user->getRoles($exclude_locked_roles = TRUE)),
        \Drupal::getContainer()->get('flexinfo.user.service')->getUserEditLinkByFlexform($user->id()),
      ];
    }

    $output = array(
      "thead" => [
        [
          "Name",
          "Role",
          "Edit",
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   * @return array
   */
  public function tableSpeakerList($meeting_nodes = array(), $limit_row = NULL, $sort_boolean = FALSE) {
    $output = array();

    $speaker_users = \Drupal::getContainer()->get('flexinfo.queryuser.service')->wrapperUsersByRoleName('speaker');

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

      // sort by set condition
      if (count($top_speaker_users) > 0 && $sort_boolean) {
        array_multisort($sort_value, SORT_DESC, $top_speaker_users);
      }

      // // cut table off to specify number
      // if ($limit_row) {
      //   if (count($top_speaker_users) > $limit_row) {
      //     $top_speaker_users = array_slice($top_speaker_users, 0, $limit_row);
      //   }
      // }

      /** - - - - - - second loop for top 10 - - - - - - - -  - - - - - - - - - - - - - - -  */
      $DashTabSpeaker = new DashTabSpeaker();

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

          $speaker_name_link =  $DashTabSpeaker->getHtmlModalContent($user);

          $output[] = array(
            'Name' => $speaker_name_link,
            'Events' => $num_meeting_nodes,
            'Reach' => $signature_total,
            'Responses' => $evaluation_nums,
            // 'Rating' => NULL,
            'Rating' => \Drupal::service('ngdata.term.question')
              ->getRaidoQuestionTidStatsAverage(134, $meeting_nodes_by_current_user),
            // 'Rating' => \Drupal::getContainer()->get('flexinfo.calc.service')->arrayAverageByMeetingNodes($meeting_nodes_by_current_user, 3006),
          );
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function dataSpeakerList($meeting_nodes = array()) {

    // for ($i = 0; $i < 20; $i++) {
    //   $tbody[] = [
    //     "Speaker",
    //     rand(3, 20),
    //     rand(3, 20),
    //     rand(3, 20),
    //     rand(3, 20),
    //   ];
    // }

    $tbody = $this->tableSpeakerList($meeting_nodes);

    $output = array(
      "thead" => [
        [
          "Speaker",
          "# Events",
          "HCP Reach",
          "Responses",
          "Rating",
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   * @return array
   */
  public function tableViewEvents($meeting_nodes = array()) {
    $output = array();

    if (is_array($meeting_nodes)) {
      foreach ($meeting_nodes as $node) {
        $program_entity = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermEntity($node, 'field_meeting_program');

        if ($program_entity) {
          $internal_url = Url::fromUserInput('/ngpage/meeting/page/' . $node->id());
          $view_button = \Drupal::l(t('View'), $internal_url);

          $date_text  = '<span class="width-90 float-left">';
            $date_text .= \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueDateFormat($node, 'field_meeting_date');
          $date_text .= '</span>';

          $program_short_name = Unicode::truncate($program_entity->getName(), 36, $wordsafe = TRUE, $add_ellipsis = TRUE);
          $program_html = '<div class="html-tooltip-wrapper">';
            $program_html .= '<span class="html-tooltip-text-wrapper">';
              $program_html .= $program_short_name;
            $program_html .= '</span>';
            $program_html .= '<span class="html-tooltip-hover-wrapper visibility-hidden color-000 min-width-120 bg-c6c6c6 text-align-center border-radius-6 padding-5 position-absolute z-index-1">';
              $program_html .= $program_entity->getName();
            $program_html .= '</span>';
          $program_html .= '</div>';

          $status_button = '<span class="color-fff tooltip">';
            $status_button .= '<i class=" fa ' . \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingStatusIcon($node) . ' fa-lg color-';
              $status_button .= \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingStatusColorCode($node);
              $status_button .= '" aria-hidden="true">';
            $status_button .= '</i>';
            $status_button .= '<span class="tooltiptext">' . \Drupal::getContainer()->get('flexinfo.node.service')->getMeetingStatus($node) . '</span>';
          $status_button .= '</span>';

          $output[] = array(
            'VIEW' => $view_button,
            'DATE' => $date_text,
            'PROGRAM' => $program_html,
            'Program ID#' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_programid'),
            "Created" => \Drupal::service('date.formatter')->format($node->getCreatedTime(), 'html_date'),
            'City' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($node, 'field_meeting_city'),
            'SPEAKER' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdUserName($node, 'field_meeting_speaker'),
            'HCP Reach' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_signature'),
            'Responses' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($node, 'field_meeting_evaluationnum'),
            'STATUS' => $status_button,
          );
        }
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  // public function tableHotelList($meeting_nodes = array()) {
  //   $NgjsonObjectContent = new NgjsonObjectContent();
  //   $output = $NgjsonObjectContent->tableDataByEventByHotel($meeting_nodes);

  //   return $output;
  // }

  /**
   *
   */
  public function dataViewEvents($meeting_nodes = array()) {
    // for ($i = 0; $i < 20; $i++) {
    //   $tbody[] = [
    //     '10/31/2017',
    //     'Program Name',
    //     "Rep Name",
    //     "Speaker",
    //     rand(3, 20),
    //     rand(3, 20),
    //     'Status',
    //     'View'
    //   ];
    // }
    $tbody = $this->tableViewEvents($meeting_nodes);
    //event list tabel
    $output = array(
      "thead" => [
        [
          "View",
          "Date",
          "Program Name",
          "Program ID#",
          "Created",
          "City",
          "Speaker",
          "HCP Reach",
          "Responses",
          "Status",
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  // public function dataHotelList($meeting_nodes = array()) {
  //   $tbody = $this->tableHotelList($meeting_nodes);
  //   //event list tabel
  //   $output = array(
  //     "thead" => [
  //       [
  //         "Hotel",
  //         "Country",
  //         "Events",
  //         "Rating",
  //       ]
  //     ],
  //     "tbody" => $tbody
  //   );

  //   return $output;
  // }

  /**
   * @return array
   */
  public function tableViewPrograms($meeting_nodes = array(), $limit_row = NULL, $sort_boolean = FALSE) {
    $output = array();

    $program_trees = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('program', 0);
    if (is_array($program_trees)) {

      // first loop get top 10 Program
      $top_program_trees = array();
      foreach ($program_trees as $key => $term) {
        $meetings_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        if (count($meetings_nodes_by_current_term) > 0) {
          $num_meetings_nodes = count($meetings_nodes_by_current_term);

          $top_program_trees[] = array(
            'term' => $term,
            'num_meetings_nodes' => $num_meetings_nodes,
          );

          // for sort order condition criteria
          $sort_value[] = $num_meetings_nodes;
        }
      }

      // sort by set condition
      if (count($top_program_trees) > 0 && $sort_boolean) {
        array_multisort($sort_value, SORT_DESC, $top_program_trees);
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

        $program_entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term->tid);

        $meetings_nodes_by_current_term = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($term->tid), 'IN');

        if (count($meetings_nodes_by_current_term) > 0) {

          $signature_total = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meetings_nodes_by_current_term, 'field_meeting_signature')
          );
          $evaluation_nums = array_sum(
            \Drupal::getContainer()->get('flexinfo.field.service')
            ->getFieldFirstValueCollection($meetings_nodes_by_current_term, 'field_meeting_evaluationnum')
          );

          $internal_url = Url::fromUserInput('/ngpage/program/page/' . $term->tid);

          $output[] = array(
            'PROGRAM' => \Drupal::l($term->name, $internal_url),
            'THERAPEUTIC AREA' => \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($program_entity, 'field_program_theraparea'),
            'EVENTS' => count($meetings_nodes_by_current_term),
            'REACH' => $signature_total,
            'EVALUATIONS' => $evaluation_nums,
          );
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function dataViewPrograms($meeting_nodes = array()) {
    /**
     * tbody example
     */
    // foreach ($terms as $term) {
    //   $tbody[] = [
    //     'Program Name',
    //     'BU',
    //     rand(3, 20),
    //     rand(3, 20),
    //     rand(3, 20),
    //     'View'
    //   ];
    // }
    $tbody = $this->tableViewPrograms($meeting_nodes);

    $output = array(
      "thead" => [
        [
          "Program Name",
          "BU",
          "# Events",
          "HCP Reach",
          "Responses",
        ]
      ],
      "tbody" => $tbody
    );

    return $output;
  }

  /**
   *
   */
  public function htmlSnippetSpeakerlist($tableData = NULL, $topValue = NULL, $col_class = 'col-md-6') {
    $speaker_users = \Drupal::getContainer()->get('flexinfo.queryuser.service')->wrapperUsersByRoleName('speaker');

    $speaker_icon = "";
    $speaker_icon .= '<div class="row bg-ffffff margin-left-0">';
      $speaker_icon .= '<div class="col-xs-2 font-size-40">';
        $speaker_icon .= '<i class="fa fa-user-circle fa-lg font-size-40 color-d6006e margin-left-20" aria-hidden="true"></i>';
      $speaker_icon .= '</div>';
      $speaker_icon .= '<div class="col-xs-4 color-009ddf padding-top-12 ">';
        $speaker_icon .= '<div class="font-size-18">';
          $speaker_icon .= count($speaker_users);
        $speaker_icon .= '</div>';
        $speaker_icon .= '<div class="active-speaker-name">Active Speakers</div>';
      $speaker_icon .= '</div>';

      $speaker_icon .= '<div class="col-xs-2 font-size-40 border-left-10-f0f3f4">';
        $speaker_icon .= '<i class="fa fa-bar-chart fa-lg font-size-40 color-7dba00 margin-left-20" aria-hidden="true"></i>';
      $speaker_icon .= '</div>';
      $speaker_icon .= '<div class="col-xs-4 color-009ddf padding-top-12">';
        $speaker_icon .= '<div class="font-size-18">';
          $speaker_icon .= \Drupal::service('ngdata.term.question')->getRaidoQuestionTidStatsAverage(134, $meeting_nodes);
        $speaker_icon .= '</div>';
        $speaker_icon .= '<div class="speaker-averge-rating">Average Rating</div>';
      $speaker_icon .= '</div>';
    $speaker_icon .= '</div>';

    // block option
    $option['top'] = array(
      'enable' => FALSE,
    );
    $option['class'] = 'speakerlist-table-header-block-wrapper col-md-6 col-sm-10';

    $output = $this->FlexpageBaseJson->getBlockHtmlSnippet(
      $option,
      $speaker_icon,
      'col-md-6'
    );
    return $output;
  }

}

/**
 *
 */
class DashtableFeatureContent extends DashtableGridData {

  /**
   * @return Array data
   */
  public function generateCommonTable($table_data = array(), $title = NULL) {
    $output = $this->FlexpageBaseJson->getBlockOne(
      array(
        'class' => "col-md-12",
        'type' => "commonTable",
        'blockClasses' => "height-400 overflow-visible",
      ),
      $this->FlexpageBaseJson->getCommonTable(NUll, $table_data)
    );

    if ($title) {
      $output["top"] = [
        "enable"=> true,
        "value"=> $title
      ];
    }

    return $output;
  }

  /**
   *
   */
  public function generateTermAddLinkHtmlSnippet($vid = NULL) {
    // default one
    // $url = Url::fromUserInput('/admin/structure/taxonomy/manage/' . $vid . '/add');
    $url = Url::fromUserInput('/flexform/entityadd/taxonomy_term/' . $vid);

    $output = $this->generateEntityAddLinkHtmlSnippet($url);
    return $output;
  }

  /**
   *
   */
  public function generateNodeAddLinkHtmlSnippet($bundle = NULL) {
    // default one
    // $url = Url::fromUserInput('/node/add/' . $bundle);
    $url = Url::fromUserInput('/flexform/entityadd/node/' . $bundle);

    $output = $this->generateEntityAddLinkHtmlSnippet($url);
    return $output;
  }

  /**
   *
   */
  public function generateUserAddLinkHtmlSnippet($bundle = NULL) {
    $url = Url::fromUserInput('/admin/people/create');
    $url = Url::fromUserInput('/flexform/entityadd/user/user');

    $output = $this->generateEntityAddLinkHtmlSnippet($url);
    return $output;
  }

  /**
   *
   */
  public function generateUserEditLinkHtmlSnippet($bundle = NULL) {
    $url = Url::fromUserInput('/flexform/entityadd/user/user');

    $output = $this->generateEntityAddLinkHtmlSnippet($url);
    return $output;
  }

  /**
   *
   */
  public function generateEntityAddLinkHtmlSnippet($url = NULL) {
    $output = '';
    $output .= '<div class="entity-add-link-wrapper">';
      $output .= '<div class="btn-group">';
        $output .= '<a href="' . $url->toString() . '" class="btn btn-block btn-info active">';
          $output .= '<i class="glyphicon glyphicon-plus-sign" aria-hidden="true"></i>';
          $output .= '<span class="margin-left-6">';
            $output .= 'Create';
          $output .= '</span>';
        $output .= '</a>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}

/**
 * @return php object, not JSON
 */
class DashtableObjectContent extends DashtableFeatureContent {

  /**
   * {@inheritdoc}
   */
  public function standardSnapshotObjectContent($section, $entity_id = NULL, $start = NULL, $end = NULL) {
    $content_method = $section . 'SnapshotObjectContent';
    if (method_exists($this, $content_method)) {
      $output = $this->{$content_method}($section, $entity_id, $start, $end);

      // insert create link
      // array_unshift($output['contentSection'], $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      //   array(),
      //   $this->generateTermAddLinkHtmlSnippet($section)
      // ));
    }
    else {
      $output = $this->standardTermSnapshotObjectContent($section, $entity_id, $start, $end);
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function standardTermSnapshotObjectContent($vid = NULL, $entity_id = NULL) {
    $table_data = $this->basicTermData($vid);

    $output['contentSection'][] = $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      array(),
      $this->generateTermAddLinkHtmlSnippet($vid)
    );
    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function meetingSnapshotObjectContent() {
    $output['contentSection'][] = $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      array(),
      $this->generateNodeAddLinkHtmlSnippet('meeting')
    );

    $table_data = $this->dataMeeting();
    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function userSnapshotObjectContent() {
    $output['contentSection'][] = $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      array(),
      $this->generateUserAddLinkHtmlSnippet()
    );

    $table_data = $this->dataUser();
    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /** - - - - - - term - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * {@inheritdoc}
   */
  public function commonVidSnapshotObjectContent($section, $entity_id, $table_data) {
    $output['contentSection'][] = $this->FlexpageBaseJson->getBlockHtmlSnippetWithoutTop(
      array(),
      $this->generateTermAddLinkHtmlSnippet($section)
    );

    $output['contentSection'][] = $this->generateCommonTable($table_data);
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function businessunitSnapshotObjectContent($section, $entity_id) {
    $table_data = $this->termBusinessunit();
    $output = $this->commonVidSnapshotObjectContent($section, $entity_id, $table_data);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function diseasestateSnapshotObjectContent($section, $entity_id) {
    $table_data = $this->termDiseasestate();
    $output = $this->commonVidSnapshotObjectContent($section, $entity_id, $table_data);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function programSnapshotObjectContent($section, $entity_id) {
    $table_data = $this->termProgram();
    $output = $this->commonVidSnapshotObjectContent($section, $entity_id, $table_data);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function evaluationformSnapshotObjectContent($section, $entity_id) {
    $table_data = $this->termEvaluationForm();
    $output = $this->commonVidSnapshotObjectContent($section, $entity_id, $table_data);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function provinceSnapshotObjectContent($section, $entity_id) {
    $table_data = $this->termProvince();
    $output = $this->commonVidSnapshotObjectContent($section, $entity_id, $table_data);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function questionlibrarySnapshotObjectContent($section, $entity_id) {
    $table_data = $this->termQuestionlibrary();
    $output = $this->commonVidSnapshotObjectContent($section, $entity_id, $table_data);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function therapeuticareaSnapshotObjectContent($section, $entity_id) {
    $table_data = $this->termTherapeuticarea();
    $output = $this->commonVidSnapshotObjectContent($section, $entity_id, $table_data);

    return $output;
  }

  /** - - - - - - table - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * {@inheritdoc}
   */
  public function viewprogramsSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $DashpageObjectContent = new DashpageObjectContent();
    $NgjsonObjectContent = new NgjsonObjectContent();
    $meeting_nodes = $NgjsonObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output['fixedSection'] = $this->FlexpageBaseJson->generateTileStyleOne($DashpageObjectContent->pageTopFixedSectionData($meeting_nodes));

    $table_data = $this->dataViewPrograms($meeting_nodes);
    $output['contentSection'][] = $this->generateCommonTable($table_data, 'View Programs');
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function vieweventsSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $DashpageObjectContent = new DashpageObjectContent();
    $NgjsonObjectContent = new NgjsonObjectContent();
    $meeting_nodes = $NgjsonObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output['fixedSection'] = $this->FlexpageBaseJson->generateTileStyleOne($DashpageObjectContent->pageTopFixedSectionData($meeting_nodes));

    $table_data = $this->dataViewEvents($meeting_nodes);
    $output['contentSection'][] = $this->generateCommonTable($table_data, 'View Program Series');
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  // public function hotelListSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
  //   $DashpageObjectContent = new DashpageObjectContent();
  //   $NgjsonObjectContent = new NgjsonObjectContent();
  //   $meeting_nodes = $NgjsonObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

  //   $output['fixedSection'] = $this->FlexpageBaseJson->generateTileStyleOne($DashpageObjectContent->pageTopFixedSectionData($meeting_nodes));

  //   $table_data = $this->dataHotelList($meeting_nodes);
  //   $output['contentSection'][] = $this->generateCommonTable($table_data, 'Hotel List');
  //   return $output;
  // }

  /**
   * {@inheritdoc}
   */
  public function speakerlistSnapshotObjectContent($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $NgjsonObjectContent = new NgjsonObjectContent();
    $meeting_nodes = $NgjsonObjectContent->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    // $output['contentSection'][] = $this->htmlSnippetSpeakerlist();

    $table_data = $this->dataSpeakerList($meeting_nodes);
    $output['contentSection'][] = $this->generateCommonTable($table_data, 'Speaker Summary');
    return $output;
  }

}
