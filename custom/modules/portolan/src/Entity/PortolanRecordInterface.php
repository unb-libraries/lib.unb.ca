<?php

namespace Drupal\portolan\Entity;

/**
 * Interface for "Portolan record" entities.
 *
 * @package Drupal\portolan\Entity
 */
interface PortolanRecordInterface {

  const FIELD_OCLC_ID = 'oclc_id';
  const FIELD_TITLE = 'title';
  const FIELD_TITLE_TRIMMED = 'title_trimmed';
  const FIELD_AUTHOR = 'author';
  const FIELD_PUBLICATION = 'publication';
  const FIELD_ABSTRACT = 'abstract';
  const FIELD_NOTE = 'note';
  const FIELD_AGE_RANGE = 'age_range';
  const FIELD_JURISDICTION = 'jurisdiction';
  const FIELD_LOCATION = 'location';
  const FIELD_DESCRIPTOR = 'descriptor';
  const FIELD_CALL_NUMBER = 'call_number';

  const AUTHOR_VID = 'portolan_authors';
  const DESCRIPTOR_VID = 'portolan_descriptors';
  const LOCATION_VID = 'portolan_locations';
  const JURISDICTION_VID = 'portolan_jurisdictions';

}
