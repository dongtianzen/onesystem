<?php

namespace Drupal\navinfo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class DateRangeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'navinfo_daterange_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['date_range'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['date-range-container primengapp-dev-wrapper'],
      ],
    ];

    $form['date_range']['plain_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Please set the date range'),
      '#required' => TRUE,
    ];

    $form['date_range']['start_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Start Date'),
      '#required' => TRUE,
    ];

    $form['date_range']['end_date'] = [
      '#type' => 'date',
      '#title' => $this->t('End Date'),
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');

    // Convert date values to timestamps if they are not null.
    $start_date_timestamp = strtotime($start_date);
    $end_date_timestamp = strtotime($end_date);

    // Ensure the dates are properly converted to timestamps.
    if ($start_date_timestamp === FALSE || $end_date_timestamp === FALSE) {
      \Drupal::messenger()->addMessage($this->t('Invalid date format. Please enter valid dates.'), 'error');
    }
    else {
      if ($start_date_timestamp > $end_date_timestamp) {
        \Drupal::messenger()->addMessage($this->t('Invalid date range. Please enter valid dates range.'), 'error');
      }
      else {
        \Drupal::messenger()->addMessage($this->t('Start date: @start, End date: @end', [
          '@start' => date('Y-m-d', $start_date_timestamp),
          '@end' => date('Y-m-d', $end_date_timestamp),
        ]));

        // Redirect to the specified URL
        $form_state->setRedirectUrl(\Drupal\Core\Url::fromUri('internal:/navinfo/daterange/redirect-referer/' . $start_date_timestamp . '/' . $end_date_timestamp));
      }
    }
  }

}
