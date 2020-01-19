<?php

namespace Drupal\ngdata\Entity\Node;

use Drupal\ngdata\Entity\NgdataEntityInterface;


/**
 * Provides an interface for node entity data.
 */
interface NgdataNodeInterface extends NgdataEntityInterface {

  public function getNodeModel();

}
