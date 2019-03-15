/**
 * @file
 * Global lib_unb_ca utilities.
 *
 */
(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.lib_unb_ca = {
        attach: function (context, settings) {
            var header_logo = $("#navbar-main .branding > *");
            var header_nav_buttons = $("#navbar-main .megamenu-li > a");

            $(window).scroll(function () {
                var scroll = $(window).scrollTop();
                var width = $(window).innerWidth();
                if (scroll >= 130 && width >= 991) {
                    header_nav_buttons.removeClass("py-lg-4");
                    header_logo.removeClass("py-2");
                } else {
                    header_nav_buttons.addClass("py-lg-4");
                    header_logo.addClass("py-2");

                }
            });
        }
    };
})(jQuery, Drupal);
