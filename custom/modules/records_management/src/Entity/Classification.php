<?php

namespace Drupal\records_management\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * The "Functional classification" entity.
 *
 * @ContententityType(
 *   id = "classification",
 *   label = @Translation("Functional classification"),
 *   label_plural = @Translation("Functional classifications"),
 *   label_collection = @Translation("Functional classifications"),
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider"
 *     }
 *   },
 *   base_table = "classification",
 *   revision_table = "classification_revision",
 *   admin_permission = "administer classification entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "rid",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 * )
 */
class Classification extends ContentEntityBase implements ClassificationInterface {

  /**
   * {@inheritDoc}
   */
  public function getCode() {
    return $this->get(self::FIELD_CODE)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getName() {
    return $this->get(self::FIELD_NAME)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getDescrition() {
    return $this->get(self::FIELD_DESCRIPTION)
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields[self::FIELD_CODE] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code'))
      ->setCardinality(1)
      ->setRequired(TRUE)
      ->addConstraint('UniqueField');

    $fields[self::FIELD_NAME] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setCardinality(1)
      ->setRequired(TRUE);

    $fields[self::FIELD_DESCRIPTION] = BaseFieldDefinition::create('text')
      ->setLabel(t('Description'))
      ->setDescription(t('Overview and high level description.'))
      ->setCardinality(1)
      ->setRequired(TRUE);

    return $fields;
  }

}
