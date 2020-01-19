<?php

/**
 * @file
 * Contains \Drupal\modalpage\Content\ModalTabSpeaker.
 */

namespace Drupal\modalpage\Content;

/**
 *
 */
class ModalTabSpeaker {

  // rating question id or NULL
  public $question_tid;

  /**
   * Constructs a new object.
   * specify the rating question id or NULL
   */
  public function __construct() {
    $this->question_tid = NULL;
  }

  /**
   *
   */
  public function getDemoModalContentBasic($user_uid = NULL) {
    $output = '';
    $output .= '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">';
      $output .= 'Launch demo modal';
    $output .= '</button>';

    // <!-- Modal -->';
    $output .= '<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
      $output .= '<div class="modal-dialog" role="document">';
        $output .= '<div class="modal-content">';
          $output .= '<div class="modal-header">';
            $output .= '<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>';
            $output .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              $output .= '<span aria-hidden="true">&times;</span>';
            $output .= '</button>';
          $output .= '</div>';
          $output .= '<div class="modal-body">';
            $output .= 'body content';
          $output .= '</div>';
          $output .= '<div class="modal-footer">';
            $output .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
            $output .= '<button type="button" class="btn btn-primary">Save changes</button>';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getDemoModalContent($user_uid = NULL) {
    $output = '';
    $output .= '<h6>Modal Example</h6>';

    $output .= $this->getDemoModalButton($user_uid);
    $output .= $this->getDemoModalPage($user_uid);

    return $output;
  }

  /**
   *
   */
  public function getDemoModalButton($user_uid = NULL) {
    $output = '';
    // $output .= '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalPageAnchor">';
    $output .= '<button type="button" id="modalPageAnchor" class="btn btn-primary" data-useruid="' . $user_uid . '">';
      $output .= 'Open Modal';
    $output .= '</button>';

    return $output;
  }

  /**
   *
   */
  public function getDemoModalPage($user_uid = NULL) {
    $output = '';
    $output .= '<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
      $output .= '<div class="modal-dialog" role="document">';
        $output .= '<div class="modal-content">';
          $output .= '<div class="modal-header">';
            $output .= '<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>';
            $output .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              $output .= '<span aria-hidden="true">&times;</span>';
            $output .= '</button>';
          $output .= '</div>';
          $output .= '<div class="modal-body">';
            $output .= 'body content - Uid - ' . $user_uid;
          $output .= '</div>';

          $output .= '<div class="modal-footer">';
            $output .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
            $output .= '<button type="button" class="btn btn-primary">Save changes</button>';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getHtmlModalContent($user = NULL) {
    $output = '';
    $output .= $this->getHtmlModalTextLink($user);
    $output .= $this->getHtmlModalPage($user);

    return $output;
  }

  /**
   *
   */
  public function getHtmlModalTextLink($user = NULL) {
    $output = '';
    $output = '<span id="modalPageAnchor" class="text-primary speaker-modal-link" data-useruid="' . $user->id() . '">';
      $output .= '<a>';
        $output .= $user->getDisplayName();
      $output .= '</a>';
    $output .= '</span>';

    return $output;
  }

  /**
   *
   */
  public function getHtmlModalPageSample($user = NULL) {
    $output = '';
    $output .= '<div class="modal fade speaker-modal-sample-wrapper" id="speakerModalTab" tabindex="-1" role="dialog" aria-labelledby="speakerModalTabLabel" aria-hidden="true">';
      $output .= '<div class="modal-dialog" role="document">';
        $output .= '<div class="modal-content">';
          $output .= '<div class="modal-header">';
            $output .= '<h5 class="modal-title" id="speakerModalTabLabel">Modal title</h5>';
            $output .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              $output .= '<span aria-hidden="true">&times;</span>';
            $output .= '</button>';
          $output .= '</div>';
          $output .= '<div class="modal-body">';
            $output .= 'body content - Uid - ' . $user->id();
          $output .= '</div>';

          $output .= '<div class="modal-footer">';
            $output .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
            $output .= '<button type="button" class="btn btn-primary">Save changes</button>';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getHtmlModalPage($user = NULL) {
    $output = '';
    $output .= '<div class="modal fade speaker-modal-sample-wrapper html-modal-speaker-' . $user->id() . '" id="speakerModalTab" tabindex="-1" role="dialog" aria-labelledby="speakerModalTabLabel" aria-hidden="true">';
      $output .= '<div class="modal-dialog" role="document">';
        $output .= '<div class="modal-content">';

          $output .= '<div class="modal-header">';
            $output .= '<div class="row bg-673ab7 margin-top-n-24 padding-top-20">';
              $output .= '<h4 class="modal-title color-fff text-align-center modal-title-speaker-name">';
                $output .= $user->getDisplayName();
              $output .= '</h4>';
            $output .= '</div>';
            $output .= $this->getModalContentHeader($user);
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
  public function getModalContentHeader($user) {
    $start_timestamp_ytd = strtotime(date('Y', time()) . "-01-01");
    $start_timestamp_all = 1483246800;

    $end_timestamp = strtotime("now");

    $output = '';
    $output .= '<div class="row bg-673ab7">';
      $output .= '<ul class="nav nav-tabs">';
        $output .= '<li class="color-fff">';
          $output .= '<a class="color-fff" data-toggle="tab" href="#tab-speaker-header-ytd-' . $user->id() . '">YTD</a>';
        $output .= '</li>';
        $output .= '<li class="color-fff">';
          $output .= '<a class="color-fff" data-toggle="tab" href="#tab-speaker-header-all-' . $user->id() . '">ALL TIME</a>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</div>';

    $output .= '<div class="tab-content margin-top-n-4 width-570">';
      $output .= '<div id="tab-speaker-header-ytd-' . $user->id() . '" class="tab-pane fade in active tab-header-html-content-ytd">';
        // when home page load, do not load user value
        // $output .= $this->getTabHeaderText($user, $start_timestamp_ytd, $end_timestamp, $tab_key = 'ytd');
      $output .= '</div>';

      $output .= '<div id="tab-speaker-header-all-' . $user->id() . '" class="tab-pane fade tab-header-html-content-all">';
        // $output .= $this->getTabHeaderText($user, $start_timestamp_all, $end_timestamp, $tab_key = 'all');
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getTabHeaderText($user = NULL, $start_timestamp = NULL, $end_timestamp = NULL, $tab_key = 'ytd') {
    $output = '';

    if ($user) {
      $tabVaule = $this->getTabHeaderValueByStartEndTime($user, $start_timestamp, $end_timestamp);

      $output .= '<div class="row margin-top-40 margin-bottom-20">';
        // $output .= '<div class="col-md-custom-20-p">';
        $output .= '<div class="col-md-3">';
          $output .= '<div class="text-align-center">';
            $output .= '<p class="font-bold">' . $tabVaule[0]["tabNumber"] . '</p>';
            $output .= '<p class="font-size-10 color-818384">' . $tabVaule[0]["tabContent"] . '</p>';
          $output .= '</div>';
        $output .= '</div>';
        for ($i = 1; $i < count($tabVaule); $i++) {
          $output .= '<div class="col-md-3">';
            $output .= '<div class="text-align-center border-left-2-e2e2e2">';
              $output .= '<p class="font-bold tab-header-text">' . $tabVaule[$i]["tabNumber"] . '</p>';
              $output .= '<p class="font-size-10 color-818384">' . $tabVaule[$i]["tabContent"] . '</p>';
            $output .= '</div>';
          $output .= '</div>';
        }

        $output .= '<div class="row marin-0 margin-top-72 margin-right-15 margin-bottom-20 margin-left-15 width-570">';
          $output .= $this->getModalContentBody($user, $start_timestamp, $end_timestamp, $tab_key);
        $output .= '</div>';
      $output .= '</div>';
    }

    return $output;
  }

  /**
   *
   */
  public function getTabHeaderValueEmpty() {
    $output = array(
      array(
        "tabNumber" => 0,
        "tabContent" => 'EVENTS',
      ),
      array(
        "tabNumber" => 0,
        "tabContent" => 'HCP REACH',
      ),
      array(
        "tabNumber" => 0,
        "tabContent" => 'RESPONSES',
      ),
      array(
        "tabNumber" => 'N/A',
        "tabContent" => 'RATING',
      ),
      array(
        "tabNumber" => 0,
        "tabContent" => 'HONORARIUM',
      )
    );

    return $output;
  }

  /**
   *
   */
  public function getTabHeaderValueByStartEndTime($user = NULL, $start_timestamp = NULL, $end_timestamp = NULL) {
    $start_query_date = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToQueryDate($start_timestamp, $type = 'html_date');
    $end_query_date = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToQueryDate($end_timestamp, $type = 'html_date');

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('meeting');

    $group = $query_container->groupByMeetingDateTime($query, $start_query_date, $end_query_date);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_speaker', $user->id());
    $query->condition($group);

    $meeting_nids = $query_container->runQueryWithGroup($query);
    $meeting_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($meeting_nids);
    $meeting_nodes_by_current_user = \Drupal::getContainer()->get('flexinfo.querynode.service')->meetingNodesBySpeakerUids($meeting_nodes, array($user->id()));

    $signature_total = array_sum(
      \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_signature')
    );
    $evaluation_nums = array_sum(
      \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_evaluationnum')
    );

    $rating_score = 0;
    if ($this->question_tid) {
      $rating_score = \Drupal::service('ngdata.term.question')
        ->getRaidoQuestionTidStatsAverage($this->question_tid, $meeting_nodes_by_current_user);
    }

    $output = array(
      array(
        "tabNumber" => count($meeting_nodes),
        "tabContent" => 'EVENTS',
      ),
      array(
        "tabNumber" => $signature_total,
        "tabContent" => 'HCP REACH',
      ),
      array(
        "tabNumber" => $evaluation_nums,
        "tabContent" => 'RESPONSES',
      ),
      array(
        "tabNumber" => $rating_score,
        "tabContent" => 'RATING',
      )
    );

    return $output;
  }

  /**
   * @param $tab_key = 'ytd' or 'all';
   */
  public function getModalContentBody($user = NULL, $start_timestamp = NULL, $end_timestamp = NULL, $tab_key = 'ytd') {
    $output = '';
    $output .= '<ul class="nav nav-tabs row margin-top-n-16 padding-top-12 bg-673ab7">';
      $output .= '<li class="color-fff">';
        $output .= '<a class="color-fff" data-toggle="tab" href="#tab-speaker-body-program-' . $tab_key . '">PROGRAMS</a>';
      $output .= '</li>';
      $output .= '<li class="color-fff">';
        $output .= '<a class="color-fff" data-toggle="tab" href="#tab-speaker-body-city-' . $tab_key . '">LOCATIONS</a>';
      $output .= '</li>';
    $output .= '</ul>';

    $output .= '<div class="tab-content">';
      $output .= '<div id="tab-speaker-body-program-' . $tab_key . '" class="tab-pane fade in active tab-body-html-content-program">';
        $output .= $this->getTableContentBodyProgram($user, $start_timestamp, $end_timestamp);
      $output .= '</div>';

      $output .= '<div id="tab-speaker-body-city-' . $tab_key . '" class="tab-pane fade tab-body-html-content-location">';
        $output .= $this->getTableContentBodyLocation($user, $start_timestamp, $end_timestamp);
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getTableContentBodyProgram($user = NULL, $start_timestamp = NULL, $end_timestamp = NULL) {
    $tabProgramValue  = $this->getTabProgramValueEmpty();
    if ($user) {
      $tabProgramValue  =$this->getTabProgramValueByStartEndTime($user, $start_timestamp, $end_timestamp);;
    }

    $output = $this->getTableContent($tabProgramValue);

    return $output;
  }

  /**
   *
   */
  public function getTableContentBodyLocation($user = NULL, $start_timestamp = NULL, $end_timestamp = NULL) {
    $tabLocationValue = $this->getTabLocationValueEmpty();
    if ($user) {
      $tabLocationValue = $this->getTabLocationValueByStartEndTime($user, $start_timestamp, $end_timestamp);
    }

    $output = $this->getTableContent($tabLocationValue);

    return $output;
  }

  /**
   *
   */
  public function getTableContent($tabVaule) {
    $output = '';
    $output .= '<div class="row margin-0 padding-top-20 padding-bottom-20">';
      $output .= '<table class="table">';
        $output .= '<thead>';
          $output .= '<tr>';
            foreach ($tabVaule['title'] as $key => $value) {
              $output .= '<th class="font-size-12">' . $value . '</th>';
            }
          $output .= '</tr>';
        $output .= '</thead>';

        $output .= '<tbody>';
        if (isset($tabVaule['content'])) {
          foreach ($tabVaule['content'] as $key => $value) {
            $output .= '<tr class="font-size-12 font-normal">';
            if (is_array($value)) {
              foreach ($value as $childValue) {
                $output .= '<td>' . $childValue . '</td>';
              }
            }
            $output .= '</tr>';
          }
        }
        $output .= '</tbody>';
      $output .= '</table>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getTabProgramValueEmpty() {
    $output = array(
      "title" => array(
        'Program Name',
        '#Events',
        'Reach',
        'Responses',
      ),
      "content" => array(
        array(
          'Hyperlink Program Name',
          '10',
          '0',
          '0',
        ),
      ),
    );

    return $output;
  }

  /**
   *
   */
  public function getTabLocationValueEmpty() {
    $output = array(
      "title" => array(
        'City',
        '#Events',
        'Reach',
        'Responses',
      ),
      "content" => array(
        array(
          'Hyperlink City Name',
          '125',
          '1125',
          '950',
        ),
        array(
          'Hyperlink City Name',
          '125',
          '1125',
          '950',
        ),
      ),
    );

    return $output;
  }

  /**
   *
   */
  public function getTabProgramValueByStartEndTime($user = NULL, $start_timestamp = NULL, $end_timestamp = NULL) {
    $start_query_date = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToQueryDate($start_timestamp, $type = 'html_date');
    $end_query_date = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToQueryDate($end_timestamp, $type = 'html_date');

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('meeting');

    $group = $query_container->groupByMeetingDateTime($query, $start_query_date, $end_query_date);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_speaker', $user->id());
    $query->condition($group);

    $meeting_nids = $query_container->runQueryWithGroup($query);
    $meeting_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($meeting_nids);

    $program_collection = array_count_values(
      \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstTargetIdCollection($meeting_nodes, 'field_meeting_program')
    );

    $output = array(
      "title" => array(
        'Program Name',
        '#Events',
        'Reach',
        'Responses',
      ),
      // "content" => array(
      //   array(
      //     'Hyperlink Program Name',
      //     '0',
      //     '0',
      //     '0',
      //   ),
      // ),
    );

    if ($program_collection && is_array($program_collection)) {
      foreach ($program_collection as $program_tid => $program_count) {
        $meetings_nodes_this_program = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_program', array($program_tid), 'IN');
        $meetings_nodes_this_city = \Drupal::getContainer()
          ->get('flexinfo.querynode.service')
          ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_city', array($program_tid), 'IN');

        $output["content"][] = array(
          \Drupal::getContainer()->get('flexinfo.term.service')->getNameByTid($program_tid),
          $program_count,
          array_sum(\Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueCollection($meetings_nodes_this_program, 'field_meeting_signature')),
          array_sum(\Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValueCollection($meetings_nodes_this_program, 'field_meeting_evaluationnum')),
        );
      }
    }

    return $output;
  }

  /**
   *
    $meetings_nodes_this_city = \Drupal::getContainer()
      ->get('flexinfo.querynode.service')
      ->wrapperMeetingNodesByFieldValue($meeting_nodes, 'field_meeting_city', array($program_tid), 'IN');
   */
  public function getTabLocationValueByStartEndTime($user = NULL, $start_timestamp = NULL, $end_timestamp = NULL) {
    $start_query_date = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToQueryDate($start_timestamp, $type = 'html_date');
    $end_query_date = \Drupal::getContainer()
      ->get('flexinfo.setting.service')->convertTimeStampToQueryDate($end_timestamp, $type = 'html_date');

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('meeting');

    $group = $query_container->groupByMeetingDateTime($query, $start_query_date, $end_query_date);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_speaker', $user->id());
    $query->condition($group);

    $meeting_nids = $query_container->runQueryWithGroup($query);
    $meeting_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($meeting_nids);

    $output = array(
      "title" => array(
        'City',
        '#Events',
        'Reach',
        'Responses',
      ),
      // "content" => array(
      //   array(
      //     'Hyperlink City Name',
      //     '125',
      //     '1125',
      //     '950',
      //   ),
      //   array(
      //     'Hyperlink City Name',
      //     '125',
      //     '1125',
      //     '950',
      //   ),
      // ),
    );

    if ($meeting_nodes && is_array($meeting_nodes)) {
      foreach ($meeting_nodes as $meeting_node) {
        $output["content"][] = array(
          \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($meeting_node, 'field_meeting_city'),
          1,
          \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($meeting_node, 'field_meeting_signature'),
          \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($meeting_node, 'field_meeting_evaluationnum'),
        );
      }
    }

    return $output;
  }

}
