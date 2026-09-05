<?php

namespace Drupal\site_rag_chat\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Site RAG Chat 配置表单。
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['site_rag_chat.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'site_rag_chat_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('site_rag_chat.settings');

    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('启用悬浮问答窗口'),
      '#default_value' => $config->get('enabled'),
    ];

    $form['api'] = [
      '#type' => 'details',
      '#title' => $this->t('API 配置'),
      '#open' => TRUE,
    ];

    $form['api']['api_endpoint'] = [
      '#type' => 'url',
      '#title' => $this->t('API Endpoint'),
      '#description' => $this->t('OpenSearch LLM 智能问答版实例的问答接口地址（在控制台 “应用详情 &gt; 接入信息” 里能找到）。例如：https://xxx.opensearch.aliyuncs.com/v3/openapi/apps/your-app/actions/chat'),
      '#default_value' => $config->get('api_endpoint'),
      '#required' => TRUE,
    ];

    $form['api']['api_key'] = [
      '#type' => 'password',
      '#title' => $this->t('API Key'),
      '#description' => $this->t('留空表示不修改已保存的密钥。密钥只保存在服务端配置里，绝不会输出到前端。'),
      '#attributes' => ['autocomplete' => 'new-password'],
    ];

    if ($config->get('api_key')) {
      $form['api']['api_key_status'] = [
        '#type' => 'item',
        '#title' => $this->t('当前状态'),
        '#markup' => $this->t('已保存密钥（末四位：@last4）', ['@last4' => substr($config->get('api_key'), -4)]),
      ];
    }

    $form['limits'] = [
      '#type' => 'details',
      '#title' => $this->t('限流与输入限制'),
      '#open' => TRUE,
    ];

    $form['limits']['max_question_length'] = [
      '#type' => 'number',
      '#title' => $this->t('单次提问最大字数'),
      '#default_value' => $config->get('max_question_length'),
      '#min' => 10,
      '#max' => 2000,
      '#required' => TRUE,
    ];

    $form['limits']['flood_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('限流：每个 IP 在窗口期内最多请求次数'),
      '#default_value' => $config->get('flood_limit'),
      '#min' => 1,
      '#required' => TRUE,
    ];

    $form['limits']['flood_window'] = [
      '#type' => 'number',
      '#title' => $this->t('限流窗口期（秒）'),
      '#default_value' => $config->get('flood_window'),
      '#min' => 10,
      '#required' => TRUE,
    ];

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('文案'),
      '#open' => TRUE,
    ];

    $form['display']['widget_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('聊天窗口标题'),
      '#default_value' => $config->get('widget_title'),
    ];

    $form['display']['welcome_message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('欢迎语'),
      '#default_value' => $config->get('welcome_message'),
      '#rows' => 2,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('site_rag_chat.settings');

    $config
      ->set('enabled', (bool) $form_state->getValue('enabled'))
      ->set('api_endpoint', $form_state->getValue('api_endpoint'))
      ->set('max_question_length', (int) $form_state->getValue('max_question_length'))
      ->set('flood_limit', (int) $form_state->getValue('flood_limit'))
      ->set('flood_window', (int) $form_state->getValue('flood_window'))
      ->set('widget_title', $form_state->getValue('widget_title'))
      ->set('welcome_message', $form_state->getValue('welcome_message'));

    // 只有填了新密钥才覆盖，避免每次保存表单都把密钥清空。
    $new_key = $form_state->getValue('api_key');
    if (!empty($new_key)) {
      $config->set('api_key', $new_key);
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
