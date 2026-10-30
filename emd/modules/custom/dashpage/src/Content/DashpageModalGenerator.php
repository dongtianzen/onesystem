<?php

/**
 * @file
 */
namespace Drupal\dashpage\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\dashpage\Content\DashTabSpeaker;


/**
 * An example controller.
 */
class DashpageModalGenerator extends ControllerBase {

  public $DashTabSpeaker;

  public function __construct() {
    $this->DashTabSpeaker = new DashTabSpeaker();
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
        $start_timestamp_ytd = strtotime(date('Y', time()) . "-01-01");
        $start_timestamp_all = 1483246800;
        $end_timestamp = strtotime("now");

        $output['speaker'] = $this->getSpeakerInfo($user);

        $output['ytd']['header']  = $this->DashTabSpeaker->getTabHeaderText($user, $start_timestamp_ytd, $end_timestamp, $tab_key = 'ytd');
        $output['all']['header']  = $this->DashTabSpeaker->getTabHeaderText($user, $start_timestamp_all, $end_timestamp, $tab_key = 'all');

        $output['ytd']['program'] = $this->DashTabSpeaker->getTableContentBodyProgram($user, $start_timestamp_ytd, $end_timestamp);
        $output['all']['program'] = $this->DashTabSpeaker->getTableContentBodyProgram($user, $start_timestamp_all, $end_timestamp);

        $output['ytd']['location'] = $this->DashTabSpeaker->getTableContentBodyLocation($user, $start_timestamp_ytd, $end_timestamp);
        $output['all']['location'] = $this->DashTabSpeaker->getTableContentBodyLocation($user, $start_timestamp_all, $end_timestamp);
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
    $output = array();

    if ($user) {
      $output['displayName'] = $user->getDisplayName();
    }

    return $output;
  }

}
