<?php

namespace Drupal\navinfo\Plugin\Block;

use Drupal\Core\Block\BlockBase;

use Drupal\navinfo\Content\NavinfoBlockGenerator;

/**
 * Provides a 'Navinfo: navigation' block.
 *
 * @Block(
 *   id = "navinfo_navigation",
 *   admin_label = @Translation("Navinfo: navigation bar")
 * )
 */
class NavinfoNavigationBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $NavinfoBlockGenerator = new NavinfoBlockGenerator();
    $output = $NavinfoBlockGenerator->contentNavigation();

    return array(
      '#type' => 'markup',
      '#markup' => $output,
      '#cache' => array('max-age' => 0,),
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
      '#attached' => array(
        'library' => array(
          'navinfo/navinfo.navbar',
        ),
      ),
    );
  }

}
