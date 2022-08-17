(function (CKEDITOR) {

  'use strict';

  CKEDITOR.plugins.add('wc-search', {
    requires: 'widget',
    icons: 'wc-search',

    init: function(editor) {
      CKEDITOR.document.appendStyleText('.cke_button__wc-search_label {display: inline;}');

      editor.widgets.add('wc-search', {
        button: 'UNB WorldCat',
        template: '<div class="wc-search"><img src="/modules/custom/guides/img/wc-search-placeholder.png" /></div>',
        allowedContent: 'div(!wc-search); img[!src]',
        requiredContent: 'div(wc-search); img[src]',
        upcast: function(element) {
          return element.name == 'div' && element.hasClass('wc-search');
        },
      });
    }
  });
})(CKEDITOR);
