<?php
/**
 * SignupResendEmail Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Signup
 * @since 0.1.1
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Signup;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\SignupHelper;
use BP_Signup;

/**
 * SignupResendEmail Class.
 */
class SignupResendEmail {

	/**
	 * Registers the SignupResendEmail mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'resendSignupEmail',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input fields.
	 *
	 * @return array
	 */
	public static function get_input_fields(): array {
		return [
			'id'         => [
				'type'        => 'ID',
				'description' => __( 'The globally unique identifier for the signup.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'The id field that matches the BP_Signup->id field.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output fields.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'sent' => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the action.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return (bool) $payload['sent'];
				},
			],
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload(): callable {
		return function ( array $input ) {

			// Check and get the signup.
			$signup = SignupHelper::get_signup_from_input( $input );

			// Resend email.
			$retval = BP_Signup::resend( [ $signup->id ] );

			if ( ! empty( $retval['errors'] ) ) {
				throw new UserError( esc_html__( 'Your account has already been activated.', 'wp-graphql-buddypress' ) );
			}

			return [ 'sent' => true ];
		};
	}
}
