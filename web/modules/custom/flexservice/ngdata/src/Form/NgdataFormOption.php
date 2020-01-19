<?php

namespace Drupal\ngdata\Form;

/**
 *
  \Drupal::service('ngdata.form.option')->demo();
 */
class NgdataFormOption {

  /**
   *
   */
  public function getSelectOptions($question_term = NULL) {
    $scale = 5;
    if ($question_term->id()) {
      $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

      if ($question_scale > 0 && ctype_digit($question_scale)) {
        $scale = $question_scale;
      }
    }

    $label_term = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetIdToEntity($question_term, 'taxonomy_term', 'field_queslibr_label');

    $label_titles = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllValues($label_term, 'field_queslabel_title');
    // set default one
    if (count($label_titles) == 0) {
      $label_titles = array(
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        10,
      );
    }

    for ($i = 0; $i < $scale; $i++) {
      $output[] = array(
        "value" => $label_titles[$i],
        "label" => $label_titles[$i],
      );
    }

    return $output;
  }

  /**
   *
   */
  public function getSelectOptionsForEntityReferenceField($field_definition = array(), $field_name = NULL) {
    $term_options = array();

    if ($field_definition->getSetting('target_type') == 'taxonomy_term') {
      $handler_settings = $field_definition->getSetting('handler_settings');

      if (isset($handler_settings['target_bundles'])) {
        // only get first one
        $target_bundles = reset($handler_settings['target_bundles']);

        $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($target_bundles, 0);
        $term_options = $this->getSelectOptionsFromTreeForBasicTerm($tree, $field_name);
      }
    }
    elseif ($field_definition->getSetting('target_type') == 'user') {
      $users = array();

      if ($field_name == 'field_patient_referral') {
        $users = \Drupal::getContainer()->get('flexinfo.queryuser.service')->wrapperUsersByRoleName('referral');
      }

      if ($users) {
        $term_options = $this->getSelectOptionsFromFullUser($users);
      }
    }

    return $term_options;
  }

  /**
   * @param $fieldCategory can be hierarchyFather, specificAnswer, filterFather or filterChildren
   * @param $parentTid is needed for child to use filter
   */
  public function getSelectOptionsFromTreeForBasicTerm($tree = array(), $field_name = NULL) {
    $term_options = array();

    if (is_array($tree)) {
      foreach ($tree as $tree_term) {
        $child_options = array(
          "termTid" => $tree_term->tid,
          "termName" => $tree_term->name,
        );

        if ($field_name) {
          $child_options = $this->addParentTid($child_options, $field_name, $tree_term->tid);
        }

        $term_options[] = $child_options;
      }
    }

    return $term_options;
  }

  /**
   *
   */
  public function getSelectOptionsFromVocabularyTree($vid = NULL, $field_name = NULL) {
    $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid, 0);
    $term_options = $this->getSelectOptionsFromTreeForBasicTerm($tree, $field_name);

    return $term_options;
  }

  /**
   * @param, $vid = 'selectkeyanswer' or 'specialty'
   */
  public function getSelectOptionsForSelectkyeAnswerOptions($question_term = NULL) {
    $term_options = array();

    $all_option_terms = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIdsEntitys($question_term, 'field_queslibr_selectkeyanswer', 'taxonomy_term');

    if (is_array($all_option_terms)) {
      foreach ($all_option_terms as $term) {
        $child_options = array(
          "value" => $term->id(),
          "label" => $term->getName(),
        );

        $term_options[] = $child_options;
      }
    }

    return $term_options;
  }

  /**
   *
   */
  public function getSelectOptionsFromFullUser($users = array()) {
    $term_options = array();

    if (is_array($users)) {
      foreach ($users as $user) {
        $term_options[] = array(
          "termTid" => $user->id(),
          "termName" => $user->getUsername(),
        );
      }
    }

    return $term_options;
  }

}
