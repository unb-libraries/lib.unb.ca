(function($) {
    'use strict';

    Drupal.behaviors.tableRowCheck = {
        attach: function (context) {
            // Click anywhere on row to toggle input.
            var checkboxes = '.views-table tbody input.form-checkbox';
            var table_rows = $(context).find(checkboxes).parents('table').find('tbody tr');
            // Disable duplicate click event following AJAX form exposed filter submit.
            var contextTag = ($(context).prop('tagName'));
            if (table_rows.length > 0 && contextTag != 'FORM') {
                table_rows.click(function (e) {
                    var target = $(e.target);
                    var $current_checkbox = $('input.form-checkbox', $(this));
                    if (target.is("input")) {
                        target.parents('table').find('tbody tr').removeClass('color-warning');
                        if ($current_checkbox.prop('checked') == true) {
                            // Limit to single selected media entity when checkbox directly clicked.
                            $('.views-table tbody input.form-checkbox').prop('checked', false);
                            $current_checkbox.prop('checked', true);
                            target.parents('tr').addClass('color-warning');
                        }
                    } else if ($current_checkbox.prop('checked') == true && target.not("input")) {
                        $current_checkbox.prop('checked', false);
                        target.parents('table').find('tbody tr').removeClass('color-warning');
                        return false;
                    } else {
                        // Limit to single selected media entity when table row clicked.
                        $('.views-table input.form-checkbox').prop('checked', false);
                        $current_checkbox.prop('checked', true);
                        target.parents('table').find('tbody tr').removeClass('color-warning');
                        target.parents('tr').addClass('color-warning');
                        return false;
                    }
                });
            }
        }
    }
}(jQuery));
