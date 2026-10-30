<?php

/**
 * @file
 * Contains \Drupal\dashpage\Controller\ExportinfoController.
 */

namespace Drupal\exportinfo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\exportinfo\Content\ExportinfoContentGenerator;
use Drupal\exportinfo\Content\ExportinfoJsonGenerator;
use Drupal\exportinfo\Content\ExportinfoPageGenerator;

/**
 * An example controller.
 */
class ExportinfoController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function exportGuidePage($another) {
    $output = $this->exportGuidePageMarkup();

    $build = array(
      '#type' => 'markup',
      '#markup' => $output,
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function exportGuidePageMarkup() {
    $class_attributes = array('attributes' => array('class' => array('color-fff')));

    $url_array = array(
      array(
        'text' => t('HCP Comments By Program'),
        'url' => Url::fromUserInput('/ngpage/hcpcomments/form/all', $class_attributes),
      ),
      array(
        'text' => t('HCP Comments By Question'),
        'url' => Url::fromUserInput('/ngpage/hcptherapareacomments/form/all', $class_attributes),
      ),
    );

    $markup = NULL;
    foreach ($url_array as $row) {
      $markup .= '<div class="row padding-0">';
        $markup .= '<div class="text-center margin-top-48">';
          $markup .= '<div class="btn btn-info">';
            $markup .= \Drupal::l($row['text'], $row['url']);
          $markup .= '</div>';
        $markup .= '</div>';
      $markup .= '</div>';
    }

    return $markup;
  }

  /**
   * {@inheritdoc}
   */
  public function exportVocabularyComments($vocabulary, $entity_id, $start, $end) {
    $ExportinfoContentGenerator = new ExportinfoContentGenerator();
    $output = $ExportinfoContentGenerator->exportVocabularyComments($vocabulary, $entity_id, $start, $end);

    return $output;
    $output = 'Debug Export Comments';

    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($output),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   * single question
   * exportinfo/comments/question/452/1546318800/1572572261
   */
  public function exportQuestionComments($question_tid, $start, $end) {
    $ExportinfoContentGenerator = new ExportinfoContentGenerator();

    $request_query_theraparea =  \Drupal::request()->query->get("theraparea");

    if ($request_query_theraparea && isset($request_query_theraparea['tid'])) {
      $output = $ExportinfoContentGenerator->exportQuestionsCommentsByTheraparea(array($question_tid), $request_query_theraparea['tid'], $start, $end);
    }
    else {
      $output = $ExportinfoContentGenerator->exportSingleQuestionComments($question_tid, $start, $end);
    }

    // return $output;
    $output = 'Debug Export Comments';

    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($output),
    );

    return $build;
  }

}
