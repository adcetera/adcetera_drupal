/**
 * @file jira.js
 */
(function (Drupal) {
  'use strict';

  Drupal.behaviors.jiraIssueTracker = {
    attach: function(context, settings) {
      var attached = window.jira;
      if (!attached) {
        // Add styles
        var ref = document.querySelector('script');
        var styles = document.createElement('style');
        styles.innerHTML =
          '#atlwdg-trigger {' +
          'z-index: 99999 !important;' +
          '}'
        ref.parentNode.insertBefore(styles, ref);
        window.jira = true;
      }
    }
  };

}(Drupal));
