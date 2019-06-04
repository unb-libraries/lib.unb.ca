#!/usr/bin/env sh
#
# Convenience script to test migration defined in this module. Should not be used in production.
#
# Migration requires several libraries/modules, including Querypath. To install:
#   composer require drupal/migrate_plus drupal/migration_tools drupal/migrate_tools querypath/querypath
$DRUSH pmu lib_unb_ca_pages
$DRUSH en lib_unb_ca_pages
$DRUSH migrate-rollback lib_unb_pages
$DRUSH migrate-import lib_unb_pages
