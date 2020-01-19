<?php

/**
 * @file
 * Contains \Drupal\flexpage\Controller\FlexpageController.
 */

namespace Drupal\flexpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\flexpage\Content\FlexpageContentGenerator;
use Drupal\flexpage\Content\FlexpageMeetingQuestionLayout;
use Drupal\flexpage\Content\FlexpageObjectContent;

use Drupal\dashpage\Content\DashpageObjectContent;

/**
 * An example controller.
 */
class FlexpageController extends ControllerBase {

  public $FlexpageContentGenerator;
  public $FlexpageObjectContent;
  public $DashpageObjectContent;

  public function __construct() {
    $this->FlexpageContentGenerator = new FlexpageContentGenerator();
    $this->FlexpageObjectContent    = new FlexpageObjectContent();

    $this->DashpageObjectContent    = new DashpageObjectContent();
  }

  /**
   *
   */
  public function angularSnapshotTemplate($object_content_data = array()) {
    \Drupal::getContainer()->get('flexinfo.setting.service')->setPageTitle();

    $markup = $this->FlexpageContentGenerator->angularSnapshotWrapper();

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
      '#attached' => array(
        'library' => array(
          'flexpage/angular.snapshot',
        ),
        'drupalSettings' => [
          'flexpage' => [
            'flexpageData' => [
              'objectContentData' => $object_content_data,
            ],
          ],
        ],
      ),
      '#cache' => ['max-age' => 0,],    // Set cache for 0 seconds.,
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function checkStandardPath($section) {
    if ($section) {

    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getFlexpageSnapshot($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    // combine like -- programSnapshotObjectContent
    $content_method = $section . 'SnapshotObjectContent';

    $object_content_data = NULL;
    if (method_exists($this->FlexpageObjectContent, $content_method)) {
      $object_content_data = $this->FlexpageObjectContent->{$content_method}($section, $entity_id, $start, $end);
    }
    else {
      if (method_exists($this->DashpageObjectContent, $content_method)) {
        $object_content_data = $this->DashpageObjectContent->{$content_method}($section, $entity_id, $start, $end);
      }
    }

    return $object_content_data;
  }

  /**
   * @param $start = 2016-01-01T23:30:00, $end = 2016-12-31T23:30:00
   */
  public function getObjectContentData($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $object_content_data = $this->getFlexpageSnapshot($section, $entity_id, $start, $end);

    return $object_content_data;
  }

  /**
   * {@inheritdoc}
   */
  public function standardContentData($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $section   = strtolower($section);
    $entity_id = strtolower($entity_id);

    $this->checkStandardPath($section);

    $output = $this->getObjectContentData($section, $entity_id, $start, $end);
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function standardJson($section, $entity_id, $start, $end) {
    $object_content_data = $this->standardContentData($section, $entity_id, $start, $end);
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
  public function devEmptyangularJson($entity_id) {
    $object_content_data = array();
    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => 'Dev Emptyangular Json',
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * use Symfony\Component\HttpFoundation\RedirectResponse;
   */
  public function standardMenuItem($section, $entity_id) {
    $start = \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTime();
    $end   = \Drupal::getContainer()->get('flexinfo.setting.service')->userEndTime();

    $uri = '/flexpage/' . $section . '/snapshot/' . $entity_id . '/' . $start . '/' . $end;
    $url = Url::fromUserInput($uri)->toString();

    return new RedirectResponse($url);
  }

  /**
   * {@inheritdoc}
   * @param $entity_id is int or "all"
   */
  public function standardSnapshot($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $object_content_data = $this->standardContentData($section, $entity_id, $start, $end);

    $build = $this->angularSnapshotTemplate($object_content_data);
    return $build;
  }

  /**
   * {@inheritdoc}
   * @param $entity_id is int or "all"
   */
  public function meetingQuestionPage($entity_id = NULL, $start = NULL, $end = NULL) {
    $FlexpageMeetingQuestionLayout = new FlexpageMeetingQuestionLayout();
    $object_content_data = $FlexpageMeetingQuestionLayout->meetingPageLayout($entity_id, $start, $end);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $object_content_data,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * @param $entity_id is int or "all"
   */
  public function programQuestionPage($entity_id = NULL, $start = NULL, $end = NULL) {
    $FlexpageMeetingQuestionLayout = new FlexpageMeetingQuestionLayout();
    $object_content_data = $FlexpageMeetingQuestionLayout->programPageLayout($entity_id, $start, $end);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $object_content_data,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

}
