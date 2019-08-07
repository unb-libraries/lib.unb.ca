(function ($, Drupal) {
    Drupal.behaviors.libChosen = {
        attach: function(context, settings) {
            // Invoke chosen() on main content select tags with options.
            // More options: https://harvesthq.github.io/chosen/options.html.
            $("select.chosen-select").chosen({
                width: "100%"
            });
            // Apply bootstrap 4 form-control class to chosen div.
            $(".chosen-container-single").addClass("form-control");
            // Resolve duplicate Discovery Search selects.
            $("select.chosen-select").hide();
        }
    }
})(jQuery, Drupal);
