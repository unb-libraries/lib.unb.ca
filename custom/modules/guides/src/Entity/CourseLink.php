<?php

namespace Drupal\guides\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityInterface;
use Drupal\lib_unb_custom_entity\Entity\ContentEntityBase;

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
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\lib_unb_custom_entity\Entity\EntityAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\lib_unb_custom_entity\Form\ContentEntityForm",
 *       "delete" = "Drupal\lib_unb_custom_entity\Form\ContentEntityConfirmForm",
 *     },
 *   },
 *   base_table = "course_link",
 *   admin_permission = "administer course link entities",
 *   entity_keys = {
 *     "id" = "id"
 *   },
 *   links = {
 *     "canonical" = "/admin/guide/{guide}/courselink/{courselink}",
 *     "add-form" = "/admin/guide/{guide}/courselink/add",
 *     "edit-form" = "/admin/guide/{guide}/courselink/{courselink}/edit",
 *     "delete-form" = "/admin/guide/{guide}/courselink/{courselink}/delete",
 *   },
 *   field_ui_base_route = "entity.course_link.settings",
 * )
 */
class CourseLink extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['guide'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Guide'))
      ->setSettings(
        [
          'target_type' => 'node',
          'handler' => 'default',
          'handler_settings' => [
            'target_bundles' => ['guide' => 'guide'],
          ],
        ]
      );

    $fields['year'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Year'))
      ->setDescription(t('E.g. @year', ['@year' => date('Y')]))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
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
      ->setDescription(t('E.g. ENGL'))
      ->setRequired(TRUE)
      ->setSettings(
        [
          'default_value' => '',
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
      ->setDescription(t('E.g. 1000'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
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
      ->setDescription(t('E.g. E1A'))
      ->setRequired(FALSE)
      ->setSettings(
        [
          'default_value' => '',
          'max_length' => 255,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

}
