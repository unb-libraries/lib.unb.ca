/**
 * @file
 * Shared helpers for the guides CKEditor 5 plugins.
 */

/**
 * Escapes a string for safe use inside HTML text or attribute values.
 *
 * @param {string} value
 *   The raw string.
 *
 * @return {string}
 *   The escaped string.
 */
export function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

/**
 * Returns the concatenated text content of a view node.
 *
 * @param {module:engine/view/node~Node} node
 *   A view element or text node.
 *
 * @return {string}
 *   The text content.
 */
export function viewText(node) {
  if (node.is('$text') || node.is('$textProxy')) {
    return node.data;
  }
  let text = '';
  if (node.getChildren) {
    for (const child of node.getChildren()) {
      text += viewText(child);
    }
  }
  return text;
}

/**
 * Finds all descendants of a view element with the given name.
 *
 * @param {module:engine/view/element~Element} element
 *   The element to search.
 * @param {string} name
 *   The element name to find.
 *
 * @return {Array}
 *   Matching view elements, in document order.
 */
export function findViewDescendants(element, name) {
  const found = [];
  for (const child of element.getChildren()) {
    if (child.is('element')) {
      if (child.name === name) {
        found.push(child);
      }
      found.push(...findViewDescendants(child, name));
    }
  }
  return found;
}

/**
 * Creates an upcast converter that turns a matching view element (and its
 * whole subtree) into a single atomic model element.
 *
 * Children are deliberately not converted: the model element is a black box
 * whose content is regenerated on downcast.
 *
 * @param {function} matches
 *   Callback receiving a view element; returns TRUE if it should be handled.
 * @param {function} create
 *   Callback receiving (viewElement, modelWriter); returns the model element.
 *
 * @return {function}
 *   A function to pass to `conversion.for('upcast').add()`.
 */
export function atomicUpcast(matches, create) {
  return (dispatcher) => {
    dispatcher.on(
      'element',
      (evt, data, conversionApi) => {
        const { viewItem } = data;
        if (!matches(viewItem)) {
          return;
        }
        if (!conversionApi.consumable.test(viewItem, { name: true })) {
          return;
        }
        const modelElement = create(viewItem, conversionApi.writer);
        if (!conversionApi.safeInsert(modelElement, data.modelCursor)) {
          return;
        }
        conversionApi.consumable.consume(viewItem, {
          name: true,
          attributes: [...viewItem.getAttributeKeys()].filter(
            (key) => key !== 'class',
          ),
          classes: [...viewItem.getClassNames()],
        });
        conversionApi.updateConversionResult(modelElement, data);
      },
      { priority: 'high' },
    );
  };
}
