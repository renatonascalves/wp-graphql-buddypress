<?php
/**
 * Registers BuddyPress types to the schema.
 *
 * @package WPGraphQL\Extensions\BuddyPress
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress;

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
use WPGraphQL\Extensions\BuddyPress\Mutation\AttachmentAvatarDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\AttachmentAvatarUpload;
use WPGraphQL\Extensions\BuddyPress\Mutation\AttachmentCoverDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\AttachmentCoverUpload;
use WPGraphQL\Extensions\BuddyPress\Mutation\FriendshipCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\FriendshipDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\FriendshipUpdate;
use WPGraphQL\Extensions\BuddyPress\Mutation\GroupCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\GroupDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\GroupUpdate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfileFieldCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfileFieldDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfileFieldUpdate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfileGroupCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfileGroupDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfileGroupUpdate;
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
		add_filter( 'graphql_data_loaders', [ __CLASS__, 'graphql_register_autoloaders' ], 10, 2 );
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
