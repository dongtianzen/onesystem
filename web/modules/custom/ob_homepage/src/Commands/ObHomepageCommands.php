<?php

namespace Drupal\ob_homepage\Commands;

use Drush\Commands\DrushCommands;

/**
 * Drush commands for the ob_homepage module.
 */
class ObHomepageCommands extends DrushCommands {

  /**
   * Points system.site:page.front at the single active homepage node.
   *
   * Looks for nodes of type `homepage` with field_home_is_active = TRUE and
   * status = published. Refuses to guess: 0 matches leaves page.front
   * untouched with a warning; more than 1 match leaves page.front untouched
   * and lists every conflicting node, both exiting non-zero.
   *
   * @command ob:set-frontpage
   * @aliases ob-sfp
   */
  public function setFrontpage() {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $ids = $storage->getQuery()
      ->condition('type', 'homepage')
      ->condition('field_home_is_active', 1)
      ->condition('status', 1)
      ->accessCheck(FALSE)
      ->execute();

    if (count($ids) === 0) {
      $this->logger()->warning(dt('No active homepage node found (type=homepage, field_home_is_active=TRUE, status=published). system.site:page.front left unchanged.'));
      return 1;
    }

    if (count($ids) > 1) {
      $this->logger()->error(dt('@count active homepage nodes found — refusing to guess which one to use. system.site:page.front left unchanged.', ['@count' => count($ids)]));
      foreach ($storage->loadMultiple($ids) as $node) {
        $this->logger()->error(dt('  nid=@nid: @title', ['@nid' => $node->id(), '@title' => $node->getTitle()]));
      }
      return 1;
    }

    $nid = reset($ids);
    \Drupal::configFactory()->getEditable('system.site')
      ->set('page.front', '/node/' . $nid)
      ->save();
    $this->logger()->success(dt('system.site:page.front set to /node/@nid.', ['@nid' => $nid]));
    return 0;
  }

}
