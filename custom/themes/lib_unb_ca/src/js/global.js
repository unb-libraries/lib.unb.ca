/**
 * @file
 * Global lib_unb_ca JS functions.
 * https://stackoverflow.com/questions/10732690/offsetting-an-html-anchor-to-adjust-for-fixed-header/29853395#29853395.
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.lib_theme = {
      attach: function (context, settings) {
          var adjustAnchor = function () {
              var $anchor = $(':target'), fixedElementHeight = 80;
              if ($anchor.length > 0) {
                  $('html, body')
                      .stop()
                      .animate({
                          scrollTop: $anchor.offset().top - fixedElementHeight
                      }, 150);
              }
          };

          $(window).on('hashchange load pageshow', function () {
              adjustAnchor();
          });
      }
  };
})(jQuery, Drupal);
