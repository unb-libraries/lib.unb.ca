(function ($, Drupal) {
    Drupal.behaviors.AskUsPopup = {
        attach: function (context, settings) {
            // Pop-up chat window
            $("a.askus-popup").click(function(event) {
                var url = $(this).attr("href");
                var windowName = "Ask Us Chat";
                var windowAttributes = "height=560,width=400,menubar=no," +
                    "resizable=yes,scrollbars=yes,status=yes,toolbar=no";
                window.open(url, windowName, windowAttributes);
                event.preventDefault();
                event.stopPropagation();
                return false;
            });

            // Direct Links to tabs
            if (location.hash) {
              showTab(location.hash);
            }

            // Push tab hash to history when it is shown
            $('#guide .Accordion-panel').on('show-guide-tab', function() {
                thisHash = '#' + this.id;
                if (location.hash != thisHash) {
                    history.pushState({}, "", thisHash);
                    ga("set", "page", location.pathname + location.search + location.hash);
                    ga("send", "pageview");
                }
            });

            // Show tab if hash exists, first tab otherwise.
            window.addEventListener('popstate', function(e) {
                if (!location.hash) {
                    $('#guide nav .navbar-nav button').attr({'aria-expanded': 'false', 'aria-disabled': 'false'});
                    $('#guide nav .navbar-nav button:first').attr({'aria-expanded': 'true', 'aria-disabled': 'true'}).focus();
                    $('#guide .Accordion-panel').attr('hidden', 'hidden');
                    $('#guide .Accordion-panel:first').removeAttr('hidden');
                }
                else {
                    showTab(location.hash);
                }
            });

            // Fire event when guide nav button is clicked
            $('#guide nav .navbar-nav button').on('click', function() {
                $('#' + $(this).attr('aria-controls')).trigger('show-guide-tab');
            });

            // Navigate to tabs and deep linked achors
            function showTab(id) {
                id = id.replace('#', '');
                tabParts = id.split('__');
                $('#guide nav .navbar-nav button[aria-controls="' + tabParts[0] + '"]').click();
                document.querySelector('#' + id).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }
    };
})(jQuery, Drupal);
