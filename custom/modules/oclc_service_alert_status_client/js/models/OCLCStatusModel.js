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
     * @prop message
     */
    defaults: {
      status: false,
      message: '',
    },
    url: '',
    alert: '',
    options: '',

    getStatus: function() {
      return this.get('status');
    },

    getMessage: function() {
      return this.get('message');
    },

    initialize: function(status, options) {
      this.set('status', status);
      this.set('message', options.message);
      this.url = options.url;

      this.alert = $(OCLCStatusTemplate({message: this.getMessage()}));
      this.options = options;
      $('#discovery-search').append(this.alert);

      this.fetch({
          success: function(model, response) {
            model.set('status', response.status);
            model.set('message', model.options.message);
            if (response.message) {
                model.set('message', response.message);
            }
            $('#oclc-service-alert-message').html(model.getMessage());
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
              success: function(model, response) {
                  model.set('status', response.status);
                  model.set('message', model.options.message);
                  if (response.message) {
                      model.set('message', response.message);
                  }
                  $('#oclc-service-alert-message').html(model.getMessage());
                  model.toggleView();
              }
          });
      }, interval);
    }
  });
})(jQuery, Backbone);
