<?php

namespace Drupal\eresources;

/**
 * KB result item.
 */
class KbResult extends ResultBase implements ResultInterface {

  /**
   * Timespan replacement values.
   *
   * @var array
   */
  private static $timespan = [
    'D' => 'day',
    'M' => 'month',
    'Y' => 'year',
  ];

  /**
   * {@inheritDoc}
   */
  public function getTitle() {
    return $this->item->title;
  }

  /**
   * {@inheritDoc}
   */
  public function getCollectionName() {
    return $this->item->{'kb:collection_name'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getOcn() {
    return $this->item->{'kb:oclcnum'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getIsbn() {
    return $this->item->{'kb:isbn'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getIssn() {
    return $this->item->{'kb:issn'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getEissn() {
    return $this->item->{'kb:eissn'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getViaUrl() {
    foreach ($this->item->links as $link) {
      if ($link->rel == 'via') {
        return $link->href;
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getAuthor() {
    return $this->item->{'kb:author'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getPublisher() {
    return $this->item->{'kb:publisher'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverage() {
    return $this->item->{'kb:coverage'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverageEnum() {
    return $this->item->{'kb:coverage_enum'} ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getCoverageStatement() {
    $coverageEnum = property_exists($this->item, 'kb:coverage_enum') ? $this->item->{'kb:coverage_enum'} : '';
    if (preg_match('/^print/', $coverageEnum)) {
      $coverage = implode('; ', [
        $this->item->{'kb:location'}, $this->item->{'kb:coverage_notes'},
      ]);
    }
    else {
      $coverage = implode(' ', [
        self::getCoverageText($this->item->{'kb:coverage'}),
        self::getEnumeratedCoverage($coverageEnum),
      ]);
    }
    return trim($coverage);
  }

  /**
   * {@inheritDoc}
   */
  public function getPermittedUseStatement() {
    $coverageEnum = property_exists($this->item, 'kb:coverage_enum') ? $this->item->{'kb:coverage_enum'} : '';
    // Ignore permitted use for print holdings.
    if (preg_match('/^print/', $coverageEnum)) {
      return '';
    }

    $data = [];
    $coverageNotes = property_exists($this->item, 'kb:coverage_notes') ? $this->item->{'kb:coverage_notes'} : '';
    $collectionUserNotes = property_exists($this->item, 'kb:collection_user_notes') ? $this->item->{'kb:collection_user_notes'} : '';
    if (preg_match('/(purchase|subscribe)/', $coverageNotes)
      && str_pos($coverageNotes, '|') === FALSE) {
      if ($collectionUserNotes) {
        $data = explode(' | ', $collectionUserNotes);
      }
      if ($coverageNotes) {
        $data = $coverageNotes;
      }
      if (!preg_match('/^(ebook|video)/', $this->item->{'kb:coverage'})) {
        $data[] = implode(' ', [
          self::getCoverageText($this->item->{'kb:coverage'}),
          self::getEnumeratedCoverage($coverageEnum),
        ]);
      }
    }
    else {
      if ($collectionUserNotes) {
        $data = explode(' | ', $collectionUserNotes);
      }
      if ($coverageNotes) {
        $data = array_merge($data, explode(' | ', $coverageNotes));
      }
      elseif (!preg_match('/^(ebook|video)/', $this->item->{'kb:coverage'})) {
        $data[] = implode(' ', [
          self::getCoverageText($this->item->{'kb:coverage'}),
          self::getEnumeratedCoverage($coverageEnum),
        ]);
      }
    }

    $lastPart = array_pop($data);
    if ($lastPart && !preg_match('/(purchase|subscribe)/i', $lastPart)) {
      $lastPart = '<span class="coverage">' . $lastPart . '</span>';
    }
    $data[] = $lastPart;

    $use = implode(' | ', $data);
    // Adjustments to license link, and "Coverage" header.
    $use = preg_replace('/<a ([^>]+)>/', '<a class="d-inline" target="_blank" $1> ', $use);
    $use = preg_replace('/href="[^"]+lid=([^"]+)"/', 'href="/eresources/license/$1"', $use);

    return $use;
  }

  /**
   * Generate enumerated coverage statement.
   */
  private static function getEnumeratedCoverage($text) {
    $fulltextRanges = self::getFulltextRanges($text);
    if (empty($fulltextRanges)) {
      // Return nothing. Some coverage information will be provided by
      // coverage() or in KBWC's coverage notes field.
      return '';
    }

    // Build an enumerated coverage list from one or more fulltext ranges.
    $formattedRanges = [];
    foreach ($fulltextRanges as $range) {
      if (preg_match("/([^~]+)(~(.*))?/", $range, $matches)) {
        $start = $matches[1];
        $end   = isset($matches[3]) ? $matches[3] : '';

        $formattedRanges[] = str_replace('-', '–', str_replace(':', ' ', str_replace(';', ', ', "$start-$end")));
      }
    }

    // If pattern match on fulltext ranges failed, return text as-is
    // coding errors aren't hidden by no-coverage messages.
    if (empty($formattedRanges)) {
      return "fail: $text";
    }

    // Returned formatted ranges as a semicolon-delimited list in parentheses.
    return '(' . implode('; ', $formattedRanges) . ')';
  }

  /**
   * Picks out fulltext ranges from coverage notes.
   */
  private static function getFulltextRanges($text) {
    $coverageRanges = preg_split("/\s+/", $text);
    $fulltextRanges = [];

    foreach ($coverageRanges as $range) {
      // Skip /^fulltext$/ matches: without a start or end date,
      // the range tells us nothing.
      if (preg_match('/(?:fulltext|ebook|video)@(.+)/', $range, $matches)) {
        $fulltextRanges[] = $matches[1];
      }
    }

    return $fulltextRanges;
  }

  /**
   * Extracts coverage details from notes.
   */
  private static function getCoverageText($text) {
    $fulltextRanges = self::getFulltextRanges($text);

    if (empty($fulltextRanges)) {
      if (preg_match('/(ebook|video)/', $text)) {
        return '';
      }
      return 'Visit site for coverage details';
    }

    // Build a coverage description from one or more fulltext ranges.
    $formattedRanges = [];
    foreach ($fulltextRanges as $range) {
      if (preg_match("/([^~]+)(~(.*))?/", $range, $matches)) {
        $start = isset($matches[1]) ? $matches[1] : '';
        $end = isset($matches[3]) ? $matches[3] : '';

        if (preg_match('/(ebook|video)/', $text)) {
          $formattedRanges[] = $start;
          continue;
        }
        elseif (empty($end)) {
          $end = 'Current';
        }
        elseif (preg_match("/P(\d+)(D|M|Y)/", $end, $matches)) {
          $end = $matches[1] . ' ' . self::$timespan[$matches[2]] . ($matches[1] > 1 ? 's' : '') . ' ago';
        }
        $formattedRanges[] = "{$start}–{$end}";
      }
    }

    // If pattern match on fulltext ranges failed, return text as-is
    // coding errors aren't hidden by no-coverage messages.
    if (empty($formattedRanges)) {
      return $text;
    }

    // Returned formatted ranges as a comma-separated list.
    return implode(', ', $formattedRanges);
  }

}