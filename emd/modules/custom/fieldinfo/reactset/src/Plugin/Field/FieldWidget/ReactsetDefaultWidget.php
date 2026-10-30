<?php

/**
 * @file
 * Contains \Drupal\reactset\Plugin\Field\FieldWidget\ReactsetDefaultWidget.
 */

namespace Drupal\reactset\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'reactset_standard_widget' widget.
 *
 * @FieldWidget(
 *   id = "reactset_standard_widget",
 *   label = @Translation("Reactset Field Widget"),
 *   field_types = {
 *     "reactset_type"
 *   }
 * )
 */
class ReactsetDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'placeholder' => '',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['placeholder'] = array(
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    );
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();

    $placeholder = $this->getSetting('placeholder');
    if (!empty($placeholder)) {
      $summary[] = t('Placeholder: @placeholder', array('@placeholder' => $placeholder));
    }
    else {
      $summary[] = t('No placeholder');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   * @todo add form element which are displyed in the node add/edit form
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['question_tid'] = $element;
    $element['question_tid'] = array(
      '#title' => t('Question Tid'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->question_tid) ? $items[$delta]->question_tid : NULL,
      '#placeholder' => $this->getSetting('placeholder'),
    );

    $element['question_answer'] = $element;
    $element['question_answer'] = array(
      '#title' => t('Question Answer'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->question_answer) ? $items[$delta]->question_answer : NULL,
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => 1024,
    );

    $element['refer_uid'] = $element;
    $element['refer_uid'] = array(
      '#title' => t('Refer User'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->refer_uid) ? $items[$delta]->refer_uid : NULL,
      '#placeholder' => $this->getSetting('placeholder'),
    );

    $element['refer_tid'] = $element;
    $element['refer_tid'] = array(
      '#title' => t('Refer Term'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->refer_tid) ? $items[$delta]->refer_tid : NULL,
      '#placeholder' => $this->getSetting('placeholder'),
    );

    $element['refer_other'] = $element;
    $element['refer_other'] = array(
      '#title' => t('Refer Other'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->refer_other) ? $items[$delta]->refer_other : NULL,
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => 1024,
    );

    return $element;
  }

}
