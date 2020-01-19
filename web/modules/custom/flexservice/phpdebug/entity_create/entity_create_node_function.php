<?php

/**
 * Basic function
 */

use \Drupal\node\Entity\Node;

function _load_terms($term_name, $vocabulary = NULL) {
  $output = NULL;
  $terms = taxonomy_term_load_multiple_by_name($term_name, $vocabulary);
  if (count($terms) > 0) {
    $term = reset($terms);

    $output = $term->get('tid')->value;
  }

  return $output;
}

function _load_user($user_name) {
  $output = NULL;

  if ($user_name) {
    $user = user_load_by_name($user_name);

    if (count($user) > 0) {
      $output = $user->get('uid')->value;
    }
  }

  return $output;
}

function _timestamp_convert($timestamp) {
  $output = NULL;

  if ($timestamp) {
    $output = date('Y-m-d', $timestamp);
  }

  return $output;
}
