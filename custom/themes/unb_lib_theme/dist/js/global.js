/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.unb_lib_theme = {
    attach: function (context, settings) {
      $('.sf-multicolumn').addClass('container');
      $('.sf-multicolumn-wrapper ').addClass('row');
    }
  };

})(jQuery, Drupal);
