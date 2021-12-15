(function ($, Drupal) {
  Drupal.behaviors.ior = {
    attach: function (context, settings) {
      $('[data-toggle="tooltip"]').tooltip()
    }
  };
})(jQuery, Drupal);
