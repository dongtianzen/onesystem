<?php

/**
 * @file
 */
namespace Drupal\modalpage\Content;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class ModalContentGenerator extends ControllerBase {

  public $ModalTabSpeaker;

  public function __construct() {
    $this->ModalTabSpeaker = new ModalTabSpeaker();
  }

  /**
   *
   */
  public function standardModalPage($section, $entity_id) {
    $output = array();

    if ($section == 'speakerpop') {
      // $output['sample'] = $this->standardModalWrapper();
      // $output['uid'] = $entity_id * rand(2, 10);

      $user = \Drupal::entityTypeManager()->getStorage('user')->load($entity_id);
      if ($user) {
        // January 1, 2017
        $start_timestamp_all = 1483246800;

        $start_timestamp_ytd = strtotime(date('Y', time()) . "-01-01");
        $end_timestamp = strtotime("now");

        $output['speaker'] = $this->getSpeakerInfo($user);

        $output['ytd']['header']  = $this->ModalTabSpeaker->getTabHeaderText($user, $start_timestamp_ytd, $end_timestamp, $tab_key = 'ytd');
        $output['all']['header']  = $this->ModalTabSpeaker->getTabHeaderText($user, $start_timestamp_all, $end_timestamp, $tab_key = 'all');

        $output['ytd']['program'] = $this->ModalTabSpeaker->getTableContentBodyProgram($user, $start_timestamp_ytd, $end_timestamp);
        $output['all']['program'] = $this->ModalTabSpeaker->getTableContentBodyProgram($user, $start_timestamp_all, $end_timestamp);

        $output['ytd']['location'] = $this->ModalTabSpeaker->getTableContentBodyLocation($user, $start_timestamp_ytd, $end_timestamp);
        $output['all']['location'] = $this->ModalTabSpeaker->getTableContentBodyLocation($user, $start_timestamp_all, $end_timestamp);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function standardModalWrapper($content = NULL) {
    $output = '';
    $output .= '<div class="row">';
      $output .= '<div class="margin-24">';
        $output .= '<div class="standard-modal-wrapper">';
          $output .= '<div class="standard-modal-content">';
            $output .= 'placeholder';
            $output .= $content;
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @from
   */
  public function getSpeakerInfo($user = NULL) {
    $output = [];

    if ($user) {
      $output['displayName'] = $user->getDisplayName();
    }

    return $output;
  }

}
