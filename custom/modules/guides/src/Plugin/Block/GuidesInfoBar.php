<?php

namespace Drupal\guides\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * The route match service.
   *
   * @var \\Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

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
   * @param \Drupal\Core\Routing\CurrentRouteMatch $route_match
   *   The route match service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, CurrentRouteMatch $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = ['#cache' => ['max-age' => 0]];
    if ($this->routeMatch->getRouteName() != 'entity.guide.canonical') {
      return $build;
    }

    $guide = $this->routeMatch->getParameter('guide');

    $isPublished = $guide->get('status')->getString();
    $published = $isPublished ? 'a <em>published</em>' : 'an <em>unpublished</em>';
    $published .= $isPublished && $guide->get('unlisted')->getString() ? ' <em>(unlisted)</em>' : '';

    $type = $guide->get('is_subject_guide')->getString() ? 'subject-level' : 'course-level';

    $status = "This is {$published} {$type} guide";

    $categories = [];
    foreach ($guide->get('guide_categories') as $category) {
      if ($category->entity) {
        $categories[] = $category->entity->toLink()->toString();
      }
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
        $course = $storage->load(reset($ids));

        $year = $course->get('year')->getString();
        $term = $course->get('term')->getString();
        $campus = $course->get('campus')->getString();
        $prefix = $course->get('prefix')->getString();
        $number = $course->get('course_number')->getString();

        if (empty($number)) {
          if ($campus) {
            $campus = " ({$campus})";
          }
          $status .= " AND the default D2L guide for {$prefix}{$campus}";
        }
        else {
          $courseCode = $year . $term . '_';
          $courseCode .= implode('*', [
            $prefix,
            $number,
            $campus . $course->get('section')->getString(),
          ]);
          $courseCode = trim($courseCode, '*_ ');
          $status .= " for {$courseCode}";
        }
      }
    }

    $build['#markup'] = '<p class="alert alert-info"><span class="fas fa-certificate"></span> ' . $status . '.</p>';
    return $build;
  }

}
