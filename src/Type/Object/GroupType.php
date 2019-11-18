<?php
/**
 * Registers Group type and queries
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Type\Object
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Object;

use GraphQL\Deferred;
use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
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
	 * Register Group type and queries to the WPGraphQL schema
	 */
	public static function register() {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress group.', 'wp-graphql-buddypress' ),
				'fields'            => [
					'id'               => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The globally unique identifier for the group.', 'wp-graphql-buddypress' ),
					],
					'groupId'          => [
						'type'        => 'Int',
						'description' => __( 'The id field that matches the BP_Groups_Group->id field.', 'wp-graphql-buddypress' ),
					],
					'parent'           => [
						'type'        => self::$type_name,
						'description' => __( 'Parent group of the current group. This field is equivalent to the BP_Groups_Group object matching the BP_Groups_Group->parent_id ID.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Group $group, array $args, AppContext $context ) {
							return ! empty( $group->parent )
								? Factory::resolve_group_object( $group->parent, $context )
								: null;
						},
					],
					'creator'        => [
						'type'        => 'User',
						'description' => __( 'The creator of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Group $group, array $args, AppContext $context ) {
							return ! empty( $group->creator )
								? DataSource::resolve_user( $group->creator, $context )
								: null;
						},
					],
					'admins'         => [
						'type'        => [
							'list_of' => 'User',
						],
						'description' => esc_html__( 'Administrators of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Group $group, array $args, AppContext $context ) {
							$admins     = [];
							$admin_mods = groups_get_group_members(
								[
									// @codingStandardsIgnoreLine.
									'group_id'   => $group->groupId,
									'group_role' => [ 'admin' ],
								]
							);

							foreach ( (array) $admin_mods['members'] as $admin ) {
								if ( ! empty( $admin->is_admin ) ) {
									$admins[] = $admin->ID;
								}
							}

							if ( empty( $admins ) || ! is_array( $admins ) ) {
								return null;
							}

							$context->getLoader( 'user' )->buffer( $admins );
							return new Deferred(
								function() use ( $context, $admins ) {
									// @codingStandardsIgnoreLine.
									return $context->getLoader( 'user' )->loadMany( $admins );
								}
							);
						},
					],
					'mods'         => [
						'type'        => [
							'list_of' => 'User',
						],
						'description' => esc_html__( 'Moderators of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Group $group, array $args, AppContext $context ) {
							$mods       = [];
							$admin_mods = groups_get_group_members(
								[
									// @codingStandardsIgnoreLine.
									'group_id'   => $group->groupId,
									'group_role' => [ 'mod' ],
								]
							);

							foreach ( (array) $admin_mods['members'] as $mod ) {
								if ( empty( $mod->is_admin ) ) {
									$mods[] = $mod->ID;
								}
							}

							if ( empty( $mods ) || ! is_array( $mods ) ) {
								return null;
							}

							$context->getLoader( 'user' )->buffer( $mods );
							return new Deferred(
								function() use ( $context, $mods ) {
									// @codingStandardsIgnoreLine.
									return $context->getLoader( 'user' )->loadMany( $mods );
								}
							);
						},
					],
					'name'             => [
						'type'        => 'String',
						'description' => __( 'Group name', 'wp-graphql-buddypress' ),
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
								'type'        => 'GroupObjectFieldFormatEnum',
								'description' => __( 'Format of the field output', 'wp-graphql' ),
							],
						],
						'resolve'     => function( Group $group, array $args ) {
							if ( empty( $group->description ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $group->description;
							}

							return bp_get_group_description( $group );
						},
					],
					'link'             => [
						'type'        => 'String',
						'description' => __( 'The link of the group.', 'wp-graphql-buddypress' ),
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
				],
				'resolve_node'      => function( $node, $id, $type, $context ) {
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
			'group',
			[
				'type'        => self::$type_name,
				'description' => __( 'A BuddyPress Group object', 'wp-graphql-buddypress' ),
				'args'        => [
					'id' => [
						'type' => [ 'non_null' => 'ID' ],
					],
				],
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$id_components = Relay::fromGlobalId( $args['id'] );

					if ( ! isset( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
						throw new UserError( __( 'The "id" is invalid', 'wp-graphql-buddypress' ) );
					}

					return Factory::resolve_group_object( absint( $id_components['id'] ), $context );
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'groupBy',
			[
				'type'        => self::$type_name,
				'description' => __( 'A BuddyPress Group object', 'wp-graphql-buddypress' ),
				'args'        => [
					'id'           => [
						'type'        => 'ID',
						'description' => __( 'Get the object by its global ID', 'wp-graphql-buddypress' ),
					],
					'groupId'      => [
						'type'        => 'Int',
						'description' => __( 'Get the object by its database ID', 'wp-graphql-buddypress' ),
					],
					'slug'         => [
						'type'        => 'String',
						'description' => __( 'Get the object by its current slug', 'wp-graphql-buddypress' ),
					],
					'previousSlug' => [
						'type'        => 'String',
						'description' => __( 'Get the object by its previous slug', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$group_id = 0;

					if ( ! empty( $args['id'] ) ) {
						$id_components = Relay::fromGlobalId( $args['id'] );

						if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
							throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
						}

						$group_id = absint( $id_components['id'] );
					} elseif ( ! empty( $args['slug'] ) ) {
						$group_id = groups_get_id( esc_html( $args['slug'] ) );
					} elseif ( ! empty( $args['previousSlug'] ) ) {
						$group_id = groups_get_id_by_previous_slug( esc_html( $args['previousSlug'] ) );
					} elseif ( ! empty( $args['groupId'] ) ) {
						$group_id = absint( $args['groupId'] );
					}

					return Factory::resolve_group_object( $group_id, $context );
				},
			]
		);
	}
}
