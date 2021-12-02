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
 *     "storage" = "Drupal\ior\Entity\Storage\ContestStorage",
 *     "route_provider" = {
 *       "html" = "Drupal\ior\Entity\Routing\ContestHtmlRouteProvider"
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler"
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
 *     "canonical" = "/researchcommons/ior/contests/{contest}",
 *     "add-form" = "/researchcommons/ior/contests/add",
 *     "edit-form" = "/researchcommons/ior/contests/{contest}/edit",
 *     "delete-form" = "/researchcommons/ior/contests/{contest}/delete",
 *     "revisions" = "/researchcommons/ior/contests/{contest}/revisions",
 *     "revision" = "/researchcommons/ior/contests/{contest}/revisions/{contest_revision}",
 *     "revision-restore-form" = "/researchcommons/ior/contests/{contest}/revisions/{contest_revision}/restore",
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
      ->date
      ->setTime(0, 0, 0);
  }

  /**
   * {@inheritDoc}
   */
  public function getCloseDate() {
    return $this->get(static::FIELD_DATE_CLOSE)
      ->date
      ->setTime(23, 59, 59);
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
