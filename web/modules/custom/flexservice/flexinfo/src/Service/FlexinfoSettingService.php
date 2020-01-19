<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoSettingService.php.
 */
namespace Drupal\flexinfo\Service;

use Drupal\Component\Utility\Xss;

/**
 * An example Service container.
 */
class FlexinfoSettingService {

  /**
   * use Drupal\Component\Utility\Xss;
   *
   * @return Xss::getAdminTagList() + custom tags
   *
   * \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag();
   */
  public function adminTag() {
    $admin_tags = Xss::getAdminTagList();
    $admin_tags_plus = [
      'button', 'canvas', 'custom-marker',
      'form', 'fieldset',
      'info-window', 'input',
      'label',
      'map', 'marker',
      'ng-map',
      'option',
      'select',
      'slot',
      'template',
      'transition',
      'vue-modal',
      'vue-navbar', 'vue-navbar-footer',
      'vue-table-component',
      'vue-table-wrapper',
      // vue-tables-2
      'vue-client-table',
      // vue-good-table
      'vue-good-table',
      'vue-good-pagination',
      'VueGoodTable',
      //
      '<vue-grid-template>',
      'demo-grid',
    ];

    $admin_tags = array_merge($admin_tags, $admin_tags_plus);
    $admin_tags = array_merge($admin_tags, $this->adminTagMaterial());

    return $admin_tags;
  }

  /**
   * use Drupal\Component\Utility\Xss;
   *
   * @return Xss::getAdminTagList() + custom tags
   *
   * \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag();
   */
  public function adminTagMaterial() {
    $output = [
      'md-button',
      'md-card', 'md-checkbox', 'md-content',
      'md-datepicker',
      'md-icon', 'md-input-container',
      'md-layout',
      'md-menu', 'md-menu-content',
      'md-option',
      'md-progress-circular', 'md-progress-linear',
      'md-radio', 'md-radio-group', 'md-radio-button',
      'md-select', 'md-select-header',
      'md-slider', 'md-slider-container',
      'md-step', 'md-step-actions', 'md-step-body', 'md-stepper', 'md-steppers',
      'md-tab', 'md-tabs', 'md-tooltip',
    ];

    return $output;
  }

  /**
   * @to distinguish local or live site or test site
   * @return Boolean
   *
   * $is_local_environment = \Drupal::getContainer()->get('flexinfo.setting.service')->checkCurrentIsLocalEnvironment();
   */
  public function checkCurrentIsLocalEnvironment() {
    $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;

    if (file_exists($app_root . '/sites/default/settings.local.php')) {
      $output = TRUE;
    }
    else {
      $output = FALSE;
    }

    return $output;
  }

  /**
   * @return get Local base path or HTTPS site base path for files
   *  http://localhost:8888/folder/web
   *  https://www.domain.ca
   */
  public function getHttpsBaseUrl() {
    $is_local_environment = $this->checkCurrentIsLocalEnvironment();

    if ($is_local_environment) {
      global $base_url;
      $base_path_url = $base_url;

      // no HTTPS
      // global $base_url only return like http://www.domain.ca
    }
    else {
      $base_path_url = 'https://';
      $base_path_url .= \Drupal::request()->getHost();

      // here include HTTPS
      // https://www.domain.ca
    }

    return $base_path_url;
  }

  /**
   * @param $pound_sign (#), #e6e6e6
   */
  public function colorPlateOne($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '56bfb5',
      '2' => 'f24b99',
      '3' => '344a5f',
      '4' => 'bfbfbf',
      '5' => 'e6e6e6',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');

    return $output;
  }

