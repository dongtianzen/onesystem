<?php

/**
 * @file
 */

namespace Drupal\flexform\Content;

/**
 * An example controller.
 $FlexformContentGenerator = new FlexformContentGenerator();
 $FlexformContentGenerator->entityAdd();
 */
class FlexformContentGenerator {

  /**
   *
   */
  public function entityAdd($entity_type, $entity_bundle) {
    if ($entity_type == 'node') {
      $entity = \Drupal::entityManager()
        ->getStorage('node')
        ->create(
          array('type' => $entity_bundle)    // node_type like "article"
        );

      // OPTIONAL - Set default values for node fields
      // $entity->set('field_article_age', "32") ;
      $entity->set('title', "Entity Form Add " . $entity_bundle);
    }
    else if ($entity_type == 'taxonomy_term') {
      $entity = \Drupal::entityManager()
        ->getStorage('taxonomy_term')
        ->create(
          array('vid' => $entity_bundle)
        );
    }
    else if ($entity_type == 'user') {
      $entity = \Drupal::entityManager()
        ->getStorage('user')
        ->create();
    }

    $entity_form = \Drupal::entityTypeManager()
      ->getFormObject($entity_type, 'default')
      ->setEntity($entity);

    return $entity_form;
  }

  /**
   *
   */
  public function entityEdit($entity_type, $entity_id) {
    $entity = NULL;

    if ($entity_type == 'node' || $entity_type == 'taxonomy_term' || $entity_type == 'user') {
      $entity = \Drupal::entityManager()->getStorage($entity_type)->load($entity_id);
    }

    $entity_form = \Drupal::entityTypeManager()
      ->getFormObject($entity_type, 'default')
      ->setEntity($entity);

    return $entity_form;
  }

  /**
   *
   */
  public function entityView($entity_type, $entity_bundle, $entity_id) {
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);
    $view_mode = 'full';  // or teaser
    $entity_view = entity_view($entity, $view_mode);

    $output = '<div class="col-xs-12 col-md-8 col-md-offset-2">';
      $output .= '<div class="margin-left-12">';
        $output .=  render($entity_view);
      $output .= '</div >';
    $output .= '</div >';

    return $output;
  }

}
