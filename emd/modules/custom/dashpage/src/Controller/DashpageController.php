<?php

/**
 * @file
 * Contains \Drupal\dashpage\Controller\DashpageController.
 */

namespace Drupal\dashpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\dashpage\Content\DashpageContentGenerator;
use Drupal\dashpage\Content\DashpageModalGenerator;
use Drupal\flexpage\Controller\FlexpageController;


/**
 * An example controller.
 */
class DashpageController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function standardPath($section) {
    if ($section) {
      switch ($section) {
        case 'angular':
        case 'emptyangular':
        case 'evaluation':
        case 'evaluationform':
        case 'home':
        case 'meeting':

        // user
        case 'speakersummary':
        case 'userlist':

        // datatable
        case 'eventstatus':
          break;

        default:
          \Drupal::getContainer()->get('flexinfo.setting.service')->throwExceptionPage(404);
          break;
      }
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function standardJson($section, $entity_id, $start, $end) {
    $FlexpageController = new FlexpageController();
    return $FlexpageController->standardJson($section, $entity_id, $start, $end);
  }

  /**
   * {@inheritdoc}
   * use Symfony\Component\HttpFoundation\RedirectResponse;
   */
  public function standardMenuItem($section, $entity_id) {
    $start = \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTime();
    $end   = \Drupal::getContainer()->get('flexinfo.setting.service')->userEndTime();

    $uri = '/dashpage/' . $section . '/snapshot/' . $entity_id . '/' . $start . '/' . $end;
    $url = Url::fromUserInput($uri)->toString();

    return new RedirectResponse($url);
  }

  /**
   * {@inheritdoc}
   */
  public function standardSnapshot($section, $entity_id, $start, $end) {
    $FlexpageController = new FlexpageController();
    return $FlexpageController->standardSnapshot($section, $entity_id, $start, $end);
  }

  /**
   * {@inheritdoc}
   */
  public function guestpageSnapshot($section, $entity_id, $start, $end) {
    if ($section != 'meeting') {
      \Drupal::getContainer()->get('flexinfo.setting.service')->throwExceptionPage(404);
    }

    return $this->standardSnapshot($section, $entity_id, $start, $end);
  }

  /**
   * {@inheritdoc}
   */
  public function guestpageJson($section, $entity_id, $start, $end) {
    $output = $this->standardJson($section, $entity_id, $start, $end);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function standardMap($section, $entity_id, $start, $end) {
    $DashpageContentGenerator = new DashpageContentGenerator();
    $markup = $DashpageContentGenerator->standardMap($entity_id);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
      '#attached' => array(
        'library' => array(
          'dashpage/standard.google.map',
        ),
      ),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function standardMapJson($section, $entity_id, $start, $end) {
    $DashpageContentGenerator = new DashpageContentGenerator();
    $object_content_data = $DashpageContentGenerator->standardMapJson($section, $entity_id, $start, $end);

    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function standardModalPage($section, $entity_id) {
    $DashpageModalGenerator = new DashpageModalGenerator();
    $markup = $DashpageModalGenerator->standardModalPage($section, $entity_id);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function standardModalJson($section, $entity_id) {
    $DashpageModalGenerator = new DashpageModalGenerator();
    $object_content_data['content'] = $DashpageModalGenerator->standardModalPage($section, $entity_id);

    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

}
