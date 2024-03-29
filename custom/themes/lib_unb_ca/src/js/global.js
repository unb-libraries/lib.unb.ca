/**
 * @file
 * Global lib_unb_ca JS functions.
 */
(function($, Drupal) {

    'use strict';

    Drupal.behaviors.lib_unb_ca = {
        attach: function(context, settings) {
            $(window).on('hashchange pageshow', function() {
                adjustAnchor();
            });

            // Mobile Quicklinks button: scroll to Quicklinks section when toggled on.
            $('#btn-quicklinks').click(function(e) {
                e.preventDefault();
                if (this.getAttribute('aria-expanded') === 'false') {
                    let $target = $('#quicklinks');
                    $('html, body').stop().animate({
                        'scrollTop': $target.offset().top
                    }, 800, 'swing');
                }
            });
        }
    };
})(jQuery, Drupal);

/**
 * Adapted from:
 * https://stackoverflow.com/questions/10732690/offsetting-an-html-anchor-to-adjust-for-fixed-header/29853395#29853395.
 */
var adjustAnchor = function() {
    var $anchor = jQuery(':target');
    if ($anchor.length > 0) {
        var $fixedElementHeight = jQuery('#navbar-main.affix').length ? 80 : 160;
        jQuery('html, body')
            .stop()
            .animate({
				scrollTop: $anchor.offset().top - $fixedElementHeight
            }, 150);
    }
};
