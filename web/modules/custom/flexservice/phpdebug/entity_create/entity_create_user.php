<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/entity_create_user.php');
  _run_batch_entity_create_user();
 */
function _run_batch_entity_create_user() {
  $users_info = _entity_user_info();
  foreach ($users_info as $user_info) {
    _entity_create_user_save($user_info);
  }
}

function _entity_user_info() {
  $users = array(
    array("name" => "zhangsan", "email" =>"zhangsan@example.com"),
    array("name" => "wangwu",   "email" =>"wangwu@example.com"),
  );

  return $users;
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
  // $user->setPassword(user_password());   // automatically set a password with the code

  $user->setEmail($user_info['email']);
  $user->enforceIsNew();                    // Set this to FALSE if you want to edit (resave) an existing user object

  // Optional settings
  // $user->set("init", 'email');
  // $user->set("langcode", $language);
  // $user->set("preferred_langcode", $language);
  // $user->set("preferred_admin_langcode", $language);

  // $user->set("setting_name", 'setting_value');
  $user->activate();

  $user->addRole('moderator');

  // Save user
  $res = $user->save();

  // return user uid
  $uid = $user->id();

  // If you want to send welcome email with out admin approval you can use after user save
  // _user_mail_notify('register_no_approval_required', $user);
}

/**
 * update
 */
use Drupal\user\Entity\User;
function _update_user_template($content) {

  // Load user with user ID
  $user = User::load($uid);

  // Load the current user.
  $user = \Drupal::currentUser();
  $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

  // Update email Id
  $user->setEmail($content['email']);

  // Update username
  $user->setUsername($content['email']);

  // Update password reset
  $user->setPassword($content['password']);

  // user role
  $user->addRole('administrator');
  $user->removeRole('administrator');

  // For User field
  $user->set("field_first_name", $firstName);
  $user->set("field_last_name", $lastName);

  //Save user
  $userss = $user->save();

  //Where "$content" array contains all user profile data
}

/**
 * load
 */
function _load_user_template() {
  // Load the current user.
  $user = \Drupal::currentUser();
  $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

  // get field data from that user
  $website = $user->get('field_website')->value;
  $body = $user->get('body')->

  $email = $user->get('mail')->value;
  $name = $user->get('name')->value;
  $uid= $user->get('uid')->value;
}


/**
 * entity_create()
 */
function _entity_create_user_template() {
  $values = array(
    'name' => 'test',        // This username must be unique and accept only a-Z,0-9, - _ @ .
    'mail' => 'test@test.com',
    'roles' => array(),
    'pass' => 'password',
    'status' => 1,
    'field_first_name' => 'Test First name',
    'fieldt_last_name' => 'Test Last name',
  );

  $account = entity_create('user', $values);
  $account->save();
}
