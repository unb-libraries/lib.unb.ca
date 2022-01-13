(function ($, Drupal) {
    Drupal.behaviors.eresourcesForms = {
        attach: function(context, settings) {
            $('#reference-guide-submit').click(function(e) {
                e.preventDefault();
                let cat = $('#reference-guide').val();
                if (cat) {
                    window.location = 'https://guides.lib.unb.ca/category/' + cat;
                }
            });
            $('#database-guide-submit').click(function(e) {
                e.preventDefault();
                let cat = $('#database-guide').val();
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
