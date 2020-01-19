<?php

namespace Drupal\ngdata\Atomic\Template;

use Drupal\ngdata\Atomic\NgdataAtomic;

/**
 * Class NgdataAtomicTemplateComponent.
 \Drupal::service('ngdata.atomic.template.component')->demo()
 */
class NgdataAtomicTemplateComponent extends NgdataAtomic {

  private $atom;
  private $molecule;
  private $organism;
  private $template;

  /**
   * Constructs a new NgdataAtomicTemplate object.
   */
  public function __construct() {
    $this->atom     = \Drupal::service('ngdata.atomic.atom');
    $this->molecule = \Drupal::service('ngdata.atomic.molecule');
    $this->organism = \Drupal::service('ngdata.atomic.organism');
    $this->template = \Drupal::service('ngdata.atomic.template');
  }

  /**
   *
   */
  public function getComponentSpinner() {
    $output = [];
    $output[0]["componentname"] = "loadspinner";
    $output[0]["componentcontent"] = TRUE;

    return $output;
  }

  /**
   *
   */
  public function getComponentPrimengChartjs($content) {
    $output = $this->getComponentSpinner();

    $output[1]["componentname"] = "primengchartjs";
    $output[1]["primengcontentdata"] = $content;

    return $output;
  }

  /**
   *
   */
  public function getComponentPrimengTable($content) {
    $output = [];

    $output = $this->getComponentSpinner();

    $output[1]["componentname"] = "primengtable";
    $output[1]["primengcontentdata"] = $content;

    return $output;
  }

}
