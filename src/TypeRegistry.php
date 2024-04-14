<?php
/**
 * Registers BuddyPress types to the schema.
 *
 * @package WPGraphQL\Extensions\BuddyPress
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress;

use WP_User;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;
use WPGraphQL\Extensions\BuddyPress\Model\Group;
use WPGraphQL\Extensions\BuddyPress\Model\Thread;
use WPGraphQL\Extensions\BuddyPress\Model\Activity;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\BlogEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GroupEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\MemberEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\SignupEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\ThreadEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GeneralEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\ActivityEnums;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\BlogType;
use WPGraphQL\Extensions\BuddyPress\Connection\BlogConnection;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\AttachmentEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\FriendshipEnums;
use WPGraphQL\Extensions\BuddyPress\Connection\GroupConnection;
use WPGraphQL\Extensions\BuddyPress\Mutation\Group\GroupCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Group\GroupDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Group\GroupUpdate;
use WPGraphQL\Extensions\BuddyPress\Type\Input\AttachmentInput;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\MemberType;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\SignupType;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\ThreadType;
use WPGraphQL\Extensions\BuddyPress\Connection\MemberConnection;
use WPGraphQL\Extensions\BuddyPress\Connection\SignupConnection;
use WPGraphQL\Extensions\BuddyPress\Connection\ThreadConnection;
use WPGraphQL\Extensions\BuddyPress\Mutation\Thread\StarMessage;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GroupMembersEnums;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\NotificationEnums;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\MessageType;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\BlogObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Mutation\Signup\SignupCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Signup\SignupDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Thread\ThreadCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Thread\ThreadDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Thread\ThreadUpdate;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\XProfileFieldEnums;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\ActivityType;
use WPGraphQL\Extensions\BuddyPress\Connection\ActivityConnection;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\GroupObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Type\InterfaceType\Invitation;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\SignupObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\ThreadObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Mutation\Signup\SignupActivate;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\AttachmentType;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\FriendshipType;
use WPGraphQL\Extensions\BuddyPress\Connection\FriendshipConnection;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\MessageObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\GroupObjectType;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\ActivityObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Mutation\Activity\ActivityCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Activity\ActivityDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Activity\ActivityUpdate;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\NotificationType;
use WPGraphQL\Extensions\BuddyPress\Connection\NotificationConnection;
use WPGraphQL\Extensions\BuddyPress\Mutation\Invites\InvitationAccept;
use WPGraphQL\Extensions\BuddyPress\Mutation\Invites\InvitationCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Invites\InvitationReject;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\XProfileFieldType;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\XProfileGroupType;
use WPGraphQL\Extensions\BuddyPress\Connection\XProfileFieldConnection;
use WPGraphQL\Extensions\BuddyPress\Connection\XProfileGroupConnection;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\FriendshipObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\InvitationObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Mutation\Activity\ActivityFavorite;
use WPGraphQL\Extensions\BuddyPress\Type\Union\NotificationObjectUnion;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\InvitationGroupType;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\NotificationObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Mutation\Friendship\FriendshipCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Friendship\FriendshipDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Friendship\FriendshipUpdate;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\XProfileFieldObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Data\Loader\XProfileGroupObjectLoader;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileFieldCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileFieldDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileFieldUpdate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileGroupCreate;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileGroupDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\XProfile\XProfileGroupUpdate;
use WPGraphQL\Extensions\BuddyPress\Type\ObjectType\XProfileFieldValueType;
use WPGraphQL\Extensions\BuddyPress\Mutation\Notification\NotificationDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Notification\NotificationUpdate;
use WPGraphQL\Extensions\BuddyPress\Mutation\Attachment\AttachmentCoverDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Attachment\AttachmentCoverUpload;
use WPGraphQL\Extensions\BuddyPress\Mutation\Attachment\AttachmentAvatarDelete;
use WPGraphQL\Extensions\BuddyPress\Mutation\Attachment\AttachmentAvatarUpload;
use WPGraphQL\Extensions\BuddyPress\Mutation\Signup\SignupResendEmail;

/**
 * Class TypeRegistry
 */
