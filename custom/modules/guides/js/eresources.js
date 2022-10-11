(function ($, Drupal, once) {
    Drupal.behaviors.eResourcesSelector = {
        attach: function (context, settings) {
            function serializeSelections() {
              var options = $('#records select option');
              var ids = $.map(options, function(option) {
                return option.value;
              }).join(',');
              $('.eresources-selector [name=ids]').val(ids);
            };
            $(once('eresources-add', '.eresources-selector .add', context)).click(function(event) {
              event.preventDefault();
              var opt = $('#record-select select option:selected').clone();
              $('#records select').append(opt);
              serializeSelections();
            });
            $(once('eresources-remove', '.eresources-selector .remove', context)).click(function(event) {
              event.preventDefault();
              $('#records select option:selected').remove();
              serializeSelections();
            });
            $(once('eresources-up', '.eresources-selector .up', context)).click(function(event) {
              event.preventDefault();
              var opt = $('#records select option:selected');
              if (opt.length > 1) {
                return;
              }
              if (opt.is(':first-child')) {
                opt.insertAfter($('#records select option:last-child'));
              }
              else {
                opt.insertBefore(opt.prev());
              }
              serializeSelections();
            });
            $(once('eresources-down', '.eresources-selector .down', context)).click(function(event) {
              event.preventDefault();
              var opt = $('#records select option:selected');
              if (opt.length > 1) {
                return;
              }
              if (opt.is(':last-child')) {
                opt.insertBefore($('#records select option:first-child'));
              }
              else {
                opt.insertAfter(opt.next());
              }
              serializeSelections();
            });
        }
    };
})(jQuery, Drupal, once);
