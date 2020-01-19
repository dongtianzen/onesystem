<?php

/**
 * @file
 * Contains \Drupal\modalpage\Controller\ModalpageController.
 */

namespace Drupal\modalpage\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\JsonResponse;

use Drupal\modalpage\Content\ModalContentGenerator;


/**
 * An example controller.
 */
class ModalpageController extends ControllerBase {

  /**
   * @return debug page
   * @param  $entity_id is speaker user uid
     modalpage/modal/speakerpop/json/32
   */
  public function standardModalPage($section, $entity_id) {
    $ModalContentGenerator = new ModalContentGenerator();
    $content = $ModalContentGenerator->standardModalPage($section, $entity_id);

    dpm($content['all']['header']);

    $html_text = '';
    $html_text .= '<div>';
      $html_text .= '<h5>';
      $html_text .= 'This is YTD data';
      $html_text .= '</h5>';
      $html_text .= '# attah on THEME_preprocess_HOOK()';
    $html_text .= '</div>';

    $markup = $html_text;
    $markup .= render($content['ytd']['header']);
    // $markup = render($content['speaker']);
    // $markup = render($content['all']['header']);

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
     modaljson/modal/speakerpop/json/32
   */
  public function standardModalJson($section, $entity_id) {
    $ModalContentGenerator = new ModalContentGenerator();
    $object_content_data['content'] = $ModalContentGenerator->standardModalPage($section, $entity_id);

    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

}
