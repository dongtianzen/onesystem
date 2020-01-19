<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/import_data/entity_create_user.php');
  drupal_set_time_limit(0);
  _run_batch_entity_create_user();
 */

function _run_batch_entity_create_user() {
  $users_array = \Drupal::getContainer()
    ->get('flexinfo.json.service')
    ->fetchConvertJsonToArray('/modules/custom/flexrepo/phpdebug/import_data/entity_create_user_json.json');

  foreach ($users_array as $user_info) {
    $UserMailExist = \Drupal::getContainer()->get('flexinfo.user.service')->checkUserMailExist($user_info['email']);
    $UserNameExist = \Drupal::getContainer()->get('flexinfo.user.service')->checkUserNameExist($user_info['name']);

    if (!$UserNameExist && !$UserMailExist) {
      // _entity_create_user_save($user_info);
    }

    sleep(0.05);
  }
}

/**
 * \Drupal\user\Entity\User::create();
 */
function _entity_create_user_save($user_info = array()) {
  $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
  $user = \Drupal\user\Entity\User::create();

  // Mandatory settings
  $user->setUsername($user_info['name']);
  $user->setPassword('your_password');

  $user->setEmail($user_info['email']);
  $user->enforceIsNew();                    // Set this to FALSE if you want to edit (resave) an existing user object

  // Optional settings
  // $user->set("init", 'email');
  // $user->set("langcode", $language);
  // $user->set("preferred_langcode", $language);
  // $user->set("preferred_admin_langcode", $language);

  // $user->set("setting_name", 'setting_value');
  $user->activate();

  if (is_array($user_info['roles'])) {
    foreach ($user_info['roles'] as $role_name) {
      if ($role_name != 'authenticated') {
        $user->addRole($role_name);
      }
    }
  }

  if (is_array($user_info['field'])) {
    foreach ($user_info['field'] as $field_name => $value) {

      if ($value) {
        $field = \Drupal\field\Entity\FieldStorageConfig::loadByName('user', $field_name);

        $field_standard_type = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldStandardType();

        if ($field) {
          if (in_array($field->getType(), $field_standard_type)) {
            $user_field_value[$field_name] = $value;
          }
          elseif ($field->getType() == 'entity_reference') {
            if ($field->getSetting('target_type') == 'taxonomy_term') {
              if (is_array($value)) {
                foreach ($value as $row_value) {
                  $vocabulary_name =  \Drupal::getContainer()->get('flexinfo.field.service')->getReferenceVidByFieldName($field_name, 'user', 'user');

                  $term_tid= \Drupal::getContainer()->get('flexinfo.term.service')->getTidByTermName($row_value, $vocabulary_name);
                  $user_field_value[$field_name][] = $term_tid;
                }
              }
            }
          }
        }
        else {
          dpm('not found field type - for this field - ' . $field_name);
        }

        $user->set($field_name, $user_field_value[$field_name]);
      }
    }
  }

  // Save user
  $res = $user->save();

  // return user uid
  $uid = $user->id();
  if ($uid > 0) {
    dpm('create entity user - ' . $user_info['name'] . ' - uid - ' . $uid);
  }

}
