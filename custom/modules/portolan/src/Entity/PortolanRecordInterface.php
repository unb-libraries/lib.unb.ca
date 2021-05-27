<?php

namespace Drupal\portolan\Entity;

/**
 * Interface for "Portolan record" entities.
 *
 * @package Drupal\portolan\Entity
 */
interface PortolanRecordInterface {

  const FIELD_TITLE = 'title';
  const FIELD_AUTHOR = 'author';
  const FIELD_PUBLICATION = 'publication';
  const FIELD_ABSTRACT = 'abstract';
  const FIELD_NOTE = 'note';
  const FIELD_AGE_RANGE = 'age_range';
  const FIELD_JURISDICTION = 'jurisdiction';
  const FIELD_LOCATION = 'location';
  const FIELD_DESCRIPTOR = 'descriptor';
  const FIELD_CALL_NUMBER = 'call_number';

}
