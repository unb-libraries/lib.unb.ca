<?php

namespace Drupal\eresources\Form;

/**
 * Interface for KB forms.
 *
 * @package Drupal\eresources\Form
 */
interface KbFormInterface {

  /**
   * KB API content type to search against.
   *
   * @return string
   *   KB API content type.
   */
  public function getKbContentType();

  /**
   * KB Form ID.
   *
   * @return string
   *   Form ID.
   */
  public function getKbFormId();

  /**
   * Form title for tabbed display.
   *
   * @return string
   *   Form title.
   */
  public static function getTitle();

  /**
   * Textual description of the search form.
   *
   * @return string
   *   Search description text.
   */
  public function getSearchDescription();

  /**
   * Example search string.
   *
   * @return string
   *   Search placeholder text.
   */
  public function getSearchPlaceholder();

}
