<?php
/**
 * SignupActivate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Signup
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Signup;

use WPGraphQL\AppContext;
use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\SignupHelper;
use BP_Signup;

/**
 * SignupActivate Class.
 */
class SignupActivate {

	/**
	 * Registers the SignupActivate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'activateSignup',
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
			'activationKey' => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'The activation key.', 'wp-graphql-buddypress' ),
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
			'signup' => [
				'type'        => 'Signup',
				'description' => __( 'The activated signup object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_signup_object( absint( $payload['id'] ), $context );
				},
			],
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		return function ( array $input ) {

			// Check empty signup key.
			if ( empty( $input['activationKey'] ) ) {
				throw new UserError( esc_html__( 'The activation key is required.', 'wp-graphql-buddypress' ) );
			}

			// Check and get the signup.
			$signup = SignupHelper::get_signup_from_input( $input );

			// User is already active.
			if ( true === $signup->active ) {
				throw new UserError( esc_html__( 'The user is already active.', 'wp-graphql-buddypress' ) );
			}

			// Activate signup.
			$retval = BP_Signup::activate( [ $signup->id ] );

			if ( ! empty( $retval['errors'] ) ) {
				throw new UserError( esc_html__( 'Could not activate the signup.', 'wp-graphql-buddypress' ) );
			}

			return [
				'id' => $signup->id,
			];
		};
	}
}
