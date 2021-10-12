<?php

/**
 * @file
 * Include global settings overrides here.
 */

// Specify install profile.
$settings['install_profile'] = 'minimal';

// Private file system.
$settings['file_private_path'] = '/app/private_filesystem';

// Redis.
$settings['cache_prefix']['default'] = 'DRUPAL_SITE_ID_';
$conf['chq_redis_cache_enabled'] = TRUE;
require_once dirname(__FILE__) . "/settings.redis.inc";

// Newrelic.
if (extension_loaded('newrelic')) {
  require_once dirname(__FILE__) . "/settings.newrelic.inc";
}

$settings['config_sync_directory'] = '../configuration';
