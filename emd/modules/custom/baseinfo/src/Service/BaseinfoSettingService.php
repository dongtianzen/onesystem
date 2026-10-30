<?php

/**
 * @file
 * Contains Drupal\baseinfo\Service\BaseinfoSettingService.php.
 */
namespace Drupal\baseinfo\Service;

use Drupal\flexinfo\Service\FlexinfoSettingService;

/**
 * An example Service container.
 */
class BaseinfoSettingService extends FlexinfoSettingService {

  /**
   * use Drupal\Component\Utility\Xss;
   *
   * @return parent::adminTag() + custom tags
   *
   * \Drupal::getContainer()->get('baseinfo.setting.service')->baseAdminTag();
   */
  public function baseAdminTag() {
    $admin_tags_plus = [
      'router-link',
      'router-view',
    ];

    $admin_tags = array_merge($admin_tags_plus, $this->adminTag());

    return $admin_tags;
  }

  /**
   * @param $pound_sign (#)
   * $legends = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateOne();
   */
  public function colorPlateForTile($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '149b5f',
      '2' => '0f69af',
      '3' => 'ffc832',
      '4' => 'e61e50',
      '5' => '69d0ff',
      '6' => '336699',
      '7' => 'ff9933',
      '8' => '339966',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   * $legends = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateOne();
   */
  public function colorPlateOne($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => 'd6006e',
      '2' => '7dba00',
      '3' => '0093d0',
      '4' => '4a245e',
      '5' => '69d0ff',
      '6' => '336699',
      '7' => 'ff9933',
      '8' => '339966',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlatePieChartOne($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '503291',
      '2' => '0f69af',
      '3' => '149b5f',
      '4' => 'ffc832',
      '5' => 'e61e50',
      '6' => 'c6c6c6',
      '7' => '',
      '8' => '',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateDoughnutChartOne($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '0093d0',
      '2' => 'e6e6e6',
      '3' => '00aeef',
      '4' => '7dba00',
      '5' => '4a245e',
      '6' => '',
      '7' => '',
      '8' => '',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateLineChartOne($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '0f69af',
      '2' => 'ffc832',
      '3' => 'e61e50',
      '4' => 'c6c6c6',
      '5' => '7dba00',
      '6' => '20ba00',
      '7' => '',
      '8' => '',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateBarChartOne($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '75d1e0',
      '2' => '0093d0',
      '3' => '616365',
      '4' => '7dba00',
      '5' => '4a245e',
      '6' => '',
      '7' => '',
      '8' => '',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateStackedBarChart($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '2fa9e0',
      '2' => '05d23e',
      '3' => 'c6c6c6',
      '4' => 'f7d417',
      '5' => 'f24b99',
      '6' => '4a245e',
      '7' => '00aeef',
      '8' => 'c6c6c6',
      '9' => '20ba00',
      '8' => '20ba00',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

  /**
   * @param $pound_sign (#)
   */
  public function colorPlateStackedBarChartByScale10($key = NULL, $pound_sign = FALSE) {
    $plate_array = array(
      '1' => '2fa9e0',
      '2' => '0099ff',
      '3' => '05d23e',
      '4' => '009900',
      '5' => 'c6c6c6',
      '6' => 'f7d417',
      '7' => 'ff9933',
      '8' => 'ff66ff',
      '9' => 'ff66cc',
      '10' => 'f24b99',
    );

    $output = $this->colorPlateOutput($plate_array, $key, $pound_sign, 'f6f6f6');
    return $output;
  }

}
