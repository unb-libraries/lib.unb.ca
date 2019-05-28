/**
 * @file
 * Drupal behavior to attach AskUs script.
 * Source: https://docs.libraryh3lp.com/html-code-samples.
 */

(function ($, Drupal) {
    Drupal.behaviors.AskUs = {
        attach: function (context, settings) {
            // Show iff javascript is enabled.
            $('.needs-js').slideDown(300);

            // Pop-up window
            $('.libraryh3lp a').click(function(event) {
                var url = $(this).attr('href');
                var w = 400;
                var h = 560;
                var windowName = 'AskUs';
                var windowAttributes = 'location=url,status=yes,copyhistory=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,width='+w+',height='+h+',modal=yes,alwaysRaised=yes';
                window.open(url, windowName, windowAttributes);
                event.preventDefault();
                event.stopPropagation();
                return false;
            });
        }
    };
})(jQuery, Drupal);
