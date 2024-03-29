<?php
/**
 * Define constants needed by the test suite.
 *
 * @since 0.1.0
 * @package WPGraphQL\Extensions\BuddyPress
 */

declare( strict_types=1 );

if ( ! defined( 'BP_TESTS_DIR' ) ) {
	define( 'BP_TESTS_DIR', dirname( __FILE__, 4 ) . '/buddypress/tests/phpunit' );
}

if ( ! defined( 'WPGRAPHQL_PLUGIN_DIR_TEST' ) ) {
	define( 'WPGRAPHQL_PLUGIN_DIR_TEST', dirname( __FILE__, 4 ) . '/wp-graphql' );
}

// My version of REST_TESTS_IMPOSSIBLY_HIGH_NUMBER.
define( 'GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER', 99999999 );

/**
 * Determine where the WP test suite lives. Three options are supported:
 *
 * - Define a WP_DEVELOP_DIR environment variable, which points to a checkout
 *   of the develop.svn.wordpress.org repository (this is recommended)
 * - Define a WP_TESTS_DIR environment variable, which points to a checkout of
 *   WordPress test suite
 * - Assume that we are inside of a develop.svn.wordpress.org setup, and walk
 *   up the directory tree
 */
if ( false !== getenv( 'WP_PHPUNIT__DIR' ) && defined( 'WPGRAPHQL_BP_USE_WP_ENV_TESTS' ) ) {
	define( 'WP_TESTS_DIR', getenv( 'WP_PHPUNIT__DIR' ) );
	define( 'WP_ROOT_DIR', '/var/www/html' );
} elseif ( false !== getenv( 'WP_TESTS_DIR' ) ) {
	define( 'WP_TESTS_DIR', getenv( 'WP_TESTS_DIR' ) );
	define( 'WP_ROOT_DIR', WP_TESTS_DIR );
} else {
	// Support WP_DEVELOP_DIR, as used by some plugins
	if ( false !== getenv( 'WP_DEVELOP_DIR' ) ) {
		define( 'WP_ROOT_DIR', getenv( 'WP_DEVELOP_DIR' ) );
	} else {
		define( 'WP_ROOT_DIR', dirname( __FILE__, 7 ) );
	}

	define( 'WP_TESTS_DIR', WP_ROOT_DIR . '/tests/phpunit' );
}
