<?php

/**
 * @file
 * Contains Drupal\baseinfo\Service\BaseinfoQueryTermService.php.
 */
namespace Drupal\baseinfo\Service;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryFactory;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\flexinfo\Service\FlexinfoQueryTermService;

/**
 * An example Service container.
 */
class BaseinfoQueryTermService extends FlexinfoQueryTermService {

  /**
   * @param
   */
  public function wrapperQuestionTidsByQuestiontypeTermName($question_tids = array(), $questiontype_term_name = NULL) {
    $questiontype_tid = \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = $questiontype_term_name, $vocabulary_name = 'fieldtype');

    $output = $this->wrapperQuestionTidsByQuestiontype($question_tids, $questiontype_tid);

    return $output;
  }

  /**
   * @param
   */
  public function wrapperQuestionTidsByQuestiontype($question_tids = array(), $questiontype_tid = NULL) {
    $query = $this->queryTidsByBundle('questionlibrary');

    $group = $this->groupStandardByFieldValue($query, 'tid', $question_tids, 'IN');
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, 'field_queslibr_fieldtype', 2060);
    $query->condition($group);

    $tids = $this->runQueryWithGroup($query);

    return $tids;
  }

  /**
   * @param Learning Objective's tid is 2451
            radios is 2493
   \Drupal::service('baseinfo.queryterm.service')->wrapperQuestionTidsOnlyRadios();
   */
  public function wrapperQuestionTidsOnlyRadios($question_tids = array()) {
    $radios_tid = \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = 'radios', $vocabulary_name = 'fieldtype');

    //
    $query = $this->queryTidsByBundle('questionlibrary');

    $group = $this->groupStandardByFieldValue($query, 'tid', $question_tids, 'IN');
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, 'field_queslibr_fieldtype', $radios_tid);
    $query->condition($group);

    $output = $this->runQueryWithGroup($query);

    return $output;
  }

  /**
   * @param $Questiontype = 'General'/'Learning Objective'
   */
  public function wrapperQuestionTidsByRadiosByQuestiontype($question_tids = array(), $questiontype = 'General') {
    $radios_tid = \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = 'radios', $vocabulary_name = 'fieldtype');

    $questiontype_tid = \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($questiontype, $vocabulary_name = 'questiontype');

    //
    $query = $this->queryTidsByBundle('questionlibrary');

    $group = $this->groupStandardByFieldValue($query, 'tid', $question_tids, 'IN');
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, 'field_queslibr_fieldtype', $radios_tid);
    $query->condition($group);

    $group = $this->groupStandardByFieldValue($query, 'field_queslibr_questiontype', $questiontype_tid);
    $query->condition($group);

    $output = $this->runQueryWithGroup($query);

    return $output;
  }

  /**
   *
   */
  public function wrapperFieldtypeQuestionTidsFromEvaluationform($field_type_name = NULL, $evaluationform_term = NULL) {
    $tid = \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = $field_type_name, $vocabulary_name = 'fieldtype');

    $all_question_tids = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    $output = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperStandardTidsByTidsByField($all_question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $tid);

    return $output;
  }

  /**
   *
   */
  public function wrapperQuestiontypeQuestionTidsFromEvaluationform($field_type_name = NULL, $evaluationform_term = NULL) {
    $tid = \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = $field_type_name, $vocabulary_name = 'questiontype');

    $all_question_tids = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    $output = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperStandardTidsByTidsByField($all_question_tids, 'questionlibrary', 'field_queslibr_questiontype', $tid);

    return $output;
  }

  /**
   *
   */
  public function wrapperMultipleQuestionTidsFromEvaluationform($evaluationform_term = NULL) {
    $radios_tid = \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = 'radios', $vocabulary_name = 'fieldtype');

    $question_tids_current_form = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    //
    $query_container = \Drupal::service('flexinfo.queryterm.service');
    $query = $query_container->queryTidsByBundle('questionlibrary');

    // filter by tids
    $group = $query_container->groupStandardByFieldValue($query, 'tid', $question_tids_current_form, 'IN');
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_queslibr_fieldtype', $radios_tid);
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_queslibr_relatedfield', NULL, 'IS NOT NULL');
    $query->condition($group);

    $output = $query_container->runQueryWithGroup($query);

    return $output;
  }

  /**
   *
   */
  public function wrapperRankingQuestionTidsFromEvaluationform($evaluationform_term = NULL) {
    $ranking_tid = \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = 'ranking', $vocabulary_name = 'fieldtype');

    $question_tids_current_form = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    //
    $query_container = \Drupal::service('flexinfo.queryterm.service');
    $query = $query_container->queryTidsByBundle('questionlibrary');

    // filter by tids
    $group = $query_container->groupStandardByFieldValue($query, 'tid', $question_tids_current_form, 'IN');
    $query->condition($group);

    $group = $query_container->groupStandardByFieldValue($query, 'field_queslibr_fieldtype', $ranking_tid);
    $query->condition($group);

    $output = $query_container->runQueryWithGroup($query);

    return $output;
  }

}
