<?php

namespace Drupal\eresources;

/**
 * Twig filters to generate coverage statements and permitted use notes.
 */
class TwigExtension extends \Twig_Extension {

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
  public function getFilters() {
    return [
      new \Twig_SimpleFilter('eresources_coverage', [$this, 'coverage']),
      new \Twig_SimpleFilter('eresources_permitted_use', [
        $this, 'permittedUse',
      ], ['is_safe' => ['html']]),
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getName() {
    return 'eresources.twig_extension';
  }

  /**
   * Generate permitted use line.
   */
  public static function permittedUse($entry) {
    $coverageEnum = property_exists($entry, 'kb:coverage_enum') ? $entry->{'kb:coverage_enum'} : '';
    // Ignore permitted use for print holdings.
    if (preg_match('/^print/', $coverageEnum)) {
      return '';
    }

    $data = [];
    $coverageNotes = property_exists($entry, 'kb:coverage_notes') ? $entry->{'kb:coverage_notes'} : '';
    $collectionUserNotes = property_exists($entry, 'kb:collection_user_notes') ? $entry->{'kb:collection_user_notes'} : '';
    if (preg_match('/(purchase|subscribe)/', $coverageNotes)
      && str_pos($coverageNotes, '|') === FALSE) {
      if ($collectionUserNotes) {
        $data = explode(' | ', $collectionUserNotes);
      }
      if ($coverageNotes) {
        $data = $coverageNotes;
      }
      if (!preg_match('/^(ebook|video)/', $entry->{'kb:coverage'})) {
        $data[] = implode(' ', [
          self::getCoverage($entry->{'kb:coverage'}),
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
      elseif (!preg_match('/^(ebook|video)/', $entry->{'kb:coverage'})) {
        $data[] = implode(' ', [
          self::getCoverage($entry->{'kb:coverage'}),
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
   * Generate coverage statement.
   */
  public static function coverage($entry) {
    $coverageEnum = property_exists($entry, 'kb:coverage_enum') ? $entry->{'kb:coverage_enum'} : '';
    if (preg_match('/^print/', $coverageEnum)) {
      $coverage = implode('; ', [
        $entry->{'kb:location'}, $entry->{'kb:coverage_notes'},
      ]);
    }
    else {
      $coverage = implode(' ', [
        self::getCoverage($entry->{'kb:coverage'}),
        self::getEnumeratedCoverage($coverageEnum),
      ]);
    }
    return trim($coverage);
  }

  /**
   * Generate enumerated coverage statement.
   */
  public static function getEnumeratedCoverage($text) {
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
  public static function getCoverage($text) {
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
