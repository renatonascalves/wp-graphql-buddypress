<?php
/**
 * PHPUnit bootstrap file.
 *
 * @since 0.0.1-alpha
 * @package WPGraphQL\Extensions\BuddyPress
 */

declare(strict_types=1);

// TODO: remove it after Mantle shims factories.
require_once dirname( __FILE__, 2 ) . '/vendor/wp-phpunit/wp-phpunit/includes/factory.php';

\Mantle\Testing\manager()
	->maybe_rsync_plugin()
	->before( fn() => require_once __DIR__ . '/includes/define-constants.php' )
	->loaded(
		function() {
			require_once BP_TESTS_DIR . '/includes/loader.php';
			echo "Installing WPGraphQL...\n";
			require_once dirname( __FILE__, 3 ) . '/wp-graphql/wp-graphql.php';
			echo "Installing WPGraphQL BuddyPress...\n";
			require_once dirname( __DIR__ ) . '/wp-graphql-buddypress.php';
		}
	)
	->after( function() {
		require_once dirname( __FILE__ ) . '/includes/class-test-case.php';
		require_once BP_TESTS_DIR . '/includes/testcase.php';

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
