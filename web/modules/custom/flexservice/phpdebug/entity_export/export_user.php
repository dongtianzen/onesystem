<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/entity_export/export_user.php');
  drupal_set_time_limit(0);
  _run_entity_export_user();
 */
_run_entity_export_user();
function _run_entity_export_user() {
  $output = _get_entity_users();

  $json = json_encode($output, JSON_UNESCAPED_UNICODE);
  dpm($json);
}

function _get_entity_users() {
  $output = [];

  // get all user object
  $query = \Drupal::entityQuery('user');
  $uids = $query->execute();

  $users = \Drupal::entityManager()->getStorage('user')->loadMultiple($uids);
  if (is_array($users)) {
    foreach ($users as $user) {
      $value = [
        'name' => $user->getUserName(),
        'email' => $user->getEmail(),
        'roles' => $user->getRoles(),
        // 'field' => _get_term_field_value($user, $vid),
      ];

      $output[$user->id()] = $value;
    }
  }

  return $output;
}
