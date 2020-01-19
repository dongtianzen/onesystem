<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoUserService.php.
 */
namespace Drupal\flexinfo\Service;

use Drupal\Core\Url;

/**
 * An example Service container.
 \Drupal::getContainer()->get('flexinfo.user.service')->checkUserNameExist($uid);
 */
class FlexinfoUserService {

  /**
   * @param, $user_info is array,
    $user_info = array(
      'name' => 'sample user',
      'email' => 'yourmail@email.com',
      'roles' => array('speaker'),
    );
   */
  public function entityCreateUser($user_info = array()) {
    if (isset($user_info['name'])) {

      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $user = \Drupal\user\Entity\User::create();

      // Mandatory settings
      $user->setUsername($user_info['name']);
      $user->setPassword('your_password');

      $user->setEmail($user_info['email']);
      $user->enforceIsNew();                    // Set this to FALSE if you want to edit (resave) an existing user object

      $user->activate();

      if ($user_info['roles'] && is_array($user_info['roles'])) {
        foreach ($user_info['roles'] as $value) {
          $user->addRole($value);
        }
      }

      // Save user
      $res = $user->save();

      // return user uid
      $uid = $user->id();

      if (\Drupal::currentUser()->id() == 1) {
        if ($uid) {
          dpm('create user - ' . $user_info['name'] . ' - uid - ' . $uid);
        }
      }

    }
  }

