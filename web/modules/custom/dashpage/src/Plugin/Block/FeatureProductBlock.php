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
    $productlogos = [];

    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

    // Load terms for feature_details
    $detail_terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadTree('feature_details');

    foreach ($detail_terms as $term_data) {
      $term = Term::load($term_data->tid);
      if ($term) {

        // ✅ force translation by current language
        if ($term->hasTranslation($langcode)) {
          $term = $term->getTranslation($langcode);
        }

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

    // Load terms for feature_product
    $logo_terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadTree('feature_product');

    foreach ($logo_terms as $term_data) {
      $term = Term::load($term_data->tid);
      if ($term) {

        // ✅ force translation by current language (for alt / any text fields)
        if ($term->hasTranslation($langcode)) {
          $term = $term->getTranslation($langcode);
        }

        $image_field = $term->get('field_feapro_image')->first();
        $image_url = '';
        if ($image_field && !$image_field->isEmpty()) {
          $file = $image_field->entity;
          if ($file) {
            $image_url = file_create_url($file->getFileUri());
          }
        }

        $link_field = $term->get('field_fea_pro_link')->first();
        $link_url = $link_field ? $link_field->getUrl()->toString() : '/taxonomy/term/' . $term->id();

        $productlogos[] = [
          'src' => $image_url,
          'alt' => $term->label(),
          'url' => $link_url,
        ];
      }
    }

    return [
      '#theme' => 'feature_product_block',
      '#productlogos' => $productlogos,
      '#details' => $feature_details,
      '#content' => $this->t('This is a custom product block.'),
    ];
  }


}
