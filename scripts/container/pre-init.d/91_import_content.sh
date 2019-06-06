#!/usr/bin/env sh
# Import content on restart.
if [[ "$IMPORT_INITIAL_CONTENT" == "TRUE" ]]; then
  cd /app/html
  composer require drupal/migrate_plus drupal/migration_tools drupal/migrate_tools querypath/querypath
  $DRUSH en lib_unb_ca_pages
  $DRUSH migrate-import lib_unb_pages
  $DRUSH pmu lib_unb_ca_pages migrate migrate_plus migrate_tools migration_tools
  composer remove drupal/migrate_plus drupal/migration_tools drupal/migrate_tools querypath/querypath
  $DRUSH cr
fi
