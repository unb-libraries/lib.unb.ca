(function($) {
    'use strict';

    Drupal.behaviors.tableRowSelect = {
        attach: function ( context, settings ) {
            // Click anywhere on row to toggle input.
            var checkboxes = '.views-table tbody input.form-checkbox';
            var $table_rows = $( checkboxes ).parents( 'table' ).find( 'tbody tr' );
            if ( $table_rows.length > 0 ) {
                $table_rows.click(function (e) {
                    var target = $( e.target );
                    var $current_checkbox = $( 'input.form-checkbox', $( this ) );
                    if ( target.is( "input") ) {
                        if ( $current_checkbox.prop( 'checked' ) == true ) {
                            // Limit to single selected media entity when checkbox directly clicked.
                            $( '.views-table tbody input.form-checkbox' ).prop( 'checked', false );
                            $current_checkbox.prop( 'checked', true );
                        }
                    }
                    else if ( $current_checkbox.prop( 'checked' ) == true && target.not( "input" ) ) {
                            $current_checkbox.prop( 'checked', false );
                    } else {
                        // Limit to single selected media entity when table row clicked.
                        $( '.views-table input.form-checkbox' ).prop( 'checked', false );
                        $current_checkbox.prop( 'checked', true );
                    }
                });
            }
        },
    };
})(jQuery);
