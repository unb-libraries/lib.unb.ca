<?php

/**
 * @file
 * Patron database configuration settings.
 */

$databases['patrons']['default'] = [
  'database' => 'PATRON_DATABASE',
  'username' => 'PATRON_DATABASE_USER',
  'password' => 'PATRON_DATABASE_PASSWORD',
  'prefix' => '',
  'host' => 'PATRON_DATABASE_HOST',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];
