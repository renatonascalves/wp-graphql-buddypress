<?php
/**
 * General Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

/**
 * GeneralEnums Class.
 */
class GeneralEnums {

	/**
	 * Registers enum types.
	 */
	public static function register(): void {

		// Register id Type Enums.
		$id_types = [
			'Activity',
			'Blog',
			'Friendship',
			'GroupInvitation',
			'Notification',
			'Signup',
			'Thread',
			'XProfileField',
			'XProfileGroup',
		];

		foreach ( $id_types as $type ) {
			register_graphql_enum_type(
				$type . 'IdTypeEnum',
				[
					'description' => __( 'The Type of the identifier used to fetch a single resource. Default is ID.', 'wp-graphql-buddypress' ),
					'values'      => [
						'ID'          => [
							'name'        => 'ID',
							'description' => __( 'The globally unique ID', 'wp-graphql-buddypress' ),
							'value'       => 'id',
						],
						'DATABASE_ID' => [
							'name'        => 'DATABASE_ID',
							'description' => __( 'The Database ID for the node', 'wp-graphql-buddypress' ),
							'value'       => 'database_id',
						],
					],
				]
			);
		}

		register_graphql_enum_type(
			'GroupIdTypeEnum',
			[
				'description' => __( 'The Type of the identifier used to fetch a single resource. Default is ID.', 'wp-graphql-buddypress' ),
				'values'      => [
					'ID'            => [
						'name'        => 'ID',
						'value'       => 'id',
						'description' => __( 'The globally unique ID', 'wp-graphql-buddypress' ),
					],
					'DATABASE_ID'   => [
						'name'        => 'DATABASE_ID',
						'value'       => 'database_id',
						'description' => __( 'The Database ID for the node', 'wp-graphql-buddypress' ),
					],
					'SLUG'          => [
						'name'        => 'SLUG',
						'value'       => 'slug',
						'description' => __( 'The current slug for the node', 'wp-graphql-buddypress' ),
					],
					'PREVIOUS_SLUG' => [
						'name'        => 'PREVIOUS_SLUG',
						'value'       => 'previous_slug',
						'description' => __( 'The previous slug for the node', 'wp-graphql-buddypress' ),
					],
				],
			]
		);

		// Content Field Format.
		register_graphql_enum_type(
			'ContentFieldFormatEnum',
			[
				'description' => __( 'The format of content field.', 'wp-graphql-buddypress' ),
				'values'      => [
					'RAW'      => [
						'name'        => 'RAW',
						'description' => __( 'Provide the field value directly from database.', 'wp-graphql-buddypress' ),
						'value'       => 'raw',
					],
					'RENDERED' => [
						'name'        => 'RENDERED',
						'description' => __( 'Apply the default WordPress rendering.', 'wp-graphql-buddypress' ),
						'value'       => 'rendered',
					],
				],
			]
		);

		// Invitaton Type Enum.
		register_graphql_enum_type(
			'InvitationTypeEnum',
			[
				'description' => __( 'The type of the invitation.', 'wp-graphql-buddypress' ),
				'values'      => [
					'INVITE'  => [
						'name'        => 'INVITE',
						'description' => __( 'The invite type.', 'wp-graphql-buddypress' ),
						'value'       => 'invite',
					],
					'REQUEST' => [
						'name'        => 'REQUEST',
						'description' => __( 'The request type.', 'wp-graphql-buddypress' ),
						'value'       => 'request',
					],
				],
			]
		);
	}

	/**
	 * ID Type args.
	 *
	 * @param string $type_name Type name.
	 * @return array
	 */
	public static function id_type_args( string $type_name ): array {
		$values = [
			'id'     => [
				'type'        => [
					'non_null' => 'ID',
				],
				'description' => __( 'The globally unique identifier of the object.', 'wp-graphql-buddypress' ),
			],
			'idType' => [
				'type'        => "{$type_name}IdTypeEnum",
				'description' => __( 'Type of unique identifier to fetch by. Default is Global ID', 'wp-graphql-buddypress' ),
			],
		];

		if ( 'GroupInvitation' === $type_name ) {
			$values['type'] = [
				'type'        => [ 'non_null' => 'InvitationTypeEnum' ],
				'description' => __( 'The type of the invitation.', 'wp-graphql-buddypress' ),
			];
		}

		return $values;
	}
}
