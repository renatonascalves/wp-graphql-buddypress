<?php
/**
 * Registers BuddyPress types to the schema.
 *
 * @package WPGraphQL\Extensions\BuddyPress
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress;

use WP_User;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Connection\BlogConnection;
use WPGraphQL\Extensions\BuddyPress\Connection\FriendshipConnection;
use WPGraphQL\Extensions\BuddyPress\Connection\GroupConnection;
use WPGraphQL\Extensions\BuddyPress\Connection\MemberConnection;
use WPGraphQL\Extensions\BuddyPress\Connection\XProfileFieldConnection;
use WPGraphQL\Extensions\BuddyPress\Connection\XProfileGroupConnection;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\BlogObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\FriendshipObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\GroupObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\XProfileFieldObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\XProfileGroupObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;
use WPGraphQL\Extensions\BuddyPress\Model\Group;
use WPGraphQL\Extensions\BuddyPress\Mutation\Attachment\AttachmentAvatarDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Attachment\AttachmentAvatarUpload;
use WPGraphQL\Extensions\BuddyPress\Mutation\Attachment\AttachmentCoverDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Attachment\AttachmentCoverUpload;
use WPGraphQL\Extensions\BuddyPress\Mutation\Friendship\FriendshipCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Friendship\FriendshipDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Friendship\FriendshipUpdate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Group\GroupCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Group\GroupDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Group\GroupUpdate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileFieldCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileFieldDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileFieldUpdate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileGroupCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileGroupDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileGroupUpdate;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\AttachmentEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\BlogEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\FriendshipEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GeneralEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GroupEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GroupMembersEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\MemberEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\XProfileFieldEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Input\AttachmentInput;
use WPGraphQL\Extensions\BuddyPress\Type\Object\AttachmentType;
use WPGraphQL\Extensions\BuddyPress\Type\Object\BlogType;
use WPGraphQL\Extensions\BuddyPress\Type\Object\FriendshipType;
use WPGraphQL\Extensions\BuddyPress\Type\Object\GroupType;
use WPGraphQL\Extensions\BuddyPress\Type\Object\MemberType;
use WPGraphQL\Extensions\BuddyPress\Type\Object\XProfileFieldType;
use WPGraphQL\Extensions\BuddyPress\Type\Object\XProfileFieldValueType;
use WPGraphQL\Extensions\BuddyPress\Type\Object\XProfileGroupType;

/**
 * Class TypeRegistry
 */
class TypeRegistry {

	/**
	 * Registers actions related to the type registry.
	 */
	public static function add_actions() {
		add_action( 'graphql_register_types', [ __CLASS__, 'graphql_register_types' ], 99 );
	}

	/**
	 * Registers filters related to the type registry.
	 */
	public static function add_filters() {

		// Register custom autoloaders.
		add_filter( 'graphql_data_loaders', [ __CLASS__, 'graphql_register_autoloaders' ], 10, 2 );

		// Add our custom types to the list of supported node types.
		/* add_filter(
			'graphql_interface_resolve_type',
			function ( $type, $node, $interface_instance ) {

				if ( bp_is_active( 'groups' ) && $node instanceof Group ) {
					return $interface_instance->type_registry->get_type( 'Group' );
				}

				if ( bp_is_active( 'blogs' ) && $node instanceof Blog ) {
					return $interface_instance->type_registry->get_type( 'Blog' );
				}

				return $type;
			},
			10,
			3
		); */

		// Resolve URI.
		add_filter(
			'graphql_pre_resolve_uri',
			function ( $node, $uri, $context ) {

				// Parse URI.
				$parsed_url = wp_parse_url( $uri );

				if ( bp_is_active( 'blogs' ) && ( empty( $parsed_url['path'] ) || '/' === $parsed_url['path'] ) ) {
					$blogs = bp_blogs_get_blogs();

					foreach ( $blogs['blogs'] ?? [] as $blog ) {

						if ( empty( $blog->domain ) ) {
							continue;
						}

						if ( $parsed_url['host'] !== $blog->domain ) {
							continue;
						}

						if ( ! empty( $blog->blog_id ) ) {
							return $context->get_loader( 'bp_blog' )->load( $blog->blog_id );
						}
					}
				}

				$array = explode( '/', $parsed_url['path'] );
				$slug  = $array[2] ?? '';

				if ( empty( $slug ) ) {
					return $node;
				}

				if ( bp_is_active( 'groups' ) ) {
					$group_id = groups_get_id( $slug );

					if ( is_numeric( $group_id ) && ! empty( $group_id ) ) {
						return $context->get_loader( 'bp_group' )->load( $group_id );
					}
				}

				if ( bp_is_active( 'members' ) ) {
					$user = get_user_by( 'slug', $slug );

					if ( $user instanceof WP_User && ! empty( $user->ID ) ) {
						return $context->get_loader( 'user' )->load( $user->ID );
					}
				}

				return $node;
			},
			10,
			3
		);

		/**
		 * Change the visibility of the user to `restricted`.
		 *
		 * BuddyPress users are "open" by default.
		 */
		add_filter(
			'graphql_object_visibility',
			function ( $visibility, $model_name ) {
				if ( 'UserObject' === $model_name && 'private' === $visibility ) {
					return 'restricted';
				}

				return $visibility;
			},
			10,
			2
		);
	}

