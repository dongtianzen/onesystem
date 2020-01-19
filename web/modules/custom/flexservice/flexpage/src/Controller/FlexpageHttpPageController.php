<?php

/**
 * @file
 * Contains \Drupal\flexpage\Controller\FlexpageHttpPageController.
 */

namespace Drupal\flexpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * An example controller.
 */
class FlexpageHttpPageController extends ControllerBase {

  /**
   *
   */
  public function http403Page() {
    $markup = NULL;
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center">';
        $markup .= '<div class="margin-0">';
          $markup .= '<span class="font-weight-400">';
            $markup .=  'You are not authorized to access this page.';
          $markup .= '</span>';
        $markup .= '</div>';

        if (\Drupal::currentUser()->isAnonymous()) {
          $markup .= $this->http403PageContent();
        }

      $markup .= '</div>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   *
   */
  public function http403PageContent() {
    $markup = NULL;

    if (\Drupal::currentUser()->isAnonymous()) {
      $uri = '/user/login';
      $url = Url::fromUserInput($uri)->toString();

      // $response = new RedirectResponse($url);
      // $response->send();

      // return $this->redirect('user.login');

      $markup .= '<div class="margin-0">';
        $markup .= '<span class="h6">';
          $markup .=  \Drupal::l(t('Login'), Url::fromUserInput($uri));
        $markup .= '</span>';
      $markup .= '</div>';
    }

    return $markup;
  }

  /**
   *
   */
  public function http404Page() {
    $uri = '/user/login';
    $url = Url::fromUserInput($uri)->toString();

    // $response = new RedirectResponse($url);
    // $response->send();

    $markup = NULL;
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center">';
        $markup .= '<div class="margin-0">';
          $markup .= '<span class="font-weight-400">';
            $markup .=  'Requested page not found!';
          $markup .= '</span>';
        $markup .= '</div>';

        if (\Drupal::currentUser()->isAnonymous()) {
          $markup .= '<div class="">';
            $markup .=  \Drupal::l(t('Login'), Url::fromUserInput($uri));
          $markup .= '</div>';
        }

      $markup .= '</div>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

}
