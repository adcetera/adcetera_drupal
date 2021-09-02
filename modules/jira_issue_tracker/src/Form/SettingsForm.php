<?php

namespace Drupal\jira_issue_tracker\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm
 *
 * @package Drupal\jira_issue_tracker\Form
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'jira_issue_tracker_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      'jira_issue_tracker.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('jira_issue_tracker.settings');
    $form['text'] = [
      '#markup' => '<p>' . $this->t('Configure the issue collector settings.') . '</p>',
    ];

    $form['txtEmbedCode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Embed URL'),
      '#description' => $this->t('Issue Collector URL from Jira'),
      '#maxlength' => 500,
      '#size' => 128,
      '#default_value' => $config->get('embed_link'),
      '#required' => FALSE
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

    $this->configFactory->getEditable('jira_issue_tracker.settings')
      ->set('embed_link', $form_state->getValue('txtEmbedCode'))
      ->save();
  }
}
