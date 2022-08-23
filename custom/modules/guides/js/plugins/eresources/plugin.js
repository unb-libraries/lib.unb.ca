(function ($, Drupal, drupalSettings, CKEDITOR) {

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

      editor.addCommand('eresources', {
        exec: function (editor) {
          var url = Drupal.url('admin/guides/eresources-dialog');
          var existingValues = {};
          var saveCallback = function (returnValues) {
            console.log(returnValues);
            editor.execCommand('eresources-widget');
          };
          var dialogSettings = {
            title: 'e-Resources',
            dialogClass: 'eresources-dialog'
          };
          Drupal.ckeditor.openDialog(editor, url, existingValues, saveCallback, dialogSettings);
        }
      });

      editor.widgets.add('eresources-widget', {
        template: '<eresources keyresources="" noheadings="false" searchbox="false"></ereources>',
        allowedContent: 'eresources[keyresources,noheadings,searchbox]',
        requiredContent: 'eresources',
        upcast: function(element) {
          return element.name == 'eresources';
        },
      });

    }
  });
})(jQuery, Drupal, drupalSettings, CKEDITOR);
