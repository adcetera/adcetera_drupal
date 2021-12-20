<?php

namespace Drupal\adcetera_drupal\Form;

use Drupal\adcetera_drupal\DocsAssetManager;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Archiver\ArchiverManager;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DocumentationSettingsForm
 *
 * @package Drupal\adcetera_drupal\Form
 */
class DocumentationSettingsForm extends FormBase {

  /**
   * @var string
   */
  protected $documentationPath = 'public://documentation/';

  /**
   * @var string
   */
  protected $styleguidePath = 'public://styleguide/';

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * A configuration object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * @var \Drupal\adcetera_drupal\DocsAssetManager
   */
  protected $assetManager;

  /**
   * @var \Drupal\Core\Archiver\ArchiverManager
   */
  protected $pluginManagerArchiver;

  /**
   * DocumentationSettingsForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   * @param \Drupal\Core\Archiver\ArchiverManager $plugin_manager_archiver
   * @param \Drupal\adcetera_drupal\DocsAssetManager $doc_asset_manager
   */
  public function __construct(ConfigFactoryInterface $config_factory, FileSystemInterface $file_system, ArchiverManager $plugin_manager_archiver, DocsAssetManager $doc_asset_manager) {
    $this->configFactory = $config_factory;
    $this->config = $config_factory->get('adcetera_drupal.settings');
    $this->fileSystem = $file_system;
    $this->pluginManagerArchiver = $plugin_manager_archiver;
    $this->assetManager = $doc_asset_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('file_system'),
      $container->get('plugin.manager.archiver'),
      $container->get('adcetera_drupal.docasset_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adcetera_drupal_doc_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Documentation
    $currentPackages = $this->config->get('documentation_package');
    $folders = $this->assetManager->getUploadedDirectoriesForDisplay();

    $options = array();
    foreach ($folders as $folder) {
      $options[$folder] = $folder;
    }

    $defaultValues = array();
    if (isset($currentPackages)) {
      $defaultValues = $currentPackages;
    }

    $form['doc'] = array(
      '#type' => 'details',
      '#title' => $this->t('Documentation'),
      '#open' => FALSE,
    );

    $form['doc']['select_package'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Available Packages'),
      '#options' => $options,
      '#default_value' => !empty($defaultValues) ? $defaultValues : [],
    ];

    $form['doc']['set_package'] = [
      '#type' => 'submit',
      '#value' => $this->t('Set Package'),
      '#submit' => ['::setPackage'],
    ];

    $form['doc']['text'] = [
      '#markup' => '<br /><br /><hr /><p>' . $this->t('Upload a new documentation package.') . '</p>',
    ];

    $validators = array(
      'file_validate_extensions' => array('zip'),
    );

    $form['doc']['zip_file'] = array(
      '#type' => 'managed_file',
      '#name' => 'my_file',
      '#title' => $this->t('File *'),
      '#size' => 20,
      '#description' => $this->t('ZIP format only'),
      '#upload_validators' => $validators,
      '#upload_location' => $this->documentationPath,
    );

    $form['doc']['upload_zip'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload ZIP'),
      '#submit' => ['::uploadArchive'],
    ];

    // Styleguide
    $form['style'] = array(
      '#type' => 'details',
      '#title' => $this->t('Style Guide'),
      '#open' => FALSE,
    );

    $form['style']['text_style'] = [
      '#markup' => '<p>' . $this->t('Upload a new style guide file.') . '</p>',
    ];

    $validatorsHtml = array(
      'file_validate_extensions' => array('html'),
    );

    $styleGuideFileId = $this->config->get('styleguide');

    $form['style']['html_file'] = array(
      '#type' => 'managed_file',
      '#name' => 'my_file',
      '#title' => $this->t('File *'),
      '#size' => 20,
      '#description' => $this->t('HTML format only'),
      '#upload_validators' => $validatorsHtml,
      '#upload_location' => $this->styleguidePath,
      '#default_value' => isset($styleGuideFileId) ? [$styleGuideFileId] : '',
    );

    $form['style']['upload_html'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload HTML'),
      '#submit' => ['::uploadHtml'],
    ];

    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function uploadArchive(array &$form, FormStateInterface $form_state) {
    $file = \Drupal::entityTypeManager()->getStorage('file')
      ->load($form_state->getValue('zip_file')[0]);

    // Get the actual path to the file
    $fileRealPath = $this->fileSystem->realpath($file->getFileUri());

    // Get an instance of the archiver
    $zip = $this->pluginManagerArchiver->getInstance(['filepath' => $fileRealPath]);

    // Extract the files
    $zip->extract($this->documentationPath);

    // Remove the zip file
    try {
      $file->delete();
    }
    catch (Exception $exception) {
      \Drupal::logger('adcetera_drupal')->error($exception->getMessage());
    }
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function uploadHtml(array &$form, FormStateInterface $form_state) {
    $file = \Drupal::entityTypeManager()->getStorage('file')
      ->load($form_state->getValue('html_file')[0]);

    // Save the file
    try {
      $file->setPermanent();
      $file->save();
    } catch (Exception $exception) {
      \Drupal::logger('adcetera_drupal')->error($exception->getMessage());
    }

    // Update the config settings
    if ($file->id() > 0) {
      $this->configFactory->getEditable('adcetera_drupal.settings')
        ->set('styleguide', $file->id())
        ->save();
    }
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function setPackage(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('adcetera_drupal.settings')
      ->set('documentation_package', $form_state->getValue('select_package'))
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }
}
