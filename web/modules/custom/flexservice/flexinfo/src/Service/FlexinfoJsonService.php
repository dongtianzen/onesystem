<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoJsonService.php.
 */
namespace Drupal\flexinfo\Service;

/**
 * An example Service container.
 */
class FlexinfoJsonService {

  /**
   * @deprecated by 2017 Nov
   * @see use fetchConvertJsonToArrayFromInternalPath() or fetchConvertJsonToArrayFromUrl()
   *
   * @return array, terms entity
   \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArray();

   * @notice
     In JSON file, every last array can't include comma sign
   */
  public function fetchConvertJsonToArray($file_url = NULL) {
    global $base_url;
    $feed_url = $base_url . $file_url;

    $response = \Drupal::httpClient()->get($feed_url, array('headers' => array('Accept' => 'text/plain')));
    $data = $response->getBody();

    $output = json_decode($data, TRUE);
    return  $output;
  }

  /**
   * @note that json_last_error is supported in PHP >= 5.3.0 only.
   * @see https://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php
   */
  function jsonValidate($string) {
    // \jsondecode() means use PHP json_decode() instead of use Drupal's
    $result = \json_decode($string);

    // switch and check possible JSON errors
    switch (json_last_error()) {
      case JSON_ERROR_NONE:
          $error = ''; // JSON is valid // No error has occurred
          break;
      case JSON_ERROR_DEPTH:
          $error = 'The maximum stack depth has been exceeded.';
          break;
      case JSON_ERROR_STATE_MISMATCH:
          $error = 'Invalid or malformed JSON.';
          break;
      case JSON_ERROR_CTRL_CHAR:
          $error = 'Control character error, possibly incorrectly encoded.';
          break;
      case JSON_ERROR_SYNTAX:
          $error = 'Syntax error, malformed JSON.';
          break;
      // PHP >= 5.3.3
      case JSON_ERROR_UTF8:
          $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
          break;
      // PHP >= 5.5.0
      case JSON_ERROR_RECURSION:
          $error = 'One or more recursive references in the value to be encoded.';
          break;
      // PHP >= 5.5.0
      case JSON_ERROR_INF_OR_NAN:
          $error = 'One or more NAN or INF values in the value to be encoded.';
          break;
      case JSON_ERROR_UNSUPPORTED_TYPE:
          $error = 'A value of a type that cannot be encoded was given.';
          break;
      default:
          $error = 'Unknown JSON error occured.';
          break;
    }

    if ($error !== '') {
      // throw the Exception or exit // or whatever :)
      exit($error);
      if (\Drupal::currentUser()->id() == 1) {
        dpm('jsonValidate found some error - ' . $error);
      }
    }
  }

  /**
   * @return array, terms entity
     \Drupal::getContainer()->get('flexinfo.json.service')->fetchConvertJsonToArrayFromInternalPath();
   *
   * @param $file_path, internal file path
     $file_path = '/sites/default/files/json/bi_lilly_meeting_sheet.json'
   */
  public function fetchConvertJsonToArrayFromInternalPath($file_path = NULL) {
    global $base_url;
    $feed_url = $base_url . $file_path;

    $output = $this->fetchConvertJsonToArrayFromUrl($feed_url);

    return $output;
  }

  /**
   * @param $file_path
     $file_path = 'http://lillymedical.education/superexport/meeting/entitylist'
   */
  public function fetchConvertJsonToArrayFromUrl($feed_url = NULL) {
    $response = \Drupal::httpClient()
      ->get(
        $feed_url,
        array(
          'headers' => array('Accept' => 'text/plain'),
          // 'auth' => ['admin', 'password']
        )
      );
    $data = $response->getBody();

    $output = json_decode($data, TRUE);

    if (!$output) {
      $this->jsonValidate($data);
    }

    return $output;
  }

}
