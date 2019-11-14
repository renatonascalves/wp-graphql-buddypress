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

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

if ( ! defined( 'BP_TESTS_DIR' ) ) {
	$bp_tests_dir = getenv( 'BP_TESTS_DIR' );
	if ( $bp_tests_dir ) {
		define( 'BP_TESTS_DIR', $bp_tests_dir );
	} else {
		define( 'BP_TESTS_DIR', dirname( __FILE__ ) . '/../../buddypress/tests/phpunit' );
	}
}

if ( ! defined( 'WPGRAPHQL_PLUGIN_DIR' ) ) {
	$wpgraphql_tests_dir = getenv( 'WPGRAPHQL_PLUGIN_DIR' );
	if ( $wpgraphql_tests_dir ) {
		define( 'WPGRAPHQL_PLUGIN_DIR', $wpgraphql_tests_dir );
	} else {
		define( 'WPGRAPHQL_PLUGIN_DIR', dirname( __FILE__ ) . '/../../wp-graphql/' );
	}
}

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	// Make sure BP is installed and loaded first.
	require BP_TESTS_DIR . '/includes/loader.php';

	// Load WP-GraphQL
	require WPGRAPHQL_PLUGIN_DIR . '/wp-graphql.php';

	// Load our plugin.
	require_once dirname( __FILE__ ) . '/../wp-graphql-buddypress.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
echo "Loading WP testing environment...\n";
require_once $_tests_dir . '/includes/bootstrap.php';

// Load the BP test files.
echo "Loading BuddyPress testcase...\n";
require_once BP_TESTS_DIR . '/includes/testcase.php';
