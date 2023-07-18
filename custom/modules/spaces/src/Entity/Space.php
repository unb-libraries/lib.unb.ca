<?php

namespace Drupal\spaces\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\spaces\Plugin\Field\FieldType\CampusFieldItemList;

/**
 * Defines the "Space" entity.
 *
 * @ContentEntityType(
 *   id = "space",
 *   label = @Translation("Space"),
 *   label_singular = @Translation("Space"),
 *   label_plural = @Translation("Spaces"),
 *   label_count = @PluralTranslation(
 *     singular = "@count space",
 *     plural = "@count spaces",
 *   ),
 *   bundle_label = @Translation("Space type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider",
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler",
 *     "permissions" = "Drupal\custom_entity\Entity\Access\EntityPermissionsHandler",
 *   },
 *   admin_permission = "administer space entities",
 *   base_table = "space",
 *   translatable = FALSE,
 *   bundle_entity_type = "space_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "bundle" = "type",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/explore-spaces/{space}",
 *     "add-form" = "/explore-spaces/add/{space_type}",
 *     "edit-form" = "/explore-spaces/{space}/edit",
 *     "delete-form" = "/explore-spaces/{space}/delete",
 *   },
 *   field_ui_base_route = "entity.space_type.edit_form",
 * )
 */
class Space extends ContentEntityBase implements SpaceInterface {

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->get('field_name')
      ->value;
  }

  /**
   * {@inheritDoc}
   */
  public function getParent() {
    return $this->get('parent')
      ->entity;
  }

  /**
   * {@inheritDoc}
   */
  public function hasImages() {
    return count($this->get('field_images')) > 0;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['parent'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Parent'))
      ->setDescription(t('The space this space belongs to.'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('target_type', 'space')
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('view', [
        'weight' => 0,
      ]);

    $fields['campus'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Campus'))
      ->setDescription(t('Campus on which this space is located.'))
      ->setComputed(TRUE)
      ->setClass(CampusFieldItemList::class)
      ->setCardinality(1)
      ->setSetting('allowed_values', [
        'fr' => t('Fredericton'),
        'sj' => t('Saint John'),
      ])
      ->setDisplayConfigurable('view', FALSE)
      ->setDisplayConfigurable('form', FALSE);

    return $fields;
  }

}
