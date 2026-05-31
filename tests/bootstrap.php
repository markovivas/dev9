<?php
<?php
/**
 * PHPUnit bootstrap for Intranet Dashboard Base theme.
 *
 * Prerequisites:
 *   - WordPress PHPUnit test suite installed
 *   - Database configured in wp-tests-config.php
 *
 * Setup: https://make.wordpress.org/cli/handbook/how-to/phpunit/
 *
 * Run: cd wp-content/themes/dev9 && phpunit
 */

$_tests_dir = getenv('WP_TESTS_DIR');

if (! $_tests_dir) {
	$_tests_dir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

if (! file_exists($_tests_dir . '/includes/functions.php')) {
	echo "Could not find $_tests_dir/includes/functions.php. Aborting.\n";
	echo "Install WP test suite first: https://make.wordpress.org/cli/handbook/how-to/phpunit/\n";
	exit(1);
}

require_once $_tests_dir . '/includes/functions.php';

function _intranet_dashboard_base_manually_load_theme() {
	require dirname(__DIR__) . '/functions.php';
}
tests_add_filter('muplugins_loaded', '_intranet_dashboard_base_manually_load_theme');

require_once $_tests_dir . '/includes/bootstrap.php';
