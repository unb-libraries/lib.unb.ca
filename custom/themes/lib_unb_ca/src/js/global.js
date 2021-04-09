/**
 * @file
 * Global lib_unb_ca JS functions.
 * https://stackoverflow.com/questions/10732690/offsetting-an-html-anchor-to-adjust-for-fixed-header/29853395#29853395.
 */
 (function($, window) {
    var adjustAnchor = function() {
        var $anchor = $(':target'), fixedElementHeight = 80;

        if ($anchor.length > 0) {
            $('html, body')
                .stop()
                .animate({
                    scrollTop: $anchor.offset().top - fixedElementHeight
                }, 150);
        }
    };

    $(window).on('hashchange load', function() {
        adjustAnchor();
    });

 })(jQuery, window);

