<?php
/**
 * InvitationHelper Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use BP_Invitation;

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
		$invitation_id = 0;

		if ( ! empty( $input['id'] ) ) {
			$id_components = Relay::fromGlobalId( $input['id'] );

			if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
				throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
			}

			$invitation_id = absint( $id_components['id'] );
		} elseif ( ! empty( $input['inviteId'] ) ) {
			$invitation_id = absint( $input['inviteId'] );
		}

		// Check the invitation type.
		if ( empty( $input['type'] ) ) {
			throw new UserError( __( 'The invitation type is required.', 'wp-graphql-buddypress' ) );
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
			throw new UserError( $error_message );
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
}
