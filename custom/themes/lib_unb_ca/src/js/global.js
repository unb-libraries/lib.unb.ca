/**
 * @file
 * Global lib_unb_ca JS functions.
 * https://stackoverflow.com/questions/10732690/offsetting-an-html-anchor-to-adjust-for-fixed-header/29853395#29853395.
 */
(function($, Drupal) {

	'use strict';

	Drupal.behaviors.lib_unb_ca = {
		attach: function(context, settings) {
			$(window).on('hashchange pageshow', function() {
				adjustAnchor();
			});
		}
	};
})(jQuery, Drupal);

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
