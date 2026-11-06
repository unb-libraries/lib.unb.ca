/**
 * @file
 * CKEditor 5 plugin for e-Resources lists.
 *
 * Content is stored as:
 *   <eresources ids="1,2,3" keyresources="10" noheadings="0">
 *     <p>List of resources as of …</p><ul><li>…</li></ul>
 *   </eresources>
 * The inner HTML is only a preview for editors; the guides eresources text
 * filter replaces the whole element with the rendered list on output.
 */

import { Plugin } from 'ckeditor5/src/core';
import { toWidget, Widget } from 'ckeditor5/src/widget';
import { ButtonView } from 'ckeditor5/src/ui';
import { DomEventObserver } from 'ckeditor5/src/engine';
import {
  atomicUpcast,
  escapeHtml,
  findViewDescendants,
  viewText,
} from './utils';

const MODEL = 'guidesEresources';
const ATTRIBUTES = ['ids', 'keyresources', 'noheadings'];

/**
 * Lets the editing view fire "dblclick" events.
 */
class DoubleClickObserver extends DomEventObserver {
  constructor(view) {
    super(view);
    this.domEventType = 'dblclick';
  }

  onDomEvent(domEvent) {
    this.fire(domEvent.type, domEvent);
  }
}

/**
 * Builds preview HTML from an upcast <eresources> view element.
 */
function previewFromView(viewElement) {
  const paragraphs = findViewDescendants(viewElement, 'p');
  const items = findViewDescendants(viewElement, 'li');
  let html = '';
  if (paragraphs.length) {
    html += `<p>${escapeHtml(viewText(paragraphs[0]).trim())}</p>`;
  }
  if (items.length) {
    html += `<ul>${items
      .map((li) => `<li>${escapeHtml(viewText(li).trim())}</li>`)
      .join('')}</ul>`;
  }
  return html;
}

export default class Eresources extends Plugin {
  static get requires() {
    return [Widget];
  }

  static get pluginName() {
    return 'Eresources';
  }

  init() {
    const { editor } = this;

    editor.model.schema.register(MODEL, {
      inheritAllFrom: '$blockObject',
      allowAttributes: [...ATTRIBUTES, 'previewHtml'],
    });

    editor.conversion.for('upcast').add(
      atomicUpcast(
        (viewElement) => viewElement.name === 'eresources',
        (viewElement, writer) =>
          writer.createElement(MODEL, {
            ids: viewElement.getAttribute('ids') || '',
            keyresources: viewElement.getAttribute('keyresources') ?? '10',
            noheadings: viewElement.getAttribute('noheadings') ?? '0',
            previewHtml: previewFromView(viewElement),
          }),
      ),
    );

    editor.conversion.for('dataDowncast').elementToElement({
      model: MODEL,
      view: (modelElement, { writer }) =>
        writer.createRawElement(
          'eresources',
          Object.fromEntries(
            ATTRIBUTES.map((key) => [key, String(modelElement.getAttribute(key) ?? '')]),
          ),
          (domElement) => {
            domElement.innerHTML = modelElement.getAttribute('previewHtml') || '';
          },
        ),
    });

    editor.conversion.for('editingDowncast').elementToElement({
      model: MODEL,
      view: (modelElement, { writer }) => {
        const container = writer.createContainerElement('div', {
          class: 'guides-eresources-widget',
        });
        const preview = writer.createRawElement(
          'div',
          { class: 'guides-eresources-widget__preview' },
          (domElement) => {
            const ids = modelElement.getAttribute('ids') || '';
            const count = ids ? ids.split(',').length : 0;
            domElement.innerHTML =
              `<div class="guides-eresources-widget__title">e-Resources list ` +
              `(${count} selected — double-click to edit)</div>` +
              (modelElement.getAttribute('previewHtml') || '');
          },
        );
        writer.insert(writer.createPositionAt(container, 0), preview);
        return toWidget(container, writer, { label: 'e-Resources list' });
      },
    });

    editor.ui.componentFactory.add('eresources', (locale) => {
      const button = new ButtonView(locale);
      button.set({
        label: Drupal.t('e-Resources'),
        tooltip: Drupal.t('Add or edit an e-Resources list'),
        withText: true,
      });
      this.listenTo(button, 'execute', () => this.openDialog());
      return button;
    });

    const view = editor.editing.view;
    view.addObserver(DoubleClickObserver);
    this.listenTo(view.document, 'dblclick', () => {
      if (this.selectedEresources()) {
        this.openDialog();
      }
    });
  }

  /**
   * Returns the selected e-Resources model element, if any.
   */
  selectedEresources() {
    const element = this.editor.model.document.selection.getSelectedElement();
    return element && element.is('element', MODEL) ? element : null;
  }

  /**
   * Opens the Drupal e-Resources dialog to insert or edit a list.
   */
  openDialog() {
    const { editor } = this;
    const options = editor.config.get('guidesEresources') || {};
    const { dialogURL, openDialog, dialogSettings = {} } = options;
    if (!dialogURL || typeof openDialog !== 'function') {
      return;
    }

    const url = new URL(dialogURL, document.baseURI);
    // Mirror the CKEditor 4 behaviour: pass the guide/category being edited.
    if (options.targetEntity) {
      const match = window.location.pathname.match(
        new RegExp(`(${options.targetEntity})/(\\d+)`),
      );
      if (match) {
        url.searchParams.set(match[1], match[2]);
      }
    }
    if (options.resourceType) {
      url.searchParams.set('type', options.resourceType);
    }

    const existing = this.selectedEresources();
    if (existing) {
      ATTRIBUTES.forEach((key) => {
        const value = existing.getAttribute(key);
        if (value !== undefined && value !== null) {
          url.searchParams.set(key, value);
        }
      });
    }

    openDialog(
      url.toString(),
      (values) => {
        editor.model.change((writer) => {
          const element = writer.createElement(MODEL, {
            ids: String(values.ids ?? ''),
            keyresources: String(values.keyresources ?? '10'),
            noheadings: values.noheadings ? '1' : '0',
            previewHtml: values.html || '',
          });
          // Replace the existing element so the editing view re-renders.
          if (existing && existing.root.rootName !== '$graveyard') {
            const position = writer.createPositionBefore(existing);
            writer.remove(existing);
            writer.insert(element, position);
            writer.setSelection(element, 'on');
          }
          else {
            editor.model.insertObject(element, null, null, {
              setSelection: 'on',
            });
          }
        });
        editor.editing.view.focus();
      },
      dialogSettings,
    );
  }
}
