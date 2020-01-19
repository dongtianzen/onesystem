<?php

namespace Drupal\ngdata\Entity;

/**
 * Class NgdataEntity.
 */
class NgdataEntity implements NgdataEntityInterface {

  /**
   * @
     子类中不能定义与父类一样的方法名，或属性名
   */
  // public $getNode;
  // public $getTerm;
  // public $getUser;

  /**
   * Constructs a new NgdataEntity object.
   */
  public function __construct() {
    // $this->getNode = \Drupal::service('ngdata.node');
    // $this->getTerm = \Drupal::service('ngdata.term');
    // $this->getUser = \Drupal::service('ngdata.user');
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType() {
    return;
  }

  /**
   * @defgroup getEntityFromLoadEntityId()
   * @param $entity_type = 'node', 'taxonomy_term', 'user'
   */
  public function getEntityFromLoadEntityId($entity_type = 'node', $method_name = NULL, ...$args) {
    $entity_id = $this->{$method_name}(...$args);

    $entity = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($entity_id);

    return $entity;
  }

  /**
   * @ingroup getEntityFromLoadEntityId()
   */
  public function getEntityFromLoadNid($method_name = NULL, ...$args) {
    return $this->getEntityFromLoadEntityId('node', $method_name, ...$args);
  }

  /**
   * @ingroup getEntityFromLoadEntityId()
    $node = \Drupal::entityTypeManager()
     ->getStorage('node')
     ->load(772);
    $cc = \Drupal::service('ngdata.node.meeting')
            ->getEntityFromLoadTid('getMeetingEvaluationformTid', $node);
    dpm($cc->id());
   */
  public function getEntityFromLoadTid($method_name = NULL, ...$args) {
    return $this->getEntityFromLoadEntityId('taxonomy_term', $method_name, ...$args);
  }

  /**
   * @ingroup getEntityFromLoadEntityId()
   */
  public function getEntityFromLoadUid($method_name = NULL, ...$args) {
    return $this->getEntityFromLoadEntityId('user', $method_name, ...$args);
  }

  /**
   * @param array entitys
   */
  public function getEntityIdsFromEntities($entities = array()) {
    $ids = array();

    if (is_array($entities)) {
      foreach ($entities as $entity) {
        $ids[] = $entity->id();
      }
    }

    // array_keys looks slow than foreach
    // $nids = array_keys($entities);

    return $ids;
  }

}
