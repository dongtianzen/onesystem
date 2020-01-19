<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/debug_term.php');
  _runCreateTermsWithFieldsValue();
 */
use Drupal\Component\Utility\Timer;

function _deleteTermByVid() {
  $tids = \Drupal::entityQuery('taxonomy_term')
    ->condition('vid', 'mycustomvocab')
    ->execute();

  $controller = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
  $entities = $controller->loadMultiple($tids);
  $controller->delete($entities);

}

function _createTermSample() {
  $fields_value = array(
    array(
      'field_name' => 'field_city_province',
      'value' => array(35),
      'vid' => 'province',    // field reference term vid
    )
  );
  \Drupal::getContainer()->get('flexinfo.term.service')->entityCreateTermWithFieldsValue('Crystal Beach2', 'city', $fields_value);
}

function _runCreateTermsWithFieldsValue() {
  $fields_value = array(
    array(
      'field_name' => 'field_keyanswer_question',
      'value' => array(25),
      'vid' => 'questionlibrary',
    )
  );

  $term_names = array(
    "IPF",
    "NSIP",
    "RB-ILD",
    "DIP",
    "COP",
    "AIP",
  );

  foreach ($term_names as $term_name) {
    \Drupal::getContainer()->get('flexinfo.term.service')->entityCreateTermWithFieldsValue($term_name, $vid = 'selectkeyanswer', $fields_value);
  }
}
