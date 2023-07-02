<?php
/**
 * Friendship Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

/**
 * FriendshipEnums Class
 */
class FriendshipEnums {

	/**
	 * Register enum type.
	 */
	public static function register(): void {

		register_graphql_enum_type(
			'FriendshipOrderByEnums',
			[
				'description' => __( 'The attribute to order friendships by.', 'wp-graphql-buddypress' ),
				'values'      => [
					'ID'                => [
						'name'        => 'ID',
						'description' => __( 'Sort friendships by the id column.', 'wp-graphql-buddypress' ),
						'value'       => 'id',
					],
					'FRIEND_USER_ID'    => [
						'name'        => 'FRIEND_USER_ID',
						'description' => __( 'Sort friendships by the friend_user_id column.', 'wp-graphql-buddypress' ),
						'value'       => 'friend_user_id',
					],
					'INITIATOR_USER_ID' => [
						'name'        => 'INITIATOR_USER_ID',
						'description' => __( 'Sort friendships by the initiator_user_id column.', 'wp-graphql-buddypress' ),
						'value'       => 'initiator_user_id',
					],
					'DATE_CREATED'      => [
						'name'        => 'DATE_CREATED',
						'description' => __( 'Sort friendships by the date_created column.', 'wp-graphql-buddypress' ),
						'value'       => 'date_created',
					],
				],
			]
		);
	}
}
