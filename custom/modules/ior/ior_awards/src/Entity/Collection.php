<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\ior\Entity\ContestInterface;
use Drupal\ior_awards\Plugin\Field\FieldType\ComputedCollectionSubmissionsEntityReferenceFieldItemList;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

/**
 * The "Collection" entity.
 *
 * @ContententityType(
 *   id = "ior_collection",
 *   label = @Translation("Collection"),
 *   label_plural = @Translation("Collections"),
 *   label_collection = @Translation("Collections"),
 *   handlers = {
 *     "views_data" = "Drupal\ior_awards\Entity\CollectionViewsData",
 *     "form" = {
 *       "default" = "Drupal\ior_awards\Form\CollectionForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ior_awards\Entity\Routing\CollectionHtmlRouteProvider"
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
 *     "add-form" = "/researchcommons/ior/contests/{contest}/collections/add",
 *     "edit-form" = "/researchcommons/ior/contests/{contest}/collections/{ior_collection}/edit",
 *     "delete-form" = "/researchcommons/ior/contests/{contest}/collections/{ior_collection}/delete",
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

  /**
   * {@inheritDoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    $uri_route_parameters['contest'] = $this->getContest()->id();
    return $uri_route_parameters;
  }

  /**
   * {@inheritDoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    if (!$this->getContest()) {
      throw new MissingMandatoryParametersException('Collections must be assigned to a contest upon creation.');
    }
    parent::preSave($storage);
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['ior_submission'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Submissions'))
      ->setDescription(t('Submissions that are in this collection.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setComputed(TRUE)
      ->setClass(ComputedCollectionSubmissionsEntityReferenceFieldItemList::class)
      ->setSetting('target_type', 'ior_submission')
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
