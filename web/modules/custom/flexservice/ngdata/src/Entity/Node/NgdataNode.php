<?php

namespace Drupal\ngdata\Entity\Node;

use Drupal\ngdata\Entity\NgdataEntity;


/**
 * Class NgdataNode.
 */
class NgdataNode extends NgdataEntity implements NgdataNodeInterface {

  /**
   * Constructs a new NgdataNode object.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public function getNodeModel() {
    return;
  }

  /**
   *
   \Drupal::getContainer()->get('flexinfo.node.service')->entityCreateNode($field_array);
   */
  public function entityCreateNode($field_array = array()) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->create($field_array);

    \Drupal::entityTypeManager()->getStorage('node')->save($node);

    if (\Drupal::currentUser()->id() == 1) {
      if (isset($node->get('nid')->value)) {
        drupal_set_message('create node - nid - ' . $node->get('nid')->value);
      }
    }
  }

  /**
   *
   */
  public function entityUpdateNode($nid = NULL, $field_array = array()) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($node->id() == $nid) {
      foreach ($field_array as $key => $value) {
        $node->set($key, $value);  // $value is either string or array
      }
      $node->save();
    }

    // unset($node);
  }


  /**
   * @param nid
   *
   \Drupal::getContainer()->get('flexinfo.node.service')->getNodeEditLink($nid);
   */
  public function getNodeEditLink($nid = NULL, $link_text = 'Edit') {
    $link = NULL;

    if ($nid) {
      $url = Url::fromUserInput('/node/' . $nid . '/edit');
      $link = \Drupal::l(t($link_text), $url);
    }

    return $link;
  }

  /**
   * @param nid
   */
  public function getNodeViewLink($nid = NULL, $view_text = 'View') {
    $link = NULL;

    if ($nid) {
      $url = Url::fromUserInput('/node/' . $nid);
      $link = \Drupal::l(t($view_text), $url);
    }

    return $link;
  }

}
