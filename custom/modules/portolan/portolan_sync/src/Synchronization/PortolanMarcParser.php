<?php

namespace Drupal\portolan_sync\Synchronization;

use Drupal\portolan\Entity\PortolanRecordInterface;

/**
 * Extracts Portolan relevant metadata out of MARC records.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
class PortolanMarcParser implements ParserInterface {

  /**
   * {@inheritDoc}
   */
  public function parse($data) {
    return [
      PortolanRecordInterface::FIELD_OCLC_ID => $this
        ->getOclcId($data),
      PortolanRecordInterface::FIELD_TITLE => $this
        ->getTitle($data),
      PortolanRecordInterface::FIELD_AUTHOR => $this
        ->getAuthor($data),
      PortolanRecordInterface::FIELD_PUBLICATION => $this
        ->getPublication($data),
      PortolanRecordInterface::FIELD_ABSTRACT => $this
        ->getAbstract($data),
      PortolanRecordInterface::FIELD_NOTE => $this
        ->getNote($data),
      PortolanRecordInterface::FIELD_AGE_RANGE => $this
        ->getAgeRange($data),
      PortolanRecordInterface::FIELD_JURISDICTION => $this
        ->getJurisdiction($data),
      PortolanRecordInterface::FIELD_LOCATION => $this
        ->getLocation($data),
      PortolanRecordInterface::FIELD_DESCRIPTOR => $this
        ->getDescriptor($data),
      PortolanRecordInterface::FIELD_CALL_NUMBER => $this
        ->getCallNumber($data),
    ];
  }

  /**
   * Extract the OCLC ID from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string
   *   A sequence of numbers.
   */
  protected function getOclcId(\File_MARC_Record $marc_record) {
    $oclcnum = '';
    $fields = $marc_record->getFields('001');
    foreach ($fields as $num) {
      $num = $num->getData();
      $oclcnum = preg_replace('/[^0-9]/', '', $num);
    }
    return $oclcnum;
  }

  /**
   * Extract the title from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string
   *   A string.
   */
  protected function getTitle(\File_MARC_Record $marc_record) {
    $title = '';

    foreach ($marc_record->getFields('245') as $subfields) {
      foreach ($subfields->getSubfields() as $code => $value) {
        if ($code == 'a' || $code == 'b' || $code == 'n' || $code == 'p') {
          $title .= " {$value->getData()}";
        }
      }
    }

    return trim($title, ' /');
  }

  /**
   * Extract the author(s) from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string[]
   *   An array of one or multiple author names.
   */
  protected function getAuthor(\File_MARC_Record $marc_record) {
    $authors = [];

    foreach ($marc_record->getFields('100|700', TRUE) as $subfields) {
      foreach ($subfields->getSubfields() as $code => $value) {
        if ($code == 'a') {
          $author = $value->getData();
          $author = rtrim($author, ' ,.');
          $authors[] = $author;
        }
      }
    }
    return array_unique($authors);
  }

  /**
   * Extract the publication from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string
   *   A string.
   */
  protected function getPublication(\File_MARC_Record $marc_record) {
    $publication = '';

    foreach ($marc_record->getFields('260|264', TRUE) as $subfields) {
      foreach ($subfields->getSubfields() as $code => $value) {
        if ($code == 'a' || $code == 'b' || $code == 'c') {
          $publication .= " {$value->getData()}";
        }
      }
    }
    return trim($publication, ' .');
  }

  /**
   * Extract the abstract from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string
   *   A (long) string.
   */
  protected function getAbstract(\File_MARC_Record $marc_record) {
    $abstract = '';

    foreach ($marc_record->getFields('590') as $subfields) {
      foreach ($subfields->getSubfields() as $code => $value) {
        $value = $value->getData();
        if ($code == 'a' && strpos($value, 'OCLC Online Computer Library Center') !== 0) {
          $abstract .= $value;
        }
      }
    }
    return $abstract;
  }

  /**
   * Extract the note from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string
   *   A string.
   */
  protected function getNote(\File_MARC_Record $marc_record) {
    return '';
  }

  /**
   * Extract the age range from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string
   *   A string indicating an age range, e.g. "8-12".
   */
  protected function getAgeRange(\File_MARC_Record $marc_record) {
    return '';
  }

  /**
   * Extract the jurisdiction(s) from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string[]
   *   An array of one or multiple jurisdiction names.
   */
  protected function getJurisdiction(\File_MARC_Record $marc_record) {
    return [];
  }

  /**
   * Extract the location(s) from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string[]
   *   An array of one or multiple location names.
   */
  protected function getLocation(\File_MARC_Record $marc_record) {
    return [];
  }

  /**
   * Extract the descriptor(s) from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string[]
   *   An array of one or multiple descriptors.
   */
  protected function getDescriptor(\File_MARC_Record $marc_record) {
    return [];
  }

  /**
   * Extract the call number from the given MARC record.
   *
   * @param \File_MARC_Record $marc_record
   *   The MARC record to parse.
   *
   * @return string
   *   A string following the call number formatting standard.
   */
  protected function getCallNumber(\File_MARC_Record $marc_record) {
    return '';
  }

}
