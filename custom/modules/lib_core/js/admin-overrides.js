(function($) {
    'use strict';

    Drupal.behaviors.tableRowSelect = {
        attach: function ( context, settings ) {
            // Quick relabel of Paragraphs 'Collapse' dropdown input button.
            $('.dropbutton .collapse > input[value="Collapse"]').val('Preview');
        },
    };
})(jQuery);