	/**
	 * Registers BuddyPress types, connections, and mutations to GraphQL schema.
	 */
	public static function graphql_register_types() {

		// General Enum(s).
		GeneralEnums::register();

		// Members component.
		if ( bp_is_active( 'members' ) ) {

			// Enum(s).
			MemberEnums::register();

			// Fields.
			MemberType::register();

			// Connections.
			MemberConnection::register_connections();
		}

		// Groups component.
		if ( bp_is_active( 'groups' ) ) {

			// Enum(s).
			GroupEnums::register();
			GroupMembersEnums::register();

			// Object(s).
			GroupType::register();

			// Connections.
			GroupConnection::register_connections();

			// Mutations.
			GroupCreate::register_mutation();
			GroupDelete::register_mutation();
			GroupUpdate::register_mutation();
		}

		// XProfile component.
		if ( bp_is_active( 'xprofile' ) ) {

			// Enum(s).
			XProfileFieldEnums::register();

			// Object(s).
			XProfileGroupType::register();
			XProfileFieldType::register();
			XProfileFieldValueType::register();

			// Connections.
			XProfileGroupConnection::register_connections();
			XProfileFieldConnection::register_connections();

			// XProfile Group Mutations.
			XProfileGroupCreate::register_mutation();
			XProfileGroupDelete::register_mutation();
			XProfileGroupUpdate::register_mutation();

			// XProfile Field Mutations.
			XProfileFieldCreate::register_mutation();
			XProfileFieldDelete::register_mutation();
			XProfileFieldUpdate::register_mutation();
		}

		// Blog component.
		if ( bp_is_active( 'blogs' ) ) {

			// Enum(s).
			BlogEnums::register();

			// Object(s).
			BlogType::register();

			// Connections.
			BlogConnection::register_connections();
		}

		// Friends component.
		if ( bp_is_active( 'friends' ) ) {

			// Enum(s).
			FriendshipEnums::register();

			// Object(s).
			FriendshipType::register();

			// Connections.
			FriendshipConnection::register_connections();

			// Mutations.
			FriendshipDelete::register_mutation();
			FriendshipUpdate::register_mutation();
			FriendshipCreate::register_mutation();
		}

		// Attachment Type/Object.
		AttachmentType::register();

		// Attachment Input, aka, Upload input type.
		// @todo will be deprecated soon.
		AttachmentInput::register();

		// Attachment Enum(s).
		AttachmentEnums::register();

		// Attachment Avatar Mutations.
		AttachmentAvatarUpload::register_mutation();
		AttachmentAvatarDelete::register_mutation();

		// Attachment Cover Mutations.
		if ( bp_is_active( 'members', 'cover_image' ) || bp_is_active( 'groups', 'cover_image' ) ) {
			AttachmentCoverUpload::register_mutation();
			AttachmentCoverDelete::register_mutation();
		}
	}

	/**
	 * Registers custom autoloaders.
	 *
	 * @param array      $loaders Autoloaders.
	 * @param AppContext $context Context.
	 * @return array
	 */
	public static function graphql_register_autoloaders( array $loaders, AppContext $context ): array {
		return array_merge(
			$loaders,
			[
				'bp_group'          => new GroupObjectLoader( $context ),
				'bp_xprofile_group' => new XProfileGroupObjectLoader( $context ),
				'bp_xprofile_field' => new XProfileFieldObjectLoader( $context ),
				'bp_friend'         => new FriendshipObjectLoader( $context ),
				'bp_blog'           => new BlogObjectLoader( $context ),
			]
		);
	}
}
