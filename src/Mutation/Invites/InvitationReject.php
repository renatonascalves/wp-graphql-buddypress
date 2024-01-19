<?php
/**
 * InvitationReject Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Invites
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Invites;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\InvitationHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Invitation;

/**
 * InvitationReject Class.
 */
class InvitationReject {

	/**
	 * Registers the InvitationReject mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'rejectInvitation',
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
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'deleted' => [
				'type'        => 'Boolean',
				'description' => __( 'The status of the invitation deletion.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload ) {
					return (bool) $payload['deleted'];
				},
			],
			'invite'  => [
				'type'        => 'GroupInvitation',
				'description' => __( 'The deleted invitation object.', 'wp-graphql-buddypress' ),
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

			$invite  = InvitationHelper::get_invitation_from_input( $input );
			$user_id = bp_loggedin_user_id();

			// Stop now if user isn't allowed to reject invite.
			if ( InvitationHelper::can_update_or_delete_invite( $user_id, $invite ) ) {
				throw new UserError( esc_html__( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' ) );
			}

			$previous_invite = new Invitation( $invite );

			if ( 'request' === $input['type'] ) {
				/**
				 * If this change is being initiated by the requesting user,
				 * use the `delete` function.
				 */
				if ( $user_id === $invite->user_id ) {
					$deleted = groups_delete_membership_request( 0, $invite->user_id, $invite->item_id );

					/**
					 * Otherwise, this change is being initiated by a group admin or site admin,
					 * and we should use the `reject` function.
					 */
				} else {
					$deleted = groups_reject_membership_request( 0, $invite->user_id, $invite->item_id );
				}

				$error_message = __( 'There was an error rejecting the membership request.', 'wp-graphql-buddypress' );
			} else {
				/**
				 * If this change is being initiated by the invited user,
				 * use the `reject` function.
				 */
				if ( $user_id === $invite->user_id ) {
					$deleted = groups_reject_invite( $invite->user_id, $invite->item_id, $invite->inviter_id );

					/**
					 * Otherwise, this change is being initiated by a group admin, site admin,
					 * or the inviter, and we should use the `uninvite` function.
					 */
				} else {
					$deleted = groups_uninvite_user( $invite->user_id, $invite->item_id, $invite->inviter_id );
				}

				$error_message = __( 'There was an error rejecting the invitation.', 'wp-graphql-buddypress' );
			}

			if ( false === $deleted ) {
				throw new UserError( esc_html( $error_message ) );
			}

			// The deleted invite status and the previous invite object.
			return [
				'deleted'        => true,
				'previousObject' => $previous_invite,
			];
		};
	}
}
