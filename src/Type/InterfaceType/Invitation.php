<?php
/**
 * Registers the Invitation interface.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\InterfaceType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\InterfaceType;

use WPGraphQL\AppContext;

/**
 * Invitation Interface Class.
 */
class Invitation {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Invitation';

	/**
	 * Register the Invitation interface.
	 */
	public static function register_type(): void {
		register_graphql_interface_type(
			self::$type_name,
			[
				'description' => __( 'An invitation object', 'wp-graphql-buddypress' ),
				'interfaces'  => [ 'Node', 'DatabaseIdentifier' ],
				'fields'      => [
					'invitee'         => [
						'type'        => 'User',
						'description' => __( 'The user object of the invited user.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( $source, array $args, AppContext $context ) {
							return ! empty( $source->invitee )
								? $context->get_loader( 'user' )->load_deferred( $source->invitee )
								: null;
						},
					],
					'inviter'         => [
						'type'        => 'User',
						'description' => __( 'The user object who made the invite.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( $source, array $args, AppContext $context ) {
							return ! empty( $source->inviter )
								? $context->get_loader( 'user' )->load_deferred( $source->inviter )
								: null;
						},
					],
					'itemId'          => [
						'type'        => 'Int',
						'description' => __( 'The ID associated with the invitation and component. E.g: the group ID if a group invitation', 'wp-graphql-buddypress' ),
					],
					'dateModified'    => [
						'type'        => 'String',
						'description' => __( 'The date the object was created or last updated, in the site\'s timezone..', 'wp-graphql-buddypress' ),
					],
					'dateModifiedGmt' => [
						'type'        => 'String',
						'description' => __( 'The date the object was created or last updated, as GMT.', 'wp-graphql-buddypress' ),
					],
					'type'            => [
						'type'        => 'InvitationTypeEnum',
						'description' => __( 'The type of the invitation.', 'wp-graphql-buddypress' ),
					],
					'inviteSent'      => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the invite has been sent to the invitee.', 'wp-graphql-buddypress' ),
					],
					'accepted'        => [
						'type'        => 'Boolean',
						'description' => __( 'Status of the invitation. Has it been accepted by the invitee?', 'wp-graphql-buddypress' ),
					],
					'message'         => [
						'type'        => 'String',
						'description' => __( 'The raw and rendered versions for the content of the message.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function ( $source, $args ) {

							if ( empty( $source->message ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $source->message;
							}

							return apply_filters( 'the_content', $source->message );
						},
					],
				],
			]
		);
	}
}
