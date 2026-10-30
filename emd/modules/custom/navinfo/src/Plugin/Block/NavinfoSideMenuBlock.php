<?php

namespace Drupal\navinfo\Plugin\Block;

use Drupal\Core\Block\BlockBase;

use Drupal\navinfo\Content\NavinfoBlockGenerator;

/**
 * Provides a 'Navinfo: side menu' block.
 *
 * @Block(
 *   id = "navinfo_sidemenu",
 *   admin_label = @Translation("Navinfo: side menu")
 * )
 */
class NavinfoSidemenuBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $NavinfoBlockGenerator = new NavinfoBlockGenerator();
    $output = $NavinfoBlockGenerator->contentSideMenu();

    return array(
      '#type' => 'markup',
      '#markup' => $output,
      '#cache' => array('max-age' => 0,),
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
      '#attached' => array(
        'library' => array(
          'fxt/metismenu',
          'navinfo/navinfo.sidebar',
        ),
      ),
    );
  }

}