class TypeRegistry {

	/**
	 * Registers actions related to the type registry.
	 */
	public static function add_actions(): void {
		add_action( 'graphql_register_types', [ __CLASS__, 'graphql_register_types' ], 9999 );
	}

	/**
	 * Registers filters related to the type registry.
	 */
	public static function add_filters(): void {

		// Register custom autoloaders.
		add_filter( 'graphql_data_loaders', [ __CLASS__, 'graphql_register_autoloaders' ], 10, 2 );

		// Add our custom types to the list of supported node types.
		add_filter(
			'graphql_interface_resolve_type',
			static function ( $type, $node, $interface_instance ) {

				if ( bp_is_active( 'groups' ) && $node instanceof Group ) {
					return $interface_instance->type_registry->get_type( 'Group' );
				}

				if ( bp_is_active( 'blogs' ) && $node instanceof Blog ) {
					return $interface_instance->type_registry->get_type( 'Blog' );
				}

				if ( bp_is_active( 'messages' ) && $node instanceof Thread ) {
					return $interface_instance->type_registry->get_type( 'Thread' );
				}

				if ( bp_is_active( 'activity' ) && $node instanceof Activity ) {
					return $interface_instance->type_registry->get_type( 'Activity' );
				}

				return $type;
			},
			10,
			3
		);

		// Resolve URI.
		add_filter(
			'graphql_pre_resolve_uri',
			static function ( $node, $uri, $context ) {

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

				if (
					bp_is_active( 'messages' )
					&& ! empty( $array[3] )
					&& 'messages' === $array[3]
					&& ! empty( $array[5] )
					&& is_numeric( $array[5] )
				) {
					return $context->get_loader( 'bp_thread' )->load( absint( $array[5] ) );
				}

				if ( empty( $slug ) ) {
					return $node;
				}

				if (
					(
						bp_is_active( 'activity' )
						&& (
							! empty( $array[1] )
							&& 'activity' === $array[1]
							&& ! empty( $array[3] )
							&& is_numeric( $array[3] )
						)
					)
					||
					(
						! empty( $array[3] )
						&& 'activity' === $array[3]
						&& ! empty( $array[4] )
						&& is_numeric( $array[4] )
						&& empty( $array[5] )
					)
				) {
					$activity_id = isset( $array[5] ) ? $array[4] : $array[3];

					return $context->get_loader( 'bp_activity' )->load( absint( $activity_id ) );
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
						return $context->get_loader( 'user' )->load( absint( $user->ID ) );
					}
				}

				return $node;
			},
			10,
			3
		);

		// Register the Group Type taxonomy object.
		add_filter(
			'register_taxonomy_args',
			static function ( $args, $taxonomy ): array {

				if ( false === bp_is_active( 'groups' ) || bp_get_group_type_tax_name() !== $taxonomy ) {
					return $args;
				}

				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'GroupTypeTerm';
				$args['graphql_plural_name'] = 'GroupTypeTerms';

				return $args;
			},
			10,
			2
		);

