<?php

/**
 * @file
 */
namespace Drupal\genpdfstyle\Content;

use Drupal\genpdf\Content\FactoryChartBlockData;
use Drupal\genpdf\Content\InitialChartBlockData;

/**
 *
 */
class ChartBlock extends FactoryChartBlockData {

  public $additionYPosition = 100;

  /**
   * init page and chart parameters.
   */
  function __construct() {
    $this->chartLeftYPosition += $this->additionYPosition;
    $this->frameFirstRowYPosition += $this->additionYPosition;
    $this->frameFirstRowBottomYPosition += $this->additionYPosition;
  }

}
