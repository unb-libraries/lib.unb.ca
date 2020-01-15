/**
 * @file
 * A Backbone Model for OCLC Status.
 */
(function ($, Backbone) {
  /**
   * Backbone model for OCLC Status.
   *
   * @constructor
   *
   * @augments Backbone.Model
   */
  OCLCStatusModel = Backbone.Model.extend({
    /**
     * @type {object}
     *
     * @prop status
     */
    defaults: {
      status: false,
    },
    url: '',
    message: '',
    alert: '',

    getStatus: function() {
      return this.get('status');
    },

    initialize: function(status, options) {
      this.set('status', status);
      this.message = options.message;
      this.url = options.url;

      this.alert = $(OCLCStatusTemplate({message: this.message}));
      $('#discovery-search').append(this.alert);

      this.fetch({
          success: function(model, response, options) {
            model.set('status', response.status);
            model.toggleView();
          }
      });
      if (options.autoRefresh) {
        this.enableAutoRefresh(options.refreshInterval);
      }
    },

    toggleView: function() {
        if(this.getStatus() === true && this.alert.is(':hidden')) {
            this.alert.slideDown(150);
        }
        else if(!this.getStatus() && this.alert.is(':visible')) {
            this.alert.slideUp(150);
        }
    },

    enableAutoRefresh: function(interval) {
      model = this;
      setInterval(function() {
          model.fetch({
              success: function(model, response, options) {
                  model.set('status', response.status);
                  model.toggleView();
              }
          });
      }, interval);
    }
  });
})(jQuery, Backbone);