  /**
   * @return boolean
   \Drupal::getContainer()->get('flexinfo.user.service')->checkUserHasSpecificRolesFromUid($valid_roles, $uid);
   */
  public function checkUserHasSpecificRolesFromUid($valid_roles = array(), $uid = NULL, $authenticated = FALSE) {
    $user_roles = $this->getUserRolesFromUid($uid, $authenticated);
    $matches_array = array_intersect($valid_roles, $user_roles);

    if ($matches_array) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   *
   */
  public function checkUserMailExist($user_mail = NULL) {
    $output = FALSE;

    if ($user_mail) {
      $user_uid = $this->getUidByMail($user_mail);
      if ($user_uid) {
        $output = TRUE;
      }
    }

    return $output;
  }

  /**
   *
   */
  public function checkUserNameExist($user_name = NULL) {
    $output = FALSE;

    if ($user_name) {
      $user_uid = $this->getUidByUserName($user_name);
      if ($user_uid) {
        $output = TRUE;
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getUidByMail($mail = NULL) {
    $output = NULL;

    if ($mail) {
      $user = user_load_by_mail($mail);
      if ($user) {
        $output = $user->id();
      }
      else {
        if (\Drupal::currentUser()->id() == 1) {
          dpm('not found this user email  - ' . $mail . ' - getUidByMail()');
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getUidByUserName($user_name = NULL) {
    $output = NULL;

    if ($user_name) {
      $user = user_load_by_name($user_name);
      if ($user) {
        $output = $user->id();
      }
      else {
        if (\Drupal::currentUser()->id() == 1) {
          dpm('not found this user name - ' . $user_name . ' - getUidByUserName()');
        }
      }
    }

    return $output;
  }

  /**
   * @return timestamp
   */
  public function getUserLastLoginTime($uid = NULL) {
    $output = NULL;

    if ($uid) {
      $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
      if ($user) {
        $output = $user->getLastLoginTime();
      }
    }

    return $output;
  }

  /**
   * @return "2017-12-31T23:55:00"
   */
  public function getUserLastLoginTimeFormat($uid = NULL, $type = 'html_datetime') {
    $output = \Drupal::service('date.formatter')->format($this->getUserLastLoginTime($uid), $type, $format = '', $timezone = 'UTC');
    return $output;
  }

  /**
   * @return user name
   */
  public function getUserNameByUid($uid = NULL) {
    $output = NULL;

    if ($uid) {
      $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
      if ($user) {
        $output = $user->getUsername();
      }
    }

    return $output;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.user.service')->getUserRolesFromUid($uid);
   */
  public function getUserRolesFromUid($uid = NULL, $authenticated = FALSE) {
    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    return $this->getUserRolesFromUserObj($user, $authenticated);
  }

  /**
   *
   */
  public function getUserRolesFromUserObj($user = NULL, $authenticated = FALSE) {
    $role_names = array();
    if ($user) {
      if ($user->id() > 0) {

        $roles = $user->getRoles();
        if ($roles && is_array($roles)) {

          foreach ($roles as $key => $value) {

            if (!$authenticated) {
              if ($value == 'authenticated') {
                continue;
              }
            }

            $role_names[] = $value;
          }
        }
      }
    }

    return $role_names;
  }


  /** - - - - - - Term Link - - - - - - - - - - - - - - - - - - - - - - - - - -  */
  /**
   *
   */
  public function getUserAddLink($link_text = 'Add') {
    $url = Url::fromUserInput('/admin/people/create');
    $link = \Drupal::l(t($link_text), $url);

    return $link;
  }

  /**
   *
   */
  public function getUserEditLink($uid = NULL, $link_text = 'Edit') {
    $link = NULL;

    if ($uid) {
      $url = Url::fromUserInput('/user/' . $uid . '/edit');
      $link = \Drupal::l(t($link_text), $url);
    }

    return $link;
  }

  /**
   * @param tid
   */
  public function getUserAddLinkByFlexform($link_text = 'Add') {
    $url = Url::fromUserInput('/flexform/entityadd/user/user');
    $link = \Drupal::l(t($link_text), $url);

    return $link;
  }

  /**
   * @param tid
   */
  public function getUserEditLinkByFlexform($uid = NULL, $link_text = 'Edit') {
    $link = NULL;

    if ($uid) {
      $url = Url::fromUserInput('/flexform/entityedit/user/' . $uid);
      $link = \Drupal::l(t($link_text), $url);
    }

    return $link;
  }

  /** - - - - - - Field - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   *
   \Drupal::getContainer()->get('flexinfo.user.service')->getHubTidByCountryTid();
   */
  public function getHubTidByCountryTid($country_tid = NULL) {
    $hub_tid = NULL;

    $country_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($country_tid);
    if ($country_term) {
      $hub_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($country_term, 'field_eventcountry_hub');
    }

    return $hub_tid;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.user.service')->getRegionTidByHubTid();
   */
  public function getRegionTidByHubTid($hub_tid = NULL) {
    $region_tid = NULL;

    $hub_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($hub_tid);
    if ($hub_term) {
      $region_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($hub_term, 'field_eventhub_region');
    }

    return $region_tid;
  }

  /**
   *
   */
  public function getRegionTidByUserDataCountryTid() {
    $user_current_country_tid = $this->getUserDataDefaultCountryTid();
    $user_current_country_vid = $this->getUserDataDefaultCountryVid();

    $region_tid = NULL;
    if ($user_current_country_vid == 'eventglobal') {
      $region_tid = $user_current_country_tid;
    }
    elseif ($user_current_country_vid == 'eventregion') {
      $region_tid = $user_current_country_tid;
    }
    elseif ($user_current_country_vid == 'eventhub') {
      $region_tid = $this->getRegionTidByHubTid($user_current_country_tid);
    }
    elseif ($user_current_country_vid == 'eventcountry') {
      $user_current_hub_tid = $this->getHubTidByCountryTid($user_current_country_tid);
      $region_tid = $this->getRegionTidByHubTid($user_current_hub_tid);
    }

    return $region_tid;
  }

  /**
   *
   */
  public function getRegionTidsByUserDataCountryTid() {
    $user_current_country_vid = $this->getUserDataDefaultCountryVid();

    $region_tids = NULL;
    if ($user_current_country_vid == 'eventglobal') {
      $region_tids = \Drupal::getContainer()->get('flexinfo.term.service')->getTidsFromVidName('eventregion');
    }
    else {
      $region_tid = $this->getRegionTidByUserDataCountryTid();
      $region_tids = array($region_tid);
    }

    return $region_tids;
  }

  /** - - - - - - User Data - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   *
   \Drupal::getContainer()->get('flexinfo.user.service')->getUserDataDefaultCountryTid();
   */
  public function getUserDataDefaultCountryTid() {
    $user_default_country_tid = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_country');

    // set default
    if (empty($user_default_country_tid)) {
      $user_default_country_tid = NULL;

      if (\Drupal::hasService('baseinfo.user.service')) {
        if (method_exists(\Drupal::getContainer()->get('baseinfo.user.service'), 'getSiteDefaultCountryTid')){
          $user_default_country_tid = \Drupal::getContainer()->get('baseinfo.user.service')->getSiteDefaultCountryTid();
        }
      }
    }

    return $user_default_country_tid;
  }

  /**
   *
   */
  public function getUserDataDefaultCountryVid() {
    $term_vid = NULL;

    $user_default_country_tid = $this->getUserDataDefaultCountryTid();
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($user_default_country_tid);
    if ($term) {
      $term_vid = $term->getVocabularyId();
    }

    return $term_vid;
  }

  /**
   *
   */
  public function getUserTherapareaTidsByCurrentRegion($user = NULL) {
    $user_current_region_tids = $this->getRegionTidsByUserDataCountryTid();

    $theraparea_tids_current_region = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')->wrapperTermTidsByField('therapeuticarea', 'field_theraparea_eventregion', $user_current_region_tids, 'IN');

    $user_theraparea_tids = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllTargetIds($user, 'field_user_theraparea');

    $current_theraparea_tids = array_intersect($user_theraparea_tids, $theraparea_tids_current_region);

    return $current_theraparea_tids;
  }

}
