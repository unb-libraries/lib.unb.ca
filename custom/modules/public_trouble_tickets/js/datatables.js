(function ($, Drupal) {
  Drupal.behaviors.datatables = {
    attach: function attach(context, settings) {
      $(context).find('table.dataTable').DataTable();
    }
  };
})(jQuery, Drupal);
