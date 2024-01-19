<?php
/**
 * Registers Group object type and queries
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use GraphQL\Deferred;
use WPGraphQL\AppContext;
use WPGraphQL\Model\Term;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\GroupHelper;
use WPGraphQL\Extensions\BuddyPress\Model\Group;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GeneralEnums;

/**
 * Class GroupObjectType
 */
class GroupObjectType {

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
						'resolve'     => function ( Group $group, array $args, AppContext $context ) {
							return Factory::resolve_group_object( $group->parent, $context );
						},
					],
					'creator'          => [
						'type'        => 'User',
						'description' => __( 'The creator of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Group $group, array $args, AppContext $context ) {
							return ! empty( $group->creator )
								? $context->get_loader( 'user' )->load_deferred( $group->creator )
								: null;
						},
					],
					'admins'           => [
						'type'        => [ 'list_of' => 'User' ],
						'description' => __( 'Administrators of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Group $group, array $args, AppContext $context ) {

							// Only logged in users can see these values.
							if ( false === is_user_logged_in() ) {
								return null;
							}

							$admins     = [];
							$admin_mods = (array) groups_get_group_members(
								[
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
								function () use ( $context, $admins ) {
									return $context->get_loader( 'user' )->load_many( $admins );
								}
							);
						},
					],
					'mods'             => [
						'type'        => [ 'list_of' => 'User' ],
						'description' => esc_html__( 'Moderators of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Group $group, array $args, AppContext $context ) {

							// Only logged users can see these values.
							if ( false === is_user_logged_in() ) {
								return null;
							}

							$mods       = [];
							$admin_mods = (array) groups_get_group_members(
								[
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
								function () use ( $context, $mods ) {
									return $context->get_loader( 'user' )->load_many( $mods );
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
						'resolve'     => function ( Group $group, array $args ) {
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
						'resolve'     => function ( Group $group, array $args ) {
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
					'attachmentAvatar' => [
						'type'        => 'Attachment',
						'description' => __( 'Attachment Avatar of the group.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Group $group ) {
							$bp = buddypress();

							// Bail early, if disabled.
							if ( isset( $bp->avatar->show_avatars ) && false === $bp->avatar->show_avatars ) {
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
				'resolve_node'      => function ( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_group_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function ( $type, $node ) {
					if ( $node instanceof Group ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			strtolower( self::$type_name ),
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress Group object.', 'wp-graphql-buddypress' ),
				'args'        => GeneralEnums::id_type_args( self::$type_name ),
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$group = GroupHelper::get_group_from_input( $args );

					return Factory::resolve_group_object( $group->id, $context );
				},
			]
		);

		register_graphql_field(
			'GroupTypeTerm',
			'singularName',
			[
				'type'        => 'String',
				'description' => __( 'The name of this type in singular form.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( Term $source ) {
					return get_term_meta( $source->databaseId, 'bp_type_singular_name', true );
				},
			]
		);

		register_graphql_field(
			'GroupTypeTerm',
			'pluralName',
			[
				'type'        => 'String',
				'description' => __( 'The name of this type in plural form.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( Term $source ) {
					return get_term_meta( $source->databaseId, 'bp_type_name', true );
				},
			]
		);

		register_graphql_field(
			'GroupTypeTerm',
			'hasDirectory',
			[
				'type'        => 'Boolean',
				'description' => __( 'Make a list of groups matching this type available on the groups directory.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( Term $source ) {

					if ( false === bp_current_user_can( 'bp_moderate' ) ) {
						return null;
					}

					$meta_value = get_term_meta( $source->databaseId, 'bp_type_has_directory', true );

					return wp_validate_boolean( $meta_value );
				},
			]
		);

		register_graphql_field(
			'GroupTypeTerm',
			'directorySlug',
			[
				'type'        => 'String',
				'description' => __( 'The Group type directory slug.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( Term $source ) {

					if ( false === bp_current_user_can( 'bp_moderate' ) ) {
						return null;
					}

					$meta_value = get_term_meta( $source->databaseId, 'bp_type_directory_slug', true );

					return $meta_value;
				},
			]
		);

		register_graphql_field(
			'GroupTypeTerm',
			'showOnGroup',
			[
				'type'        => 'Boolean',
				'description' => __( 'Show where group types may be listed, like in the group header.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( Term $source ) {

					if ( false === bp_current_user_can( 'bp_moderate' ) ) {
						return null;
					}

					$meta_value = get_term_meta( $source->databaseId, 'bp_type_show_in_list', true );

					return wp_validate_boolean( $meta_value );
				},
			]
		);

		register_graphql_field(
			'GroupTypeTerm',
			'showOnGroupCreation',
			[
				'type'        => 'Boolean',
				'description' => __( 'Show during group creation, and when a group admin is on the group&rsquo;s settings page.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( Term $source ) {

					if ( false === bp_current_user_can( 'bp_moderate' ) ) {
						return null;
					}

					$meta_value = get_term_meta( $source->databaseId, 'bp_type_show_in_create_screen', true );

					return wp_validate_boolean( $meta_value );
				},
			]
		);
	}
}
