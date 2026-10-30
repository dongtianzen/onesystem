<?php

/**
 * @file
 * Contains \Drupal\navinfo\Controller\NavinfoController.
 */

namespace Drupal\navinfo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\navinfo\Content\NavinfoContentGenerator;

/**
 * An example controller.
 */
class NavinfoController extends ControllerBase {

  /**
   *
   */
  public function dateRangeSetValueAndRedirectRefererPage($start, $end) {
    $user_id = \Drupal::currentUser()->id();
    $user_data = \Drupal::service('user.data');

    // Set user data for date range.
    $user_data->set('navinfo', $user_id, 'start_time', $start);
    $user_data->set('navinfo', $user_id, 'end_time', $end);

    // Logging date range updates.
    \Drupal::logger('daterange')->notice("$user_id set start_time $start");
    \Drupal::logger('daterange')->notice("$user_id set end_time $end");

    // Determine YTD status.
    $begin_of_today = strtotime("midnight", time());
    $end_of_today = strtotime("tomorrow", $begin_of_today);

    $is_ytd = ($start == strtotime(date('Y-01-01 00:00:00')) && ($end > $begin_of_today && $end < $end_of_today));
    $user_data->set('navinfo', $user_id, 'ytd_boolean', $is_ytd);

    // Get and validate the referer.
    $request = \Drupal::request();
    $referer = $request->headers->get('referer');
    $base_url = $request->getSchemeAndHttpHost();

    if (!$referer || strpos($referer, $base_url) !== 0) {
      $referer = $base_url;
    }

    // Redirect to the referer.
    if ($referer) {
      return new RedirectResponse($referer);
    }

    // If no referer, display a confirmation message.
    $markup = 'Updated date range from ' . $begin_of_today . ' to ' . $end_of_today . ' successfully.';
    return [
      '#type' => 'markup',
      '#markup' => $markup,
    ];
  }

  /**
   *
   */
  public function daterangeSetvalue($start, $end) {
    \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), 'start_time', $start);
    \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), 'end_time', $end);

    $message = \Drupal::currentUser()->id() .  ' start_time ' . $start;
    \Drupal::logger('daterange')->notice($message);
    $message = \Drupal::currentUser()->id() .  ' end_time ' . $end;
    \Drupal::logger('daterange')->notice($message);

    $begin_of_today = strtotime("midnight", time());
    $end_of_today   = strtotime("tomorrow", $begin_of_today);

    if ($start == strtotime(date('Y-01-01 00:00:00')) && ($end > $begin_of_today && $end < $end_of_today)) {
      \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), 'ytd_boolean', TRUE);
    }
    else {
      \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), 'ytd_boolean', FALSE);
    }

    $markup = 'Set start and end time as ' . $start . ' - ' . $end;
    $build = array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   *
   */
  public function levelfilterSetvalue($name = NULL, $value = NULL) {
    $redirect_path = \Drupal::request()->query->get('destination');

    if ($redirect_path) {
    }
    else {
      $start = \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTime();
      $end = \Drupal::getContainer()->get('flexinfo.setting.service')->userEndTime();

      $redirect_path = '/ngpage/dashboard/page/all/' . $start . '/' . $end;
    }

    $redirect_url = $this->levelfilterRedirect($name, $value, $redirect_path);

    $response = new RedirectResponse($redirect_url->toString());
    $response->send();

    $output = 'Set Levelfilter Value as ' . $name . ' - ' . $value . ' - as - ' . $redirect_path;

    $build = array(
      '#type' => 'markup',
      '#markup' => $output,
    );

    return $build;
  }

  /**
   *
   */
  public function levelfilterRedirect($name = NULL, $value = NULL, $redirect_path = NULL) {
    if ($value == 'all') {
      $value = NULL;
    }

    // set default value
    \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), $name, $value);

    // clean children level
    if ($name == 'default_businessunit') {
      \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), 'default_theraparea', NULL);
      \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), 'default_diseasestate', NULL);
    }
    if ($name == 'default_theraparea') {
      \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), 'default_diseasestate', NULL);
    }


    $url = NULL;
    if ($redirect_path) {
      $url = Url::fromUserInput($redirect_path);
    }

    return $url;
  }

  /**
   * {@inheritdoc}
   */
  public function provinceFilter() {
    $NavinfoContentGenerator = new NavinfoContentGenerator();
    $output = $NavinfoContentGenerator->provinceFilter();

    return new JsonResponse($output);

    $output = json_encode($output, JSON_PRETTY_PRINT);

    $build = array(
      '#type' => 'markup',
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
      '#markup' => $output,
    );

    return $build;
  }

  /**
   *
   */
  public function provinceSetvalue($tid) {
    // \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), 'default_province', $tid);
    $redirect_path = \Drupal::request()->query->get('destination');
    $redirect_url = $this->levelfilterRedirect($name, $value, $redirect_path);

    $response = new RedirectResponse($redirect_url->toString());
    $response->send();

    $output = 'Set Province Value as ' . $tid;

    $build = array(
      '#type' => 'markup',
      '#markup' => $output,
    );

    return $build;
  }


}
