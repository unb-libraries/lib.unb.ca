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
 *     "list_builder" = "Drupal\records_management\Entity\ClassificationListBuilder",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
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
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/records/classifications/add",
 *     "edit-form" = "/records/classifications/{classification}/edit",
 *     "delete-form" = "/records/classifications/{classification}/delete",
 *     "collection" = "/records/classifications",
 *   }
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
      ->processed;
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
      ->addConstraint('UniqueField')
      ->setDisplayOptions('form', [
        'weight' => 0,
      ]);

    $fields[self::FIELD_NAME] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setCardinality(1)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'weight' => 10,
      ]);

    $fields[self::FIELD_DESCRIPTION] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDescription(t('Overview and high level description.'))
      ->setCardinality(1)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'weight' => 20,
      ]);

    return $fields;
  }

}
