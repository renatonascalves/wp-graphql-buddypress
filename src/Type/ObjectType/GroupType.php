<?php
/**
 * Registers Group type and queries
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use GraphQL\Deferred;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\GroupHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Group;

/**
 * Class GroupType
 */
class GroupType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Group';

	/**
	 * Registers the group type and queries to the WPGraphQL schema.
	 */
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress group.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Node', 'DatabaseIdentifier', 'UniformResourceIdentifiable' ],
				'eagerlyLoadType'   => true,
				'fields'            => [
					'parent'           => [
						'type'        => self::$type_name,
						'description' => __( 'Parent group of the current group. This field is equivalent to the BP_Groups_Group object matching the BP_Groups_Group->parent_id ID.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Group $group, array $args, AppContext $context ) {
							return Factory::resolve_group_object( $group->parent, $context );
						},
					],
					'creator'          => [
						'type'        => 'User',
						'description' => __( 'The creator of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Group $group, array $args, AppContext $context ) {
							return ! empty( $group->creator )
								? $context->get_loader( 'user' )->load_deferred( $group->creator )
								: null;
						},
					],
					'admins'           => [
						'type'        => [ 'list_of' => 'User' ],
						'description' => __( 'Administrators of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Group $group, array $args, AppContext $context ) {

							// Only logged users can see these values.
							if ( false === is_user_logged_in() ) {
								return null;
							}

							$admins     = [];
							$admin_mods = (array) groups_get_group_members(
								[
									// @codingStandardsIgnoreLine.
									'group_id'   => $group->databaseId,
									'group_role' => [ 'admin' ],
								]
							);

							foreach ( $admin_mods['members'] as $admin ) {
								if ( ! empty( $admin->is_admin ) && ! empty( $admin->ID ) ) {
									$admins[] = $admin->ID;
								}
							}

							if ( empty( $admins ) || ! is_array( $admins ) ) {
								return null;
							}

							$context->get_loader( 'user' )->buffer( $admins );
							return new Deferred(
								function() use ( $context, $admins ) {
									// @codingStandardsIgnoreLine.
									return $context->get_loader( 'user' )->loadMany( $admins );
								}
							);
						},
					],
					'mods'             => [
						'type'        => [ 'list_of' => 'User' ],
						'description' => esc_html__( 'Moderators of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Group $group, array $args, AppContext $context ) {

							// Only logged users can see these values.
							if ( false === is_user_logged_in() ) {
								return null;
							}

							$mods       = [];
							$admin_mods = (array) groups_get_group_members(
								[
									// @codingStandardsIgnoreLine.
									'group_id'   => $group->databaseId,
									'group_role' => [ 'mod' ],
								]
							);

							foreach ( $admin_mods['members'] as $mod ) {
								if ( empty( $mod->is_admin ) ) {
									$mods[] = $mod->ID;
								}
							}

							if ( empty( $mods ) || ! is_array( $mods ) ) {
								return null;
							}

							$context->get_loader( 'user' )->buffer( $mods );
							return new Deferred(
								function() use ( $context, $mods ) {
									// @codingStandardsIgnoreLine.
									return $context->get_loader( 'user' )->loadMany( $mods );
								}
							);
						},
					],
					'name'             => [
						'type'        => 'String',
						'description' => __( 'Group name', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function( Group $group, array $args ) {
							if ( empty( $group->name ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $group->name;
							}

							return bp_get_group_name( $group->databaseId ?? 0 );
						},
					],
					'slug'             => [
						'type'        => 'String',
						'description' => __( 'The slug of the group.', 'wp-graphql-buddypress' ),
					],
					'description'      => [
						'type'        => 'String',
						'description' => __( 'The description of the group.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function( Group $group, array $args ) {
							if ( empty( $group->description ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $group->description;
							}

							return bp_get_group_description( $group->databaseId ?? 0 );
						},
					],
					'hasForum'         => [
						'type'        => 'Boolean',
						'description' => __( 'Whether forums are enabled for the group.', 'wp-graphql-buddypress' ),
					],
					'totalMemberCount' => [
						'type'        => 'Int',
						'description' => __( 'Count of all group members.', 'wp-graphql-buddypress' ),
					],
					'lastActivity'     => [
						'type'        => 'String',
						'description' => __( 'The date the group was last active.', 'wp-graphql-buddypress' ),
					],
					'dateCreated'      => [
						'type'        => 'String',
						'description' => __( 'The date the group was created.', 'wp-graphql-buddypress' ),
					],
					'status'           => [
						'type'        => 'GroupStatusEnum',
						'description' => __( 'The status of the group.', 'wp-graphql-buddypress' ),
					],
					'types'            => [
						'type'        => [ 'list_of' => 'GroupTypeEnum' ],
						'description' => __( 'The types of the group.', 'wp-graphql-buddypress' ),
					],
					'attachmentAvatar' => [
						'type'        => 'Attachment',
						'description' => __( 'Attachment Avatar of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Group $group ) {

							// Bail early, if disabled.
							if ( false === buddypress()->avatar->show_avatars ) {
								return null;
							}

							return Factory::resolve_attachment( $group->databaseId ?? 0, 'group' );
						},
					],
					'attachmentCover'  => [
						'type'        => 'Attachment',
						'description' => __( 'Attachment Cover of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Group $group ) {

							// Bail early, if disabled.
							if ( false === bp_is_active( 'groups', 'cover_image' ) ) {
								return null;
							}

							return Factory::resolve_attachment_cover( $group->databaseId ?? 0, 'groups' );
						},
					],
				],
				'resolve_node'      => function( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_group_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function( $type, $node ) {
					if ( $node instanceof Group ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'groupBy',
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress Group object.', 'wp-graphql-buddypress' ),
				'args'        => [
					'id'           => [
						'type'        => 'ID',
						'description' => __( 'Get the object by its global ID.', 'wp-graphql-buddypress' ),
					],
					'groupId'      => [
						'type'        => 'Int',
						'description' => __( 'Get the object by its database ID.', 'wp-graphql-buddypress' ),
					],
					'slug'         => [
						'type'        => 'String',
						'description' => __( 'Get the object by its current slug.', 'wp-graphql-buddypress' ),
					],
					'previousSlug' => [
						'type'        => 'String',
						'description' => __( 'Get the object by its previous slug.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$group = GroupHelper::get_group_from_input( $args );

					return Factory::resolve_group_object( $group->id, $context );
				},
			]
		);
	}
}
