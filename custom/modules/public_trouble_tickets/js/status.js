(function ($, Drupal) {
  function loadContent(context) {
    $(context).find('#status-page').each(function() {
      var urlParams = new URLSearchParams(window.location.search);
      var level = urlParams.get('level') ?? '';
      $(this).load('https://web.lib.unb.ca/status/' + level + ' #status-page', function() {
        window.setTimeout(loadContent, 120000, context);
      });
    });
  }

  Drupal.behaviors.loadStatus = {
    attach: function (context) {
      loadContent(context);
    }
  };
})(jQuery, Drupal);
