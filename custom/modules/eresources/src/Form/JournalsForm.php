<?php

namespace Drupal\eresources\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * KB Journals form.
 */
class JournalsForm extends KbFormBase implements KbFormInterface {

  /**
   * {@inheritDoc}
   */
  public function getKbFormId() {
    return 'journals';
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchOptions() {
    return [
      'title' => 'Word(s) in title',
      'browse' => 'Title starts with',
      'exact' => 'Exact title',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public static function getTitle() {
    return 'Journals & Newspapers';
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchPlaceholder() {
    return $this->t('Search for journal and newspaper titles');
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchDescription() {
    return $this->t('Search for individual journals, newspapers and conference proceedings by title.');
  }

  /**
   * {@inheritDoc}
   */
  public function getKbContentType() {
    return 'fulltext,print';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $form['journals_wrapper']['links'] = [
      '#markup' => '<div class="wrapper-list-inline item-list">
<ul>
  <li><a href="' . Url::fromRoute('eresources.packages', ['type' => 'journals'])->toString() . '">Journal Packages</a></li>
  <li><a href="' . Url::fromRoute('eresources.packages', ['type' => 'newspapers'])->toString() . '">Newspaper Packages</a></li>
  <li><a href="https://lib.unb.ca/eresources/newspaper-guide"> Newspaper Guide</a></li>
</ul>
</div>',
    ];
    return $form;
  }

}
