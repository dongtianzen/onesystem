<?php

/**
 * @file
 */

namespace Drupal\navinfo\Content;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Component\Utility\Html;

/**
 * An example controller.
 use Drupal\navinfo\Content\NavinfoBlockGenerator;

 $NavinfoBlockGenerator = new NavinfoBlockGenerator();
 $NavinfoBlockGenerator->contentBlockCharts();
 */
class NavinfoBlockGenerator extends NavinfoBlockMenuItem {

  /*
   * Content Block Header
   */
  public function contentNavigation() {
    $path_args = \Drupal::getContainer()->get('flexinfo.setting.service')->getCurrentPathArgs();

    $output = '';
    $output .= '<div class="z-index-900">';
      $output .= '<nav class="navbar navbar-default container padding-0 max-width-inherit width-inherit">';

        $output .= '<div class="navbar-first-wrapper height-66">';
          $output .= '<div class="navbar-toggle-button-wrapper pull-left">';
            $output .= '<a data-toggle="offcanvas" class="navbar-toggle-button">';
              $output .= '<span class="fa fa-bars fa-dedent" aria-hidden="true"></span>';
            $output .= '</a>';
          $output .= '</div>';

          $output .= '<div class="navbar-filter-wrapper float-left">';
            $output .= $this->contentNavigationStatusbar();
          $output .= '</div>';

          // $output .= '<div class="navbar-filter-wrapper pull-right">';
          //   $output .= $this->contentDatepickerDateRange();
          // $output .= '</div>';

          $output .= '<div class="navbar-filter-wrapper pull-right">';
            $output .= $this->contentProvinceFilters();
          $output .= '</div>';

          $output .= '<div class="navbar-useroptions-wrapper pull-right">';
            $output .= $this->contentNavigationUserOptions();
          $output .= '</div>';

          // $output .= '<div class="navbar-customerlink-wrapper pull-right">';
          //   $output .= '<div class="padding-top-24 padding-right-24">';
          //     $output .= \Drupal::l(t('HELP LINK'), \Drupal\Core\Url::fromUri('https://teams.abbvienet.com/rnd/MAPE/ECE/MEPOD/Pages/Home.aspx'));
          //   $output .= '</div>';
          // $output .= '</div>';
        $output .= '</div>';

        if (isset($path_args[1]) && $path_args[1] != 'exportinfo') {
          $output .= '<div class="navbar-second-wrapper float-left width-pt-100 min-height-66 clear-both">';
            $output .= '<div class="margin-left-48">';
              $output .= $this->navLevelFilter();
            $output .= '</div>';
          $output .= '</div>';
        }

      $output .= '</nav>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function contentProvinceFiltersForm($default_provinces, $image_canada_render_array) {
    $form = \Drupal::formBuilder()->getForm('\Drupal\navinfo\Form\ProvinceFilterForm');

    $output = '';
    if ($default_provinces) {
      $output .= '<li>';
        $output .= '<a href="' . base_path() . 'navinfo/levelfilter/setvalue/default_province/all">';
          $output .= '<span class="province-flag-image float-left margin-top-4">';
            $output .= render($image_canada_render_array);
          $output .= '</span>';
          $output .= '<span class="margin-left-6 float-left height-24 line-height-24">';
            $output .= 'All Province';
          $output .= '</span>';
        $output .= '</a>';
      $output .= '</li>';
    }
    $output .= '</li>';
      $output .= render($form);
    $output .= '</li>';

    return $output;
  }

  public function contentProvinceFilters() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('province');

    $image_canada_file = 'canada.png';
    $image_canada_render_array = [
      '#theme' => 'image_style',
      '#style_name' => 'thumbnail',
      '#uri' => 'public://images/' . $image_canada_file,
      '#width' => 30,
      '#height' => 26,
      // optional parameters
    ];

    #
    $default_name = 'All Province';
    $default_image_render_array = $image_canada_render_array;

    $default_provinces = \Drupal::service('user.data')->get('navinfo', \Drupal::currentUser()->id(), 'default_province');

    if ($default_provinces && count($default_provinces) > 0) {
      $default_name = 'Multiple Province';
    }

