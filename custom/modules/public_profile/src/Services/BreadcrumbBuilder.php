<?php

namespace Drupal\public_profile\Services;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Custom breadcrumb for public profile pages.
 */
class BreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    if ($route_match->getRouteName() == 'entity.profile.canonical') {
      $profile = $route_match->getParameter('profile');
      if ($profile->bundle() == 'public') {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addCacheContexts(['url.path']);

    $breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));
    $breadcrumb->addLink(Link::createFromRoute($this->t('Get Specialized Help'), 'view.connect_with_a_librarian.page_1'));

    $profile = $route_match->getParameter('profile');
    $breadcrumb->addLink(Link::createFromRoute($profile->label(), '<none>'));
    return $breadcrumb;
  }

}
