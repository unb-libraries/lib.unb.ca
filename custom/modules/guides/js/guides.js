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
            var url = document.location.toString();
            if (url.match('#')) {
              showTab(url);
            }

            // Navigate to tabs and deep linked achors
            function showTab(url) {
                anchor = url.split('#')[1];
                tabParts = anchor.split('__');
                $('#guide button[aria-controls="' + tabParts[0] + '"]').click();
                document.querySelector('#' + anchor).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }
    };
})(jQuery, Drupal);
