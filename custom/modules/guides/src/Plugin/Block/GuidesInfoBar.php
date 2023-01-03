<?php

namespace Drupal\guides\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
 * Provides an information bar for guides.
 *
 * @Block(
 *   id = "guides_info_bar_block",
 *   admin_label = @Translation("Guides Information Bar"),
 *   category = @Translation("UNB Libraries Guides"),
 * )
 */
class GuidesInfoBar extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new CartBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $routeMatch = \Drupal::routeMatch();

    if ($routeMatch->getRouteName() == 'entity.guide.canonical') {
      $guide = $routeMatch->getParameter('guide');

      $isPublished = $guide->get('status')->getString();
      $published = $isPublished ? 'a <em>published</em>' : 'an <em>unpublished</em>';
      $published .= $isPublished && $guide->get('unlisted')->getString() ? ' <em>(unlisted)</em>' : '';

      $type = $guide->get('is_subject_guide')->getString() ? 'subject-level' : 'course-level';

      $status = "This is {$published} {$type} guide";

      $categories = [];
      foreach ($guide->get('guide_categories') as $category) {
        $categories[] = $category->entity->toLink()->toString();
      }

      if (!empty($categories)) {
        $status .= ' within ' . implode(' and ', $categories);
      }

      $storage = $this->entityTypeManager->getStorage('course_link');
      $query = $storage->getQuery();
      $ids = $query
        ->condition('guide', $guide->id())
        ->execute();

      if (!empty($ids)) {
        if (count($ids) > 1) {
          $linkUrl = Url::fromRoute('entity.course_link.collection', ['guide' => $guide->id()])->toString();
          $status .= ' AND matches several <a href="' . $linkUrl . '">specific D2L courses</a>';
        }
        else {
          $course = $storage->loadMultiple(reset(ids));
          // @todo Add course info.
        }
      }

      $build['#markup'] = '<p class="alert alert-info"><span class="fas fa-certificate"></span> ' . $status . '.</p>';
    }

    return $build;
  }

}
