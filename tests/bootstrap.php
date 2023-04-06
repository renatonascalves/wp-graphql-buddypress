<?php
/**
 * PHPUnit bootstrap file.
 *
 * @since 0.0.1-alpha
 * @package WPGraphQL\Extensions\BuddyPress
 */

// TODO: remove it after Mantle shims factories.
require_once dirname( __FILE__, 2 ) . '/vendor/wp-phpunit/wp-phpunit/includes/factory.php';

\Mantle\Testing\manager()
	->before( function() {
		define( 'WP_TESTS_CONFIG_FILE_PATH', '/tmp/wordpress-tests-lib/wp-tests-config.php' );
		define( 'WP_TESTS_CONFIG_PATH', WP_TESTS_CONFIG_FILE_PATH );

		require_once dirname( __FILE__, 2 ) . '/vendor/wp-phpunit/wp-phpunit/__loaded.php';
		require_once dirname( __FILE__ ) . '/includes/define-constants.php';
	})
	->loaded(
		function() {

			// Load plugins.
			require_once BP_TESTS_DIR . '/includes/loader.php';
			require_once dirname( __FILE__, 3 ) . '/wp-graphql/wp-graphql.php';
			require_once dirname( __DIR__ ) . '/wp-graphql-buddypress.php';
		}
	)
	->after( function() {
		require_once BP_TESTS_DIR . '/includes/testcase.php';
		require_once dirname( __FILE__ ) . '/includes/testcase.php';

		uses( \WPGraphQL_BuddyPress_UnitTestCase::class );
	})
	->install();

/**
 * Remove Extensions from the response.
 */
\Mantle\Testing\tests_add_filter(
	'graphql_request_results',
	function( $response ) {
		unset( $response['extensions'] );

		return $response;
	}
);
