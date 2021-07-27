<?php

namespace Drupal\portolan_sync\Synchronization;

use Drupal\taxonomy\TermStorageInterface;

/**
 * Provides methods to load taxonomy terms.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
class TaxonomyTermMapper {

  /**
   * The taxonomy term entity storage handler.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $termStorage;

  /**
   * The portolan record entity type definition.
   *
   * @var \Drupal\Core\Entity\ContentEntityTypeInterface
   */
  protected $portolanRecordType;

  /**
   * Get the taxonomy term entity storage handler.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   An entity storage handler.
   */
  protected function termStorage() {
    return $this->termStorage;
  }

  /**
   * Create a new TaxonomyTermMapper instance.
   *
   * @param \Drupal\taxonomy\TermStorageInterface $term_storage
   *   An entity storage handler for taxonomy term entities.
   */
  public function __construct(TermStorageInterface $term_storage) {
    $this->termStorage = $term_storage;
  }

  /**
   * Map the given value to a taxonomy term of the given vocabulary ID.
   *
   * @param string $vid
   *   A vocabulary ID string.
   * @param string $value
   *   A string.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   A taxonomy term entity. NULL if an error occurred.
   */
  public function getTerm(string $vid, string $value) {
    try {
      if (!$term = $this->loadByNameAndVocabulary($value, $vid)) {
        $term = $this->createTerm($vid, $value);
      }
      return $term;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Load a single taxonomy term with the given name and vocabulary.
   *
   * @param string $name
   *   A string.
   * @param string $vid
   *   A vocabulary ID string.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   A taxonomy term entity. NULL if none could be loaded.
   */
  protected function loadByNameAndVocabulary(string $name, string $vid) {
    $terms = $this->termStorage()->loadByProperties([
      'vid' => $vid,
      'name' => $name,
    ]);

    if (!empty($terms)) {
      return $terms[array_keys($terms)[0]];
    }
    return NULL;
  }

  /**
   * Create a new term of the given vocabulary ID and the given name.
   *
   * @param string $vid
   *   A vocabulary ID string.
   * @param string $name
   *   A string.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   A taxonomy term entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createTerm($vid, $name) {
    $term = $this->termStorage()->create([
      'vid' => $vid,
      'name' => $name,
    ]);
    $term->save();
    return $term;
  }

}
