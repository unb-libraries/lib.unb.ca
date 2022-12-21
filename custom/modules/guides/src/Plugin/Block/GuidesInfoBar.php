<?php

namespace Drupal\guides\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an information bar for guides.
 *
 * @Block(
 *   id = "guides_info_bar_block",
 *   admin_label = @Translation("Guides Information Bar"),
 *   category = @Translation("UNB Libraries Guides"),
 * )
 */
class GuidesInfoBar extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $routeMatch = \Drupal::routeMatch();

    if ($routeMatch->getRouteName() == 'entity.guide.canonical') {
      $guide = $routeMatch->getParameter('guide');

      $isPublished = $guide->get('status')->getString();
      $published = $isPublished ? 'a <em>published</em>' : 'an <em>unpublished</em>';
      $published .= $isPublished && $guide->get('unlisted')->getString() ? ' <em>(unlisted)</em>' : '';

      $type = $guide->get('is_subject_guide')->getString() ? 'subject-level' : 'course-level';

      $status = "This is {$published} {$type} guide";

      $categories = [];
      foreach ($guide->get('guide_categories') as $category) {
        $categories[] = $category->entity->toLink()->toString();
      }

      if (!empty($categories)) {
        $status .= ' within ' . implode(' and ', $categories);
      }

      // @todo Add course info part.
      $build['#markup'] = '<p class="alert alert-info"><span class="fas fa-certificate"></span> ' . $status . '.</p>';
    }

    return $build;
  }

}
