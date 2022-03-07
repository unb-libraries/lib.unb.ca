<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_api\Entity\Index;
use Drupal\eresources\LocalResult;

/**
 * KB Videos Form.
 */
class VideosForm extends KbFormBase implements KbFormInterface {

  /**
   * {@inheritDoc}
   */
  public function getKbFormId() {
    return 'videos';
  }

  /**
   * {@inheritDoc}
   */
  public static function getTitle() {
    return 'Videos';
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchDescription() {
    return $this->t('Search across our video collections, including VHS/DVD titles.');
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchPlaceholder() {
    return $this->t('Search for video titles');
  }

  /**
   * {@inheritDoc}
   */
  public function getKbContentType() {
    return 'video,other';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $req = $this->getRequest()->query;
    $query = $req->get('query');
    if (empty($query) || $this->getFormId() != $req->get('form_id')) {
      $form_wrapper = $this->getKbFormId() . "_wrapper";

      $index = Index::load('eresources');
      $indexQuery = $index->query();
      $parseMode = \Drupal::service('plugin.manager.search_api.parse_mode')->createInstance('direct');
      $indexQuery->setParseMode($parseMode);

      $indexQuery->addCondition('status', TRUE);
      $indexQuery->addCondition('kb_data_type', 'video');
      $indexQuery->addCondition('metadata_local_is_collection', TRUE);
      $indexQuery->sort('title', 'ASC');
      $indexQuery->range(0, 10000);
      $result = $indexQuery->execute();

      $total = $result->getResultCount();
      if ($total > 0) {
        $form[$form_wrapper]['collections_wrapper'] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => [
              'border-top',
              'border-dark',
              'pt-4',
              'pb-3',
            ],
          ],
        ];

        $form[$form_wrapper]['collections_wrapper']['header'] = [
          '#markup' => '<p class="font-weight-bold"><span class="text-danger">OR</span> Browse for Video Collections</p>',
        ];

        $entries = array_map(function ($i) {
          return new LocalResult($i);
        }, $result->getResultItems());

        $form[$form_wrapper]['collections_wrapper']['results'] = [
          '#prefix' => '<div id="search_results_wrapper" class="mt-4 mx-n4">',
          '#suffix' => '</div>',
          '#theme' => 'eresources',
          '#eresources' => $entries,
          '#form_id' => $this->getKbFormId(),
        ];
      }
    }

    return $form;
  }

}
