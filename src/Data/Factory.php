<?php
/**
 * Factory Class
 *
 * This class serves as a factory for all resolvers.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Deferred;
use GraphQLRelay\Relay;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileFieldValue;
use WPGraphQL\Extensions\BuddyPress\Model\Attachment;
use WPGraphQL\Extensions\BuddyPress\Model\Friendship;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\GroupsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\GroupMembersConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\XProfileFieldsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\XProfileGroupsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\BlogsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\FriendshipsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\XProfileFieldOptionsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\MembersConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\ThreadConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\MessagesConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\RecipientsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\NotificationConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\ActivitiesConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\ActivityCommentsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\SignupConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\GroupInvitationsConnectionResolver;
use stdClass;
use WP_User;
use BP_Friends_Friendship;

/**
 * Class Factory.
 */
class Factory {

	/**
	 * Get object id.
	 *
	 * @throws UserError User error for invalid activity.
	 *
	 * @param array|int $input Input.
	 * @return int
	 */
	public static function get_id( $input ): int {
		$object_id = 0;

		if ( is_int( $input ) ) {
			$id_type = 'integer';
		} elseif ( isset( $input['databaseId'] ) ) {
			$id_type = 'object_id';
		} elseif ( isset( $input['idType'] ) && 'slug' === $input['idType'] ) {
			$id_type = 'slug';
		} elseif ( isset( $input['idType'] ) && 'previous_slug' === $input['idType'] ) {
			$id_type = 'previous_slug';
		} else {
			$id_type = $input['idType'] ? $input['idType'] : 'global_id';
		}

		switch ( $id_type ) {
			case 'object_id':
				$object_id = $input['databaseId'];
				break;

			case 'database_id':
				$object_id = $input['id'];
				break;

			case 'slug':
				$object_id = groups_get_id( esc_html( $input['id'] ) );
				break;

			case 'previous_slug':
				$object_id = groups_get_id_by_previous_slug( esc_html( $input['id'] ) );
				break;

			case 'integer':
				$object_id = $input;
				break;

			case 'global_id':
			default:
				$id_components = Relay::fromGlobalId( $input['id'] );

				if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
					throw new UserError( esc_html__( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
				}

				$object_id = $id_components['id'];
				break;
		}

		return absint( $object_id );
	}

	/**
	 * Get the user object, if the ID is valid.
	 *
	 * @param int $user_id Supplied user ID.
	 * @return WP_User|null
	 */
	public static function get_user( int $user_id ): ?WP_User {
		if ( $user_id <= 0 ) {
			return null;
		}

		$user = get_userdata( (int) $user_id );
		if ( empty( $user ) || ! $user->exists() ) {
			return null;
		}

		return $user;
	}

	/**
	 * Returns an Notification object.
	 *
	 * @param int        $id      Notification ID or null.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_notification_object( $id, AppContext $context ): ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$notification_id = absint( $id );
		$context->get_loader( 'bp_notification' )->buffer( [ $notification_id ] );

		return new Deferred(
			function () use ( $notification_id, $context ) {
				return $context->get_loader( 'bp_notification' )->load( $notification_id );
			}
		);
	}

	/**
	 * Returns an Invitation object.
	 *
	 * @param int        $id      Invitation ID or null.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_invitation_object( $id, AppContext $context ): ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$invite_id = absint( $id );
		$context->get_loader( 'bp_invitation' )->buffer( [ $invite_id ] );

		return new Deferred(
			function () use ( $invite_id, $context ) {
				return $context->get_loader( 'bp_invitation' )->load( $invite_id );
			}
		);
	}

	/**
	 * Returns a Signup object.
	 *
	 * @param int        $id      Activity ID or null.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_signup_object( $id, AppContext $context ): ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$signup_id = absint( $id );
		$context->get_loader( 'bp_signup' )->buffer( [ $signup_id ] );

		return new Deferred(
			function () use ( $signup_id, $context ) {
				return $context->get_loader( 'bp_signup' )->load( $signup_id );
			}
		);
	}

	/**
	 * Returns an Activity object.
	 *
	 * @param int        $id      Activity ID or null.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_activity_object( $id, AppContext $context ): ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$activity_id = absint( $id );
		$context->get_loader( 'bp_activity' )->buffer( [ $activity_id ] );

		return new Deferred(
			function () use ( $activity_id, $context ) {
				return $context->get_loader( 'bp_activity' )->load( $activity_id );
			}
		);
	}

	/**
	 * Returns a Thread object.
	 *
	 * @param int        $id      Thread ID or null.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_thread_object( $id, AppContext $context ): ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$thread_id = absint( $id );
		$context->get_loader( 'bp_thread' )->buffer( [ $thread_id ] );

		return new Deferred(
			function () use ( $thread_id, $context ) {
				return $context->get_loader( 'bp_thread' )->load( $thread_id );
			}
		);
	}

	/**
	 * Returns a Message object.
	 *
	 * @param int        $id      Message ID or null.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_message_object( $id, AppContext $context ): ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$message_id = absint( $id );
		$context->get_loader( 'bp_message' )->buffer( [ $message_id ] );

		return new Deferred(
			function () use ( $message_id, $context ) {
				return $context->get_loader( 'bp_message' )->load( $message_id );
			}
		);
	}

	/**
	 * Returns a Group object.
	 *
	 * @param int        $id      Group ID or null.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_group_object( $id, AppContext $context ): ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$group_id = absint( $id );
		$context->get_loader( 'bp_group' )->buffer( [ $group_id ] );

		return new Deferred(
			function () use ( $group_id, $context ) {
				return $context->get_loader( 'bp_group' )->load( $group_id );
			}
		);
	}

	/**
	 * Returns a XProfile Group object.
	 *
	 * @param int        $id      XProfile group ID.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_xprofile_group_object( $id, AppContext $context ): ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$xprofile_group_id = absint( $id );
		$context->get_loader( 'bp_xprofile_group' )->buffer( [ $xprofile_group_id ] );

		return new Deferred(
			function () use ( $xprofile_group_id, $context ) {
				return $context->get_loader( 'bp_xprofile_group' )->load( $xprofile_group_id );
			}
		);
	}

	/**
	 * Returns a XProfile Field object.
	 *
	 * @param ?int       $id      XProfile field ID or null.
	 * @param AppContext $context AppContext object.
	 * @return XProfileField|null
	 */
	public static function resolve_xprofile_field_object( $id, AppContext $context ): ?XProfileField {
		if ( empty( $id ) ) {
			return null;
		}

		// Get the user ID if available.
		$user_id = $context->config['userId'] ?? null;

		// Get the XProfile field object.
		$xprofile_field_object = XProfileFieldHelper::get_xprofile_field_from_input( absint( $id ), $user_id );

		$field = new XProfileField( $xprofile_field_object );

		if ( empty( $field->fields ) ) {
			return null;
		}

		return $field;
	}

	/**
	 * Returns a XProfile Field Value object.
	 *
	 * @param XProfileField $xprofile_field XProfile field value or null.
	 * @return XProfileFieldValue|null
	 */
	public static function resolve_xprofile_field_data_object( XProfileField $xprofile_field ): ?XProfileFieldValue {
		if ( empty( $xprofile_field->value ) ) {
			return null;
		}

		$value = new XProfileFieldValue( $xprofile_field->value );

		if ( empty( $value->fields ) ) {
			return null;
		}

		return $value;
	}

	/**
	 * Resolve an attachment avatar for the object.
	 *
	 * @param int    $id        ID of the object.
	 * @param string $bp_object Object (user, group, blog, etc). Default: 'user'.
	 * @return Attachment|null
	 */
	public static function resolve_attachment( $id, string $bp_object = 'user' ): ?Attachment {
		if ( empty( $id ) ) {
			return null;
		}

		$attachment = new stdClass();

		foreach ( [ 'full', 'thumb' ] as $type ) {
			$args = [
				'item_id' => $id,
				'object'  => $bp_object,
				'no_grav' => true,
				'html'    => false,
				'type'    => $type,
			];

			if ( 'blog' === $bp_object ) {

				// Unset item ID and add correct item id key.
				unset( $args['item_id'] );

				$args['blog_id']   = $id;
				$attachment->$type = bp_get_blog_avatar( $args );
			} else {
				$attachment->$type = bp_core_fetch_avatar( $args );
			}
		}

		if ( empty( $attachment->full ) && empty( $attachment->thumb ) ) {
			return null;
		}

		return new Attachment( $attachment );
	}

	/**
	 * Resolve an attachment cover for a object (user, group, blog, etc).
	 *
	 * @param int    $id        ID of the object.
	 * @param string $bp_object Object (members, groups, blogs, etc). Default: 'members'.
	 * @return Attachment|null
	 */
	public static function resolve_attachment_cover( $id, string $bp_object = 'members' ): ?Attachment {
		if ( empty( $id ) ) {
			return null;
		}

		$url = bp_attachments_get_attachment(
			'url',
			[
				'object_dir' => $bp_object,
				'item_id'    => $id,
			]
		);

		if ( empty( $url ) ) {
			return null;
		}

		$attachment       = new stdClass();
		$attachment->full = $url;

		return new Attachment( $attachment );
	}

	/**
	 * Return a Blog object.
	 *
	 * @param int        $id Blog ID.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_blog_object( $id, AppContext $context ): ?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$blog_id = absint( $id );
		$context->get_loader( 'bp_blog' )->buffer( [ $blog_id ] );

		return new Deferred(
			function () use ( $blog_id, $context ) {
				return $context->get_loader( 'bp_blog' )->load( $blog_id );
			}
		);
	}

	/**
	 * Returns a Friendship object.
	 *
	 * @throws UserError User error.
	 *
	 * @param int $id Friendship ID.
	 * @return Friendship|null
	 */
	public static function resolve_friendship_object( $id ): ?Friendship {
		if ( empty( $id ) ) {
			return null;
		}

		// Get the friendship object.
		$friendship = new BP_Friends_Friendship( $id ); // This is cached.

		// Check if friendship exists.
		if ( false === FriendshipHelper::friendship_exists( $friendship ) ) {
			throw new UserError(
				sprintf(
					// translators: %d is the friendship ID.
					esc_html__( 'No Friendship was found with ID: %d', 'wp-graphql-buddypress' ),
					absint( $id )
				)
			);
		}

		return new Friendship( $friendship );
	}

	/**
	 * Wrapper for the BlogsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_blogs_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new BlogsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the XProfileGroupsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_xprofile_groups_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new XProfileGroupsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the XProfileFieldsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_xprofile_fields_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new XProfileFieldsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the XProfileFieldOptionsConnectionResolver class.
	 *
	 * @param XProfileField $source  Source.
	 * @param array         $args    Query args to pass to the connection resolver.
	 * @param AppContext    $context The context of the query to pass along.
	 * @return array|null
	 */
	public static function resolve_xprofile_field_options_connection( XProfileField $source, array $args, AppContext $context ): ?array {
		return XProfileFieldOptionsConnectionResolver::resolve( $source, $args, $context );
	}

	/**
	 * Wrapper for the ThreadConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_thread_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new ThreadConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the MessagesConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_messages_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new MessagesConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the RecipientsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_recipients_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new RecipientsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the GroupsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_groups_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new GroupsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the GroupMembersConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_group_members_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new GroupMembersConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the GroupMembershipRequestsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_group_invitations_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new GroupInvitationsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the FriendshipConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_friendship_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new FriendshipsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the MembersConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_members_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new MembersConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the ActivitiesConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_activity_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new ActivitiesConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the ActivityCommentsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_activity_comments_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new ActivityCommentsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the SignupConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_signup_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new SignupConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the NotificationConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 * @return Deferred
	 */
	public static function resolve_notification_connection( $source, array $args, AppContext $context, ResolveInfo $info ): Deferred {
		return ( new NotificationConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}
}
