<?php
/**
 * PHPUnit bootstrap file
 *
 * @since 0.1.0
 * @package WPGraphQL\Extensions\BuddyPress
 */

declare(strict_types=1);

// Setting PHPUnit polyfills.
const WP_TESTS_PHPUNIT_POLYFILLS_PATH = __DIR__ . '/../vendor/yoast/phpunit-polyfills';

// Use WP PHPUnit.
if ( defined( 'WPGRAPHQL_BP_USE_WP_ENV_TESTS' ) ) {
	require_once dirname( __FILE__, 2 ) . '/vendor/wp-phpunit/wp-phpunit/__loaded.php';
}

// Define constants.
require dirname( __FILE__ ) . '/includes/define-constants.php';

if ( ! file_exists( WP_TESTS_DIR . '/includes/functions.php' ) ) {
	die( "The WordPress PHPUnit test suite could not be found.\n" );
}

if ( ! file_exists( BP_TESTS_DIR . '/includes/loader.php' ) ) {
	die( "The BuddyPress plugin could not be found.\n" );
}

if ( ! file_exists( WPGRAPHQL_PLUGIN_DIR_TEST . '/wp-graphql.php' ) ) {
	die( "The WP Graphql plugin could not be found.\n" );
}

// Give access to tests_add_filter() function.
require_once WP_TESTS_DIR . '/includes/functions.php';

/**
 * Manually load the plugins being tested.
 */
tests_add_filter(
	'muplugins_loaded',
	function() {

		// Load BuddyPress
		require_once BP_TESTS_DIR . '/includes/loader.php';

		// Load WP-GraphQL
		require_once WPGRAPHQL_PLUGIN_DIR_TEST . '/wp-graphql.php';

		// Load our plugin.
		require_once dirname( __FILE__, 2 ) . '/wp-graphql-buddypress.php';
	}
);

/**
 * Remove Extensions from the response.
 */
tests_add_filter(
	'graphql_request_results',
	function( $response ) {
		unset( $response['extensions'] );

		return $response;
	}
);

echo "Loading WP testing environment...\n";
require_once WP_TESTS_DIR . '/includes/bootstrap.php';

echo "Loading BuddyPress testcase...\n";
require_once BP_TESTS_DIR . '/includes/testcase.php';

echo "Loading WPGraphQL BuddyPress testcase...\n";
require_once dirname( __FILE__ ) . '/includes/class-test-case.php';
