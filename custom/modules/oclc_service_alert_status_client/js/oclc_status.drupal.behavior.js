(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.oclcStatus = {
    attach: function attach(context, settings) {
      Drupal.oclcAlertStatus = Drupal.oclcAlertStatus || new OCLCStatusModel(null, {
        'url': drupalSettings.oclcAlertStatus.baseUrl,
        'autoRefresh': true,
        'refreshInterval': drupalSettings.oclcAlertStatus.refreshInterval,
        'message': drupalSettings.oclcAlertStatus.message
      });
    },
  };
})(jQuery, Drupal, drupalSettings);
