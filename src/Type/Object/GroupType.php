<?php
/**
 * Registers Group type and queries
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Type\WPObject
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\WPObject;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\Type\WPObjectType;
use WPGraphQL\Types;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;

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
			array(
				'description'       => __( 'Info about a BuddyPress group', 'wp-graphql-buddypress' ),
				'interfaces'        => array( WPObjectType::node_interface() ),
				'fields'            => array(
					'id'               => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The globally unique identifier for the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group ) {
							return ! empty( $group->id ) ? $group->id : null;
						},
					],
					'groupId'          => [
						'type'        => 'Int',
						'description' => __( 'The id field that matches the BP_Groups_Group->id field.', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group ) {
							return ! empty( $group->id ) ? absint( $group->id ) : null;
						},
					],
					'parent'           => [
						'type'        => self::$type_name,
						'description' => __( 'Parent group of the current group. This field is equivalent to the BP_Groups_Group object matching the BP_Groups_Group->parent_id ID.', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group, $args, AppContext $context ) {
							return ! empty( $group->parent_id )
								? Factory::resolve_group_object( $group->parent_id, $context )
								: null;
						},
					],
					'name'             => [
						'type'        => 'String',
						'description' => __( 'Group name', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group ) {
							return bp_get_group_name( $group );
						},
					],
					'creatorId'        => [
						'type'        => 'User',
						'description' => __( 'The creator of the group', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group, $args, AppContext $context ) {
							$creator_id = ! empty( $group->creator_id ) ? $group->creator_id : null;

							return ! empty( $creator_id )
								? DataSource::resolve_user( $creator_id, $context )
								: null;
						},
					],
					'slug'             => [
						'type'        => 'String',
						'description' => __( 'The slug of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group ) {
							return bp_get_group_slug( $group );
						},
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
						'resolve'     => function( \BP_Groups_Group $group, $args ) {
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
						'resolve'     => function( \BP_Groups_Group $group ) {
							return bp_get_group_permalink( $group );
						},
					],
					'enableForum'      => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the group has a forum or not', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group ) {
							return bp_group_is_forum_enabled( $group );
						},
					],
					'totalMemberCount' => [
						'type'        => 'Int',
						'description' => __( 'Count of all group members.', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group, $args, AppContext $context ) {
							// Context aware.
							if ( 'edit' !== $context ) {
								return null;
							}

							$count = groups_get_groupmeta( $group->id, 'total_member_count' );

							return absint( $count );
						},
					],
					'lastActivity'     => [
						'type'        => 'String',
						'description' => __( 'The date the group was last active', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group, $args, AppContext $context ) {
							// Context aware.
							if ( 'edit' !== $context ) {
								return null;
							}

							return Types::prepare_date_response( groups_get_groupmeta( $group->id, 'last_activity' ) );
						},
					],
					'dateCreated'      => [
						'type'        => 'String',
						'description' => __( 'The date the group was created.', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group ) {
							return Types::prepare_date_response( $group->date_created );
						},
					],
					'status'           => [
						'type'        => 'String',
						'description' => __( 'The status of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function( \BP_Groups_Group $group ) {
							return bp_get_group_status( $group );
						},
					],
				),
				'resolve_node'      => function( $node, $id, $type, $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_group_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function( $type, $node ) {
					if ( $node instanceof \BP_Groups_Group ) {
						$type = self::$type_name;
					}

					return $type;
				},
			)
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
							throw new UserError( __( 'The "id" is invalid', 'wp-graphql-buddypress' ) );
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
