(function ($, Drupal, drupalSettings, CKEDITOR) {

  'use strict';

  var dialog = {
    url: Drupal.url('admin/guides/eresources-dialog'),
    save: function(editor, values) {
      var widgetDef = editor.widgets.registered['eresources-widget'];
      var template = widgetDef.template;
      var element = CKEDITOR.dom.element.createFromHtml(template.output(values), editor.document);
      editor.insertElement(element);
      var widget = editor.widgets.initOn(element, widgetDef.name);
      widget.setData(values);
    }
  };

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
          Drupal.ckeditor.openDialog(editor, dialog.url, {}, function(v) { dialog.save(editor, v); }, {});
        }
      });

      editor.widgets.add('eresources-widget', {
        template: '<eresources ids="{ids}" keyresources="{keyresources}" noheadings="{noheadings}" searchbox="{searchbox}">{html}</ereources>',
        allowedContent: 'eresources[ids,keyresources,noheadings,searchbox]; ul; li',
        requiredContent: 'eresources',
        upcast: function(element, data) {
          if (element.name == 'eresources') {
            Object.assign(data, element.attributes);
            return true;
          }
        },
        defaults: {
          ids: '',
          keyresources: 10,
          noheadings: 0,
          searchbox: 0,
          html: '',
        },
        init: function() {
          this.setData(this.element.getAttributes());
          var widget = this;
          this.on('doubleclick', function(e) {
            Drupal.ckeditor.openDialog(editor, dialog.url, widget.data, function(values) {
              widget.setData(values);
              widget.element.setAttribute('ids', values.ids);
              widget.element.setAttribute('keyresources', values.keyresources);
              widget.element.setAttribute('searchbox', values.searchbox);
              widget.element.setAttribute('noheadings', values.noheadings);
              widget.element.setHtml(values.html);
            }, {});
          });
        }
      });
    }
  });
})(jQuery, Drupal, drupalSettings, CKEDITOR);
