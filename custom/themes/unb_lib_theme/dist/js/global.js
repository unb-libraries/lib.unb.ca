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
      $(window).scroll(function () {
        var scroll = $(window).scrollTop();
        var width = $(window).innerWidth();
        if (scroll >= 130 && width >= 991) {
          header.addClass("affix");
        } else {
          header.removeClass("affix");
        }
      });
    }
  };
})(jQuery, Drupal);
