/**
 * @file
 * Transforms links into radio buttons.
 */

(function ($, Drupal, once) {

  'use strict';

  Drupal.facets = Drupal.facets || {};
  Drupal.behaviors.facetsRadioWidget = {
    attach: function (context) {
      Drupal.facets.makeRadios(context);
    }
  };

  window.onbeforeunload = function(e) {
    if (Drupal.facets) {
      var $radioWidgets = $('.js-facets-radio-links');
      if ($radioWidgets.length > 0) {
        $radioWidgets.each(function (index, widget) {
          var $widget = $(widget);
          var $widgetLinks = $widget.find('.facet-item > a');
          $widgetLinks.each(Drupal.facets.updateRadio);
        });
      }
    }
  };

  /**
   * Turns all facet links into radio buttons.
   */
  Drupal.facets.makeRadios = function (context) {
    // Find all radio facet links and give them a radio.
    var $radioWidgets = $(once('facets-radio-transform', '.js-facets-radio-links', context));

    if ($radioWidgets.length > 0) {
      $radioWidgets.each(function (index, widget) {
        var $widget = $(widget);
        var $widgetLinks = $widget.find('.facet-item > a');

        // Add correct CSS selector for the widget. The Facets JS API will
        // register handlers on that element.
        $widget.addClass('js-facets-widget');

        // Transform links to radio buttons.
        $widgetLinks.each(Drupal.facets.makeRadio);

        // We have to trigger attaching of behaviours, so that Facets JS API can
        // register handlers on radio widgets.
        Drupal.attachBehaviors(this.parentNode, Drupal.settings);
      });

    }

    // Set indeterminate value on parents having an active trail.
    $('.facet-item--expanded.facet-item--active-trail > input').prop('indeterminate', true);
  };

  /**
   * Replace a link with a checked radio.
   */
  Drupal.facets.makeRadio = function () {
    var $link = $(this);
    var active = $link.hasClass('is-active');
    var description = $link.html();
    var href = $link.attr('href');
    var id = $link.data('drupal-facet-item-id');

    var radio = $('<input type="radio" class="facets-radio">')
      .attr('id', id)
      .data($link.data())
      .data('facetsredir', href);
    var label = $('<label for="' + id + '">' + description + '</label>');

    radio.on('change.facets', function (e) {
      e.preventDefault();

      var $widget = $(this).closest('.js-facets-widget');

      Drupal.facets.disableFacet($widget);
      $widget.trigger('facets_filter', [ href ]);
    });

    if (active) {
      radio.attr('checked', true);
      label.find('.js-facet-deactivate').remove();
    }

    $link.before(radio).before(label).hide();

  };

  /**
   * Update radio active state.
   */
  Drupal.facets.updateRadio = function () {
    var $link = $(this);
    var active = $link.hasClass('is-active');

    if (!active) {
      $link.parent().find('input.facets-radio').prop('checked', false);
    }
  };

  /**
   * Disable all facet radio buttons in the facet and apply a 'disabled' class.
   *
   * @param {object} $facet
   *   jQuery object of the facet.
   */
  Drupal.facets.disableFacet = function ($facet) {
    $facet.addClass('facets-disabled');
    $('input.facets-radio', $facet).click(Drupal.facets.preventDefault);
    $('input.facets-radio', $facet).attr('disabled', true);
  };

  /**
   * Event listener for easy prevention of event propagation.
   *
   * @param {object} e
   *   Event.
   */
  Drupal.facets.preventDefault = function (e) {
    e.preventDefault();
  };

})(jQuery, Drupal, once);
