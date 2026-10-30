<?php

/**
 * @file
 */

namespace Drupal\navinfo\Content;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class NavinfoContentGenerator extends ControllerBase {

  /**
   *
   */
  public function countryFilter() {
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $user_default_country_tid = \Drupal::getContainer()->get('flexinfo.user.service')->getUserDataDefaultCountryTid();

    $output['navInfoMenus'] = array(
    );
    $output['navInfoMenus'][] = $this->getCountryFilterRegion($user);

    $output['navInfoSettings'] = array(
      'postUrl' => "navinfo/country/setvalue/",
      'defaultTermTid' => $user_default_country_tid,
    );

    return $output;
  }

  public function getCountryFilterRegion($user) {
    $output = array(
      "title" => t("Region"),
      "iconClass"  => "fa fa-th",
      "showMenu" => TRUE,
      "menuValues" => array(
      ),
    );

    $output['menuValues'][] = array(
      "termTid" => "5082",
      "termName" => t('National'),
    );
    $output['menuValues'][] = array(
      "termTid" => "9",
      "termName" => t('R1'),
    );
    $output['menuValues'][] = array(
      "termTid" => "10",
      "termName" => t('R2'),
    );
    $output['menuValues'][] = array(
      "termTid" => "11",
      "termName" => t('R3'),
    );

    return $output;
  }

  public function getCountryFilterHub($user) {
    $hub_trees = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('eventhub', 0);

    $output = array(
      "title" => t("Hub"),
      "iconClass"  => "fa fa-th",
      "showMenu" => FALSE,
      "menuValues" => array(
      ),
    );

    if (is_array($hub_trees) && count($hub_trees) > 0) {
      foreach ($hub_trees as $hub_term) {
        $output['menuValues'][] = array(
          "termTid" => $hub_term->tid,
          "termName" => $hub_term->name,
          "parentTid" => 2065,
        );
      }
    }

    return $output;
  }

  public function getCountryFilterCountry($user) {
    $query = \Drupal::getContainer()->get('flexinfo.queryterm.service')->queryTidsByBundle('eventcountry');
    $country_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')->runQueryWithGroup($query);
    $country_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($country_tids);

    $output = array(
      "title" => t("Country"),
      "iconClass"  => "fa fa-th",
      "showMenu" => FALSE,
      "menuValues" => array(
      ),
    );

    if (is_array($country_terms) && count($country_terms) > 0) {
      foreach ($country_terms as $country_term) {
        $hub_tid = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($country_term, 'field_eventcountry_hub');
        $output['menuValues'][] = array(
          "termTid" => $country_term->id(),
          "termName" => $country_term->getName(),
          "parentTid" => $hub_tid,
        );
      }
    }

    return $output;
  }

}
