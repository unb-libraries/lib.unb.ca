(function ($, Drupal) {
    Drupal.behaviors.libSelectize = {
        attach: function(context, settings) {
            // Selectize library
            $("select.selectize").selectize({
                allowEmptyOption: true,
                hideSelected: true,
            }).removeAttr("style");

            $(context).find('#title_results button[type="submit"]').click(function (e) {
                let db_select = document.querySelector("#databaseID");
                let db_selectize = document.querySelector("#databaseID-selectized");
	            if (!db_select.value) {
                    db_selectize.setCustomValidity("Please select a database option");
                    db_selectize.reportValidity();
                } else {
                    db_selectize.setCustomValidity(""); // Leave this empty!
                }
            });
        }
    }
})(jQuery, Drupal);
