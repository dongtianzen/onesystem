<?php

namespace Drupal\ngdata\Entity\Term;

use Drupal\ngdata\Entity\NgdataEntity;

/**
 * Class NgdataTerm.
 \Drupal::service('ngdata.term')->demo();
 */
class NgdataTerm extends NgdataEntity implements NgdataTermInterface {

  /**
   * Constructs a new NgdataTerm object.
   */
  public function __construct() {

  }

  /**
   * @return array(
      'label' => [],
      'tid' => [],
    )
   */
  public function getTermListByVocabulary($vid = 'eventtype') {
    $output = array();
    $output['label'] = array();
    $output['tid'] = array();

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName($vid);

    if (is_array($terms)) {
      foreach ($terms as $key => $term) {
        $output['label'][] = $term->getName();
        $output['tid'][] = $term->id();
      }
    }

    return $output;
  }

  /**
   * @return Array data
   * @param $businessunit_tid is current businessunit tid
   */
  public function getTermTherapeuticAreaListByBu($businessunit_tid = NULL) {
    $output = array();
    $output['label'] = array();
    $output['tid'] = array();

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('therapeuticarea');
    if (is_array($terms)) {
      foreach ($terms as $key => $term) {
        $bu_tid = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldFirstTargetId($term, 'field_theraparea_businessunit');

        if ($bu_tid == $businessunit_tid) {
          $output['label'][] = $term->getName();
          $output['tid'][] = $term->id();
        }
      }
    }

    return $output;
  }

}
