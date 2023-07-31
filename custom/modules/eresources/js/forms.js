(function ($, Drupal) {
    Drupal.behaviors.eresourcesForms = {
        attach: function(context, settings) {
            $('#reference-guide-selectized').bind('keypress', function(e) {
                if(e.which === 13) {
                    e.preventDefault();
                    $('#reference-guide-submit').click();
                }
            });
            $('#reference-guide-submit').click(function(e) {
                let cat = $('#reference-guide').val();
                e.preventDefault();
                if (cat) {
                    window.location = 'https://lib.unb.ca' + cat;
                }
            });

            $('#database-guide-selectized').bind('keypress', function(e) {
                if(e.which === 13) {
                    e.preventDefault();
                    $('#database-guide-submit').click();
                }
            });
            $('#database-guide-submit').click(function(e) {
                let cat = $('#database-guide').val();
                e.preventDefault();
                if (cat) {
                    window.location = 'https://lib.unb.ca' + cat;
                }
            });

            $('#database-selectized').bind('keypress', function(e) {
                if(e.which === 13) {
                    e.preventDefault();
                    $('#database-submit').click();
                }
            });
            $('#database-submit').click(function(e) {
                let query = $('#database').val();
                e.preventDefault();
                if (query) {
                    $('input[name=query]').val(query);
                    $('#eres-databases').submit();
                }
            });

            // Scroll to search results if available
            let resultsSelector = '.Accordion-panel:not([hidden]) .search-results-wrapper';
            if ($(resultsSelector).length) {
                $('html, body').once().animate({
                    scrollTop: $(resultsSelector).offset().top - 250
                }, 'slow');
            }
        }
    }
})(jQuery, Drupal);
