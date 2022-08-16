(function (CKEDITOR) {

  'use strict';

  CKEDITOR.plugins.add('eresources', {
    requires: 'widget',
    icons: 'eresources',

    init: function(editor) {
      editor.ui.addButton('eresources', {
        label: 'e-Resources',
        title: 'Add an e-Resources list',
        command: 'eresources',
        toolbar: 'lists'
      });
      CKEDITOR.document.appendStyleText('.cke_button__eresources_label {display: inline;}');

      editor.widgets.add('eresources', {
        button: 'Add an e-Resources list',
        template: '<eresources keyresources="" noheadings="false" searchbox="false"></ereources>',
        allowedContent: 'eresources[keyresources,noheadings,searchbox]',
        requiredContent: 'eresources',
        upcast: function(element) {
          return element.name == 'eresources';
        },
      });
    }
  });
})(CKEDITOR);
