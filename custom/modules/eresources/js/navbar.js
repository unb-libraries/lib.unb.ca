(function ($, Drupal, DrupalSettings) {
    Drupal.behaviors.eresources = {
        attach: function(context, settings) {
            $(document, context).once('bookmarks').each( function() {
                var queryString = window.location.search;
                var urlParams = new URLSearchParams(queryString);
                var form_id = urlParams.get('form_id');
                if (form_id) {
                    switch (form_id) {
                        case 'eresources_journals_form':
                            var aria_controls = 'journals';
                            break;
                        case 'eresources_reference_form':
                            var aria_controls = 'refmat';
                            break;
                        case 'eresources_ebooks_form':
                            var aria_controls = 'ebooks';
                            break;
                        case 'eresources_videos_form':
                            var aria_controls = 'video';
                            break;
                        default:
                            var aria_controls = 'indexes';
                    }
                    var button = $("#eresources-discovery-search button[aria-controls='" + aria_controls + "']");
                    if (button) {
                        $(button).click().focus();
                    }
                } else {
                    // Backwards support for web.lib | sub | url argument
                    var sub = urlParams.get('sub');
                    if (sub) {
                        var button = $("#eresources-discovery-search button[aria-controls='" + sub + "']");
                        if (button) {
                            $(button).click().focus();
                        }
                    }
                }
            });
        }
    }
})(jQuery, Drupal, drupalSettings);
