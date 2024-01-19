<?php
/**
 * InvitationHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use BP_Invitation;
use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

/**
 * InvitationHelper Class.
 */
class InvitationHelper {

	/**
	 * Get invitation helper.
	 *
	 * @throws UserError User error for invalid Invitation.
	 *
	 * @param array $input Array of possible input fields.
	 * @return BP_Invitation
	 */
	public static function get_invitation_from_input( $input ): BP_Invitation {
		$invitation_id = Factory::get_id( $input );

		// Check the invitation type.
		if ( empty( $input['type'] ) ) {
			throw new UserError( esc_html__( 'The invitation type is required.', 'wp-graphql-buddypress' ) );
		}

		if ( 'request' === $input['type'] ) {
			$invitation    = current( groups_get_requests( [ 'id' => $invitation_id ] ) );
			$error_message = __( 'This group membership request does not exist.', 'wp-graphql-buddypress' );
		} else {
			$invitation    = current( groups_get_invites( [ 'id' => $invitation_id ] ) );
			$error_message = __( 'This invitation does not exist.', 'wp-graphql-buddypress' );
		}

		// Confirm if invitation exists.
		if ( empty( $invitation->id ) || ! $invitation instanceof BP_Invitation ) {
			throw new UserError( esc_html( $error_message ) );
		}

		return $invitation;
	}

	/**
	 * Get invitation.
	 *
	 * @param int $invitation_id Invitation ID.
	 * @return BP_Invitation
	 */
	public static function get_invitation( int $invitation_id ): BP_Invitation {
		return new BP_Invitation( $invitation_id );
	}

	/**
	 * Check if an invitation exists.
	 *
	 * @param int $invitation_id Invitation ID.
	 * @return bool
	 */
	public static function invitation_exists( int $invitation_id ): bool {
		$invitation = self::get_invitation( absint( $invitation_id ) );
		return ( $invitation instanceof BP_Invitation && ! empty( $invitation->id ) );
	}

	/**
	 * Mapping invite params.
	 *
	 * @param array $input The input for the mutation.
	 * @return array
	 */
	public static function prepare_invite_args( array $input ): array {
		$mutation_args = [
			'user_id'     => $input['userId'] ?? false,
			'inviter_id'  => $input['inviterId'] ?? bp_loggedin_user_id(),
			'group_id'    => $input['itemId'] ?? false,
			'send_invite' => $input['sendInvite'] ?? 1,
			'content'     => $input['message'] ?? null,
		];

		if ( 'request' === $input['type'] ) {
			$mutation_args = [
				'user_id'  => $input['userId'] ?? false,
				'group_id' => $input['itemId'] ?? false,
				'content'  => $input['message'] ?? null,
			];
		}

		/**
		 * Allows updating mutation args.
		 *
		 * @param array $mutation_args Mutation output args.
		 * @param array $input         Mutation input args.
		 */
		return apply_filters( "bp_graphql_invitation_{$input['type']}_mutation_args", $mutation_args, $input );
	}

	/**
	 * Check if user has permission to update or delete invitation.
	 *
	 * - the inviter
	 * - the invitee
	 * - group admins
	 * - group mods
	 * - and site admins.
	 *
	 * @param integer       $user_id User ID.
	 * @param BP_Invitation $invite  Invite object.
	 * @return bool
	 */
	public static function can_update_or_delete_invite( int $user_id, BP_Invitation $invite ): bool {
		return (
			! bp_current_user_can( 'bp_moderate' )
			&& ! in_array( $user_id, [ $invite->user_id, $invite->inviter_id ], true )
			&& ! groups_is_user_admin( $user_id, $invite->item_id )
			&& ! groups_is_user_mod( $user_id, $invite->item_id )
		);
	}
}
