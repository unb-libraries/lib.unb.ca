(function ($, Drupal, DrupalSettings) {
    Drupal.behaviors.eresources = {
        attach: function(context, settings) {
            var location = window.location;
            var url = location.href;
            var urlParams = new URLSearchParams(location.search);
            var formId = urlParams.get('form_id');
            var id = urlParams.get('id');
            var original = formId ? formId : id ? 'id=' + id : '';

            // Add history on tab change.
            $('#navbarNav button').once().click(function(e, triggered) {
                if(triggered) {
                  return;
                }
                var href = $(this).attr('href');
                if(original && href.includes(original)) {
                    history.pushState({}, "", url);
                }
                else {
                    history.pushState({}, "", href);
                }
            });

            // Restore tab on back button.
            window.addEventListener("popstate", function(e) {
                var newUrlParams = new URLSearchParams(window.location.search);
                var newFormId = newUrlParams.get('form_id');
                var newId = newUrlParams.get('id');
                var selector = '#navbarNav button:first';

                // TODO: Fix tab button highlight.
                // Existing form ID.
                if(newFormId) {
                  newFormId = newFormId.replace('eres_', '');
                  selector = 'button[aria-controls=' + newFormId + ']';
                }
                else if(newId) {
                  selector = 'button[aria-controls=resource]';
                }

                $(selector).trigger('click', [true]).focus();
            });
        }
    }
})(jQuery, Drupal, drupalSettings);
