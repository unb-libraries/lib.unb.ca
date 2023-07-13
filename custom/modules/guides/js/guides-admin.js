(function ($, Drupal) {
    Drupal.behaviors.SelectizeHighlight = {
        attach: function (context, settings) {
            var control = $('select.selectize')[0].selectize;
            control.settings.render.option = function(data, escape) {
                var bg = '#FCE4EC';
                if (data.text.includes("[LOCAL]")) {
                    bg = '#E3F2FD'
                }
                return '<div style="background-color: ' + bg + '">' + escape(data.text) + '</div>';
            }
        }
    };
})(jQuery, Drupal);
