(function ($, Drupal) {
    Drupal.behaviors.eresourcesDatabaseForm = {
        attach: function(context, settings) {
            $('#guide option:first-child').attr('disabled', 'disabled');
            $('#guide-submit').click(function(e) {
                e.preventDefault();
                let cat = $('#guide').val();
                if (cat) {
                    window.location = 'https://guides.lib.unb.ca/category/' + cat;
                }
            });
            $('#query').attr('required','required');
            $('#query option:first-child').attr('disabled', 'disabled');
        }
    }
})(jQuery, Drupal);
