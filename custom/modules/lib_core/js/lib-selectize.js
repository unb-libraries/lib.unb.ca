(function ($, Drupal) {
    Drupal.behaviors.libSelectize = {
        attach: function(context, settings) {
            // Selectize library
            $("select.selectize").selectize({
                allowEmptyOption: false,
                hideSelected: true,
                maxOptions: 7500,
            }).removeAttr("style");

            $(context).find('form.custom-selectize button[type="submit"]').click(function (e) {
                let form = this.closest('form');
                let selectized_select = form.querySelector("select.selectize");
                let selectize_input = form.querySelector(".selectize-input input");

	            if (!selectized_select.value) {
                    selectize_input.setCustomValidity("Please select a value to submit");
                    selectize_input.reportValidity();
                } else {
                    selectize_input.setCustomValidity(""); // Leave this empty!
                }
            });
        }
    }
})(jQuery, Drupal);
