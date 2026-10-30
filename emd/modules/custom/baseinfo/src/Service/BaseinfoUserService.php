<?php

/**
 * @file
 * Contains Drupal\baseinfo\Service\BaseinfoUserService.php.
 */
namespace Drupal\baseinfo\Service;

use Drupal\flexinfo\Service\FlexinfoUserService;

/**
 * An example Service container.
 * \Drupal::getContainer()->get('baseinfo.user.service')->getUserDataDefaultCountryTid();
 */
class BaseinfoUserService extends FlexinfoUserService {

  /** - - - - - - User Data - - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return tid
   \Drupal::getContainer()->get('baseinfo.user.service')->getUserDataDefaultBusinessUnitTid();
   */
  public function getUserDataDefaultBusinessUnitTid() {
    $output = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_businessunit');

    return $output;
  }

}
