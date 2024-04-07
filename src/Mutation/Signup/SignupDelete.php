<?php
/**
 * SignupDelete Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Signup
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Signup;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\SignupHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Signup;
use BP_Signup;

/**
 * SignupDelete Class.
 */
class SignupDelete {

	/**
	 * Registers the SignupDelete mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'deleteSignup',
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
			'deleted' => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the signup deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'signup'  => [
				'type'        => 'Signup',
				'description' => __( 'The deleted signup object.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return $payload['previousObject'] ?? null;
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

			// Check and get the signup.
			$signup = SignupHelper::get_signup_from_input( $input );

			// Bail now if a user isn't allowed to delete a signup.
			if ( false === SignupHelper::can_see() ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			// Get and save the Signup object before it is deleted.
			$previous_signup = new Signup( $signup );

			$retval = BP_Signup::delete( [ $signup->id ] );

			if ( ! empty( $retval['errors'] ) ) {
				throw new UserError( esc_html__( 'Could not delete the signup.', 'wp-graphql-buddypress' ) );
			}

			// The deleted signup status and the previous signup object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_signup,
			];
		};
	}
}
