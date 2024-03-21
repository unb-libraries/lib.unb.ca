(function ($, Drupal) {
    Drupal.behaviors.libSelectize = {
        attach: function(context) {
            // Selectize library
            $("select.selectize").selectize({
                hideSelected: true,
                maxItems: 1,
                maxOptions: 7500,
            });

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
