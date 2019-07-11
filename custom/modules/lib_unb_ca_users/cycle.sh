#!/usr/bin/env sh
#
# Convenience script to test migration defined in this module. Should not be used in production.
#
# Migration requires several libraries/modules. To install:
#   composer require drupal/migrate_plus drupal/migration_tools drupal/migrate_tools drupal/migrate_source_csv
$DRUSH pmu lib_unb_ca_users
$DRUSH en lib_unb_ca_users
$DRUSH migrate-rollback lib_unb_ca_users
$DRUSH migrate-import lib_unb_ca_users
