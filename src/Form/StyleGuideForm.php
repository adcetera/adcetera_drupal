<?php

namespace Drupal\adcetera_drupal\Form;

use Drupal\adcetera_drupal\DocsAssetManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StyleGuideForm
 *
 * @package Drupal\adcetera_drupal\Form
 */
class StyleGuideForm extends FormBase {

  /**
   * @var \Drupal\adcetera_drupal\DocsAssetManager
   */
  protected DocsAssetManager $assetManager;

  /**
   * StyleGuideForm constructor.
   *
   * @param \Drupal\adcetera_drupal\DocsAssetManager $doc_asset_manager
   */
  public function __construct(DocsAssetManager $doc_asset_manager) {
    $this->assetManager = $doc_asset_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('adcetera_drupal.docasset_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adcetera_drupal_styleguide_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $styleGuidePath = $this->assetManager->getStyleGuidePath();
    if (isset($styleGuidePath)) {

      // Load contents of styleguide file
      $data = file_get_contents($styleGuidePath);
      $form['output'] = [
        '#markup' => $data,
      ];
      $form['#region'] = 'content';
      $form['#theme'] = 'adcetera_styleguide';
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
