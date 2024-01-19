<?php
/**
 * Registers BuddyPress member fields.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use GraphQL\Error\UserError;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Model\User;
use BP_User_Query;

/**
 * MemberType Class.
 */
class MemberType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'User';

	/**
	 * Register Member fields to the WPGraphQL schema.
	 */
	public static function register(): void {
		register_graphql_field(
			self::$type_name,
			'memberTypes',
			[
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Member types associated with the user.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( User $source ) {
					$types = bp_get_member_type( $source->databaseId ?? 0, false );

					return ! empty( $types ) ? $types : null;
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
						throw new UserError( esc_html__( 'The Activity component needs to be active to use this field.', 'wp-graphql-buddypress' ) );
					}

					$mention_name = bp_activity_get_user_mentionname( $source->databaseId ?? 0 );

					return ! empty( $mention_name ) ? $mention_name : null;
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
					$link = bp_members_get_user_url( $source->databaseId ?? 0 );

					return ! empty( $link ) ? $link : null;
				},
			]
		);

		register_graphql_field(
			self::$type_name,
			'latestUpdate',
			[
				'type'        => 'String',
				'description' => __( 'The content of the latest activity posted by the member.', 'wp-graphql-buddypress' ),
				'args'        => [
					'format' => [
						'type'        => 'ContentFieldFormatEnum',
						'description' => __( 'Format of the field output', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'     => function ( User $source, array $args ) {
					if ( ! bp_is_active( 'activity' ) ) {
						throw new UserError( esc_html__( 'The Activity component needs to be active to use this field.', 'wp-graphql-buddypress' ) );
					}

					// Get the member with BuddyPress extra data.
					$member_query = new BP_User_Query(
						[
							'user_ids'        => [ $source->databaseId ],
							'populate_extras' => true,
						]
					);

					$member = reset( $member_query->results );

					if ( empty( $member->latest_update ) ) {
						return null;
					}

					$activity_data = maybe_unserialize( $member->latest_update );

					if ( empty( $activity_data['content'] ) ) {
						return null;
					}

					if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
						return $activity_data['content'];
					}

					return apply_filters( 'bp_get_activity_content', $activity_data['content'] );
				},
			]
		);

		register_graphql_field(
			self::$type_name,
			'totalFriendCount',
			[
				'type'        => 'Int',
				'description' => __( 'Total number of friends for the member.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( User $source ) {

					if ( ! bp_is_active( 'friends' ) ) {
						throw new UserError( esc_html__( 'The Friends component needs to be active to use this field.', 'wp-graphql-buddypress' ) );
					}

					// Get the member with BuddyPress extra data.
					$member_query = new BP_User_Query(
						[
							'user_ids'        => [ $source->databaseId ],
							'populate_extras' => true,
						]
					);

					$member = reset( $member_query->results );

					if ( empty( $member->total_friend_count ) ) {
						return null;
					}

					return absint( $member->total_friend_count );
				},
			]
		);

		register_graphql_field(
			self::$type_name,
			'totalMessagesUnreadCount',
			[
				'type'        => 'Int',
				'description' => __( 'Total number of unread messages for the member.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( User $source ) {

					if ( ! bp_is_active( 'messages' ) ) {
						throw new UserError( esc_html__( 'The Messages component needs to be active to use this field.', 'wp-graphql-buddypress' ) );
					}

					if ( empty( $source->databaseId ) ) {
						return null;
					}

					return absint( messages_get_unread_count( $source->databaseId ) );
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
					$bp = buddypress();

					// Bail early, if disabled.
					if ( isset( $bp->avatar->show_avatars ) && false === $bp->avatar->show_avatars ) {
						return null;
					}

					return Factory::resolve_attachment( $source->databaseId ?? 0 );
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

					// Bail early, if disabled.
					if ( false === bp_is_active( 'members', 'cover_image' ) ) {
						return null;
					}

					return Factory::resolve_attachment_cover( $source->databaseId ?? 0 );
				},
			]
		);
	}
}
