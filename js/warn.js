/**
 * @file warn.js
 */
(function (drupalSettings) {
  'use strict';

  function scriptLoader(url, callback) {
    var head = document.head;
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;
    script.onreadystatechange = callback;
    script.onload = callback;
    head.appendChild(script);
  }

  var displayWarning = function() {
    picoModal({
      content: "<p>" + drupalSettings.adcetera_drupal_ie_message + "</p>"
    }).afterCreate(function(modal) {
      modal.modalElem().addEventListener("click", function(evt) {
        if (evt.target && evt.target.matches(".ok")) {
          modal.close(true);
        }
      });
    }).show();
  };

  if (window.document.documentMode) {
    scriptLoader(window.location.origin + '/modules/contrib/adcetera_drupal/js/picoModal-3.0.0.min.js', displayWarning);
  }
}(drupalSettings));
