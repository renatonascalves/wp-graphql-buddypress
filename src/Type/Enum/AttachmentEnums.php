<?php
/**
 * AttachmentEnums Enums.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Enum
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Enum;

/**
 * AttachmentEnums Class
 */
class AttachmentEnums {

	/**
	 * Registers enum type.
	 */
	public static function register(): void {

		// Attachment Avatar Enum.
		register_graphql_enum_type(
			'AttachmentAvatarEnum',
			[
				'description' => __( 'Avatar Attachment objects.', 'wp-graphql-buddypress' ),
				'values'      => [
					'USER'  => [
						'name'        => 'USER',
						'description' => __( 'Avatar Attachment for the user.', 'wp-graphql-buddypress' ),
						'value'       => 'user',
					],
					'GROUP' => [
						'name'        => 'GROUP',
						'description' => __( 'Avatar Attachment for the group.', 'wp-graphql-buddypress' ),
						'value'       => 'group',
					],
					'BLOG'  => [
						'name'        => 'BLOG',
						'description' => __( 'Avatar Attachment for the blog.', 'wp-graphql-buddypress' ),
						'value'       => 'blog',
					],
				],
			]
		);

		// Attachment Cover Enum.
		register_graphql_enum_type(
			'AttachmentCoverEnum',
			[
				'description' => __( 'Cover Attachment objects.', 'wp-graphql-buddypress' ),
				'values'      => [
					'MEMBERS' => [
						'name'        => 'MEMBERS',
						'description' => __( 'Cover Attachment for the members/users.', 'wp-graphql-buddypress' ),
						'value'       => 'members',
					],
					'GROUPS'  => [
						'name'        => 'GROUPS',
						'description' => __( 'Cover Attachment for the groups.', 'wp-graphql-buddypress' ),
						'value'       => 'groups',
					],
					'BLOGS'   => [
						'name'        => 'BLOGS',
						'description' => __( 'Cover Attachment for the blogs.', 'wp-graphql-buddypress' ),
						'value'       => 'blogs',
					],
				],
			]
		);
	}
}