    $output = '';
    $output = '<div class="navbar-nav-province-filter">';
      $output .= '<ul class="nav navbar-nav navbar-right margin-0">';
        $output .= '<li class="dropdown">';
          $output .= '<a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';
            $output .= '<span class="margin-left-12 float-left">';
              $output .= $default_name;
            $output .= '</span>';
            if (!$default_provinces) {
              $output .= '<span class="province-flag-image float-left margin-top-10 margin-left-6">';
                $output .= render($default_image_render_array);
              $output .= '</span>';
            }
            $output .= '<span class="padding-left-12 float-right">';
              $output .= '<i class="sidebar-nav-icon fa fa-angle-down"></i>';
            $output .= '</span>';
          $output .= '</a>';

          // province term and flag
          $output .= '<ul id="menu1" class="dropdown-menu nav-user-dropdown-menu">';
            $output .= $this->contentProvinceFiltersForm($default_provinces, $image_canada_render_array);
            // $output .= $this->contentProvinceFiltersJs($terms, $default_provinces, $image_canada_render_array);
          $output .= '</ul>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</div>';

    return $output;
  }

  /*
   * Content Block Header
   */
  public function contentSideMenu() {
    $output = '';
    $output .= '<div class="clear-both metismenu-wrapper">';
      $output .= '<div class="row-offcanvas row-offcanvas-left">';
        $output .= $this->contentMetisMenu();
      $output .= '</div>';
    $output .= '</div>';
    return $output;
  }

}

/**
 *
 */
class NavinfoBlockMenuItem {

  /**
   *
   */
  public function contentDatepicker() {
    $user_end_time = \Drupal::getContainer()->get('flexinfo.setting.service')->userEndTimeFormatDate();
    $user_start_time = \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTimeFormatDate();

    $output = '';
    $output .= '<div class="datepicker-prefix-wrapper">';
      $output .= '<div id="datepicker-wrapper" class="pull-right margin-top-22 margin-right-48 line-height-32">';
        $output .= '<span class="font-size-14 padding-12 padding-left-24">';
          $output .= '<i class="sidebar-nav-icon fa fa-calendar padding-right-10 color-00aeef"></i>';
        $output .= '</span>';
        $output .= '<span class="naveinfo-date-block color-616365">' . $user_start_time . ' - ' . $user_end_time . ' </span>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function contentDatepickerDateRange() {
    $user_end_time = \Drupal::getContainer()->get('flexinfo.setting.service')->userEndTimeFormatDate();
    $user_start_time = \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTimeFormatDate();

    $output = '';
    $output .= '<div class="datepicker-prefix-wrapper">';
      $output .= '<div id="datepicker-wrapper" class="pull-right margin-right-48">';
        $output .= '<div class="margin-top-12">';
          $output .= '<span class="naveinfo-date-block-title">Date Range</span>';
        $output .= '</div>';
        $output .= '<span class="naveinfo-date-block color-616365">' . $user_start_time . ' - ' . $user_end_time . ' </span>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getDateRangeSpanText() {
    $current_year = date("Y");

    $begin_of_today = strtotime("midnight", time());
    $end_of_today = strtotime("tomorrow", $begin_of_today);

    $user_start_timestamp = \Drupal::getContainer()->get('flexinfo.setting.service')->userStartTime();
    $user_end_timestamp = \Drupal::getContainer()->get('flexinfo.setting.service')->userEndTime();

    // Define ranges dynamically
    $quarters = [
      1 => ['start' => strtotime("$current_year-01-01"), 'end' => strtotime("$current_year-03-31 23:59:59")],
      2 => ['start' => strtotime("$current_year-04-01"), 'end' => strtotime("$current_year-06-30 23:59:59")],
      3 => ['start' => strtotime("$current_year-07-01"), 'end' => strtotime("$current_year-09-30 23:59:59")],
      4 => ['start' => strtotime("$current_year-10-01"), 'end' => strtotime("$current_year-12-31 23:59:59")],
    ];

    // Match ranges
    foreach ($quarters as $quarter => $range) {
      if ($user_start_timestamp == $range['start'] && $user_end_timestamp == $range['end']) {
        return "$current_year Q$quarter";
      }
    }

    // Match yearly range
    if ($user_start_timestamp == strtotime("$current_year-01-01") &&
        $user_end_timestamp == strtotime("$current_year-12-31 23:59:59")) {
      return "$current_year Year";
    }
    if ($user_start_timestamp == strtotime(($current_year - 1) . "-01-01") &&
        $user_end_timestamp == strtotime(($current_year - 1) . "-12-31 23:59:59")) {
      return ($current_year - 1) . " Year";
    }

    // Match YTD
    if ($user_start_timestamp == strtotime("$current_year-01-01") &&
        $user_end_timestamp > $begin_of_today && $user_end_timestamp < $end_of_today) {
      return 'YTD';
    }

    // Default: Custom range
    $service = \Drupal::getContainer()->get('flexinfo.setting.service');
    return $service->convertTimeStampToHtmlDate($user_start_timestamp, 'american_format') .
           ' - ' .
           $service->convertTimeStampToHtmlDate($user_end_timestamp, 'american_format');
  }

