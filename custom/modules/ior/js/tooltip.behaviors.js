(function ($, Drupal) {
  Drupal.behaviors.tooltip = {
    attach: function (context, settings) {
      $('[data-toggle="tooltip"]').tooltip()
    }
  };
})(jQuery, Drupal);
