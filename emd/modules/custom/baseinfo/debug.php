<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/baseinfo/debug.php');
  _showTermSample();
 */

function _showTermSample() {
  $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load(2);
  ksm($term);
  dpm($term->bundle());
  $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load(27);
  dpm($term->getName());
  dpm($term->bundle());
  $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load(20);
  dpm($term->getName());
  dpm($term->bundle());
}

function _showTermVocabulary() {
  $vid = 'businessunit';

  $vocabulary = taxonomy_vocabulary_load($vid);
  dpm($vocabulary->id());

  $vocabulary = \Drupal\taxonomy\Entity\Vocabulary::load($vid);
  dpm(2 . $vocabulary->id());

  $vocabulary = \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->load($vid);
  dpm(3 . $vocabulary->id());
}
