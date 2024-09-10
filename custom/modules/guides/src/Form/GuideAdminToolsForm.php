<?php

namespace Drupal\guides\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Simplify some guide admin tasks.
 */
class GuideAdminToolsForm extends FormBase {

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
    return 'guide_admin_tools_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['help'] = [
      '#markup' => '<h2>Add/remove a user to/from another user\'s guides and/or categories</h2>',
    ];

    $form['user_mod']['op'] = [
      '#type' => 'select',
      '#title' => $this->t('I would like to'),
      '#options' => [
        'add' => $this->t('add'),
        'del' => $this->t('remove'),
      ],
    ];

    $form['user_mod']['user_src'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#title' => $this->t('The following user'),
      '#selection_settings' => [
        'include_anonymous' => FALSE,
        'filter' => [
          'role' => ['guide_editor'],
        ],
      ],
    ];

    $form['user_mod']['user_dest'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#title' => $this->t('to/from this user\'s guides and/or categories'),
      '#selection_settings' => [
        'include_anonymous' => FALSE,
        'filter' => [
          'role' => ['guide_editor'],
        ],
      ],
    ];

    $form['user_mod']['types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Types:'),
      '#options' => [
        'guides' => $this->t('Guides'),
        'categories' => $this->t('Categories'),
      ],
      '#default_value' => ['guides'],
    ];

    $form['user_mod']['contact'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('When adding:'),
      '#options' => [
        'contact' => $this->t('Display editor as contact?'),
      ],
    ];

    $form['user_mod']['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#name' => 'user_mod',
        '#value' => $this->t('Run'),
        '#submit' => ['::userModSubmitForm'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Form action to add or remove users from guides and categories.
   */
  public function userModSubmitForm(array &$form, FormStateInterface $form_state) {
    $sourceUserId = $form_state->getValue('user_src');
    $targetUserId = $form_state->getValue('user_dest');
    $op = $form_state->getValue('op');
    $types = $form_state->getValue('types');
    $asContact = $form_state->getValue('contact')['contact'] ? TRUE : FALSE;

    if (empty($sourceUserId) || empty($targetUserId)) {
      $this->messenger()->addError('Source and target users must be selected');
      return;
    }

    if ($types['guides']) {
      $storage = $this->entityTypeManager->getStorage('guide');
      $query = $storage->getQuery();
      $ids = $query
        ->condition('editors.entity:paragraph.field_user.target_id', [$targetUserId], 'IN')
        ->sort('title', 'ASC')
        ->execute();
      $guides = $storage->loadMultiple($ids);

      $count = 0;
      foreach ($guides as $guide) {
        $found = FALSE;
        $foundEditor = NULL;
        foreach ($guide->editors as $index => $editor) {
          $id = $editor->entity->field_user->target_id;
          if ($id == $sourceUserId) {
            $found = TRUE;
            $foundEditor = $index;
          }
        }
        if ($op == 'add') {
          if ($found) {
            continue;
          }
          $newEditor = $this->entityTypeManager->getStorage('paragraph')->create([
            'type' => 'guide_editor',
            'field_display_editor' => $asContact,
            'field_user' => $sourceUserId,
          ]);
          $newEditor->save();
          $guide->editors->appendItem($newEditor);
          $guide->save();
          $count++;
        }
        elseif ($op == 'del') {
          if (!$found) {
            continue;
          }
          $guide->editors->removeItem($foundEditor);
          $guide->save();
          $count++;
        }
      }
      $this->messenger()->addStatus(($op == 'add' ? 'Added to' : 'Removed from') . " $count guide" . ($count == 1 ? '' : 's') . '.');
    }

    if ($types['categories']) {
      $storage = $this->entityTypeManager->getStorage('guide_category');
      $query = $storage->getQuery();
      $ids = $query
        ->condition('editors.entity:paragraph.field_user.target_id', [$targetUserId], 'IN')
        ->sort('title', 'ASC')
        ->execute();
      $categories = $storage->loadMultiple($ids);

      $count = 0;
      foreach ($categories as $category) {
        $found = FALSE;
        $foundEditor = NULL;
        foreach ($category->editors as $index => $editor) {
          $id = $editor->entity->field_user->target_id;
          if ($id == $sourceUserId) {
            $found = TRUE;
            $foundEditor = $index;
          }
        }
        if ($op == 'add') {
          if ($found) {
            continue;
          }
          $newEditor = $this->entityTypeManager->getStorage('paragraph')->create([
            'type' => 'guide_editor',
            'field_display_editor' => $asContact,
            'field_user' => $sourceUserId,
          ]);
          $newEditor->save();
          $category->editors->appendItem($newEditor);
          $category->save();
          $count++;
        }
        elseif ($op == 'del') {
          if (!$found) {
            continue;
          }
          $category->editors->removeItem($foundEditor);
          $category->save();
          $count++;
        }
      }
      $this->messenger()->addStatus(($op == 'add' ? 'Added to' : 'Removed from') . " $count " . ($count == 1 ? 'category' : 'categories') . '.');
    }
  }

}
