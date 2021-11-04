<?php

namespace Drupal\ior\Entity;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * The "Contest" entity.
 *
 * @ContententityType(
 *   id = "contest",
 *   label = @Translation("Contest"),
 *   label_plural = @Translation("Contests"),
 *   label_collection = @Translation("Contests"),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider"
 *     }
 *   },
 *   base_table = "ior_contest",
 *   revision_table = "ior_contest_revision",
 *   admin_permission = "administer contest entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "rid",
 *     "label" = "field_title",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/researchcommons/ior/{contest}",
 *     "add-form" = "/researchcommons/ior/add",
 *     "edit-form" = "/researchcommons/ior/{contest}/edit",
 *     "delete-form" = "/researchcommons/ior/{contest}/delete",
 *     "revisions" = "/researchcommons/ior/{contest}/revisions",
 *     "revision" = "/researchcommons/ior/{contest}/revisions/{contest_revision}",
 *     "revision-restore-form" = "/records/{contest}/revisions/{contest_revision}/restore",
 *   },
 *   field_ui_base_route = "entity.contest.settings",
 * )
 */
class Contest extends ContentEntityBase implements ContestInterface {

  /**
   * {@inheritDoc}
   */
  public function getTitle() {
    return $this->get(static::FIELD_TITLE)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getDescription() {
    return $this->get(static::FIELD_DESCRIPTION)
      ->processed;
  }

  /**
   * {@inheritDoc}
   */
  public function getOpenDate() {
    return $this->get(static::FIELD_DATE_OPEN)
      ->date;
  }

  /**
   * {@inheritDoc}
   */
  public function getCloseDate() {
    return $this->get(static::FIELD_DATE_CLOSE)
      ->date;
  }

  /**
   * {@inheritDoc}
   */
  public function isOpen() {
    $now = new DrupalDateTime();
    return $now >= $this->getOpenDate() && $now <= $this->getCloseDate();
  }

  /**
   * {@inheritDoc}
   */
  public function isComingUp() {
    $now = new DrupalDateTime();
    return $now < $this->getOpenDate();
  }

}
