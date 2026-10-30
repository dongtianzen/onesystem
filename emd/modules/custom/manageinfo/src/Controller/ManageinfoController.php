<?php

/**
 * @file
 * Contains \Drupal\dashpage\Controller\ManageinfoController.
 */

namespace Drupal\manageinfo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

use Drupal\Component\Utility\Timer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * An example controller.
 */
class ManageinfoController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function redirectMeetingInsert($entity_id = NULL) {
    $meeting_url = Url::fromUserInput('/flexform/node/add/evaluation/form/' . $entity_id, array('attributes' => array('class' => array('color-fff'))));
    $summary_url = Url::fromUserInput('/flexform/node/add/summaryevaluation/form/' . $entity_id, array('attributes' => array('class' => array('color-fff'))));
    $program_url = Url::fromUserInput('/ngpage/standardterm/page/program', array('attributes' => array('class' => array('color-fff'))));

    $markup = NULL;
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center margin-top-48">';
        $markup .= '<div class="btn btn-info">';
          $markup .= \Drupal::l(t('Go Evaluation'), $meeting_url);
        $markup .= '</div>';
      $markup .= '</div>';
    $markup .= '</div>';

    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center margin-top-48">';
        $markup .= '<div class="btn btn-success">';
          $markup .= \Drupal::l(t('Go Summary'), $summary_url);
        $markup .= '</div>';
      $markup .= '</div>';
    $markup .= '</div>';

    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center margin-top-48">';
        $markup .= '<div class="btn btn-warning color-fff">';
          $markup .= \Drupal::l(t('Program List'), $program_url);
        $markup .= '</div>';
      $markup .= '</div>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

}
