<?php

namespace Drupal\ngdata\Atomic;

/**
 * Class NgdataAtomic.
 */
class NgdataAtomic implements NgdataAtomicInterface {

  /**
   * @
     子类中不能定义与父类一样的方法名，或属性名
   */
  public $getAtom;
  public $getMolecule;
  public $getOrganism;
  public $getTemplate;
  public $getBlock;
  public $getBlockgroup;

  /**
   * Constructs a new NgdataAtomic object.
   */
  public function __construct() {
    $this->getAtom = \Drupal::service('ngdata.atomic.atom');
    $this->getMolecule = \Drupal::service('ngdata.atomic.molecule');
    $this->getOrganism = \Drupal::service('ngdata.atomic.organism');
    $this->getTemplate = \Drupal::service('ngdata.atomic.template');
    $this->getBlock = \Drupal::service('ngdata.atomic.block');
    $this->getBlockgroup = \Drupal::service('ngdata.atomic.blockgroup');
  }

}
