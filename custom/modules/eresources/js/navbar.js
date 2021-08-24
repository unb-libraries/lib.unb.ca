(function ($, Drupal, DrupalSettings) {
    Drupal.behaviors.eresources = {
        attach: function(context, settings) {
             $(document, context).once('bookmarks').each( function() {
                var queryString = window.location.search;
                var urlParams = new URLSearchParams(queryString);
                var sub = urlParams.get('sub');
                if (sub) {
                    var button = $("#eresources-discovery-search button[aria-controls='" + sub + "']");
                    if (button) {
                        $(button).click().focus();
                    }
                }
            });
        }
    }
})(jQuery, Drupal, drupalSettings);
