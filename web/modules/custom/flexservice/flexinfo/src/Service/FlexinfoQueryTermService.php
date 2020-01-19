<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoQueryTermService.php.
 */
namespace Drupal\flexinfo\Service;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryFactory;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\Component\Utility\Timer;
/**
 * An example Service container.
   $FlexinfoQueryTermService = new FlexinfoQueryTermService();
   $FlexinfoQueryTermService->runQueryWithGroup();
 *
   \Drupal::getContainer()->get('flexinfo.queryterm.service')->programTidsByBusinessunit();
 */
class FlexinfoQueryTermService extends ControllerBase {

  protected $entity_query;

  /**
   * {@inheritdoc}
   */
  public function __construct(QueryFactory $entity_query) {
    $this->entity_query = $entity_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /** - - - - - - query not run execute() - - - - - - - - - - - - - - - - -  */

  /**
   * @param, $vid is entity_bundle
   */
  public function queryTidsByBundle($vid = NULL) {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vid);

    return $query;
  }

  /** - - - - - - execute - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return array, nids
   */
  public function runQueryWithGroup($query = NULL) {
    $result = $query->execute();

    return array_values($result);
  }

  /** - - - - - - Term Standard Group - - - - - - - - - - - - - - - - - - - - - -  */
  /**
   * @param $field_name like "field_city_province", containing
   * @see https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Entity!Query!QueryInterface.php/function/QueryInterface%3A%3Acondition/8.2.x
   */
  public function groupStandardByFieldValue($query = NULL, $field_name = NULL, $field_value = NULL, $operator = NULL, $langcode = NULL) {
    if ($operator == 'IN' || $operator == 'NOT IN') {
      if (is_array($field_value) && count($field_value) == 0) {
        // $field_value cannot be empty array
        $field_value = array(-1);
      }
    }

    $group = $query->andConditionGroup()
      ->condition($field_name, $field_value, $operator);

    return $group;
  }

  /** - - - - - - wrapper - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return array, term tid
   \Drupal::getContainer()->get('flexinfo.queryterm.service')->wrapperTermTidsByField();
   */
  public function wrapperTermTidsByField($vid = NULL, $field_name = NULL, $field_value = NULL, $operator = NULL, $langcode = NULL) {
    $query = $this->queryTidsByBundle($vid);
    $group = $this->groupStandardByFieldValue($query, $field_name, $field_value, $operator);
    $query->condition($group);
    $tids = $this->runQueryWithGroup($query);

    return $tids;
  }

  /**
   * @return array, term tid
   \Drupal::getContainer()->get('flexinfo.queryterm.service')->wrapperTermTidsByField();
   */
  public function wrapperTermEntitysByField($vid = NULL, $field_name = NULL, $field_value = NULL, $operator = NULL, $langcode = NULL) {
    $tids = $this->wrapperTermTidsByField($vid, $field_name, $field_value, $operator, $langcode);
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);

    return $terms;
  }

  /**
   * @param array, term tids
   * @return array, term tids
   \Drupal::getContainer()->get('flexinfo.queryterm.service')->wrapperStandardTidsByTidsByField($tids);
   */
  public function wrapperStandardTidsByTidsByField($tids = NULL, $vid = NULL, $field_name = NULL, $field_value = NULL, $operator = NULL, $langcode = NULL) {
    $query = $this->queryTidsByBundle($vid);

    // filter by tids
    $group = $this->groupStandardByFieldValue($query, 'tid', $tids, 'IN');
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, $field_name, $field_value, $operator);
    $query->condition($group);

    $tids = $this->runQueryWithGroup($query);

    return $tids;
  }

  /**
   * @param Learning Objective's tid is 2451
            radios is 2493
   \Drupal::getContainer()->get('flexinfo.queryterm.service')->wrapperQuestionTidsByRadiosByLearningObjective();
   */
  public function wrapperQuestionTidsByRadiosByLearningObjective($question_tids = array(), $learning_objective = TRUE) {
    $query = $this->queryTidsByBundle('questionlibrary');

    // filter by tids
    $group = $this->groupStandardByFieldValue($query, 'tid', $question_tids, 'IN');
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, 'field_queslibr_fieldtype', 2493);
    $query->condition($group);

    // Learning Objective
    if ($learning_objective) {
      $group = $this->groupStandardByFieldValue($query, 'field_queslibr_questiontype', 2451);
      $query->condition($group);
    }
    else {
      $query->notExists('field_queslibr_questiontype');
    }

    $tids = $this->runQueryWithGroup($query);

    return $tids;
  }

  /**
   * @param Learning Objective's tid is 2451
            radios is 2493
   \Drupal::getContainer()->get('flexinfo.queryterm.service')->wrapperQuestionTidsByRadiosByLearningObjective();
   */
  public function wrapperQuestionTidsByRadiosByQuestiontype($question_tids = array(), $questiontype_tid = NULL) {
    $query = $this->queryTidsByBundle('questionlibrary');

    // filter by tids
    $group = $this->groupStandardByFieldValue($query, 'tid', $question_tids, 'IN');
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, 'field_queslibr_fieldtype', 2493);
    $query->condition($group);

    if ($questiontype_tid) {
      $group = $this->groupStandardByFieldValue($query, 'field_queslibr_questiontype', $questiontype_tid);
      $query->condition($group);
    }
    else {
      $query->notExists('field_queslibr_questiontype');
    }

    $tids = $this->runQueryWithGroup($query);

    return $tids;
  }

  /** - - - - - - other - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return array, term object
   \Drupal::getContainer()->get('flexinfo.queryterm.service')->programTermsByBusinessunit();
   */
  public function programTermsByBusinessunit($program_terms = array(), $businessunit_tids = array()) {
    $output = array();

    if (is_array($program_terms)) {
      foreach ($program_terms as $program_term) {
        $businessunit_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($program_term, 'field_program_businessunit');
        if ($businessunit_tid) {
          if (in_array($businessunit_tid, $businessunit_tids)) {
            $output[] = $program_term;
          }
        }
      }
    }

    return $output;
  }

  /**
   * @return array, term tid
   \Drupal::getContainer()->get('flexinfo.queryterm.service')->programTidsByBusinessunit();
   */
  public function programTidsByBusinessunit($businessunit_tids = array()) {
    $tids = $this->wrapperTermTidsByField('program', 'field_program_businessunit', $businessunit_tids, 'IN');
    return $tids;
  }

  /**
   * @return array, term tid
   */
  public function programTidsByDiseasestate($diseasestate_tids = array()) {
    $tids = $this->wrapperTermTidsByField('program', 'field_program_diseasestate', $diseasestate_tids, 'IN');
    return $tids;
  }

  /**
   * @return array, term tid
   */
  public function programTidsByTheraparea($theraparea_tids = array()) {
    $tids = $this->wrapperTermTidsByField('program', 'field_program_theraparea', $theraparea_tids, 'IN');
    return $tids;
  }

  /**
   * @param Learning Objective's tid is 2451
   \Drupal::getContainer()->get('flexinfo.queryterm.service')->questionTermsOnlyLearningObjective();
   */
  public function questionTermsOnlyLearningObjective($question_terms = array()) {
    $output = NULL;

    if (is_array($question_terms)) {
      foreach ($question_terms as $question_term) {

        $queslibr_fieldtype_tid = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstTargetId($question_term, 'field_queslibr_questiontype');

        if ($queslibr_fieldtype_tid == 2451) {
          $output[] = $question_term;
        }
      }
    }

    return $output;
  }

}
