<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoTermService.php.
 */
namespace Drupal\flexinfo\Service;

use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

/**
 *
 */
class FlexinfoTermService {

  /**
   *
   */
  public function entityCreateTerm($term_name = NULL, $vocabulary = NULL) {
    return $this->entityCreateTermWithFieldsValue($term_name, $vocabulary);
  }

  /**
   * @param, $fields_value is array,
    $fields_value = array(
      array(
        'field_name' => 'field_city_province',
        'value' => array(25),
        'vid' => 'province',
      ),
    );
   */
  public function entityCreateTermWithFieldsValue($term_name = NULL, $vocabulary = NULL, $fields_value = array()) {
    $tid = NULL;

    if ($term_name) {
      $vocabulary_entity = taxonomy_vocabulary_load($vocabulary);
      if ($vocabulary_entity) {
        $term_value = [
          'name' => $term_name,
          'vid'  => $vocabulary_entity->get('vid'),
        ];

        // fields
        if ($fields_value && is_array($fields_value)) {
          foreach ($fields_value as $field_row) {
            $field = \Drupal\field\Entity\FieldStorageConfig::loadByName('taxonomy_term', $field_row['field_name']);

            if ($field) {
              $field_standard_type = \Drupal::getContainer()
                ->get('flexinfo.field.service')
                ->getFieldStandardType();

              if (is_array($field_row['value'])) {
                foreach ($field_row['value'] as $row_value) {

                  if (in_array($field->getType(), $field_standard_type)) {
                    $term_value[$field_row['field_name']][] = $row_value;
                  }
                  elseif ($field->getType() == 'entity_reference') {
                    if ($field->getSetting('target_type') == 'taxonomy_term') {
                      $term_value[$field_row['field_name']][] = $row_value;
                    }
                    else{
                      $term_value[$field_row['field_name']][] = $row_value;
                    }
                  }
                  else {
                    dpm('no found this field type - ' . $field->getType());
                  }
                }
              }
            }
            else {
              dpm('not found field type - for this field - ' . $field_row['field_name']);
            }
          }
        }

        $term = Term::create($term_value);
        $term->save();

        if (isset($term->get('tid')->value)) {
          $tid = $term->get('tid')->value;

          if (\Drupal::currentUser()->id() == 1) {
            dpm('create term -' . $term->get('name')->value . ' - tid - ' . $term->get('tid')->value);
          }
        }
      }
    }

    return $tid;
  }

  /**
   * @return array, terms entity
   \Drupal::getContainer()->get('flexinfo.term.service')->getFullTermsFromVidName($vid);
   */
  public function getFullTermsFromVidName($vid = NULL) {
    $tids = $this->getTidsFromVidName($vid);
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);

