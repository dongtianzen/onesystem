<?php

namespace Drupal\ngpage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @todo use form
 $form = \Drupal::formBuilder()->getForm('Drupal\ngpage\Form\EventtypeFilterForm');
 */

/**
 * Implements an example form.
 */
class EventtypeFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eventtype_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['eventtype_selection'] = array(
      '#type' => 'checkboxes',
      '#options' => $this->_getTermOptions(),
      '#default_value' => $this->_getUserDefaultValue(),
      '#title' => '',
      '#attributes' => array('class' => array('display-inline-block', 'float-left', 'margin-left-24')),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#weight' => -100,
      '#attributes' => array('class' => array('display-inline-block', 'float-left', 'margin-left-24')),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (array_sum($form_state->getValue('eventtype_selection')) < 1) {
      // $form_state->setErrorByName('phone_number', $this->t('Your eventtype selection is empty. Please selct a eventtype.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $filter_result = $this->_filterEventtypeResult($form_state->getValue('eventtype_selection'));

    \Drupal::service('user.data')->set(
      'ngpage', \Drupal::currentUser()->id(), 'default_term_eventtype', $filter_result
    );

    if (\Drupal::currentUser()->id() == 1) {
      drupal_set_message(
        $this->t('Your eventtype is @number', ['@number' => implode(", ", $filter_result)])
      );
    }
  }

  /**
   * @param is $raw_array like array(58, 0, 0, 0, 0, 0, 0, 0, 0, 0)
   * remove all empty array elements
   * @return is $raw_array like array(58)
   */
  public function _filterEventtypeResult($raw_array = array()) {
    $output = array_filter($raw_array);
    return $output;
  }



  /**
   * @return
   */
  public function _getTermOptions() {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('eventtype');
    foreach ($terms as $term) {
      $row = '';
      $row .= '<li class="province-filter-option-wrapper display-inline-block">';
        $row .= '<span class="display-inline-block float-left margin-right-6">';
          $row .= $term->getName();
        $row .= '</span>';
      $row .= '</li>';
      $options[$term->id()] = $row;
    }

    return $output;
  }

  /**
   * @return
   */
  public function _getUserDefaultValue() {
    $output = \Drupal::service('user.data')
      ->get('ngpage', \Drupal::currentUser()->id(), 'default_term_eventtype');
    if (!$output) {
      $output = [];
    }

    return $output;
  }

}
