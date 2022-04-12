(function ($, Drupal) {
    Drupal.behaviors.eresourcesForms = {
        attach: function(context, settings) {
            $('#eres-reference .chosen-container, #eres-databases .chosen-container').bind('keypress', function(e) {
                if(e.which === 13) {
                    var id = this.id.replace('_chosen', '-submit').replace('_', '-');
                    $('#' + id).click();
                }
            });
            $('#reference-guide-submit').click(function(e) {
                let cat = $('#reference-guide').val();
                if (cat) {
                    e.preventDefault();
                    window.location = 'https://guides.lib.unb.ca/category/' + cat;
                }
            });
            $('#database-guide-submit').click(function(e) {
                let cat = $('#database-guide').val();
                if (cat) {
                    e.preventDefault();
                    window.location = 'https://guides.lib.unb.ca/category/' + cat;
                }
            });
            $('#database-submit').click(function(e) {
                let query = $('#database').val();
                if (query) {
                    e.preventDefault();
                    $('input[name=query]').val(query);
                    $('#eres-databases').submit();
                }
            });
            $('#eres-reference input[name=query], #eres-databases input[name=query]').on('keypress', function (e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    $(e.target).closest('form').find('input[type=submit]').last().focus().click();
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
