<?php

namespace Drupal\guides\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Clone a guide as template for a new guide.
 */
class GuideCloningForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   An entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'guide_cloning_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['help'] = [
      '#markup' => '<p>This option allows you to create an exact clone of an existing guide and all of its tabbed content, including resource lists. It is a one-time operation and there is no further or ongoing connection between the existing guide and the new clone.
Course Linking is not cloned, as it is unique, but guide metadata is, so you should edit it immediately.</p>
<p>New titles will be prepended with [CLONE OF].</p>
<p>Clones are by default Unpublished.</p>',
    ];

    $form['guide'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'guide',
      '#title' => $this->t('Select a guide'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Create Clone'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $storage = $this->entityTypeManager->getStorage('guide');
    $entity = $storage->load($form_state->getValue('guide'));
    $guide = $storage->create();

    $deep_clone_fields = [
      'sections',
      'editors',
      'feeds',
    ];

    $copy_fields = [
      'unlisted',
      'guide_categories',
      'related_guide_categories',
      'related_guides',
    ];

    foreach ($guide->getFieldDefinitions() as $field_id => $field_definition) {
      if (in_array($field_id, $copy_fields)) {
        $guide->set($field_id, $entity->get($field_id)->getValue());
      }
      elseif (in_array($field_id, $deep_clone_fields)) {
        $field = $entity->get($field_id);
        if ($field->count() > 0) {
          $referenced_entities = [];
          foreach ($field as $value) {
            $referenced_entity = $value->get('entity')->getTarget()->getValue();
            $referenced_entities[] = $referenced_entity->createDuplicate();
          }

          $guide->set($field_id, $referenced_entities);
        }
      }
    }

    // Set new title.
    $guide->set('title', '[CLONE OF] ' . $entity->label());

    $guide->save();

    $form_state->setRedirect('entity.guide.canonical', ['guide' => $guide->id()]);
  }

}
