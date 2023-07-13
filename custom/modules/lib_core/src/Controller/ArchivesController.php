<?php

namespace Drupal\lib_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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
    $url = 'https://7067.sydneyplus.com/archive/final/Portal.aspx';

    $id = $request->query->get('id');
    if (!empty($id)) {
      $id = str_replace('KEY_', '', $id);
      $length = strlen($id) + 1;
      $search = "{$id},";
      $uuid = NULL;

      $fh = fopen(__DIR__ . '/../../data/eloquent-redirects.csv', 'r');
      while (($line = fgets($fh)) !== FALSE) {
        if (substr($line, 0, $length) == $search) {
          $uuid = substr($line, $length, -1);
        }
      }
      fclose($fh);

      if (!empty($uuid)) {
        $url = "https://7067.sydneyplus.com/archive/final/Portal/Default.aspx?component=bsearch&record={$uuid}";
      }
    }

    $block = $this->blockContentStorage->load($this->blockId);
    $blockValue = !empty($block) ? $block->body->value : '';

    $build['info'] = [
      '#markup' => '<div class="layout layout--twocol-section layout--twocol-section--67-33">
  <div class="layout__region layout__region--first">
    <p>The Gateway Archives server has been migrated to a new platform powered by ArchivEra.</p>
    <p>Depending on the link you followed, the button below <strong>may</strong> take you to the original record. If not, it will take you to the homepage for the new platform. Any bookmarks or links to specific resources, collections or searches will need to be recreated on the new platform.</p>
    <p><strong>Please update your bookmarks.</strong></p>
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
