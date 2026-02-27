<?php

/**
 * @file
 * Contains rm_paragraph.php
 *
 * Safe helper to remove paragraph entities.
 *
 * Usage (with drush):
 *   drush php-script rm_paragraph.php -- [--live-run] [--min-created="YYYY-MM-DD" | timestamp]
 *
 * Notes:
 * - By default this script is a DRY RUN and will NOT delete anything.
 * - Pass --live-run to actually delete the matched paragraphs.
 * - Use --min-created to limit paragraphs to those created on or after the given date.
 *   The value may be a Unix timestamp or any date string accepted by strtotime()
 *   (e.g. "2024-01-01", "2024-01-01 15:00", "2024-01-01T00:00:00Z").
 *
 * Example:
 *   # Dry run (default)
 *   drush php-script rm_paragraph.php --
 *
 *   # Dry run, only paragraphs created on/after 2024-01-01
 *   drush php-script rm_paragraph.php -- --min-created="2024-01-01"
 *
 *   # Live run, delete paragraphs created on/after 2024-01-01
 *   drush php-script rm_paragraph.php -- --live-run --min-created="2024-01-01"
 */

if (PHP_SAPI !== 'cli') {
  fwrite(STDERR, "This script must be run from the command line.\n");
  exit(1);
}

// Parse CLI long options: --live-run and --min-created=
$options = getopt('', ['live-run', 'min-created:']);
$live_run = isset($options['live-run']);
$min_created_raw = isset($options['min-created']) ? $options['min-created'] : NULL;
$min_ts = NULL;

if ($min_created_raw !== NULL) {
  // Accept numeric timestamp or parse via strtotime()
  if (is_numeric($min_created_raw)) {
    $min_ts = (int) $min_created_raw;
  }
  else {
    $min_ts = strtotime($min_created_raw);
    if ($min_ts === false) {
      fwrite(STDERR, "Could not parse --min-created value: '{$min_created_raw}'. Use YYYY-MM-DD or a timestamp.\n");
      exit(2);
    }
  }
}

$storage = \Drupal::entityTypeManager()->getStorage('paragraph');
$query = \Drupal::entityQuery('paragraph')->accessCheck(FALSE);
if ($min_ts !== NULL) {
  $query->condition('created', $min_ts, '>=');
}

// Execute query to get ids
$ids = $query->execute();
$count = count($ids);

// Date formatter service for readable output
$formatter = \Drupal::service('date.formatter');

echo "Paragraphs matched: $count\n";

if ($count === 0) {
  // Nothing to do
  exit(0);
}

// Show a sample list (first N) to confirm which entities would be affected
$sample_limit = 50;
$sample_ids = array_slice($ids, 0, $sample_limit);
$sample_entities = $storage->loadMultiple($sample_ids);

echo "Sample of matching paragraphs (up to {$sample_limit}):\n";
foreach ($sample_entities as $entity) {
  // paragraph entities implement getCreatedTime() and id() / bundle()
  $created = $entity->getCreatedTime();
  $created_str = $formatter->format($created, 'custom', 'Y-m-d H:i:s');
  printf(" - id:%d  bundle:%s  created:%s\n", $entity->id(), $entity->bundle(), $created_str);
}
if ($count > $sample_limit) {
  echo "  ... (showing first {$sample_limit} of {$count})\n";
}

if (!$live_run) {
  echo "\nDRY RUN (no deletions were performed). To delete these paragraphs re-run with --live-run.\n";
  exit(0);
}

// Live run: delete the matched paragraphs
echo "\nLIVE RUN: deleting {$count} paragraph(s)...\n";

try {
  $all_entities = $storage->loadMultiple($ids);
  if (!empty($all_entities)) {
    $storage->delete($all_entities);
  }
  echo "Deletion complete: {$count} paragraph(s) removed.\n";
}
catch (\Throwable $e) {
  // Catch Throwable to include \Error and \Exception for robustness
  fwrite(STDERR, "Error during deletion: " . $e->getMessage() . "\n");
  exit(3);
}

exit(0);