  /**
   * @param $pound_sign (#)
     \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateTwo();
   */
  public function colorPlateTwo($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '344a5f',
      '2' => '2fa9e0',
      '3' => 'f24b99',
      '4' => '99dc3b',
      '5' => '56bfb5',
      '6' => '5577fd',
      '7' => 'f3c848',
      '8' => '4fc1ff',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign);
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateLilly($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '344a5f',
      '2' => '2fa9e0',
      '3' => '99dc3b',
      '4' => 'f3c848',
      '5' => 'f24b99',
      '6' => '56bfb5',
      '7' => '5577fd',
      '8' => '4fc1ff',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign);
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateThree($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => 'bfbfbf',
      '2' => '5577fd',
      '3' => 'f3c848',
      '4' => 'f24b99',
      '5' => '05d23e',
      '6' => '2fa9e0',   // 5
      '7' => '344a5f',
      '8' => '4fc1ff',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateFour($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '2fa9e0',
      '2' => '05d23e',
      '3' => 'f24b99',
      '4' => '99dc3b',
      '5' => '56bfb5',
      '6' => '2fa9e0',
      '7' => '344a5f',
      '8' => '4fc1ff',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
     \Drupal::getContainer()->get('flexinfo.setting.service')->colorPlateFive();
   */
  public function colorPlateFive($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '5577fd',
      '2' => 'f3c848',
      '3' => 'f24b99',
      '4' => '05d23e',
      '5' => '2fa9e0',
      '6' => 'bfbfbf',
      '7' => 'd6006e',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateSix($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '5577fd',
      '2' => '56bfb5',
      '3' => '99dc3b',
      '4' => 'f24b99',
      '5' => '2fa9e0',
      '6' => 'bfbfbf',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateSeven($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => 'bfbfbf',
      '2' => '5577fd',
      '3' => '56bfb5',
      '4' => '99dc3b',
      '5' => 'f24b99',
      '6' => '2fa9e0',   // 5
      '7' => '344a5f',
      '8' => '4fc1ff',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @deprecated by 2017 Nov
   * @see $this->colorPlateFour()
   */
  public function colorPlateDoughnut($key = NULL, $pound_sign = FALSE) {
    return $this->colorPlateFour($key, $pound_sign);
  }

  /**
   *
   */
  public function colorPlateOutput($plate_array = array(), $color_key = NULL, $pound_sign = FALSE, $default = NULL) {
    $output = NULL;

    if ($pound_sign) {
      foreach ($plate_array as $key => $value) {
        $plate_array[$key] = '#' . $value;
      }
    }

    if ($color_key || $color_key === 0) {
      if (isset($plate_array[$color_key])) {
        $output = $plate_array[$color_key];
      }
      else {
        $output = $default;          // default color for not exist key request
        if ($default && $pound_sign) {
          $output = '#' . $default;
        }
      }
    }
    else {
      $output = $plate_array;
    }

    return $output;
  }

  /**
   *
   */
  public function colorPlateOutputKeyPlusOne($plate_array = array(), $color_key = NULL, $pound_sign = FALSE, $default = NULL) {
    $color_array = $this->colorPlateOutput($plate_array, $color_key, $pound_sign, $default);

    foreach ($color_array as $key => $value) {
      $output[$key+ 1] = $color_array[$key];
    }

    return $output;
  }

  /**
   *
   */
  public function colorPlateOutputKeyByPaletteName($palette_name = NULL, $color_key = NULL, $pound_sign = FALSE, $default = NULL) {
    $output = NULL;

    $palette_term = \Drupal::getContainer()->get('flexinfo.term.service')->getTermByTermName($palette_name, 'palette');
    if ($palette_term) {
      $color_array = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllValues($palette_term, 'field_palette_rgb');

      $output = $this->colorPlateOutput($color_array, $color_key, $pound_sign, $default);
    }

    return $output;
  }

  /**
   *
   */
  public function colorPlateOutputKeyPlusOneByPaletteName($palette_name = NULL, $color_key = NULL, $pound_sign = FALSE, $default = NULL) {
    $output = NULL;

    $palette_term = \Drupal::getContainer()->get('flexinfo.term.service')->getTermByTermName($palette_name, 'palette');
    if ($palette_term) {
      $color_array = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldAllValues($palette_term, 'field_palette_rgb');

      $output = $this->colorPlateOutputKeyPlusOne($color_array, $color_key, $pound_sign, $default);
    }

    return $output;
  }

  /**
   * @return timestamp
   *
   * @param '$html_datetime' is HTML Date like 2017-12-01T19:00:00
   */
  public function convertHtmlDatetimeToTimeStamp($html_datetime) {
    $output = date_format(date_create($html_datetime, timezone_open('America/Toronto')), "U");
    return $output;
  }

  /**
   * $timestamp = date_format(date_create($date_time, timezone_open('America/Toronto')), "U");
   *
   * @param 'html_date' is HTML Date like 2017-03-24
   \Drupal::getContainer()->get('flexinfo.setting.service')->convertTimeStampToHtmlDate();
   *
   * @param $timezone default is NULL means current timezone by date_default_timezone_get();
   */
  public function convertTimeStampToHtmlDate($time_stamp, $type = 'html_date', $format = NULL, $timezone = NULL) {
    $output = NULL;
    if ($time_stamp) {
      $output = \Drupal::service('date.formatter')
        ->format($time_stamp, $type, $format, $timezone);
    }

    return $output;
  }

  /**
   * @param 'page_daytime' is HTML Date like 2018-01-08T22:44:24
   * 'page_daytime' need to be add on Date and time formats
   */
  public function convertTimeStampToQueryDate($time_stamp, $type = 'page_daytime') {
    $output = \Drupal::service('date.formatter')
      ->format($time_stamp, $type, $format = '', $timezone = 'UTC');

    return $output;
  }

  /**
   * @return array
   * Array(
   *   [0] =>
   *   [1] => manageinfo
   *   [2] => program
   *   [3] => list
   *   [4] => all
   *   [5] => 1483246800
   *   [6] => 1491019199
   * );
   *
   \Drupal::getContainer()->get('flexinfo.setting.service')->getCurrentPathArgs();
   */
  public function getCurrentPathArgs() {
    $current_path = \Drupal::service('path.current')->getPath();
    $path_args = explode('/', $current_path);

    return $path_args;
  }

  /**
   * @return array
   */
  public function getMonthNameAbb() {
    $output = array(
      "JAN",
      "FEB",
      "MAR",
      "APR",
      "MAY",
      "JUN",
      "JUL",
      "AUG",
      "SEP",
      "OCT",
      "NOV",
      "DEC"
    );

    return $output;
  }

  /**
   * @return array
   *
   */
  public function getQuarterNameAbb() {
    $output = array(
      "Q1",
      "Q2",
      "Q3",
      "Q4",
    );

    return $output;
  }

  /**
   * @return array
   *
   */
  public function getProvinceDescriptions() {
    $output = [];

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    foreach ($terms as $term) {
      $output[] = strip_tags($term->getDescription());
    }

    return $output;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.setting.service')->getPageTitle();
   */
  public function getPageTitle() {
    $output = 'Snapshot';

    $path_args = $this->getCurrentPathArgs();

    if (isset($path_args[2])) {
      $output = $path_args[2] . ' ' . $output;

      if (isset($path_args[3])) {
        $output = $path_args[2] . ' ' . $path_args[3];

        // taxonomy/term/32
        if ($path_args[1] == 'taxonomy' && $path_args[2] == 'term' && isset($path_args[3]) && is_numeric($path_args[3])) {
          $entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($path_args[3]);

          if ($entity) {
            $output = $entity->getName();
          }
        }
      }
    }

    if (isset($path_args[2])) {
      if ($path_args[2] == 'custom_feedback' || $path_args[2] == 'feedback') {
        $output = 'feedback';
      }

      if ($path_args[2] == 'eventstatus') {
        $output = 'Overview of Events';
      }
      elseif ($path_args[2] == 'eventsummary') {
        $output = 'Event Summary';
      }
      elseif ($path_args[2] == 'programsummary') {
        $output = 'Program Performance';
      }
      elseif ($path_args[2] == 'speakerrate') {
        $output = 'Speaker Performance';
      }
      elseif ($path_args[2] == 'user') {
        $output = 'User Settings';
      }
    }

    if (isset($path_args[4]) && is_numeric($path_args[4])) {
      $entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($path_args[4]);

      if ($entity) {
        $output = $entity->getName() . ' Overview';
      }

      if ($path_args[4] == 2064) {
        $output = 'National Overview';
      }
    }

    $output = ucwords($output);

    return $output;
  }

  /**
   * Checks if a string is a valid UNIX timestamp.
   *
   * @param  string $timestamp Timestamp to validate.
   *
   * @return Boolean
   */
  public function isTimestamp($timestamp) {
    $check = (is_int($timestamp) OR is_float($timestamp))
             ? $timestamp
             : (string) (int) $timestamp;

    $result = ($check === $timestamp)
              AND ( (int) $timestamp <=  PHP_INT_MAX)
              AND ( (int) $timestamp >= ~PHP_INT_MAX);

    return $result;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.setting.service')->setPageTitle();
   */
  public function setPageTitle($title = NULL) {
    $request = \Drupal::request();
    if ($route = $request->attributes->get(\Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT)) {
      $route->setDefault('_title', $title);
    }
  }

  /**
   * @param 403, 404
   *
   \Drupal::getContainer()->get('flexinfo.setting.service')->throwExceptionPage(403);
   */
  public function throwExceptionPage($code = NULL) {
    if ($code) {
      switch ($code) {
        case 403:
          throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
          break;

        case 404:
          throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
          break;

        default:
          break;
      }
    }
  }

  /**
   * @return timestamp
   */
  public function userStartTime() {
    $user_start_time = \Drupal::service('user.data')->get('navinfo', \Drupal::currentUser()->id(), 'start_time');

    if (empty($user_start_time)) {
      // return this year first day
      $user_start_time = strtotime(date('Y', time()) . "-01-01");
    }

    return $user_start_time;
  }
  /**
   *
   \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTimeFormatDate();
   */
  public function userStartTimeFormatDate() {
    $output = $this->convertTimeStampToHtmlDate($this->userStartTime());

    return $output;
  }

  /**
   * @return get user End Time With Ytd
   */
  public function userEndTime() {
    $user_ytd_boolean = \Drupal::service('user.data')->get('navinfo', \Drupal::currentUser()->id(), 'ytd_boolean');
    if ($user_ytd_boolean) {
      $user_end_time = strtotime("now");
    }
    else {
      $user_end_time = \Drupal::service('user.data')->get('navinfo', \Drupal::currentUser()->id(), 'end_time');
    }

    if (empty($user_end_time)) {
      $user_end_time = strtotime("now");
    }

    return $user_end_time;
  }
  /**
   *
   \Drupal::getContainer()->get('flexinfo.setting.service')->userEndtTimeFormatDate();
   */
  public function userEndTimeFormatDate() {
    $output = $this->convertTimeStampToHtmlDate($this->userEndTime());

    return $output;
  }

}
