<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';


/**
 * Skipping permissions hardening will make scaffolding
 * work better, but will also raise a warning when you
 * install Drupal.
 *
 * https://www.drupal.org/project/drupal/issues/3091285
 */
// $settings['skip_permissions_hardening'] = TRUE;

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}
$settings['config_sync_directory'] = '../config';
$settings['hash_salt'] = 'ppayA4PRb80lapYp0UuYNFxR70H5BqTSqoqfGqP3zuwStBmBnL6BZRZPcqF2YFXG3ImUdotw8w';
$settings['twig_tweak_enable_php_filter'] = TRUE;
$settings['file_private_path'] = 'sites/default/files/private/';