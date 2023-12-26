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
* Set session lifetime (in seconds), i.e. the time from the user's last visit
* to the active session may be deleted by the session garbage collector. When
* a session is deleted, authenticated users are logged out, and the contents
* of the user's $_SESSION variable is discarded.
*/
ini_set('session.gc_maxlifetime', 200000);

/**
* Set session cookie lifetime (in seconds), i.e. the time from the session is
* created to the cookie expires, i.e. when the browser is expected to discard
* the cookie. The value 0 means "until the browser is closed".
*/
ini_set('session.cookie_lifetime', 2000000);

ini_set('session.cookie_samesite', 'none');
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_path', '/; samesite=None');
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