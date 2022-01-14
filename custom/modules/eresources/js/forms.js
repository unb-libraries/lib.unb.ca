(function ($, Drupal) {
    Drupal.behaviors.eresourcesForms = {
        attach: function(context, settings) {
            $('#eresources-reference-form .chosen-container, #eresources-databases-form .chosen-container').bind('keypress', function(e) {
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
                    $('#eresources-databases-form').submit();
                }
            });
            $('#eresources-reference-form input[name=query], #eresources-databases-form input[name=query]').on('keypress', function (e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    $(e.target).closest('form').find('input[type=submit]').last().focus().click();
                }
            });
        }
    }
})(jQuery, Drupal);
