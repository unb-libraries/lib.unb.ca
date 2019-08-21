#!/usr/bin/env sh
# Import content.
if [[ "$IMPORT_INITIAL_CONTENT" == "TRUE" ]]; then
  cd /app/html
  # Install prerequisite modules for import.
  composer require drupal/migrate_plus:4.2 drupal/migration_tools drupal/migrate_tools querypath/querypath drupal/migrate_source_csv:2.2

  # Ensure we query the proper host for content
  echo "131.202.38.2    lib.unb.ca" >> /etc/hosts

  # Run migrations.
  $DRUSH en lib_unb_ca_users lib_unb_ca_pages lib_unb_ca_news
  $DRUSH migrate-import lib_unb_ca_users
  $DRUSH migrate-import lib_unb_pages
  $DRUSH migrate-import lib_unb_ca_news
  $DRUSH pmu lib_unb_ca_pages migrate migrate_plus migrate_tools migration_tools

  # Remove host entry
  sed -i '/131.202/d' /etc/hosts

  # Remove modules added for import only.
  composer remove drupal/migrate_plus drupal/migration_tools drupal/migrate_tools querypath/querypath drupal/migrate_source_csv
  $DRUSH cr
fi
