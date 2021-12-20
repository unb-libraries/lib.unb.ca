(function ($, Drupal) {
    Drupal.behaviors.eresourcesDatabaseForm = {
        attach: function(context, settings) {
            $('#guide-submit').click(function(e) {
                e.preventDefault();
                let cat = $('#guide').val();
                if (cat) {
                    window.location = 'https://guides.lib.unb.ca/category/' + cat;
                }
            });
            $('#database-submit').click(function(e) {
                e.preventDefault();
                let query = $('#database').val();
                if (query) {
                  $('input[name=query]').val(query);
                  $('#eresources-databases-form').submit();
                }
            });
        }
    }
})(jQuery, Drupal);
