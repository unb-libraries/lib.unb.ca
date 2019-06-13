(function ($, Drupal) {
    Drupal.behaviors.libChosen = {
        attach: function(context, settings) {
            // Apply chosen plugin to chosen-select + config width.
            $(".chosen-select").chosen({width: "100%"});
            // Apply bootstrap 4 form-control class to chosen div.
            $(".chosen-container-single").addClass("form-control");
        }
    }
})(jQuery, Drupal);
