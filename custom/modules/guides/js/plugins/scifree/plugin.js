(function (CKEDITOR) {

  'use strict';

  CKEDITOR.plugins.add('scifree', {
    requires: 'widget',
    icons: 'scifree',

    init: function(editor) {
      CKEDITOR.document.appendStyleText('.cke_button__scifree_label {display: inline;}');

      editor.widgets.add('scifree', {
        button: 'SciFree',
        template: '<div class="scifree">SciFree Search Widget</div>',
        allowedContent: 'div(!scifree)',
        requiredContent: 'div(scifree)',
        upcast: function(element) {
          return element.name == 'div' && element.hasClass('scifree');
        },
      });
    }
  });
})(CKEDITOR);
