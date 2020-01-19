<?php

/**
 * @file
 * Contains \Drupal\flexpage\Content\FlexpageObjectContent.
 */

namespace Drupal\flexpage\Content;

use Drupal\Core\Url;
use Drupal\Component\Utility\Unicode;

use Drupal\flexpage\Content\FlexpageEventLayout;
use Drupal\flexpage\Content\FlexpageJsonGenerator;
use Drupal\flexpage\Content\FlexpageSampleGenerator;

/**
 *
 */
class FlexpageGridContent {

  public $FlexpageJsonGenerator;

  public function __construct() {
    $this->FlexpageJsonGenerator = new FlexpageJsonGenerator();
  }

}

/**
 *
 */
class FlexpageTableContent extends FlexpageGridContent {

}

/**
 *
 */
class FlexpageBlockContent extends FlexpageTableContent {

}

/**
 * @return php object, not JSON
 */
class FlexpageObjectContent extends FlexpageBlockContent {

  /**
   * {@inheritdoc}
   */
  public function angularSnapshotObjectContent() {
    $output = $this->FlexpageJsonGenerator->angularJson();
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function emptyangularSnapshotObjectContent() {
    $output = NULL;
    return $output;
  }

}
