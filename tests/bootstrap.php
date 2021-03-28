<?php
/**
 * PHPUnit bootstrap file
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

if ( ! defined( 'BP_TESTS_DIR' ) ) {
	define( 'BP_TESTS_DIR', dirname( dirname( dirname( __FILE__ ) ) ) . '/buddypress/tests/phpunit' );
}

if ( ! defined( 'WPGRAPHQL_PLUGIN_DIR_TEST' ) ) {
	define( 'WPGRAPHQL_PLUGIN_DIR_TEST', dirname( dirname( dirname( __FILE__ ) ) ) . '/wp-graphql' );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugins being tested.
 */
tests_add_filter(
	'muplugins_loaded',
	function() {

		// Make sure BP is installed and loaded first.
		require BP_TESTS_DIR . '/includes/loader.php';

		// Load WP-GraphQL
		require WPGRAPHQL_PLUGIN_DIR_TEST . '/wp-graphql.php';

		// Load our plugin.
		require_once dirname( __FILE__ ) . '/../wp-graphql-buddypress.php';
	}
);

/**
 * Remove Extensions.
 */
tests_add_filter(
	'graphql_request_results',
	function( $response ) {
		unset( $response['extensions'] );

		return $response;
	}
);

// Start up the WP testing environment.
echo "Loading WP testing environment...\n";
require_once $_tests_dir . '/includes/bootstrap.php';

// Load the BP test files.
echo "Loading BuddyPress testcase...\n";
require_once BP_TESTS_DIR . '/includes/testcase.php';
