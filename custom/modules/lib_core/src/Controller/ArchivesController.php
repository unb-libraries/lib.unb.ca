<?php

namespace Drupal\lib_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides route responses for the lib_core module.
 */
class ArchivesController extends ControllerBase {

  /**
   * Block ID for Archives & Special Collections.
   *
   * @var string
   */
  private $blockId = '55';

  /**
   * The custom block storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $blockContentStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityStorageInterface $block_content_storage) {
    $this->blockContentStorage = $block_content_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity_type.manager');
    return new static($entity_manager->getStorage('block_content'));
  }

  /**
   * Handle redirects from archives.lib, with an optional permalink ID.
   */
  public function eloquentRedirect(Request $request) {
    $url = '#';

    $id = $request->query->get('id');
    if (!empty($id)) {
      $id = str_replace('KEY_', '', $id);
      $url = "https://gencat.eloquent-systems.com/unb_permalink.html?key={$id}";
    }

    $block = $this->blockContentStorage->load($this->blockId);
    $blockValue = !empty($block) ? $block->body->value : '';

    $build['info'] = [
      '#markup' => '<div class="layout layout--twocol-section layout--twocol-section--67-33">
  <div class="layout__region layout__region--first">
    <p>The Gateway Archives server has been migrated to a new platform powered by ArchivEra.</p>
    <p>Unfortunately, the vendor is not able to map URLs or permalinks to the new system. As a result, any bookmarks or links to specific resources, collections or searches will need to be recreated on the new platform.</p>
    <p><strong>Please update your bookmarks</strong>.</p>
    <p>If you need assistance, please contact <a href="/contact-unb-libraries-staff?recipient=archives&amp;subject=Gateway%20Migration%20Question">archives@unb.ca</a>.</p>
    <p><a class="btn btn-danger" href="' . $url . '">Connect to the new Gateway server</a></p>
  </div>
  <div class="layout__region layout__region--second">
' . $blockValue . '
  </div>
</div>',
    ];

    $build['#attached']['library'][] = 'layout_builder/twocol_section';
    return $build;
  }

}