  /**
   *
   */
  public function contentDatepickerForLevel() {
    $output = '';
    $output .= '<div class="datepicker-prefix-wrapper">';
      $output .= '<div id="datepicker-wrapper" class="float-left margin-top-n-2">';
        $output .= '<span class="naveinfo-date-block color-009ddf">' . $this->getDateRangeSpanText() . ' </span>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /*
   *
   */
  public function contentMetisMenu() {
    $output = '';
    $output .= '<div class="column sidebar-offcanvas">';
      $output .= '<nav class="sidebar-nav">';
        $output .= '<ul class="metismenu" id="metis-menu">';
          $output .= $this->contentMetisMenuBasicItems();

          $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
          if ($user->hasRole('siteadmin') || $user->hasRole('administrator')) {
            $output .= $this->contentManageinfoMenuItems();
          }
        $output .= '</ul>';

      $output .= '</nav>';
    $output .= '</div>';

    return $output;
  }

  /*
   *
   */
  public function contentMetisMenuBasicItems() {
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

    $logo_path = base_path() . drupal_get_path('module', 'navinfo') . '/image/emd_logo.png';

    $output = '';
    $output .= '<li class="sidebar-nav-logo-wrapper">';
      $output .= '<a class="sidebar-site-logo sidebar-nav-logo-img-big padding-0 height-66 text-align-center" href="' . base_path() . 'ngpage/dashboard/page/all">';
        $output .= '<span class="sidebar-nav-logo">';
          $output .= '<img src="' . $logo_path . '" class="height-52 width-pt-100" alt="Logo">';
        $output .= '</span>';
      $output .= '</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="" href="' . base_path() . 'ngpage/dashboard/page/all">';
        $output .= '<i class="sidebar-nav-icon fas fa-tachometer-alt"></i>';
        $output .= '<span class="sidebar-nav-item collapse in margin-left-6">Dashboard</span>';
      $output .= '</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="" href="' . base_path() . 'ngpage/metric/page/all">';
        $output .= '<i class="sidebar-nav-icon fas fa-chart-line"></i>';
        $output .= '<span class="sidebar-nav-item collapse in margin-left-6">Key Questions</span>';
      $output .= '</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="" href="' . base_path() . 'ngpage/programEngagement/page/all">';
        $output .= '<i class="sidebar-nav-icon fas fa-code-branch"></i>';
        $output .= '<span class="sidebar-nav-item collapse in margin-left-6">Program Engagement</span>';
      $output .= '</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="" href="' . base_path() . 'htmlpage/program-chair-evaluation/page/1340/1727755200/1735707599">';
        $output .= '<i class="sidebar-nav-icon fas fa-code-branch"></i>';
        $output .= '<span class="sidebar-nav-item collapse in margin-left-6">Faculty Evaluation</span>';
      $output .= '</a>';
    $output .= '</li>';

    // $output .= '<li class="metismenu-item-link">';
    //   $output .= '<a class="" href="' . base_path() . 'navinfo/levelfilter/setvalue/default_businessunit/2?destination=/ngpage/businessunit/page/2">';
    //     $output .= '<i class="sidebar-nav-icon fas fa-syringe"></i>';
    //     $output .= '<span class="sidebar-nav-item collapse in margin-left-6">Immunology</span>';
    //   $output .= '</a>';
    // $output .= '</li>';

    // $output .= $this->contentDivisonPihMenuItems();

    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="" href="' . base_path() . 'ngpage/eventlist/page/all">';
        $output .= '<i class="sidebar-nav-icon fa fa-calendar"></i>';
        $output .= '<span class="sidebar-nav-item collapse in margin-left-6">View Events</span>';
      $output .= '</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="" href="' . base_path() . 'ngpage/programlist/page/all">';
        $output .= '<i class="sidebar-nav-icon fa fa-sticky-note"></i>';
        $output .= '<span class="sidebar-nav-item collapse in margin-left-6">View Programs</span>';
      $output .= '</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="" href="' . base_path() . 'ngpage/speakerlist/page/all">';
        $output .= '<i class="sidebar-nav-icon fa fa-users"></i>';
        $output .= '<span class="sidebar-nav-item collapse in margin-left-6">Speaker List</span>';
      $output .= '</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="" href="' . base_path() . 'exportinfo/guide/page/all">';
        $output .= '<i class="sidebar-nav-icon fa fa-comments"></i>';
        $output .= '<span class="sidebar-nav-item collapse in margin-left-6">HCP Comments</span>';
      $output .= '</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="" href="' . base_path() . 'dashpage/basic/map/all/22/33">';
        $output .= '<i class="sidebar-nav-icon fas fa-map"></i>';
        $output .= '<span class="sidebar-nav-item collapse in margin-left-6">Maps</span>';
      $output .= '</a>';
    $output .= '</li>';

    return $output;
  }

  /*
   *
   */
  public function contentDivisonPihMenuItems() {
    $output = '';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="has-arrow" href="#" aria-expanded="false">';
        $output .= '<i class="sidebar-nav-icon fa fa-plus-square fa-3"></i>';
        $output .= '<span class="sidebar-nav-item collapse in hidden-xs margin-left-6">Immunology</span>';
      $output .= '</a>';
      $output .= '<ul aria-expanded="false" class="metismenu-item-link-ul-second-level">';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'navinfo/levelfilter/setvalue/default_businessunit/27">IM</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'navinfo/levelfilter/setvalue/default_businessunit/28">I&I</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'navinfo/levelfilter/setvalue/default_businessunit/2173">RD</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'navinfo/levelfilter/setvalue/default_businessunit/2174">Vaccines</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'navinfo/levelfilter/setvalue/default_businessunit/2175">Oncology</a>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</li>';

    return $output;
  }

  /*
   *
   */
  public function contentManageinfoMenuItems() {
    $output = '';
    $output .= '<li class="metismenu-item-link active">';
      $output .= '<a class="has-arrow" href="#" aria-expanded="true">';
        $output .= '<span class="sidebar-nav-icon fa fa-cogs fa-3"></span>';
        $output .= '<span class="sidebar-nav-item collapse in hidden-xs">Manage</span>';
      $output .= '</a>';
      $output .= '<ul aria-expanded="false" class="collapse in">';
        $output .= $this->contentManageinfoMenuItemsSecondLevel();
      $output .= '</ul>';
    $output .= '</li>';

    return $output;
  }

  /*
   *
   */
  public function contentManageinfoMenuItemsSecondLevel() {
    $output = '';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/program">Program</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a href="' . base_path() . 'ngpage/standardnode/page/meeting">Meeting</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a href="' . base_path() . 'ngpage/standarduser/page/user">User</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/questionlibrary">Question</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/evaluationform">Evaluation Form</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/questionevalform">Question / Evaluation</a>';
    $output .= '</li>';
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/evaluationlayout">Evaluation Layout</a>';
    $output .= '</li>';

    $output .= $this->contentManageinfoMenuItemsThirdLevel();

    return $output;
  }

  /*
   *
   */
  public function contentManageinfoMenuItemsThirdLevel() {
    $output = '';

    // third level
    $output .= '<li class="metismenu-item-link">';
      $output .= '<a class="has-arrow" href="#" aria-expanded="false">Other</a>';
      $output .= '<ul aria-expanded="false">';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/BusinessUnit">BusinessUnit</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/therapeuticarea">Theraparea</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/chartlegend">Chart Legend</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/questiontype">Question Type</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/selectkeyanswer">Selectkey Answer</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/rankinganswer">Ranking Answer</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/Province">Province</a>';
        $output .= '</li>';
        $output .= '<li class="metismenu-item-link">';
          $output .= '<a href="' . base_path() . 'ngpage/standardterm/page/City">City</a>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</li>';

    return $output;
  }

  /**
   * Implements statusbar content
   */
  public function contentNavigationStatusbar() {
    $output = '';
    $output .= '<div class="margin-top-6 color-616365">';
      $output .= '<span class="pull-left float-left">';
        $output .= '<span class="header-page-title-wrapper">';
          $output .= 'EMD Medical Education Dept Dashboard';
        $output .= '</span>';
      $output .= '</span>';

    $output .= '</div>';

    return $output;
  }

  /**
   * Implements statusbar content
   */
  public function getNavigationStatusbarPageTitle() {
    $path_args = \Drupal::getContainer()->get('flexinfo.setting.service')->getCurrentPathArgs();
    $user = \Drupal::service('current_user');

    $output = "Dashboard";
    if ($user) {
      $output = $user->getAccountName() . "'s " . $output;
    }

    if ($path_args[1] == 'dashpage') {
      if ($path_args[3] == 'snapshot') {
        $output = 'Business Overview';
      }
      elseif ($path_args[3] == 'map') {
        $output = 'Google Map View';
      }

      if ($path_args[2] == 'meeting') {
        $output = 'Event View';
      }
      elseif ($path_args[2] == 'program') {
        $output = 'Program View';

        if (isset($path_args[4])) {
          $program_entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($path_args[4]);

          if ($program_entity) {
            $output = $program_entity->getName() . ' - Program View';
          }
        }
      }
    }
    elseif ($path_args[1] == 'exportinfo') {
      if ($path_args[2] == 'program') {
        $output = 'HCP Comments';
      }
    }

    return $output;
  }

  /**
   *
   */
  public function contentNavigationUserOptions() {
    $output = '';
    $output = '<div class="nav-user-option-wrapper">';
      $output .= '<ul class="nav navbar-nav navbar-right margin-0">';
        $output .= '<li class="dropdown">';
          $output .= '<a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';
            $output .= '<i class="sidebar-nav-icon fa fa-user fa-lg"></i>';
            $output .= '<span class="margin-left-12">';
              $output .= \Drupal::currentUser()->getUsername();
            $output .= '</span>';
            $output .= '<span class="padding-left-24 float-right">';
              $output .= '<i class="sidebar-nav-icon fa fa-angle-down"></i>';
            $output .= '</span>';
          $output .= '</a>';
          $output .= '<ul id="menu1" class="dropdown-menu nav-user-dropdown-menu">';
            if (\Drupal::currentUser()->id() == 1) {
              $output .= '<li checked="">';
                $output .= '<span>';
                  $output .= '<span class="fas fa-pencil-alt"></span>';
                  $output .=  \Drupal::getContainer()->get('flexinfo.user.service')->getUserEditLinkByFlexform(\Drupal::currentUser()->id());
                $output .= '</span>';
              $output .= '</li>';
            }
            $output .= '<li>';
              $output .= '<a href="' . base_path() . 'user/logout?destination=user/login">';
                $output .= '<i class="fas fa-sign-in-alt"></i>';
                $output .= '<span class="margin-left-6">Logout</span>';
              $output .= '</a>';
            $output .= '</li>';
          $output .= '</ul>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function levelFilterForDateRange($title = NULL) {
    $output = '';
    $output .= '<div class="col-sm-12 col-md-4">';
      $output .= '<div class="padding-left-12">';
        $output .= '<div class="text-left margin-right-12 margin-top-10">';
          $output .= '<span class="font-weight-460">';
            $output .= $title;
          $output .= '</span>';
        $output .= '</div>';
        $output .= '<div class="level-filter-dropdown-wrapper margin-right-12">';
          $output .= $this->contentDatepickerForLevel();
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @return timestamp
   * return this year first day
   */
  public function getYTDStartTimestamp() {
    $output = strtotime(date('Y', time()) . "-01-01");
    return $output;
  }

  /**
   * @return timestamp
   * return next year first timestamp - 1
   */
  public function getThisYearLastTimestamp() {
    $output = strtotime((date('Y', time()) + 1) . "-01-01") - 1;
    return $output;
  }

  /**
   *
   */
  public function levelFilterForDateRangeExpand($title = NULL) {
    $output = '';

    $ytd_start_timestamp = $this->getYTDStartTimestamp();
    $current_timestamp = strtotime("now");
    $ytd_end_timestamp = $current_timestamp;

    $year = date("Y", $current_timestamp);
    $month = date('m', $current_timestamp);

    $quarter = ceil($month / 3);

    $this_quarter_start_timestamp = strtotime($year . "-" . ((3 * $quarter) - 2) . "-01");
    if ($quarter < 4) {
      $this_quarter_end_timestamp = strtotime($year . "-" . ((3 * $quarter) + 1) . "-01") - 1;
    }
    else {
      $this_quarter_end_timestamp = $this->getThisYearLastTimestamp();
    }

    $current_page_path = '/navinfo/daterange/redirect-referer/';
    $ytd_path = $current_page_path . $ytd_start_timestamp . '/' . $ytd_end_timestamp;
    $ytd_url = Url::fromUserInput($ytd_path);

    $this_quarter_path = $current_page_path . $this_quarter_start_timestamp . '/' . $this_quarter_end_timestamp;
    $this_quarter_url = Url::fromUserInput($this_quarter_path);

    $this_year_path = $current_page_path . $ytd_start_timestamp . '/' . $this->getThisYearLastTimestamp();
    $this_year_url = Url::fromUserInput($this_year_path);

    $daterange_set_url = Url::fromUserInput('/navinfo/daterange/set/form/page');

    $output .= '<div class="float-left min-width-160 col-sm-6 col-md-4 col-lg-3">';
      $output .= '<div class="margin-left-12 margin-top-10">';
        $output .= '<span class="font-weight-460">';
          $output .= "Date Range";
        $output .= '</span>';
      $output .= '</div>';
      $output .= '<div class="level-filter-dropdown-wrapper">';
        $output .= '<ul class="nav navbar-nav margin-0">';
          $output .= '<li class="dropdown padding-top-n-12">';

            $output .= '<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">';
              $output .= '<span class="color-009ddf">';
                $output .= $this->getDateRangeSpanText();
              $output .= '</span>';
              $output .= '<span class="padding-left-24 margin-left-6">';
                $output .= '<i class="sidebar-nav-icon fa fa-caret-down"></i>';
              $output .= '</span>';
            $output .= '</a>';

            $output .= '<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">';
              $output .= '<div class="panel-body">';
                $output .= '<div class="row">';
                  $output .= '<div class="col-xs-12">';
                    $output .= '<div class="ranges">';
                      $output .= '<ul class="margin-top-10">';
                        $output .= '<li data-range-key="2024 Year" class="padding-6">';
                          $output .= '<a href="' . $this_year_url->toString() . '">';
                            $output .= $year . ' Year';
                          $output .= '</a>';
                        $output .= '</li>';
                        $output .= '<li data-range-key="YTD" class="padding-6">';
                          $output .= '<a href="' . $ytd_url->toString() . '">';
                            $output .= 'YTD';
                          $output .= '</a>';
                        $output .= '</li>';
                        $output .= '<li data-range-key="2024 Q1" class="padding-6">';
                          $output .= '<a href="' . $this_quarter_url->toString() . '">';
                            $output .= $year . ' Q' . $quarter;
                          $output .= '</a>';
                        $output .= '</li>';
                        $output .= '<li data-range-key="2024 Custom" class="padding-6">';
                          $output .= '<a href="' . $daterange_set_url->toString() . '">';
                            $output .= 'Custom Range';
                          $output .= '</a>';
                        $output .= '</li>';
                      $output .= '</ul>';
                    $output .= '</div>';
                  $output .= '</div>';
                $output .= '</div>';
              $output .= '</div>';
            $output .= '</div>';

          $output .= '</li>';
        $output .= '</ul>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function levelFilterByVid($vid = NULL, $set_key = NULL, $default_value = NULL, $parent_field_name = NULL, $parent_tids = array(), $show_child_option = TRUE) {
    $current_path = \Drupal::service('path.current')->getPath();

    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName($vid);

    $vocabulary = \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->load(strtolower($vid));

    $default_name = 'All';
    if ($default_value && isset($terms[$default_value])) {
      $default_name = $terms[$default_value]->getName();
    }

    $output = '';
    $output .= '<div class="float-left min-width-160 col-sm-6 col-md-2 col-lg-2">';
      $output .= '<div class="margin-left-12 margin-top-10">';
        $output .= '<span class="font-weight-460">';
          $output .= $vocabulary->getDescription();
        $output .= '</span>';
      $output .= '</div>';
      $output .= '<div class="level-filter-dropdown-wrapper">';
        $output .= '<ul class="nav navbar-nav margin-0">';
          $output .= '<li class="dropdown padding-top-n-12">';

            $output .= '<a class="dropdown-toggle padding-left-0" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';
              $output .= '<span class="color-009ddf">';
                $output .= $default_name;
              $output .= '</span>';
              $output .= '<span class="padding-left-24 float-right">';
                $output .= '<i class="sidebar-nav-icon fa fa-caret-down"></i>';
              $output .= '</span>';
            $output .= '</a>';

            if ($show_child_option) {
              $output .= '<ul class="dropdown-menu nav-user-dropdown-menu">';
                if ($default_name != 'All') {
                  $levelfilter_url = '/navinfo/levelfilter/setvalue/' . $set_key . '/' . 'all' . '?destination=' . $current_path;
                  $set_url = Url::fromUserInput($levelfilter_url);

                  $output .= '<li class="height-48">';
                    $output .= '<a href="' . $set_url->toString() . '">';
                      $output .= '<span class="margin-left-6 line-height-48">';
                        $output .= 'All';
                      $output .= '</span>';
                    $output .= '</a>';
                  $output .= '</li>';
                }

                foreach ($terms as $term) {
                  if ($parent_tids) {
                    $get_parent_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($term, $parent_field_name);

                    if (!in_array($get_parent_tid, $parent_tids) ) {
                      continue;
                    }
                  }

                  $levelfilter_url = '/navinfo/levelfilter/setvalue/' . $set_key . '/' . $term->id() . '?destination=' . $current_path;
                  $set_url = Url::fromUserInput($levelfilter_url);

                  $output .= '<li class="height-48">';
                    $output .= '<a href="' . $set_url->toString() . '">';
                      $output .= '<span class="margin-left-6 line-height-48">';
                        $output .= $term->getName();
                      $output .= '</span>';
                    $output .= '</a>';
                  $output .= '</li>';
                }
              $output .= '</ul>';
            }

          $output .= '</li>';
        $output .= '</ul>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function navLevelFilter($vid = NULL) {
    $show_child_option = TRUE;

    $path_args = \Drupal::getContainer()->get('flexinfo.setting.service')->getCurrentPathArgs();
    if (isset($path_args[2]) && $path_args[1] == 'ngpage' && $path_args[2] == 'businessunit') {
      $show_child_option = FALSE;
    }

    $default_bu = \Drupal::service('user.data')->get('navinfo', \Drupal::currentUser()->id(), 'default_businessunit');
    $default_ta = \Drupal::service('user.data')->get('navinfo', \Drupal::currentUser()->id(), 'default_theraparea');
    $default_eventtype = \Drupal::service('user.data')->get('navinfo', \Drupal::currentUser()->id(), 'default_eventtype');
    // $default_fundingsource = \Drupal::service('user.data')->get('navinfo', \Drupal::currentUser()->id(), 'default_fundingsource');

    $theraparea_businessunit_tids = array();
    if ($default_bu) {
      $theraparea_businessunit_tids = array($default_bu);
    }

    $output = '';
    $output .= '<div class="row">';
      // $output .= $this->levelFilterByVid('FundingSource', 'default_fundingsource', $default_fundingsource);
      $output .= $this->levelFilterByVid('BusinessUnit', 'default_businessunit', $default_bu, $parent_field_name = NULL, $parent_tids = array(), $show_child_option);
      $output .= $this->levelFilterByVid('TherapeuticArea', 'default_theraparea', $default_ta, 'field_theraparea_businessunit', $theraparea_businessunit_tids);
      $output .= $this->levelFilterByVid('EventType', 'default_eventtype', $default_eventtype);

      if (isset($path_args[4]) && $path_args[1] == 'htmlpage' && $path_args[2] == 'meeting' && $path_args[4] == 1781) {
        $output .= $this->levelFilterForDateRangeExpand('Date Range');
      }
      elseif (isset($path_args[3]) && $path_args[2] == 'example' && $path_args[3] == 'flatpickr') {
        // old one
        $output .= $this->levelFilterForDateRange('Date Range');
      }
      else {
        $output .= $this->levelFilterForDateRangeExpand('Date Range');
      }
    $output .= '</div>';

    return $output;
  }

}
