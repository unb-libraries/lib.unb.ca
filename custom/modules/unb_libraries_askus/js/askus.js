/**
 * @file
 * Drupal behavior to attach AskUs script.
 * Source: https://docs.libraryh3lp.com/html-code-samples.
 */

(function ($, Drupal) {
    Drupal.behaviors.AskUs = {
        attach: function (context, settings) {
            var x = document.createElement("script");
            x.type = "text/javascript";
            x.async = true;
            x.src = (document.location.protocol === "https:" ? "https://" : "http://") + "libraryh3lp.com/js/libraryh3lp.js?multi";
            var y = document.getElementsByTagName("script")[0];
            y.parentNode.insertBefore(x, y);
        }
    };
})(jQuery, Drupal);
