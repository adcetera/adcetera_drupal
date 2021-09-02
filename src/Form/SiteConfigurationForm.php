<?php

namespace Drupal\adcetera_drupal\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SiteConfigurationForm
 *
 * @package Drupal\adcetera_drupal\Form
 */
class SiteConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adcetera_drupal_site_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      'adcetera_drupal.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('adcetera_drupal.settings');

    $form['text'] = [
      '#markup' => '<p>' . $this->t('Configure general site settings.') . '</p>',
    ];

    $form['chkShowIeWarning'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show IE Warning'),
      '#description' => $this->t('Show a warning message to Internet Explorer users.'),
      '#default_value' => $config->get('ie_warning')
    ];

    $form['rdIeWarningMode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Warning Mode'),
      '#description' => $this->t('Select either a modal popup or a redirect for IE users.'),
      '#options' => [
        'modal' => $this->t('Modal Popup'),
        'redirect' => $this->t('Redirect')
      ],
      '#default_value' => !empty($config->get('ie_warning_mode')) ? $config->get('ie_warning_mode') : '',
      '#states' => [
        'invisible' => [
          ':input[name="chkShowIeWarning"]' => array('checked' => FALSE),
        ]
      ]
    ];

    $form['txtIeRedirect'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect'),
      '#description' => $this->t('Use a path relative to the docroot.'),
      '#default_value' => !empty($config->get('ie_warning_redirect')) ? $config->get('ie_warning_redirect') : '',
      '#states' => [
        'visible' => [
          ':input[name="chkShowIeWarning"]' => array('checked' => TRUE),
          ':input[name="rdIeWarningMode"]' => ['value' => 'redirect'],
        ],
      ]
    ];

    $form['txtIeWarning'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Warning Message'),
      '#default_value' => !empty($config->get('ie_warning_message')) ? $config->get('ie_warning_message') : $this->t('This site is not compatible with Internet Explorer. Please use a different browser.'),
      '#states' => [
        'visible' => [
          ':input[name="chkShowIeWarning"]' => array('checked' => TRUE),
          ':input[name="rdIeWarningMode"]' => ['value' => 'modal'],
        ],
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    //parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->configFactory->getEditable('adcetera_drupal.settings')
      ->set('ie_warning', $form_state->getValue('chkShowIeWarning'))
      ->set('ie_warning_mode', $form_state->getValue('rdIeWarningMode'))
      ->set('ie_warning_redirect', $form_state->getValue('txtIeRedirect'))
      ->set('ie_warning_message', $form_state->getValue('txtIeWarning'))
      ->save();
  }
}