		/**
		 * Change the visibility of the user to `restricted`.
		 *
		 * BuddyPress users are "open" by default.
		 */
		add_filter(
			'graphql_object_visibility',
			static function ( $visibility, $model_name ): string {
				if ( 'UserObject' === $model_name ) {

					// Check if the user can view the members component.
					if ( ! bp_current_user_can( 'bp_view', [ 'bp_component' => 'members' ] ) ) {
						return 'private';
					}

					if ( 'private' === $visibility ) {
						return 'restricted';
					}
				}

				return $visibility;
			},
			10,
			2
		);
	}

	/**
	 * Registers BuddyPress types, connections, and mutations to GraphQL schema.
	 *
	 * @param \WPGraphQL\Registry\TypeRegistry $type_registry The Type Registry.
	 */
	public static function graphql_register_types( $type_registry ): void {

		// General Enum(s).
		GeneralEnums::register();

		// Register Interfaces.
		Invitation::register_type();

		if ( bp_is_active( 'notifications' ) ) {

			// Enum(s).
			NotificationEnums::register();

			// Fields.
			NotificationType::register();

			// Connections.
			NotificationConnection::register_connections();

			// Mutations.
			NotificationDelete::register_mutation();
			NotificationUpdate::register_mutation();
		}

		// Members component.
		if ( bp_is_active( 'members' ) ) {

			// Enum(s).
			MemberEnums::register();

			// Fields.
			MemberType::register();

			// Connections.
			MemberConnection::register_connections();

			// Signup.
			if ( bp_get_signup_allowed() ) {

				// Enum(s).
				SignupEnums::register();

				// Fields.
				SignupType::register();

				// Connections.
				SignupConnection::register_connections();

				// Mutations.
				SignupDelete::register_mutation();
				SignupActivate::register_mutation();
				SignupCreate::register_mutation();
				SignupResendEmail::register_mutation();
			}
		}

		// Acvitity component.
		if ( bp_is_active( 'activity' ) ) {

			// Enum(s).
			ActivityEnums::register();

			// Fields.
			ActivityType::register();

			// Connections.
			ActivityConnection::register_connections();

			// Mutations.
			ActivityFavorite::register_mutation();
			ActivityCreate::register_mutation();
			ActivityDelete::register_mutation();
			ActivityUpdate::register_mutation();
		}

		// Groups component.
		if ( bp_is_active( 'groups' ) ) {

			// Enum(s).
			GroupEnums::register();
			GroupMembersEnums::register();

			// Object(s).
			GroupObjectType::register();
			InvitationGroupType::register();

			// Connections.
			GroupConnection::register_connections();

			// Mutations.
			GroupCreate::register_mutation();
			GroupDelete::register_mutation();
			GroupUpdate::register_mutation();

			// Invitation related.
			InvitationReject::register_mutation();
			InvitationAccept::register_mutation();
			InvitationCreate::register_mutation();
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

		// Thread/Messages component.
		if ( bp_is_active( 'messages' ) ) {

			// Enum(s).
			ThreadEnums::register();

			// Object(s).
			ThreadType::register();
			MessageType::register();

			// Connections.
			ThreadConnection::register_connections();

			// Mutations.
			StarMessage::register_mutation();
			ThreadDelete::register_mutation();
			ThreadUpdate::register_mutation();
			ThreadCreate::register_mutation();
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

		// Unions.
		NotificationObjectUnion::register( $type_registry );
	}

	/**
	 * Registers custom autoloaders.
	 *
	 * @param array                 $loaders Autoloaders.
	 * @param \WPGraphQL\AppContext $context Context.
	 * @return array
	 */
	public static function graphql_register_autoloaders( array $loaders, AppContext $context ): array {
		return array_merge(
			$loaders,
			[
				'bp_activity'       => new ActivityObjectLoader( $context ),
				'bp_blog'           => new BlogObjectLoader( $context ),
				'bp_friend'         => new FriendshipObjectLoader( $context ),
				'bp_group'          => new GroupObjectLoader( $context ),
				'bp_invitation'     => new InvitationObjectLoader( $context ),
				'bp_message'        => new MessageObjectLoader( $context ),
				'bp_notification'   => new NotificationObjectLoader( $context ),
				'bp_signup'         => new SignupObjectLoader( $context ),
				'bp_thread'         => new ThreadObjectLoader( $context ),
				'bp_xprofile_field' => new XProfileFieldObjectLoader( $context ),
				'bp_xprofile_group' => new XProfileGroupObjectLoader( $context ),
			]
		);
	}
}
