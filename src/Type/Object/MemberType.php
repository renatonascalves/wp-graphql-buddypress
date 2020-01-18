<?php
/**
 * Registers BuddyPress member fields.
 *
 * @package \WPGraphQL\Extensions\BuddyPress\Type\Object
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Object;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Model\User;

/**
 * MemberType Class.
 */
class MemberType implements RegisterInterface {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'User';

	/**
	 * Register Member fields to the WPGraphQL schema.
	 */
	public static function register() {
		register_graphql_field(
			self::$type_name,
			'memberTypes',
			[
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Member types associated with the user.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( User $source ) {
					$types = bp_get_member_type( $source->userId ?? 0, false );

					if ( empty( $types ) ) {
						return null;
					}

					return $types;
				},
			]
		);

		register_graphql_field(
			self::$type_name,
			'mentionName',
			[
				'type'        => 'String',
				'description' => __( 'The name used for the user in @-mentions.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( User $source ) {
					if ( ! bp_is_active( 'activity' ) ) {
						throw new UserError( __( 'The Activity component needs to be active to use this field.', 'wp-graphql-buddypress' ) );
					}

					$mention_name = bp_activity_get_user_mentionname( $source->userId ?? 0 );

					if ( empty( $mention_name ) ) {
						return null;
					}

					return $mention_name;
				},
			]
		);

		register_graphql_field(
			self::$type_name,
			'link',
			[
				'type'        => 'String',
				'description' => __( 'Profile URL of the member.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( User $source ) {
					$link = bp_core_get_user_domain( $source->userId ?? 0 );

					return $link ?? null;
				},
			]
		);

		register_graphql_field(
			self::$type_name,
			'attachmentAvatar',
			[
				'type'        => 'Attachment',
				'description' => __( 'Attachment Avatar of the member.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( User $source ) {
					return Factory::resolve_attachment( $source->userId ?? 0 );
				},
			]
		);

		register_graphql_field(
			self::$type_name,
			'attachmentCover',
			[
				'type'        => 'Attachment',
				'description' => __( 'Attachment Cover of the member.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( User $source ) {
					return Factory::resolve_attachment_cover( $source->userId ?? 0 );
				},
			]
		);
	}
}
