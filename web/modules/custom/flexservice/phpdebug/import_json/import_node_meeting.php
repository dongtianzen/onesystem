<?php

/**
 *
 */

use Drupal\Component\Utility\Timer;

/**
 *
 require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/import_json/import_node_meeting.php');
 _run_create_meeting_from_json();
 */
function _run_create_meeting_from_json() {
  $url = 'http://lillymedical.education/node/45354?_format=json';
  $url = 'http://lillymedical.education/superexport/meeting/entitylist';
  $url = 'http://localhost/mildder8/superexport/meeting/entitylist';
  $url = 'http://localhost/mildder8/node/45354?_format=json';

  $response = \Drupal::httpClient()
    ->get(
      $url,
      array(
        'auth' => ['siteadmin', 'flexia123'],
      )
      // array('allow_redirects' => false)
    );

  $json_string = (string) $response->getBody();

  dpm($json_string);

}

function _run_create_meeting_from_json2() {
  $client = new GuzzleHttp\Client();

  $request = $client->request('GET', 'http://www.google.com');

  dpm($request);
  // $request->getEmitter()->on('before', public function (GuzzleHttp\Event\BeforeEvent $e) {
  //     echo $e->getRequest()->getUrl() . PHP_EOL;
  // });
  // $response = $client->send($request);
}


