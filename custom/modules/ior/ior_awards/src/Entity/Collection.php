<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\ior\Entity\ContestInterface;

/**
 * The "Collection" entity.
 *
 * @ContententityType(
 *   id = "ior_collection",
 *   label = @Translation("Collection"),
 *   label_plural = @Translation("Collections"),
 *   label_collection = @Translation("Collections"),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider"
 *     },
 *     "storage" = "Drupal\ior_awards\Entity\Storage\CollectionStorage",
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler"
 *   },
 *   base_table = "ior_collection",
 *   admin_permission = "administer collection entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "field_title",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/researchcommons/ior/collections/{ior_collection}",
 *     "add-form" = "/researchcommons/ior/collections/add",
 *     "edit-form" = "/researchcommons/ior/collections/{ior_collection}/edit",
 *     "delete-form" = "/researchcommons/ior/collections/{ior_collection}/delete",
 *   },
 *   field_ui_base_route = "entity.ior_collection.settings",
 * )
 */
class Collection extends ContentEntityBase implements CollectionInterface {

  /**
   * {@inheritDoc}
   */
  public function getContest() {
    return $this->get(self::FIELD_CONTEST)
      ->entity;
  }

  /**
   * {@inheritDoc}
   */
  public function setContest(ContestInterface $contest) {
    $this->set(self::FIELD_CONTEST, $contest);
  }

}
