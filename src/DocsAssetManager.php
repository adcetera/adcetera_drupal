<?php

namespace Drupal\adcetera_drupal;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\file\Entity\File;

/**
 * Class DocsAssetManager
 *
 * @package Drupal\adcetera_drupal
 */
class DocsAssetManager {

  /**
   * A configuration object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * @var string
   */
  protected $docPath = '/sites/default/files/documentation';

  /**
   * @var string
   */
  protected $stylePath = '/sites/default/files/styleguide';

  /**
   * DocsAssetManager constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('adcetera_drupal.settings');

    $dirDoc = $_SERVER['DOCUMENT_ROOT']. $this->docPath;
    if (!is_dir($dirDoc)) {
      mkdir($dirDoc, 0755, TRUE);
    }
    $dirStyle = $_SERVER['DOCUMENT_ROOT']. $this->stylePath;
    if (!is_dir($dirStyle)) {
      mkdir($dirStyle, 0755, TRUE);
    }
  }

  /**
   * @return string|null
   */
  public function getActiveDocumentationPath() {
    $activePackage = $this->config->get('documentation_package');
    if (isset($activePackage)) {
      return $this->getUploadedDirectoryPath(key($activePackage));
    }
    return null;
  }

  /**
   * @return array
   */
  public function getUploadedDirectoriesForDisplay() {
    $retVal = array();
    $dir = $_SERVER['DOCUMENT_ROOT']. $this->docPath;
    $dirs = glob($dir . '/*', GLOB_ONLYDIR);
    if ($dirs) {
      foreach ($dirs as $d) {
        $path = explode('/', $d);
        $len = count($path);
        array_push($retVal, $path[$len - 1]);
      }
    }
    return $retVal;
  }

  /**
   * Gets the correct path for a directory based on name
   *
   * @param {string} $directoryName
   *
   * @return string|null
   */
  public function getUploadedDirectoryPath($directoryName) {
    $dir = $_SERVER['DOCUMENT_ROOT']. $this->docPath;
    $dirs = glob($dir . '/*', GLOB_ONLYDIR);
    if ($dirs) {
      foreach ($dirs as $d) {
        $path = explode('/', $d);
        $len = count($path);
        if ($path[$len - 1] === $directoryName) {
          return explode('/docroot', $d)[1] . '/';
        }
      }
    }
    return null;
  }

  /**
   * Gets the path to the uploaded style guide HTML file
   *
   * @return string|null
   */
  public function getStyleGuidePath() {
    $styleGuideFileId = $this->config->get('styleguide');
    if (isset($styleGuideFileId)) {
      $file = File::load($styleGuideFileId);
      return $file->getFileUri();
    }
    return null;
  }

}
