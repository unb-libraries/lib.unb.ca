(function (CKEDITOR) {

  'use strict';

  CKEDITOR.plugins.add('scifree', {
    requires: 'widget',
    icons: 'scifree',

    init: function(editor) {
      CKEDITOR.document.appendStyleText('.cke_button__scifree_label {display: inline;}');

      editor.widgets.add('scifree', {
        button: 'SciFree',
        template: '<div class="scifree"><img src="/modules/custom/guides/img/scifree-placeholder.png" /></div>',
        allowedContent: 'div(!scifree); img[!src]',
        requiredContent: 'div(scifree); img[src]',
        upcast: function(element) {
          return element.name == 'div' && element.hasClass('scifree');
        },
      });
    }
  });
})(CKEDITOR);
