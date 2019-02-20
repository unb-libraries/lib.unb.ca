/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.unb_lib_theme = {
    attach: function (context, settings) {
      var header = $("#navbar-main");
      $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 150) {
          header.addClass("affix");
        } else {
          header.removeClass("affix");
        }
      });
    }
  };

})(jQuery, Drupal);
