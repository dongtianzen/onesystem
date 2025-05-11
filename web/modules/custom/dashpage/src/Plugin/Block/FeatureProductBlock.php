<?php

namespace Drupal\dashpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Custom Feature Block' block.
 *
 * @Block(
 *   id = "feature_product_block",
 *   admin_label = @Translation("Feature Product Block"),
 *   category = @Translation("Custom")
 * )
 */
class FeatureProductBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $feature_details = [];

    $terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadTree('feature_details');

    foreach ($terms as $term_data) {
      $term = Term::load($term_data->tid);
      if ($term) {
        $title = $term->label();
        $description = $term->get('description')->value ?? '';
        $link_field = $term->get('field_feade_link')->first();
        $url = $link_field ? $link_field->getUrl()->toString() : '/taxonomy/term/' . $term->id();

        $feature_details[] = [
          'title' => $title,
          'url' => $url,
          'description' => $description,
        ];
      }
    }

    return [
      '#theme' => 'feature_product_block',
      '#productlogos' => [
        [
          'src' => 'themes/custom/wanbo/images/product-logo/liveu_logo.png',
          'alt' => 'liveu_logo',
          'url' => '/taxonomy/term/10',
        ],
        [
          'src' => 'themes/custom/wanbo/images/product-logo/Phabrix_logo.jpg',
          'alt' => 'Phabrix_logo',
          'url' => '/taxonomy/term/23',
        ],
        [
          'src' => 'themes/custom/wanbo/images/product-logo/Harmonic_logo_1.png',
          'alt' => 'Harmonic_logo_1',
          'url' => '/taxonomy/term/27',
        ],
        [
          'src' => 'themes/custom/wanbo/images/product-logo/ATEME_logo.png',
          'alt' => 'ATEME_logo',
          'url' => '/taxonomy/term/134',
        ],
        [
          'src' => 'themes/custom/wanbo/images/product-logo/Appear_logo.png',
          'alt' => 'Appear_logo',
          'url' => '/taxonomy/term/15',
        ],
      ],
      '#details' => $feature_details,
      '#content' => $this->t('This is a custom product block.'),
    ];
  }

}
