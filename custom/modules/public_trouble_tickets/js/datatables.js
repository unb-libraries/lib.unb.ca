(function ($, Drupal, once) {
  Drupal.behaviors.datatables = {
    attach: function attach(context, settings) {
      once('listTickets', 'html').forEach(function (element) {
        Drupal.ajax({ url: Drupal.url('help/tickets/data') })
          .execute();
      });
    }
  };
})(jQuery, Drupal, once);
