<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoEntityService.php.
 */
namespace Drupal\flexinfo\Service;

/**
 * An example Service container.
 *
   \Drupal::getContainer()->get('flexinfo.entity.service')->getEntity($entity_type);
 */
class FlexinfoEntityService {

  /**
   * Entity
   * @param $entity_type
   */
  function getEntity($entity_type) {
    switch ($entity_type) {
      case 'calc':
        $container = \Drupal::getContainer()->get('flexinfo.calc.service');
        break;

      case 'chart':
        $container = \Drupal::getContainer()->get('flexinfo.chart.service');
        break;

      case 'entityform':
        $container = \Drupal::getContainer()->get('flexinfo.entityform.service');
        break;

      case 'field':
        $container = \Drupal::getContainer()->get('flexinfo.field.service');
        break;

      case 'json':
        $container = \Drupal::getContainer()->get('flexinfo.json.service');
        break;

      case 'node':
        $container = \Drupal::getContainer()->get('flexinfo.node.service');
        break;

      case 'querynode':
        $container = \Drupal::getContainer()->get('flexinfo.querynode.service');
        break;

      case 'queryterm':
        $container = \Drupal::getContainer()->get('flexinfo.queryterm.service');
        break;

      case 'queryuser':
        $container = \Drupal::getContainer()->get('flexinfo.queryuser.service');
        break;

      case 'setting':
        $container = \Drupal::getContainer()->get('flexinfo.setting.service');
        break;

      case 'term':
        $container = \Drupal::getContainer()->get('flexinfo.term.service');
        break;

      case 'user':
        $container = \Drupal::getContainer()->get('flexinfo.user.service');
        break;

      default:
        break;
    }

    return $container;
  }

}
