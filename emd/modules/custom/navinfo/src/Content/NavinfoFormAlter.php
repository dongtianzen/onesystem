<?php

/**
 * @file Drupal\navinfo\Content\NavinfoBlockGenerator;
 */

namespace Drupal\navinfo\Content;

/**
 * An example controller.
 $NavinfoFormAlter = new NavinfoFormAlter();
 $NavinfoFormAlter->contentBlockCharts();
 */
class NavinfoFormAlter {

  /*
   *
   */
  public function contactMessageFeedbackFormNamePrefix() {
    $prefix = '';
    $prefix .= '<div class="margin-left-n-24">';
      $prefix .= '<div class="text-center">';
        $prefix .= '<div class="margin-left-n-16">';
          $prefix .= '<div class="h5">';
            $prefix .= 'CONTACT INFO';
          $prefix .= '</div>';
          $prefix .= '<div class="nav-contact-message-wrapper">';
            $prefix .= 'Feel free to contact us, if you have any questions';
          $prefix .= '</div>';
          $prefix .= '<div class="nav-contact-email-wrapper">';
            $prefix .= '<span class="">';
              $prefix .= '<a href="mailto:admin@mildder.ca?Subject=Contact" target="_top">admin@mildder.ca</a>';
            $prefix .= '</span>';
            $prefix .= '<span class="margin-left-16">';
              $prefix .= '<i class="fa fa-phone margin-right-6 color-2fa9e0" aria-hidden="true"></i>';
              $prefix .= '(416)855-9595';
            $prefix .= '</span>';
            $prefix .= '<span class="margin-left-16">';
              $prefix .= '<i class="fa fa-fax margin-right-6 color-2fa9e0" aria-hidden="true"></i>';
              $prefix .= '(416)855-9595';
            $prefix .= '</span>';
          $prefix .= '</div>';
        $prefix .= '</div>';

        $prefix .= '<div class="margin-top-48 margin-bottom-24">';
          $prefix .= '<div class="h5">';
            $prefix .= 'SEND US A MESSAGE';
          $prefix .= '</div>';
        $prefix .= '</div>';
      $prefix .= '</div>';
    $prefix .= '</div>';

    return $prefix;
  }

}
