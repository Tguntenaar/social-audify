<?php
/**
 * PHPUnit Bootstrap
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load Restrict Content Pro
 */
function _manually_load_plugin() {
	require __DIR__ . '/../restrict-content-pro.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

tests_add_filter( 'rcp_show_deprecated_notices', '__return_false' );
tests_add_filter( 'rcp_show_backtrace', '__return_false' );

require $_tests_dir . '/includes/bootstrap.php';

/**
 * Run installation
 */
rcp_options_install();

require_once 'phpunit/class-unittestcase.php';
require_once 'phpunit/factory.php';
