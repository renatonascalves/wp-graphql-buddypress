<?php
/**
 * SignupHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use BP_Signup;
use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * SignupHelper Class.
 */
class SignupHelper {

	/**
	 * Get signup helper.
	 *
	 * @throws UserError User error for invalid signup or activation key.
	 *
	 * @param array|int|string $input Possible values.
	 * @return BP_Signup
	 */
	public static function get_signup_from_input( $input ): BP_Signup {
		$query_args    = [];
		$error_message = __( 'This signup does not exist.', 'wp-graphql-buddypress' );

		if ( ! empty( $input['activationKey'] ) ) {
			$query_args['activation_key'] = $input['activationKey'];
			$error_message                = __( 'Invalid activation key.', 'wp-graphql-buddypress' );
		} elseif ( is_string( $input ) && is_email( $input ) ) {
			$query_args['usersearch'] = $input;
		} else {
			$query_args['include'] = Factory::get_id( $input );
		}

		// Get signup.
		$signups = BP_Signup::get( $query_args );

		// Confirm if signup exists.
		if ( empty( $signups['signups'] ) ) {
			throw new UserError( esc_html( $error_message ) );
		}

		return reset( $signups['signups'] );
	}

	/**
	 * Get signup.
	 *
	 * @param int $signup_id Signup ID.
	 * @return BP_Signup
	 */
	public static function get_signup( int $signup_id ): BP_Signup {
		return new BP_Signup( $signup_id );
	}

	/**
	 * Check if an signup exists.
	 *
	 * @param int $signup_id Signup ID.
	 * @return bool
	 */
	public static function signup_exists( int $signup_id ): bool {
		$signup = self::get_signup( absint( $signup_id ) );
		return ( $signup instanceof BP_Signup && ! empty( $signup->id ) );
	}

	/**
	 * Can user see a signup object?
	 *
	 * @return bool
	 */
	public static function can_see(): bool {
		return bp_current_user_can( is_multisite() ? 'manage_network_users' : 'edit_users' );
	}

	/**
	 * Is it signup with a blog enabled?
	 *
	 * @param int $network_id Network ID.
	 * @return bool
	 */
	public static function is_blog_signup_enabled( int $network_id = 0 ): bool {

		if ( empty( $network_id ) ) {
			$network_id = get_main_network_id();
		}

		$active_signup = get_network_option( $network_id, 'registration' );

		return ( 'blog' === $active_signup || 'all' === $active_signup );
	}
}
