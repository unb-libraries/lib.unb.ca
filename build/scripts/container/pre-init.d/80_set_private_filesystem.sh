#!/usr/bin/env sh
mkdir -p ${DRUPAL_PRIVATE_FILE_PATH} && chown ${NGINX_RUN_USER}:${NGINX_RUN_GROUP} ${DRUPAL_PRIVATE_FILE_PATH}
DRUSH_COMMAND="drush --root=${DRUPAL_ROOT} --uri=default --yes"
$DRUSH_COMMAND eval 'use Drupal\Component\FileSecurity\FileSecurity; print FileSecurity::htaccessLines(FALSE)' > ${DRUPAL_PRIVATE_FILE_PATH}/.htaccess
chown ${NGINX_RUN_USER}:${NGINX_RUN_GROUP} ${DRUPAL_PRIVATE_FILE_PATH}/.htaccess
