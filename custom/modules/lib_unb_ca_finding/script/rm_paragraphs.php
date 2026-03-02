<?php

/**
 * @file
 * Safe helper to remove paragraph entities, compatible with `drush scr`.
 *
 * Usage with drush:
 *   drush scr path/to/rm_paragraphs.php -- --live-run --min-created="2024-01-01"
 *
 * Usage with plain PHP:
 *   php path/to/rm_paragraphs.php --live-run --min-created="2024-01-01"
 *
 * Notes:
 * - Default is DRY RUN (no deletions). Pass --live-run to perform deletions.
 * - --min-created accepts a UNIX timestamp or any date string parseable by strtotime().
 *
 * Important: This file is intended to be included by Drush, so it should
 * not call exit(). It returns control to the caller (Drush) instead.
 */

if (PHP_SAPI !== 'cli') {
  fwrite(STDERR, "This script must be run from the command line.\n");
  return;
}

/**
 * Parse options in a way that works when Drush includes this file (drush scr).
 *
 * Recognized options:
 *   --live-run            (flag)
 *   --min-created=VALUE   (or --min-created VALUE)
 */
function parse_options_from_argv() {
  $result = [
    'live_run' => false,
    'min_created_raw' => null,
  ];

  // Try getopt() first (works if script is executed directly by PHP).
  $opts = @getopt('', ['live-run', 'min-created:']);
  if (!empty($opts)) {
    $result['live_run'] = !empty($opts['live-run']);
    if (isset($opts['min-created'])) {
      $result['min_created_raw'] = $opts['min-created'];
    }
    return $result;
  }

  // Fallback: robust parsing of $_SERVER['argv'] (works with drush scr).
  $argv = $_SERVER['argv'] ?? [];
  foreach ($argv as $i => $arg) {
    // Skip the main command token and any '--' separators.
    if ($i === 0 || $arg === '--') {
      continue;
    }

    if ($arg === '--live-run' || $arg === '-live-run') {
      $result['live_run'] = true;
      continue;
    }

    if (strpos($arg, '--min-created=') === 0) {
      $result['min_created_raw'] = substr($arg, strlen('--min-created='));
      continue;
    }

    if ($arg === '--min-created' && isset($argv[$i + 1])) {
      $result['min_created_raw'] = $argv[$i + 1];
      continue;
    }
  }

  return $result;
}

try {
  $options = parse_options_from_argv();
  $live_run = (bool) $options['live_run'];
  $min_created_raw = $options['min_created_raw'] ?? null;
  $min_ts = null;

  if ($min_created_raw !== null) {
    if (is_numeric($min_created_raw)) {
      $min_ts = (int) $min_created_raw;
    } else {
      $min_ts = strtotime($min_created_raw);
      if ($min_ts === false) {
        fwrite(STDERR, "Could not parse --min-created value: '{$min_created_raw}'. Use YYYY-MM-DD or a timestamp.\n");
        return;
      }
    }
  }

  /** @var \Drupal\Core\Entity\EntityStorageInterface $storage */
  $storage = \Drupal::entityTypeManager()->getStorage('paragraph');

  /** @var \Drupal\Core\Entity\Query\QueryInterface $query */
  $query = \Drupal::entityQuery('paragraph')->accessCheck(FALSE);
  $or = $query->orConditionGroup()
    ->condition('type', 'body_section')
    ->condition('type', 'body_sidebar_section');
  $query->condition($or);
  
  if ($min_ts !== null) {
    $query->condition('created', $min_ts, '>=');
  }

  // Execute the query
  $ids = $query->execute();
  $count = count($ids);

  $formatter = \Drupal::service('date.formatter');

  echo "Paragraphs matched: {$count}\n";

  if ($count === 0) {
    // Nothing to do; return to Drush.
    return;
  }

  // Show a sample of matching entities to inspect (first N).
  $sample_limit = 50;
  $sample_ids = array_slice($ids, 0, $sample_limit);
  $sample_entities = $storage->loadMultiple($sample_ids);

  echo "Sample of matching paragraphs (up to {$sample_limit}):\n";
  foreach ($sample_entities as $entity) {
    // Paragraph entity: id(), bundle(), getCreatedTime()
    $created = $entity->getCreatedTime();
    $created_str = $formatter->format($created, 'custom', 'Y-m-d H:i:s');
    printf(" - id:%d  bundle:%s  created:%s\n", $entity->id(), $entity->bundle(), $created_str);
  }
  if ($count > $sample_limit) {
    echo "  ... (showing first {$sample_limit} of {$count})\n";
  }

  if (!$live_run) {
    echo "\nDRY RUN: no deletions performed. Re-run with --live-run to delete the matched paragraphs.\n";
    return;
  }

  // LIVE RUN: perform deletion in chunks to avoid blowing memory for very large sets.
  echo "\nLIVE RUN: deleting {$count} paragraph(s)...\n";

  $chunk_size = 200;
  $chunks = array_chunk($ids, $chunk_size);
  $deleted_total = 0;

  foreach ($chunks as $i => $chunk_ids) {
    $entities = $storage->loadMultiple($chunk_ids);
    if (!empty($entities)) {
      $storage->delete($entities);
      $deleted_total += count($chunk_ids);
      echo sprintf("  Deleted chunk %d: %d items (total deleted: %d)\n", $i + 1, count($chunk_ids), $deleted_total);
    }
  }

  echo "Deletion complete: {$deleted_total} paragraph(s) removed.\n";
  // Done: return to Drush cleanly.
  return;
}
catch (\Throwable $e) {
  // Print unexpected errors but do not call exit().
  fwrite(STDERR, "Unhandled error: " . $e->getMessage() . "\n");
  // Optionally print trace when verbose, but keep quiet otherwise.
  if (!empty($_SERVER['DRUSH_VERBOSE']) || (isset($_SERVER['argv']) && in_array('-v', $_SERVER['argv'], true))) {
    fwrite(STDERR, $e->getTraceAsString() . "\n");
  }
  return;
}