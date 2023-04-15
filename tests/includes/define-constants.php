<?php
/**
 * Define constants needed by the test suite.
 *
 * @since 0.0.1-alpha
 * @package WPGraphQL\Extensions\BuddyPress
 */

declare(strict_types=1);

if ( ! defined( 'BP_TESTS_DIR' ) ) {
	define( 'BP_TESTS_DIR', dirname( __FILE__, 4 ) . '/buddypress/tests/phpunit' );
}

// My version of REST_TESTS_IMPOSSIBLY_HIGH_NUMBER.
define( 'GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER', 99999999 );
