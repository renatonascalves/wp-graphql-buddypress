<?php
/**
 * InvitationCreate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Invites
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Invites;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\InvitationHelper;

/**
 * InvitationCreate Class.
 */
class InvitationCreate {

	/**
	 * Registers the InvitationCreate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'createInvitation',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input field configuration.
	 *
	 * @return array
	 */
	public static function get_input_fields(): array {
		return [
			'userId'     => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'The ID of the invited user.', 'wp-graphql-buddypress' ),
			],
			'itemId'     => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'The ID associated with the invitation and component. E.g: the group ID if a group invitation.', 'wp-graphql-buddypress' ),
			],
			'inviterId'  => [
				'type'        => 'Int',
				'description' => __( 'The ID of the user who made the invite. Defaults to logged in user.', 'wp-graphql-buddypress' ),
			],
			'sendInvite' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the invite should be sent to the invitee.', 'wp-graphql-buddypress' ),
			],
			'message'    => [
				'type'        => 'String',
				'description' => __( 'The optional message to send to the invited user.', 'wp-graphql-buddypress' ),
			],
			'type'       => [
				'type'        => [ 'non_null' => 'InvitationTypeEnum' ],
				'description' => __( 'The type of the invitation.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output field configuration.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'invite' => [
				'type'        => 'GroupInvitation',
				'description' => __( 'The invitation object that was created.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_invitation_object( absint( $payload['id'] ), $context );
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

			$error_message    = __( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' );
			$invalid_id_error = __( 'Invalid member ID.', 'wp-graphql-buddypress' );

			if ( false === is_user_logged_in() ) {
				throw new UserError( esc_html( $error_message ) );
			}

			$user  = Factory::get_user( $input['userId'] );
			$group = bp_get_group( $input['itemId'] );

			if ( empty( $group->id ) ) {
				throw new UserError( esc_html__( 'Invalid group ID.', 'wp-graphql-buddypress' ) );
			}

			$group_id = $group->id;

			if ( 'request' === $input['type'] ) {

				if ( empty( $user->ID ) ) {
					throw new UserError( esc_html( $invalid_id_error ) );
				}

				if ( false === ( bp_loggedin_user_id() === $user->ID || bp_current_user_can( 'bp_moderate' ) ) ) {
					throw new UserError( esc_html( $error_message ) );
				}
			} else {
				$inviter = Factory::get_user( $input['inviterId'] );

				if ( empty( $user->ID ) || empty( $inviter->ID ) || $user->ID === $inviter->ID ) {
					throw new UserError( esc_html( $invalid_id_error ) );
				}

				$can_create = (
					! bp_current_user_can( 'bp_moderate' )
					|| ! groups_is_user_admin( $inviter->ID, $group_id )
					|| ! groups_is_user_mod( $inviter->ID, $group_id )
				);

				if ( false === $can_create ) {
					throw new UserError( esc_html( $error_message ) );
				}
			}

			$params = InvitationHelper::prepare_invite_args( $input );

			if ( 'request' === $input['type'] ) {
				// Avoid duplicate requests.
				if ( false !== groups_check_for_membership_request( $user->ID, $group_id ) ) {
					throw new UserError( esc_html__( 'There is already a request to this member.', 'wp-graphql-buddypress' ) );
				}

				$invite_id     = groups_send_membership_request( $params );
				$error_message = __( 'Could not send membership request to this group.', 'wp-graphql-buddypress' );
			} else {
				$invite_id     = groups_invite_user( $params );
				$error_message = __( 'Could not invite member to the group.', 'wp-graphql-buddypress' );
			}

			if ( false === $invite_id ) {
				throw new UserError( esc_html( $error_message ) );
			}

			// Return the invite ID.
			return [
				'id' => $invite_id,
			];
		};
	}
}
