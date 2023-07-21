(function ($, Drupal, drupalSettings, CKEDITOR) {

  'use strict';

  var dialog = {
    url: Drupal.url.toAbsolute('/admin/guides/eresources-dialog'),
    save: function(editor, values) {
      var widgetDef = editor.widgets.registered['eresources-widget'];
      var template = widgetDef.template;
      var element = CKEDITOR.dom.element.createFromHtml(template.output(values), editor.document);
      editor.insertElement(element);
      var widget = editor.widgets.initOn(element, widgetDef.name);
      widget.setData(values);
    }
  };

  CKEDITOR.dtd['eresources'] = {
    p: 1,
    ul: 1,
    li: 1
  };
  CKEDITOR.dtd.body['eresources'] = 1;
  CKEDITOR.dtd.$block['eresources'] = 1;

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
          Drupal.ckeditor.openDialog(editor, adjustUrl(dialog.url, editor), {}, function(v) { dialog.save(editor, v); }, { dialogClass: 'eresources-dialog-widget'});
        }
      });

      editor.widgets.add('eresources-widget', {
        template: '<eresources ids="{ids}" keyresources="{keyresources}" noheadings="{noheadings}">{html}</eresources>',
        allowedContent: 'eresources[ids,keyresources,noheadings]; p; ul; li',
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
          html: '',
        },
        init: function() {
          this.setData(this.element.getAttributes());
          var widget = this;
          this.on('doubleclick', function(e) {
            Drupal.ckeditor.openDialog(editor, adjustUrl(dialog.url, editor), widget.data, function(values) {
              widget.setData(values);
              widget.element.setAttribute('ids', values.ids);
              widget.element.setAttribute('keyresources', values.keyresources);
              widget.element.setAttribute('noheadings', values.noheadings);
              widget.element.setHtml(values.html);
            }, {});
          });
        }
      });
    }
  });

  function adjustUrl(url, editor) {
    var newUrl = new URL(url);
    if (editor.config.eresources.target_entity) {
      const typeRegex = new RegExp('(' + editor.config.eresources.target_entity + ')\/(\\d+)');
      const found = window.location.pathname.match(typeRegex);
      if (found) {
        newUrl.searchParams.set(found[1], found[2]);
      }
    }
    if (editor.config.eresources.resource_type) {
      newUrl.searchParams.set('type', editor.config.eresources.resource_type);
    }

    return newUrl.toString();
  }
})(jQuery, Drupal, drupalSettings, CKEDITOR);
