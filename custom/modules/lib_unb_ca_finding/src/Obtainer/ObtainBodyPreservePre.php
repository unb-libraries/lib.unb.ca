<?php

namespace Drupal\lib_unb_ca_finding\Obtainer;

use Drupal\migration_tools\Obtainer\ObtainHtml;

/**
 * Custom obtainer to preserve whitespace/newlines (esp. in <pre>).
 */
class ObtainBodyPreservePre extends ObtainHtml {

  /**
   * Preserve line endings/tabs; avoid aggressive normalization.
   */
  public static function cleanString($string) {
    // Normalize line endings only.
    return str_replace(["\r\n", "\r"], "\n", $string);
  }

  /**
   * Override to avoid QueryPath innerHtml() whitespace normalization.
   */
  protected function pluckSelector($selector, $n = 1, $method = 'text') {
    $n = ($n > 0) ? $n - 1 : 0;

    if (empty($selector)) {
      return '';
    }

    $elements = $this->queryPath->find($selector);

    foreach ((is_object($elements)) ? $elements : [] as $i => $element) {
      if ($i !== $n) {
        continue;
      }

      // Mark for removal (keeps behavior consistent with parent).
      $this->setElementToRemove($element);
      $this->setCurrentFindMethod("pluckSelector($selector, " . ($n + 1) . ", $method)");

      // Intercept innerHtml specifically.
      if (strtolower($method) === 'innerhtml') {
        $dom_element = $element->get(0, TRUE);

        // Serialize children using DOMDocument; tends to preserve newline text nodes.
        if ($dom_element instanceof \DOMNode && $dom_element->ownerDocument instanceof \DOMDocument) {
          $html = '';
          foreach ($dom_element->childNodes as $child) {
            $html .= $dom_element->ownerDocument->saveHTML($child);
          }
          return $html;
        }

        // Fallback.
        return $element->innerHtml();
      }

      // Default behavior for other methods (text/html/etc).
      return $element->$method();
    }

    return '';
  }

}