<?php
/**
 * Registers BuddyPress XProfile Group object and fields.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileGroup;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GeneralEnums;
use WPGraphQL\Extensions\BuddyPress\Data\XProfileGroupHelper;

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
	 * Register XProfile Group object and its fields to the WPGraphQL schema.
	 */
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress XProfile group.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Node', 'DatabaseIdentifier' ],
				'fields'            => [
					'name'        => [
						'type'        => 'String',
						'description' => __( 'XProfile group name.', 'wp-graphql-buddypress' ),
					],
					'groupOrder'  => [
						'type'        => 'Int',
						'description' => __( 'Order of the group relative to other groups.', 'wp-graphql-buddypress' ),
					],
					'canDelete'   => [
						'type'        => 'Boolean',
						'description' => __( 'Can this group be deleted?', 'wp-graphql-buddypress' ),
					],
					'description' => [
						'type'        => 'String',
						'description' => __( 'The description of the XProfile group.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output.', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function ( XProfileGroup $group, array $args ) {
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
				'resolve_node'      => function ( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_xprofile_group_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function ( $type, $node ) {
					if ( $node instanceof XProfileGroup ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'xprofileGroup',
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress XProfile Group object.', 'wp-graphql-buddypress' ),
				'args'        => GeneralEnums::id_type_args( self::$type_name ),
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					return Factory::resolve_xprofile_group_object(
						XProfileGroupHelper::get_xprofile_group_from_input( $args )->id,
						$context
					);
				},
			]
		);
	}
}
