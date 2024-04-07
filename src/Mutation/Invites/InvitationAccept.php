<?php
/**
 * InvitationAccept Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Invites
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Invites;

use WPGraphQL\AppContext;
use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\InvitationHelper;

/**
 * InvitationAccept Class.
 */
class InvitationAccept {

	/**
	 * Registers the InvitationAccept mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'acceptInvitation',
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
				'description' => __( 'Get the object by its global ID.', 'wp-graphql-buddypress' ),
			],
			'databaseId' => [
				'type'        => 'Int',
				'description' => __( 'Get the object by its database ID.', 'wp-graphql-buddypress' ),
			],
			'type'       => [
				'type'        => [ 'non_null' => 'InvitationTypeEnum' ],
				'description' => __( 'The type of the invitation.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output fields.
	 *
	 * @todo Create group member type or interface to append group related information.
	 * See BP_Groups_Member for a list of fields not currently supported here.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'user' => [
				'type'        => 'User',
				'description' => __( 'The new member accepted.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
						return null;
					}

					return $context->get_loader( 'user' )->load_deferred( absint( $payload['id'] ) );
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

			$invite  = InvitationHelper::get_invitation_from_input( $input );
			$user_id = bp_loggedin_user_id();

			// Stop now if user isn't allowed to accept invite.
			if ( InvitationHelper::can_update_or_delete_invite( $user_id, $invite ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			if ( 'request' === $input['type'] ) {
				$accept        = groups_accept_membership_request( 0, $invite->user_id, $invite->item_id );
				$error_message = __( 'There was an error accepting the membership request.', 'wp-graphql-buddypress' );
			} else {
				$accept        = groups_accept_invite( $invite->user_id, $invite->item_id );
				$error_message = __( 'There was an error accepting the invitation.', 'wp-graphql-buddypress' );
			}

			if ( false === $accept ) {
				throw new UserError( esc_html( $error_message ) );
			}

			return [
				'id' => $invite->user_id,
			];
		};
	}
}
