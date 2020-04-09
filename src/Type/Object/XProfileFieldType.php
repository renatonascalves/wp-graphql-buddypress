<?php
/**
 * Registers BuddyPress XProfile Field and fields.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Object
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Object;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;

/**
 * XProfileFieldType Class.
 */
class XProfileFieldType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'XProfileField';

	/**
	 * Register XProfile Field and fields to the WPGraphQL schema.
	 */
	public static function register() {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress XProfile field.', 'wp-graphql-buddypress' ),
				'fields'            => [
					'id'               => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The globally unique identifier for the XProfile field.', 'wp-graphql-buddypress' ),
					],
					'fieldId'          => [
						'type'        => 'Int',
						'description' => __( 'The id field that matches the BP_XProfile_Field->id field.', 'wp-graphql-buddypress' ),
					],
					'groupId'          => [
						'type'        => 'Int',
						'description' => __( 'The id field that matches the BP_XProfile_Field->group_id field.', 'wp-graphql-buddypress' ),
					],
					'parent'           => [
						'type'        => self::$type_name,
						'description' => __( 'Parent field of the current field. This field is equivalent to the BP_XProfile_Field object matching the BP_XProfile_Field->parent_id ID.', 'wp-graphql-buddypress' ),
						'resolve'     => function( XProfileField $field, array $args, AppContext $context ) {
							return ! empty( $field->parent )
								? Factory::resolve_xprofile_field_object( $field->parent, $context )
								: null;
						},
					],
					'name'             => [
						'type'        => 'String',
						'description' => __( 'XProfile field name.', 'wp-graphql-buddypress' ),
					],
					'type'             => [
						'type'        => 'XProfileFieldTypesEnum',
						'description' => __( 'XProfile field type.', 'wp-graphql-buddypress' ),
					],
					'canDelete'             => [
						'type'        => 'Boolean',
						'description' => __( 'Can this field be deleted?', 'wp-graphql-buddypress' ),
					],
					'isRequired'             => [
						'type'        => 'Boolean',
						'description' => __( 'Is it a required field?', 'wp-graphql-buddypress' ),
					],
					'fieldOrder'             => [
						'type'        => 'Int',
						'description' => __( 'The order of the profile field into the group of fields.', 'wp-graphql-buddypress' ),
					],
					'optionOrder'             => [
						'type'        => 'Int',
						'description' => __( 'The order of the option into the profile field list of options.', 'wp-graphql-buddypress' ),
					],
					'orderBy'             => [
						'type'        => 'OrderEnum',
						'description' => __( 'The way profile field\'s options are ordered.', 'wp-graphql-buddypress' ),
					],
					'isDefaultOption'             => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the option is the default one for the profile field.', 'wp-graphql-buddypress' ),
					],
					'visibilityLevel'             => [
						'type'        => 'XProfileFieldVisibilityLevelEnum',
						'description' => __( 'Who may see the saved value for this profile field.', 'wp-graphql-buddypress' ),
					],
					'doAutolink'             => [
						'type'        => 'Boolean',
						'description' => __( 'Is autolink enabled for this profile field.', 'wp-graphql-buddypress' ),
					],
					'memberTypes'             => [
						'type'        => [
							'list_of' => 'MemberTypesEnum',
						],
						'description' => __( 'Member types to which this field should be available.', 'wp-graphql-buddypress' ),
					],
					'description'      => [
						'type'        => 'String',
						'description' => __( 'The description of the XProfile field.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function( XProfileField $field, array $args ) {
							if ( empty( $field->description ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $field->description;
							}

							return apply_filters( 'bp_get_the_profile_field_description', stripslashes( $field->description ) );
						},
					],
					'value'      => [
						'type'        => 'String',
						'description' => __( 'The value of the XProfile field.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'XProfileFieldValueFormatEnum',
								'description' => __( 'Format of the field value output', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function( XProfileField $field, array $args ) {
							if ( empty( $field->value ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $field->value;
							}

							/**
							 * This is not working correctly, mainly because the type of the field is a String.
							 *
							 * @todo Fix serialized output.
							 */
							if ( isset( $args['format'] ) && 'unserialized' === $args['format'] ) {
								return self::get_profile_field_unserialized_value( $field->value );
							}

							return self::get_profile_field_rendered_value( $field->value, $field->fieldId );
						},
					],
				],
				'resolve_node'      => function( $node, $id, $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_xprofile_field_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function( $type, $node ) {
					if ( $node instanceof XProfileField ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'xprofileFieldBy',
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress XProfile Field object', 'wp-graphql-buddypress' ),
				'args'        => [
					'id'           => [
						'type'        => 'ID',
						'description' => __( 'Get the object by its global ID.', 'wp-graphql-buddypress' ),
					],
					'fieldId'      => [
						'type'        => 'Int',
						'description' => __( 'Get the object by its database ID', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$xprofile_field_id = 0;

					if ( ! empty( $args['id'] ) ) {
						$id_components = Relay::fromGlobalId( $args['id'] );

						if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
							throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
						}

						$xprofile_field_id = absint( $id_components['id'] );
					} elseif ( ! empty( $args['fieldId'] ) ) {
						$xprofile_field_id = absint( $args['fieldId'] );
					}

					return Factory::resolve_xprofile_field_object( $xprofile_field_id, $context );
				},
			]
		);
	}

	/**
	 * Retrieve the unserialized value of a profile field.
	 *
	 * @param string $value The raw value of the field.
	 * @return array
	 */
	protected static function get_profile_field_unserialized_value( $value = '' ): array {
		if ( empty( $value ) ) {
			return [];
		}

		$unserialized_value = maybe_unserialize( $value );
		if ( ! is_array( $unserialized_value ) ) {
			$unserialized_value = (array) $unserialized_value;
		}

		return $unserialized_value;
	}

	/**
	 * Retrieve the rendered value of a profile field.
	 *
	 * @param string   $value         The raw value of the field.
	 * @param int|null $profile_field The ID of the object for the field.
	 * @return string
	 */
	protected static function get_profile_field_rendered_value( $value = '', $profile_field = null ): string {
		if ( ! $value ) {
			return '';
		}

		$profile_field = xprofile_get_field( $profile_field );

		if ( ! isset( $profile_field->id ) ) {
			return '';
		}

		// Unserialize the BuddyPress way.
		$value = bp_unserialize_profile_field( $value );

		global $field;
		$reset_global = $field;

		// Set the $field global as the `xprofile_filter_link_profile_data` filter needs it.
		$field = $profile_field;

		/**
		 * Apply filters to sanitize XProfile field value.
		 *
		 * @param string $value Value for the profile field.
		 * @param string $type  Type for the profile field.
		 * @param int    $id    ID for the profile field.
		 */
		$value = apply_filters( 'bp_get_the_profile_field_value', $value, $field->type, $field->id );

		// Reset the global before returning the value.
		$field = $reset_global;

		return $value;
	}
}
