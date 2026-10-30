<?php

namespace Drupal\ngjson\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\JsonResponse;

use Drupal\ngjson\Content\NgjsonFormContent;
use Drupal\ngjson\Content\NgjsonModalContent;
use Drupal\ngjson\Content\NgjsonObjectContent;
use Drupal\flexpage\Content\FlexpageObjectContent;

/**
 * Class Controller.
 */
class NgjsonController extends ControllerBase {


  public function __construct() {
  }

  /**
   * Standardpagejson.
   *
   * @return string
   *   Return Hello string.
   */
  public function speakerModalJson($entity_id) {
    $NgjsonModalContent = new NgjsonModalContent();
    $object_content_data = $NgjsonModalContent->speakerModalContent($entity_id);

    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

  /**
   * Standardpagejson.
   *
   * @return string
   *   Return Hello string.
   */
  public function standardFormJson($section, $entity_id, $start, $end) {
    $NgjsonFormContent   = new NgjsonFormContent();
    $object_content_data = $NgjsonFormContent->standardFormContentData($section, $entity_id);

    $output[0]["componentname"] = "primengforms";
    $output[0]["formsBasicInfo"] = [
      "postUrl" => "node",
      "redirectUrl" => "ngpage/evaluation/form/" . $entity_id,
      "deleteRedirectUrl" => "nodeinfo/redirect/meetinginsert",
      "formType" => "add",
      "formSubmitTag" => "standard",
      "resultSubmit" => [
        "type" => [
          "target_id" => "evaluation"
        ],
        "field_sample_textfield" => [
          "value" => "some comment from user",
        ]
      ],
    ];
    $output[0]["primengforms"] = $object_content_data;

    if ($section == 'evaluation') {
      $output[0]["formsBasicInfo"]["formSubmitTag"] = 'evaluation';
      $output[0]["formsBasicInfo"]["resultSubmit"] = [
        "type" => [
          [
            "target_id" => "evaluation"
          ],
        ],
        "title" => [
          [
            "value" => "Evaluation for meeting " . $entity_id,
          ],
        ],
        "field_evaluation_meetingnid" => [
          [
            "target_id" => $entity_id,
            "target_type" => "node",
          ],
        ],
      ];
    }
    else if ($section == 'hcpcomments') {
      $output[0]["formsBasicInfo"]["formSubmitTag"] = 'hcpcomments';
    }
    else if ($section == 'hcpquestioncomments') {
      $output[0]["formsBasicInfo"]["formSubmitTag"] = 'hcpquestioncomments';
    }
    else if ($section == 'hcptherapareacomments') {
      $output[0]["formsBasicInfo"]["formSubmitTag"] = 'hcptherapareacomments';
    }

    return new JsonResponse($output);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($output),
    );

    return $build;
  }

  /**
   * Standardpagejson.
   *
   * @return string
   *   Return Hello string.
   */
  public function standardPageJson($section, $entity_id, $start, $end) {
    $object_content_data = $this->standardPageContentData($section, $entity_id, $start, $end);

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
  public function standardPageContentData($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    if ($start == 'start') {
      $start = \Drupal::service('flexinfo.setting.service')->userStartTime();
    }
    if ($end == 'end') {
      $end = \Drupal::service('flexinfo.setting.service')->userEndTime();
    }
    $output = $this->getFlexpageSnapshot(strtolower($section), strtolower($entity_id), $start, $end);

    return $output;
  }

  /**
   * {@inheritdoc}

   $FlexpageObjectContent = new FlexpageObjectContent();

   $object_content_data = [];
   if (method_exists($FlexpageObjectContent, $content_method)) {
     $object_content_data = $FlexpageObjectContent->{$content_method}($section, $entity_id, $start, $end);
   }
   else {
     $NgjsonObjectContent   = new NgjsonObjectContent();
     if (method_exists($NgjsonObjectContent, $content_method)) {
       $object_content_data = $NgjsonObjectContent->{$content_method}($section, $entity_id, $start, $end);
     }
   }
   */
  public function getFlexpageSnapshot($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    $output = [];

    // combine like -- programSnapshotObjectContent
    $content_method = $section . 'PageContent';

    $NgjsonObjectContent = new NgjsonObjectContent();

    if (method_exists($NgjsonObjectContent, $content_method)) {
      $output = $NgjsonObjectContent->{$content_method}($section, $entity_id, $start, $end);
    }

    return $output;
  }

}
