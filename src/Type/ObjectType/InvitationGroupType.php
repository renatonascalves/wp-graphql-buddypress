<?php
/**
 * Registers Invitation Group type and queries.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\InvitationHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Invitation;

/**
 * Class InvitationGroupType
 */
class InvitationGroupType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'GroupInvitation';

	/**
	 * Registers the group invitation type and queries to the WPGraphQL schema.
	 */
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress group invitation.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Invitation' ],
				'eagerlyLoadType'   => true,
				'fields'            => [
					'group' => [
						'type'        => 'Group',
						'description' => __( 'The group object to which the user has been invited.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Invitation $invitation, array $args, AppContext $context ) {
							return Factory::resolve_group_object( $invitation->itemId, $context );
						},
					],
				],
				'resolve_node'      => function( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_invitation_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function( $type, $node ) {
					if ( $node instanceof Invitation ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'getInviteBy',
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a Group invitation/request object.', 'wp-graphql-buddypress' ),
				'args'        => [
					'id'       => [
						'type'        => 'ID',
						'description' => __( 'Get the object by its global ID.', 'wp-graphql-buddypress' ),
					],
					'inviteId' => [
						'type'        => 'Int',
						'description' => __( 'Get the object by its database ID.', 'wp-graphql-buddypress' ),
					],
					'type'     => [
						'type'        => [ 'non_null' => 'InvitationTypeEnum' ],
						'description' => __( 'The type of the invitation.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$invite = InvitationHelper::get_invitation_from_input( $args );

					return Factory::resolve_invitation_object( $invite->id, $context );
				},
			]
		);
	}
}