<?php

namespace Drupal\ior\Entity\Routing;

use Drupal\Core\Entity\Controller\EntityController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\custom_entity\Entity\Routing\HtmlRouteProvider;
use Drupal\custom_entity_revisions\Entity\Routing\RevisionsRouteProviderTrait;
use Symfony\Component\Routing\Route;

/**
 * Html route provider for "submission" entities.
 */
class SubmissionHtmlRouteProvider extends HtmlRouteProvider {

  use RevisionsRouteProviderTrait;

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);
    $routes->addCollection($this->getAllRevisionRoutes($entity_type));

    foreach ($routes as $route) {
      if (!$parameters = $route->getOption('parameters')) {
        $parameters = [];
      }
      $parameters['contest'] = [
        'type' => 'entity:contest',
      ];
      $route->setOption('parameters', $parameters);
    }

    return $routes;
  }

  /**
   * {@inheritDoc}
   */
  protected function getAddFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('add-form')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('add-form'));
      $route->setDefaults([
        '_controller' => 'submission.form_controller:addForm',
        '_entity_form' => $entity_type->getFormClass('add')
        ? "{$entity_type_id}.add"
        : "{$entity_type_id}.default",
        '_title_callback' => EntityController::class . '::addTitle',
        'entity_type_id' => $entity_type_id,
      ])->setRequirements([
        '_entity_create_access' => $entity_type_id,
      ]);
      return $route;
    }
    return FALSE;
  }

}
