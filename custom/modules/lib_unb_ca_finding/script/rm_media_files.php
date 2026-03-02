<?php

/**
 * Usage:
 *   drush scr scripts/media_cleanup.php
 *   drush scr scripts/media_cleanup.php -- --min-created=2026-01-01
 *   drush scr scripts/media_cleanup.php -- --min-created=2026-01-01 --live-run
 *
 * Notes:
 * - Options must come after `--` so they are passed to the script.
 * - Dry-run by default.
 */

use Drupal\Core\Datetime\DrupalDateTime;

// Bootstrapped by drush scr.
if (!\Drupal::hasContainer()) {
  throw new \RuntimeException('This script must be run via `drush scr` (Drupal container not available).');
}

/**
 * Parse CLI options passed after `--`.
 */
function parse_options(array $argv): array {
  $opts = [
    'live-run' => FALSE,
    'min-created' => NULL,
  ];

  // $argv includes script path at index 0.
  foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--live-run') {
      $opts['live-run'] = TRUE;
      continue;
    }
    if (str_starts_with($arg, '--min-created=')) {
      $opts['min-created'] = substr($arg, strlen('--min-created='));
      continue;
    }
    if ($arg === '--min-created') {
      // Allow "--min-created 2026-01-01"
      $opts['_expect_min_created_value'] = TRUE;
      continue;
    }
    if (!empty($opts['_expect_min_created_value'])) {
      $opts['min-created'] = $arg;
      unset($opts['_expect_min_created_value']);
      continue;
    }
  }

  unset($opts['_expect_min_created_value']);
  return $opts;
}

/**
 * Return fid referenced by the Media source field (if any).
 */
function get_media_source_fid(\Drupal\media\MediaInterface $media): ?int {
  try {
    $source = $media->getSource();
    $field_name = $source->getSourceFieldDefinition($media->bundle())->getName();

    if (!$media->hasField($field_name) || $media->get($field_name)->isEmpty()) {
      return NULL;
    }

    // Typical case: single-value entity reference to file.
    $target_id = $media->get($field_name)->target_id ?? NULL;
    return $target_id ? (int) $target_id : NULL;
  }
  catch (\Throwable $e) {
    // Remote media sources (oEmbed, etc.) won't map to file entities.
    return NULL;
  }
}

/**
 * Compute total file usage count from file.usage service response.
 */
function total_file_usage(array $usage_list): int {
  $total = 0;
  foreach ($usage_list as $module => $types) {
    foreach ($types as $type => $ids) {
      foreach ($ids as $id => $count) {
        $total += (int) $count;
      }
    }
  }
  return $total;
}

$options = parse_options($_SERVER['argv'] ?? []);
$live_run = (bool) ($options['live-run'] ?? FALSE);
$min_created = $options['min-created'] ?? NULL;

$min_timestamp = NULL;
if (!empty($min_created)) {
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $min_created)) {
    throw new \InvalidArgumentException("Invalid --min-created value. Expected YYYY-MM-DD, got: {$min_created}");
  }
  $dt = new DrupalDateTime($min_created . ' 00:00:00', 'UTC');
  $min_timestamp = (int) $dt->getTimestamp();
}

$etm = \Drupal::entityTypeManager();
$media_storage = $etm->getStorage('media');
$file_storage = $etm->getStorage('file');
$file_usage = \Drupal::service('file.usage');

$query = $media_storage->getQuery()->accessCheck(FALSE);
if ($min_timestamp !== NULL) {
  $query->condition('created', $min_timestamp, '>=');
}
$mids = $query->execute();

$media_count = count($mids);
print "Found {$media_count} media entities";
if ($min_timestamp !== NULL) {
  print " created >= {$min_created}";
}
print ".\n";
print $live_run ? "LIVE RUN: will delete.\n" : "DRY RUN: will not delete.\n";

if ($media_count === 0) {
  exit(0);
}

/** @var \Drupal\media\MediaInterface[] $media_entities */
$media_entities = $media_storage->loadMultiple($mids);

$candidate_fids = [];

foreach ($media_entities as $media) {
  $created_iso = gmdate('Y-m-d\TH:i:s\Z', $media->getCreatedTime());
  print sprintf(
    "Media mid=%d type=%s name=\"%s\" created=%s\n",
    $media->id(),
    $media->bundle(),
    $media->label(),
    $created_iso
  );

  $fid = get_media_source_fid($media);
  if ($fid) {
    $candidate_fids[$fid] = TRUE;
    print "  - referenced file fid={$fid}\n";
  }
}

print "\nCandidate referenced file IDs: " . count($candidate_fids) . "\n";

if (!$live_run) {
  print "Dry run complete.\n";
  exit(0);
}

// 1) Delete media entities.
$media_storage->delete($media_entities);
print "Deleted {$media_count} media entities.\n";

// 2) Delete files if no usage remains.
if (!empty($candidate_fids)) {
  $files = $file_storage->loadMultiple(array_keys($candidate_fids));

  $deleted_files = 0;
  $skipped_files = 0;

  foreach ($files as $file) {
    if (!$file) {
      continue;
    }

    $usage_list = $file_usage->listUsage($file);
    $usage_total = total_file_usage($usage_list);

    if ($usage_total > 0) {
      $skipped_files++;
      print sprintf(
        "Skipping file fid=%d (%s): still has usage=%d\n",
        $file->id(),
        $file->getFileUri(),
        $usage_total
      );
      continue;
    }

    $uri = $file->getFileUri();
    $fid = $file->id();
    $file->delete(); // Removes file entity; core will remove the physical file too.
    $deleted_files++;

    print "Deleted file fid={$fid} ({$uri})\n";
  }

  print "File cleanup complete. Deleted={$deleted_files}, Skipped={$skipped_files}\n";
}

print "Done.\n";