<?php

namespace Drupal\guides\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\custom_entity\Entity\EntityChangedTrait;
use Drupal\custom_entity\Entity\EntityCreatedTrait;
use Drupal\custom_entity\Entity\UserCreatedInterface;
use Drupal\custom_entity\Entity\UserEditedInterface;

/**
 * Defines a course link entity.
 *
 * @ContentEntityType(
 *   id = "course_link",
 *   label = @Translation("Course Link"),
 *   label_plural = @Translation("Course Links"),
 *   label_collection = @Translation("Course Links"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "list_builder" = "Drupal\guides\Entity\CourseLinkListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\guides\Entity\Access\CourseLinkAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\guides\Form\CourseLinkForm",
 *       "edit" = "Drupal\guides\Form\CourseLinkForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *   },
 *   base_table = "course_link",
 *   admin_permission = "administer course_link entities",
 *   entity_keys = {
 *     "id" = "id"
 *   },
 *   links = {
 *     "canonical" = "/admin/guides/{guide}/courselink/{course_link}",
 *     "collection" = "/admin/guides/{guide}/courselink",
 *     "add-form" = "/admin/guides/{guide}/courselink/add",
 *     "edit-form" = "/admin/guides/{guide}/courselink/{course_link}/edit",
 *     "delete-form" = "/admin/guides/{guide}/courselink/{course_link}/delete",
 *   },
 *   field_ui_base_route = "entity.course_link.settings",
 * )
 */
class CourseLink extends ContentEntityBase implements ContentEntityInterface, UserEditedInterface, UserCreatedInterface {

  use EntityChangedTrait;
  use EntityCreatedTrait;

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['guide'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Guide'))
      ->setSettings(
        [
          'target_type' => 'guide',
          'handler' => 'default',
        ],
      );

    $fields['year'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Year'))
      ->setDescription(t('E.g. @year', ['@year' => date('Y')]))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'max_length' => 4,
          'text_processing' => 0,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['term'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Term'))
      ->setDescription(t('Fall, Summer or Winter'))
      ->setRequired(FALSE)
      ->setSettings([
        'allowed_values' => [
          'FA' => 'Fall',
          'SM' => 'Summer',
          'WI' => 'Winter',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['campus'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Campus'))
      ->setDescription(t('Campus'))
      ->setRequired(FALSE)
      ->setSettings([
        'allowed_values' => [
          'BJ' => 'Beijing',
          'ET' => 'Etobicoke',
          'FR' => 'Fredericton',
          'MO' => 'Moncton',
          'MR' => 'Montreal',
          'SJ' => 'Saint John',
          'TR' => 'Trinidad and Tobago',
          'WO' => 'Woodstock',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['prefix'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Prefix'))
      ->setDescription(t('E.g. ENGL (ONLY a single value is permitted)'))
      ->setRequired(TRUE)
      ->addConstraint('SingleValue')
      ->setSettings(
        [
          'max_length' => 255,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['course_number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Course Number'))
      ->setDescription(t('E.g. 1000 (ONLY a single value is permitted)'))
      ->setRequired(FALSE)
      ->addConstraint('SingleValue')
      ->setSettings(
        [
          'max_length' => 255,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['section'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Section'))
      ->setDescription(t('E.g. E1A (ONLY a single value is permitted)'))
      ->setRequired(FALSE)
      ->addConstraint('SingleValue')
      ->setSettings(
        [
          'max_length' => 255,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields[self::FIELD_CREATED] = static::getCreatedBaseFieldDefinition($entity_type);
    $fields[self::FIELD_EDITED] = static::getEditedBaseFieldDefinition($entity_type);

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  protected function urlRouteParameters($rel) {
    $parameters = parent::urlRouteParameters($rel);
    $parameters['guide'] = $this->guide->target_id;
    return $parameters;
  }

}
