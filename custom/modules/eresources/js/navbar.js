(function ($, Drupal, DrupalSettings) {
    Drupal.behaviors.eresources = {
        attach: function(context, settings) {
            var location = window.location;
            var url = location.href;
            var urlParams = new URLSearchParams(location.search);
            var formId = urlParams.get('form_id');
            var original = formId ? formId : '';

            // Add history on tab change.
            $('#navbarNav button').on('click', function() {
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

                // TODO: trigger tab via js.
                // Existing form ID.
                if(newFormId) {
                  newFormId = newFormId.replace('eresources_', '');
                  newFormId = newFormId.replace('_form', '');
                }
                // No form ID, trigger first tab.
                else {
                }
            });
        }
    }
})(jQuery, Drupal, drupalSettings);
