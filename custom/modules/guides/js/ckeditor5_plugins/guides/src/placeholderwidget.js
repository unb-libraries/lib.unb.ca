/**
 * @file
 * Factory for simple placeholder widgets (WorldCat search, SciFree).
 *
 * Content is stored as:
 *   <div class="CLASS"><img src="PLACEHOLDER"></div>
 * and replaced with the real widget by a guides text filter on output.
 */

import { Plugin } from 'ckeditor5/src/core';
import { toWidget, Widget } from 'ckeditor5/src/widget';
import { ButtonView } from 'ckeditor5/src/ui';
import { atomicUpcast } from './utils';

/**
 * Builds a placeholder widget plugin class.
 *
 * @param {object} options
 *   - pluginName: CKEditor plugin name.
 *   - modelName: model element name.
 *   - cssClass: class of the wrapping div.
 *   - imgSrc: placeholder image path.
 *   - toolbarItem: toolbar item name.
 *   - label: button/widget label.
 *
 * @return {typeof Plugin}
 *   The plugin class.
 */
export default function createPlaceholderWidget(options) {
  const { pluginName, modelName, cssClass, imgSrc, toolbarItem, label } =
    options;

  const buildView = (writer) => {
    const div = writer.createContainerElement('div', { class: cssClass });
    const img = writer.createEmptyElement('img', { src: imgSrc, alt: '' });
    writer.insert(writer.createPositionAt(div, 0), img);
    return div;
  };

  return class PlaceholderWidget extends Plugin {
    static get requires() {
      return [Widget];
    }

    static get pluginName() {
      return pluginName;
    }

    init() {
      const { editor } = this;

      editor.model.schema.register(modelName, {
        inheritAllFrom: '$blockObject',
      });

      editor.conversion.for('upcast').add(
        atomicUpcast(
          (viewElement) =>
            viewElement.name === 'div' && viewElement.hasClass(cssClass),
          (viewElement, writer) => writer.createElement(modelName),
        ),
      );

      editor.conversion.for('dataDowncast').elementToElement({
        model: modelName,
        view: (modelElement, { writer }) => {
          // Data output must not carry the alt attribute the editing view
          // uses, so that the guides text filters keep matching.
          const div = writer.createContainerElement('div', { class: cssClass });
          const img = writer.createEmptyElement('img', { src: imgSrc });
          writer.insert(writer.createPositionAt(div, 0), img);
          return div;
        },
      });

      editor.conversion.for('editingDowncast').elementToElement({
        model: modelName,
        view: (modelElement, { writer }) =>
          toWidget(buildView(writer), writer, { label }),
      });

      editor.ui.componentFactory.add(toolbarItem, (locale) => {
        const button = new ButtonView(locale);
        button.set({ label, withText: true, tooltip: true });
        this.listenTo(button, 'execute', () => {
          editor.model.change((writer) => {
            editor.model.insertObject(writer.createElement(modelName), null, null, {
              setSelection: 'on',
            });
          });
          editor.editing.view.focus();
        });
        return button;
      });
    }
  };
}
