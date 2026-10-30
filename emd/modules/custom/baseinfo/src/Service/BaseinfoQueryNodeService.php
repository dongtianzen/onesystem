<?php

/**
 * @file
 * Contains Drupal\baseinfo\Service\BaseinfoQueryNodeService.php.
 */

namespace Drupal\baseinfo\Service;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryFactory;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\flexinfo\Service\FlexinfoQueryNodeService;

/**
 * An example Service container.
   \Drupal::getContainer()->get('baseinfo.querynode.service')->sample();
 *
 */
class BaseinfoQueryNodeService extends FlexinfoQueryNodeService {

  /**
   *
   */
  public function wrapperEvaluationNidsFromMeetingNodes($meeting_nodes = array()) {
    $meeting_nids = \Drupal::getContainer()
      ->get('flexinfo.node.service')
      ->getNidsFromNodes($meeting_nodes);

    $output = \Drupal::getContainer()
      ->get('flexinfo.querynode.service')
      ->nodeNidsByStandardByFieldValue('evaluation', 'field_evaluation_meetingnid', $meeting_nids, 'IN');

    return $output;
  }

  /**
   *
   */
  public function wrapperEvaluationNodeFromMeetingNodes($meeting_nodes = array()) {
    $meeting_nids = \Drupal::getContainer()
      ->get('flexinfo.node.service')
      ->getNidsFromNodes($meeting_nodes);

    $output = \Drupal::getContainer()
      ->get('flexinfo.querynode.service')
      ->nodesByStandardByFieldValue('evaluation', 'field_evaluation_meetingnid', $meeting_nids, 'IN');

    return $output;
  }

  /**
   *
   */
  public function wrapperEvaluationNodeForProgramSecondPage($program_tids = array()) {
    $output = \Drupal::getContainer()
      ->get('flexinfo.querynode.service')
      ->nodesByStandardByFieldValue('evaluation', 'field_evaluation_programtid', $program_tids, 'IN');

    return $output;
  }

  /**
   *
   */
  public function wrapperEvaluationNodesFromMeetingNodesAndType($meeting_nodes = array(), $type_tid = NULL) {
    $meeting_nids = \Drupal::getContainer()
      ->get('flexinfo.node.service')
      ->getNidsFromNodes($meeting_nodes);

    $evaluation_nids = \Drupal::getContainer()
      ->get('flexinfo.querynode.service')
      ->nodeNidsByTwoStandardByFieldValue('evaluation', 'field_evaluation_meetingnid', $meeting_nids, 'IN', 'field_evaluation_type', $type_tid);

    $output = \Drupal::entityManager()->getStorage('node')->loadMultiple($evaluation_nids);

    return $output;
  }

  /**
   *
   */
  public function getQuestionAnswerAllDataWithReferUid($meeting_nodes = array(), $question_tid = NULL) {
    $evaluation_nodes = $this->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = array();
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $output[$row['refer_uid']][] = $row['question_answer'];
          }
        }
      }
    }

    return $output;
  }

}
