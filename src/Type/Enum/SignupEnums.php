<?php
/**
 * Signup Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

/**
 * SignupEnums Class.
 */
class SignupEnums {

	/**
	 * Registers enum type.
	 */
	public static function register(): void {

		// Signup Order By Enum.
		register_graphql_enum_type(
			'SignupOrderByEnum',
			[
				'description' => __( 'Paremeters to order the Signups by.', 'wp-graphql-buddypress' ),
				'values'      => [
					'SIGNUP_ID'  => [
						'name'        => 'SIGNUP_ID',
						'description' => __( 'Used to order results by the signup_id paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'signup_id',
					],
					'LOGIN'      => [
						'name'        => 'LOGIN',
						'description' => __( 'Used to order results by the login paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'login',
					],
					'EMAIL'      => [
						'name'        => 'EMAIL',
						'description' => __( 'Used to order results by the email paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'email',
					],
					'REGISTERED' => [
						'name'        => 'REGISTERED',
						'description' => __( 'Used to order results by the registered paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'registered',
					],
					'ACTIVATED'  => [
						'name'        => 'ACTIVATED',
						'description' => __( 'Used to order results by the activated paremeter.', 'wp-graphql-buddypress' ),
						'value'       => 'activated',
					],
				],
			]
		);
	}
}
