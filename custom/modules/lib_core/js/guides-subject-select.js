(function ($, Drupal) {
    Drupal.behaviors.unbLibTabs = {
        attach: function(context, settings) {
            $('form#category-select').submit(function(e) {
                e.preventDefault();
                let cat = $(this).find('select').val();
                if (cat) {
                    window.location = 'https://guides.lib.unb.ca/category/' + cat;
                }
                e.reportValidity();
            });
        }
    }
})(jQuery, Drupal);