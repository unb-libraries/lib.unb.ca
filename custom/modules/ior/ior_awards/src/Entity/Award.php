<?php

namespace Drupal\ior_awards\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\ior_awards\Plugin\Field\FieldType\ComputedEntityReferenceFieldItemList;

/**
 * The "IOR Award" entity.
 *
 * @ContententityType(
 *   id = "ior_award",
 *   label = @Translation("Award"),
 *   label_plural = @Translation("Awards"),
 *   label_collection = @Translation("Awards"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ior_awards\Entity\Routing\AwardHtmlRouteProvider"
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler"
 *   },
 *   base_table = "ior_award",
 *   admin_permission = "administer award entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/researchcommons/ior/awards/{ior_award}",
 *     "add-form" = "/researchcommons/ior/awards/add",
 *     "edit-form" = "/researchcommons/ior/awards/{ior_award}/edit",
 *     "delete-form" = "/researchcommons/ior/{ior_award}/delete",
 *   },
 *   field_ui_base_route = "entity.ior_award.settings",
 * )
 */
class Award extends ContentEntityBase implements AwardInterface {

  /**
   * {@inheritDoc}
   */
  public function label() {
    $label = $this->get('field_title');
    return $label->view([
      'label' => 'hidden',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['ior_submissions'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Submissions'))
      ->setDescription(t('Submissions that have received this award.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setComputed(TRUE)
      ->setClass(ComputedEntityReferenceFieldItemList::class)
      ->setSetting('target_type', 'ior_submission')
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
