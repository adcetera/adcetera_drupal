/**
 * @file script.js
 */
(function (Drupal, drupalSettings, $) {
  'use strict';

  Drupal.behaviors.adceteraDrupal = {
    attach: function(context, settings) {
      if (context === document) {
        if (!settings.adcetera_drupal.has_docs) {
          $('#adc_site_documentation_link').parent().remove();
        }
        if (!settings.adcetera_drupal.has_style_guide) {
          $('#adc_site_styleguide_link').parent().remove();
        }
      }
    }
  };

}(Drupal, drupalSettings, jQuery));
