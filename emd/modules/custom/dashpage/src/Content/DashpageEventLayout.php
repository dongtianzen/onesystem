<?php

/**
 * @file
 */

namespace Drupal\dashpage\Content;

use Drupal\Core\Controller\ControllerBase;

use Drupal\flexpage\Content\FlexpageEventLayout;


/**
 * An example controller.
 */
class DashpageEventLayout extends ControllerBase {

  /**
   * @deprecated
   */
  public function blockEventsSnapshot($meeting_nodes = array(), $evaluationform_tid = NULL) {
    $FlexpageEventLayout = new FlexpageEventLayout();
    $output = $FlexpageEventLayout->blockEventsSnapshot($meeting_nodes, $evaluationform_tid);

    return $output;
  }

}
