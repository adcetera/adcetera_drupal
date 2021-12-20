<?php

namespace Drupal\adcetera_drupal\Controller;

use Drupal\adcetera_drupal\DocsAssetManager;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DocumentationController
 *
 * @package Drupal\adc_documentation\Controller
 */
class DocumentationController extends ControllerBase {

  /**
   * @var \Drupal\adcetera_drupal\DocsAssetManager
   */
  protected $assetManager;

  /**
   * DocumentationController constructor.
   *
   * @param \Drupal\adcetera_drupal\DocsAssetManager $asset_manager
   */
  public function __construct(DocsAssetManager $asset_manager) {
    $this->assetManager = $asset_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('adcetera_drupal.docasset_manager')
    );
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function index() {
    $path = $this->assetManager->getActiveDocumentationPath();

    $frameMarkup = "
        <!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name='robots' content='noindex, nofollow' />
    <title>" . $this->t('Website Reference Guide') . "</title>
    <style type=\"text/css\">
        body {
            margin: 0;
        }
        iframe {
            display: block;
            background: #fff;
            border: none;
            height: 100vh;
            width: 100vw;
        }
    </style>
</head>
<body>
    <iframe id=\"srcFrame\" src='" . $path . "index.html' onload=\"content_finished_loading(this);\"></iframe>
    <script>
        var content_start_loading = function() {
            document.getElementById('srcFrame').style.display = 'none';
        }
        var content_finished_loading = function(iFrame) {
            var frame = iFrame.contentWindow;
            var style = frame.document.createElement('link');
            style.rel = 'stylesheet';
            style.href = window.location.origin + '/modules/contrib/adcetera_drupal/assets/style.css';
            frame.document.head.appendChild(style);
            var script = frame.document.createElement('script');
            script.src = window.location.origin + '/modules/contrib/adcetera_drupal/assets/main.js';
            frame.document.body.appendChild(script);
            frame.onunload = content_start_loading;
        }
    </script>
</body>
</html>
    ";
    return new Response($frameMarkup);
  }
}
