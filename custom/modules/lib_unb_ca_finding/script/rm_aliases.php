<?php

declare(strict_types=1);

use Drupal\Core\Database\Connection;

$container = \Drupal::getContainer();
if (!$container) {
  throw new \RuntimeException('Drupal container not available. Run via Drush with a fully bootstrapped site.');
}

/** @var \Drupal\Core\Database\Connection $db */
$db = $container->get('database');
if (!$db instanceof Connection) {
  throw new \RuntimeException("Service 'database' did not return a Drupal Core Connection.");
}

// Count aliases.
$total = (int) $db->select('path_alias', 'pa')
  ->countQuery()
  ->execute()
  ->fetchField();

print "Found {$total} URL aliases in {path_alias}.\n";

if ($total === 0) {
  print "Nothing to delete.\n";
  return;
}

// Delete all aliases.
$deleted = $db->delete('path_alias')->execute();
print "Deleted {$deleted} rows from {path_alias}.\n";

// Clear alias manager runtime cache if available (method exists on core AliasManager implementations).
if ($container->has('path_alias.manager')) {
  $alias_manager = $container->get('path_alias.manager');
  if (is_object($alias_manager) && method_exists($alias_manager, 'cacheClear')) {
    $alias_manager->cacheClear();
    print "Cleared path_alias.manager runtime cache.\n";
  }
}

// Rebuild caches (core function in Drupal 9).
// If you prefer, remove this and run `drush cr` after the script.
if (function_exists('cache_rebuild')) {
  cache_rebuild();
  print "cache_rebuild() completed.\n";
}

print "Done.\n";