    return $terms;
  }

  /**
   * @return term name
   \Drupal::getContainer()->get('flexinfo.term.service')->getNameByTid($target_id);
   */
  public function getNameByTid($tid = NULL) {
    $output = NULL;

    if ($tid) {
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
      if ($term) {
        $output = $term->get('name')->value;
      }
    }

    return $output;
  }

  /**
   * @return array, term tids
   */
  public function getNamesFromTermTree($term_tree = array()) {
    $output = array();

    if (is_array($term_tree)) {
      foreach ($term_tree as $term) {
        $output[] = $term->name;
      }
    }

    return $output;
  }

  /**
   * @return array, term tids
   \Drupal::getContainer()->get('flexinfo.term.service')->getTidsFromVidName($vid);
   */
  public function getNamesFromVidName($vid = NULL) {
    $trees = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid, 0);
    $tids = $this->getTidsFromTermTree($trees);

    return $tids;
  }

  /**
   * @return term
   \Drupal::getContainer()->get('flexinfo.term.service')->getTermByTermName($term_name);
   */
  public function getTermByTermName($term_name = NULL, $vocabulary = NULL) {
    $output = NULL;

    $tid = $this->getTidByTermName($term_name, $vocabulary);
    if ($tid) {
      $output = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
    }
    return $output;
  }

  /**
   * @return array
   */
  public function getTermTidsTermNameFromVid($vid = NULL) {
    $terms = $this->getFullTermsFromVidName($vid);
    foreach ($terms as $term) {
      $output[] = array(
        "termTid" => $term->id(),
        "termName" => $term->getName(),
      );
    }

    return $output;
  }

  /**
   * @return array, terms entity
   \Drupal::getContainer()->get('flexinfo.term.service')->getTermsFromTids($tids);
   */
  public function getTermsFromTids($tids = array()) {
    $terms = array();

    if (is_array($tids)) {
      $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);
    }

    return $terms;
  }

  /**
   * @return term tid
   */
  public function getTidByTermName($term_name = NULL, $vocabulary = NULL) {
    $output = NULL;

    $terms = taxonomy_term_load_multiple_by_name($term_name, $vocabulary);
    if (count($terms) > 0) {
      $term = reset($terms);

      $output = $term->get('tid')->value;

      if (count($terms) > 1) {
        if (\Drupal::currentUser()->id() == 1) {
          dpm('found this term_name - ' . $term_name . ' in vocabulary more than one - ' . implode(" ", array_keys($terms)) . ' $vocabulary is - ' . $vocabulary);
        }
      }
    }
    else {
      if (\Drupal::currentUser()->id() == 1) {
        dpm('no found this term_name - ' . $term_name . ' - in vocabulary - ' . $vocabulary . ' on getTidByTermName()');
      }
    }

    return $output;
  }

  /**
   * @return array, term tids
   \Drupal::getContainer()->get('flexinfo.term.service')->getTidsFromFullTerms($terms);
   */
  public function getTidsFromFullTerms($terms = array()) {
    $tids = array();

    if (is_array($terms)) {
      foreach ($terms as $term) {
        $tids[] = $term->id();
      }
    }

    return $tids;
  }

  /**
   * @return array, term tids
   */
  public function getTidsFromTermTree($term_tree = array()) {
    $output = array();

    if (is_array($term_tree)) {
      foreach ($term_tree as $term) {
        $output[] = $term->tid;
      }
    }

    return $output;
  }

  /**
   * @return array, term tids
   \Drupal::getContainer()->get('flexinfo.term.service')->getTidsFromVidName($vid);
   */
  public function getTidsFromVidName($vid = NULL) {
    $trees = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid, 0);
    $tids = $this->getTidsFromTermTree($trees);

    return $tids;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.term.service')->sortTermByTermName($terms);
   */
  public function sortTermByTermName($terms = array(), $asc_order = TRUE) {
    $output = array();
    if ($terms) {
      foreach ($terms as $term) {
        $sort_terms[$term->getName()] = $term;
      }

      if ($asc_order) {
        ksort($sort_terms);
      }
      else {
        krsort($sort_terms);
      }

      $output = array_values($sort_terms);
    }

    return $output;
  }

  /**
   *
   */
  public function sortTreeByTermName($tree = array(), $asc_order = TRUE) {
    $output = array();
    if ($tree) {
      foreach ($tree as $term) {
        $sort_terms[$term->name] = $term;
      }

      if ($asc_order) {
        ksort($sort_terms);
      }
      else {
        krsort($sort_terms);
      }

      $output = array_values($sort_terms);
    }

    return $output;
  }

  /** - - - - - - Term Link - - - - - - - - - - - - - - - - - - - - - - - - - -  */
  /**
   * @param tid
   */
  public function getTermAddLink($vid = NULL, $link_text = 'Add') {
    $link = NULL;

    if ($vid) {
      $url = Url::fromUserInput('/admin/structure/taxonomy/manage/' . $vid . '/add');
      $link = \Drupal::l(t($link_text), $url);
    }

    return $link;
  }

  /**
   * @param tid
   */
  public function getTermEditLink($tid = NULL, $link_text = 'Edit') {
    $link = NULL;

    if ($tid) {
      $url = Url::fromUserInput('/taxonomy/term/' . $tid . '/edit');
      $link = \Drupal::l(t($link_text), $url);
    }

    return $link;
  }

  /**
   * @param tid
   */
  public function getTermViewLink($tid = NULL, $view_text = 'View') {
    $link = NULL;

    if ($tid) {
      $url = Url::fromUserInput('/taxonomy/term/' . $tid);
      $link = \Drupal::l(t($view_text), $url);
    }

    return $link;
  }

  /** - - - - - - Field - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return City term tid
   */
  public function getTidByCityNameAndProvinceTid($term_name = NULL, $vocabulary = NULL, $province_tid = NULL) {
    $output = NULL;

    $terms = taxonomy_term_load_multiple_by_name($term_name, $vocabulary);

    // only one city or none
    if (count($terms) < 2) {
      $output = $this->getTidByTermName($term_name, $vocabulary);
    }
    else {
      foreach ($terms as $term) {
        $get_province_tid = $term->get('field_city_province')->target_id;

        // check provice name match when city name duplicate
        if ($get_province_tid == $province_tid) {
          $output = $term->get('tid')->value;
        }
      }
    }

    if (empty($output)) {
      if (\Drupal::currentUser()->id() == 1) {
        dpm('not found this city term_name - ' . $term_name . ' in vocabulary is - ' . $vocabulary . ' at getTidByCityNameAndProvinceTid()');
      }
    }

    return $output;
  }
  /**
   * @return City term tid
   */
  public function getTidByCityNameAndProvinceName($term_name = NULL, $vocabulary = NULL, $province_name = NULL) {
    $output = NULL;

    $province_terms = taxonomy_term_load_multiple_by_name($province_name, $vocabulary = 'province');
    if (count($province_terms) > 0) {
      $province_term = reset($province_terms);   // province should not have duplicate name
      $province_tid = $province_term->get('tid')->value;

      $output = $this->getTidByCityNameAndProvinceTid($term_name, $vocabulary = 'city', $province_tid);
    }

    return $output;
  }

  /**
   * @param Question Library Name, $vocabulary, FieldType Tid
   * @return QuestionLibrary term tid
   */
  public function getTidByQuestionNameAndFieldTypeTid($term_name = NULL, $vocabulary = NULL, $fieldtype_tid = NULL) {
    $output = NULL;

    $terms = taxonomy_term_load_multiple_by_name($term_name, $vocabulary);

    // only one city or none
    if (count($terms) < 2) {
      $output = $this->getTidByTermName($term_name, $vocabulary);
    }
    else {
      foreach ($terms as $term) {
        // check provice name when city name duplicate
        $get_fieldtype_tid = $term->get('field_queslibr_fieldtype')->target_id;

        if ($get_fieldtype_tid == $fieldtype_tid) {
          $output = $term->get('tid')->value;
        }
      }
    }

    return $output;
  }
  /**
   * @param Question Library Name, $vocabulary, FieldType Tid
   * @return QuestionLibrary term tid
   */
  public function getTidByQuestionNameAndFieldTypeName($term_name = NULL, $vocabulary = NULL, $fieldtype_name = NULL) {
    $output = NULL;

    $terms = taxonomy_term_load_multiple_by_name($term_name, $vocabulary);

    $fieldtype_terms = taxonomy_term_load_multiple_by_name($fieldtype_name, 'fieldtype');
    if (count($fieldtype_terms) > 0) {
      $term = reset($fieldtype_terms);

      $get_fieldtype_tid = $term->get('tid')->value;

      $output = $this->getTidByQuestionNameAndFieldTypeTid($term_name, $vocabulary, $get_fieldtype_tid);
    }
    else {
      if (\Drupal::currentUser()->id() == 1) {
        dpm('not found this question_name - ' . $term_name . ' in vocabulary is - ' . $vocabulary . ' at getTidByQuestionNameAndFieldTypeName()');
      }
    }

    return $output;
  }

  /** - - - - - - By - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @param
   * @return businessunit term
   */
  public function getBuTermFromProgramTid($program_tid) {
    $output = NULL;

    $program_entity = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($program_tid);

    if ($program_entity) {
      $output = $this->getBuTermFromProgramTerm($program_entity);
    }

    return $output;
  }

  /**
   * @param
   * @return businessunit term
   */
  public function getBuTermFromProgramTerm($program_entity) {
    $output = NULL;

    if ($program_entity) {
      $theraparea_entity = \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstTargetIdTermEntity($program_entity, 'field_program_theraparea');

      if ($theraparea_entity) {
        $output = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstTargetIdTermEntity($theraparea_entity, 'field_theraparea_businessunit');
      }
    }

    return $output;
  }

}
