<?php

namespace Drupal\eresources\Form;

/**
 * Interface for KB forms.
 *
 * @package Drupal\eresources\Form
 */
interface KbFormInterface {

  /**
   * KB Form ID.
   *
   * @return string
   *   Form ID.
   */
  public function getKbFormId();

  /**
   * Textual description of the search form.
   *
   * @return string
   *   Search description text.
   */
  public function getSearchDescription();

  /**
   * KB API content type to search against.
   *
   * @return string
   *   KB API content type.
   */
  public function getKbContentType();

}
