<?php
/**
 * Registers BuddyPress XProfile Group object and fields.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Type\Object
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Object;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileGroup;

/**
 * XProfileGroupType Class.
 */
class XProfileGroupType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'XProfileGroup';

	/**
	 * Register XProfile object and fields to the WPGraphQL schema.
	 */
	public static function register() {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress XProfile group.', 'wp-graphql-buddypress' ),
				'fields'            => [
					'id'               => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The globally unique identifier for the XProfile group.', 'wp-graphql-buddypress' ),
					],
					'groupId'          => [
						'type'        => 'Int',
						'description' => __( 'The id field that matches the BP_XProfile_Group->id field.', 'wp-graphql-buddypress' ),
					],
					'name'             => [
						'type'        => 'String',
						'description' => __( 'XProfile group name.', 'wp-graphql-buddypress' ),
					],
					'groupOrder'             => [
						'type'        => 'Int',
						'description' => __( 'Order of the group relative to other groups.', 'wp-graphql-buddypress' ),
					],
					'canDelete'             => [
						'type'        => 'Boolean',
						'description' => __( 'Can this group be deleted?', 'wp-graphql-buddypress' ),
					],
					'description'      => [
						'type'        => 'String',
						'description' => __( 'The description of the XProfile group.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output.', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function( XProfileGroup $group, array $args ) {
							if ( empty( $group->description ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $group->description;
							}

							return apply_filters( 'bp_get_the_profile_field_description', stripslashes( $group->description ) );
						},
					],
				],
				'resolve_node'      => function( $node, $id, $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_xprofile_group_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function( $type, $node ) {
					if ( $node instanceof XProfileGroup ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'xprofileGroupBy',
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress XProfile Group object.', 'wp-graphql-buddypress' ),
				'args'        => [
					'id'           => [
						'type'        => 'ID',
						'description' => __( 'Get the object by its global ID.', 'wp-graphql-buddypress' ),
					],
					'groupId'      => [
						'type'        => 'Int',
						'description' => __( 'Get the object by its database ID.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$xprofile_group_id = 0;

					if ( ! empty( $args['id'] ) ) {
						$id_components = Relay::fromGlobalId( $args['id'] );

						if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
							throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
						}

						$xprofile_group_id = absint( $id_components['id'] );
					} elseif ( ! empty( $args['groupId'] ) ) {
						$xprofile_group_id = absint( $args['groupId'] );
					}

					return Factory::resolve_xprofile_group_object( $xprofile_group_id, $context );
				},
			]
		);
	}
}
