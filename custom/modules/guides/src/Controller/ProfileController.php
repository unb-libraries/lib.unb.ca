<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides route responses for the guides module.
 */
class ProfileController extends ControllerBase {

  /**
   * Profile object.
   *
   * @var \Drupal\profile\Entity\Profile
   */
  private $profile;

  /**
   * Get profile data based on url fragment field.
   *
   * @param string $url_fragment
   *   URL fragment.
   */
  private function getProfile($url_fragment) {
    if (empty($profile)) {
      $storage = $this->entityTypeManager()->getStorage('profile');
      $query = $storage->getQuery();
      $ids = $query
        ->condition('status', 1)
        ->condition('type', 'guides')
        ->condition('field_url_fragment', $url_fragment)
        ->execute();

      if (!empty($ids)) {
        $id = reset($ids);
        $this->profile = $storage->load($id);
      }
    }

    return $this->profile;
  }

  /**
   * Page title for profiles.
   *
   * @param string $url_fragment
   *   URL fragment.
   */
  public function title($url_fragment) {
    $profile = $this->getProfile($url_fragment);
    if (empty($profile)) {
      throw new NotFoundHttpException();
    }

    $account = $profile->uid->entity;
    return implode(' ', [
      $account->field_first_name->value,
      $account->field_last_name->value,
    ]);
  }

  /**
   * Profile page.
   *
   * @param string $url_fragment
   *   URL fragment.
   */
  public function view($url_fragment) {
    $profile = $this->getProfile($url_fragment);
    if (empty($profile)) {
      throw new NotFoundHttpException();
    }

    $account = $profile->uid->entity;

    $storage = $this->entityTypeManager()->getStorage('guide');
    $query = $storage->getQuery();
    $ids = $query
      ->condition('status', 1)
      ->condition('editors.entity:paragraph.field_user.target_id', $profile->uid->target_id, 'IN')
      ->condition('editors.entity:paragraph.field_display_editor', 1)
      ->execute();
    $guides = $storage->loadMultiple($ids);

    return [
      '#theme' => 'guides_profile',
      '#profile' => $profile,
      '#account' => $account,
      '#guides' => $guides,
      '#cache' => [
        'tags' => [
          'guide_list',
        ],
      ],
    ];
  }

}
