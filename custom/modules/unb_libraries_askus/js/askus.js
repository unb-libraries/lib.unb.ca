/**
 * @file
 * Drupal behavior to attach AskUs script.
 * Source: https://docs.libraryh3lp.com/html-code-samples.
 */

(function ($, Drupal) {
    Drupal.behaviors.AskUs = {
        attach: function (context, settings) {
            // Show iff javascript is enabled.
            $('.requires-js').slideDown(300);

            // Pop-up chat window
            $('.libraryh3lp a').click(function(event) {
                var url = $(this).attr('href');
                var windowName = 'Ask Us Chat';
                var windowAttributes = 'height=560,width=400,menubar=no,resizable=yes,scrollbars=yes,status=yes,toolbar=no';
                window.open(url, windowName, windowAttributes);
                event.preventDefault();
                event.stopPropagation();
                return false;
            });

            // Place this script as near to the end of your BODY as possible (noscript behaviour emulation?).
            // var x = document.createElement("script"); x.type = "text/javascript"; x.async = true;
            // x.src = (document.location.protocol === "https:" ? "https://" : "http://") + "ca.libraryh3lp.com/js/libraryh3lp.js?multi,poll";
            // var y = document.getElementsByTagName("script")[0]; y.parentNode.insertBefore(x, y);
        }
    };
})(jQuery, Drupal);
