<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines an IOR Award.
 *
 * @ConfigEntityType(
 *   id = "ior_award_type",
 *   label = @Translation("Award type"),
 *   label_plural = @Translation("Award types"),
 *   label_collection = @Translation("Award types"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\ior\Form\AwardTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider",
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler",
 *   },
 *   config_prefix = "ior_award_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "quantity",
 *     "weight",
 *   },
 *   links = {
 *     "add-form" = "/researchcommons/ior/awards/add",
 *     "edit-form" = "/researchcommons/ior/awards/{ior_award_type}/edit",
 *     "delete-form" = "/researchcommons/ior/awards/{ior_award_type}/delete",
 *   }
 * )
 */
class AwardType extends ConfigEntityBase implements AwardTypeInterface {

  /**
   * {@inheritDoc}
   */
  public function getQuantity() {
    return $this->get('quantity');
  }

  /**
   * {@inheritDoc}
   */
  public function getWeight() {
    return $this->get('weight');
  }

}
