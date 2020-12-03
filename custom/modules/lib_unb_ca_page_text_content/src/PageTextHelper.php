<?php

namespace Drupal\lib_unb_ca_page_text_content;

use Html2Text\Html2Text;

/**
 * Defines an object to help with library pages.
 */
class PageTextHelper {

  /**
   * Extract textual content from a page.
   */
  public static function getPageTextualContent($node) {
    $content_paragraphs = [];
    $content = $node->get('field_page_content');

    // Get first paragraph.
    $html = NULL;
    $paragraphs = $content->referencedEntities();
    if (!empty($paragraphs)) {
      foreach ($paragraphs as $cur_paragraph) {
        if (!empty($cur_paragraph->field_body)) {
          $content_paragraphs = $paragraphs;
        }
        if (!empty($cur_paragraph->field_column_1)) {
          $content_paragraphs = $cur_paragraph->get('field_column_1')->referencedEntities();
        }
      }
    }

    $html = NULL;
    $builder = \Drupal::entityTypeManager()->getViewBuilder('paragraph');
    foreach ($content_paragraphs as $content_paragraph) {
      $render_array = $builder->view($content_paragraph, 'full');
      $html .= \Drupal::service('renderer')->renderPlain($render_array);
    }

    $options = [
      'do_links' => 'none',
      'width' => 0,
    ];

    $html = new Html2Text($html, $options);
    $text = trim($html->getText());
    $text = preg_replace('/\[.*?\]/', '', $text);
    return $text;
  }

}
