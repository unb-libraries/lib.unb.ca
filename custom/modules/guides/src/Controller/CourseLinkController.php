<?php

namespace Drupal\guides\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides route responses for course_link entities.
 */
class CourseLinkController extends ControllerBase {

  /**
   * Collection page title.
   *
   * @param \Drupal\Core\Entity\EntityInterface $guide
   *   An entity.
   *
   * @return string
   *   The title.
   */
  public function pageTitle(EntityInterface $guide) {
    return 'Course Linking for ' . $guide->label();
  }

  /**
   * Redirect from D2L course ID (eg. 2013FA_ENGL*6786*FR01A) to a guide.
   *
   * @param string $id
   *   D2L course ID.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request.
   */
  public function d2lRedirect($id, Request $request) {
    $toJson = $request->query->has('json') ? TRUE : FALSE;
    $startGuide = $this->config('guides.settings')->get('start_guide');

    $defaultLink = Url::fromRoute('guides.categories', [], ['absolute' => TRUE])->toString();
    if ($startGuide) {
      $defaultLink = Url::fromRoute('entity.guide.canonical', ['guide' => $startGuide], ['absolute' => TRUE])->toString();
    }

    $info = [
      'type' => 'Research Guide',
      'title' => 'Getting Started',
      'link' => $defaultLink,
    ];

    $pattern = '/^(?P<year>\d{4})(?P<term>\w{2})_(?P<prefix>\w+)\*(?P<course_number>\d+)\*?(?P<campus>\w{2})(?P<section>\S+)(\s+MULTI(\s\d+)?)?$/';
    $fields = ['prefix', 'course_number', 'campus', 'year', 'term', 'section'];
    $empties = [];

    if (preg_match($pattern, $id, $matches)) {
      $storage = $this->entityTypeManager()->getStorage('course_link');
      $queryBase = $storage->getQuery()
        ->condition('guide.entity.status', 1);

      while (count($fields) != 0) {
        $query = clone $queryBase;
        foreach ($fields as $field) {
          $query = $query->condition($field, $matches[$field]);
        }
        foreach ($empties as $field) {
          $query = $query->notExists($field);
        }
        $ids = $query->execute();

        if (!empty($ids)) {
          $course = $storage->load(reset($ids));
          $guide = $course->guide->entity;
          $link = $guide->toUrl('canonical', ['absolute' => TRUE])->toString();

          if (!$toJson) {
            $request->getSession()->set('guides.d2l', TRUE);
            return new RedirectResponse($link);
          }

          $info['link'] = $link;
          $info['title'] = $guide->label();
          if (!$guide->is_subject_guide->getString()) {
            $info['type'] = 'Course Guide';
          }

          return new JsonResponse($info);
        }

        $empties[] = array_pop($fields);
      }
    }

    // Invalid D2L course id or no matches.
    if ($toJson) {
      return new JsonResponse($info);
    }
    return new RedirectResponse($defaultLink);
  }

}
