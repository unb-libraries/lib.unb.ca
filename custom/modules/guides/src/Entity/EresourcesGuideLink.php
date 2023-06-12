<?php

namespace Drupal\guides\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the e-Resources Guide Link entity.
 *
 * Used as an index to show which guides and sections use a
 * particular e-Resource.
 *
 * @ContentEntityType(
 *   id = "eresources_guide_link",
 *   label = @Translation("e-Resources Guide Link"),
 *   base_table = "eresources_guide_link",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class EresourcesGuideLink extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['guide'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Guide'))
      ->setSetting('target_type', 'guide')
      ->setSetting('handler', 'default');

    $fields['eresource'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('e-Resource'))
      ->setSetting('target_type', 'eresources_record')
      ->setSetting('handler', 'default');

    $fields['section'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Section'))
      ->setSetting('handler_settings', ['target_bundles' => ['guide_section' => 'guide_section']])
      ->setSetting('target_type', 'paragraph')
      ->setSetting('handler', 'default:paragraph');

    return $fields;
  }

}
