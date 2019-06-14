(function ($, Drupal) {
    Drupal.behaviors.unbLibTabs = {
        attach: function(context, settings) {
            $('form#category-select').submit(function(e) {
                e.preventDefault();
                var cat = $(this).find('select').val();
                if (cat) {
                    window.location = '//guides.lib.unb.ca/category/' + cat;
                }
            });
        }
    }
})(jQuery, Drupal);
