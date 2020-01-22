<?php
/**
 * Factory Class
 *
 * This class serves as a factory for all resolvers.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Data
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Data;

use GraphQL\Deferred;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\GroupsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\GroupMembersConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\MembersConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\XProfileFieldsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\XProfileGroupsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\BlogsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Connection\Resolvers\FriendshipsConnectionResolver;
use WPGraphQL\Extensions\BuddyPress\Model\XProfileField;
use WPGraphQL\Extensions\BuddyPress\Model\Attachment;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;
use WPGraphQL\Extensions\BuddyPress\Model\Friendship;

/**
 * Class Factory.
 */
class Factory {

	/**
	 * Returns a Group object.
	 *
	 * @param int|null   $id      Group ID or null.
	 * @param AppContext $context AppContext object.
	 *
	 * @return Deferred|null
	 */
	public static function resolve_group_object( $id, AppContext $context ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		$group_id = absint( $id );
		$context->getLoader( 'group_object' )->buffer( [ $group_id ] );

		return new Deferred(
			function () use ( $group_id, $context ) {
				return $context->getLoader( 'group_object' )->load( $group_id );
			}
		);
	}

	/**
	 * Returns a XProfile Group object.
	 *
	 * @param int|null   $id      XProfile group ID or null.
	 * @param AppContext $context AppContext object.
	 *
	 * @return Deferred|null
	 */
	public static function resolve_xprofile_group_object( $id, AppContext $context ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		$xprofile_group_id = absint( $id );
		$context->getLoader( 'xprofile_group_object' )->buffer( [ $xprofile_group_id ] );

		return new Deferred(
			function () use ( $xprofile_group_id, $context ) {
				return $context->getLoader( 'xprofile_group_object' )->load( $xprofile_group_id );
			}
		);
	}

	/**
	 * Returns a XProfile Field object.
	 *
	 * @throws UserError User error.
	 *
	 * @param int|null   $id      XProfile field ID or null.
	 * @param AppContext $context AppContext object.
	 *
	 * @return \XProfileField|null
	 */
	public static function resolve_xprofile_field_object( $id, AppContext $context ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		// Get the user ID if available.
		$user_id = $context->config['userId'] ?? null;

		/**
		 * Get the XPofile field object.
		 */
		$xprofile_field_object = xprofile_get_field( absint( $id ), $user_id );

		if ( empty( $xprofile_field_object ) || ! $xprofile_field_object instanceof \BP_XProfile_Field ) {
			throw new UserError(
				sprintf(
					// translators: XProfile Field ID.
					__( 'No XProfile field was found with ID: %d', 'wp-graphql-buddypress' ),
					absint( $id )
				)
			);
		}

		return new XProfileField( $xprofile_field_object );
	}

	/**
	 * Resolve an attachment avatar for a object (user, group, blog, etc).
	 *
	 * @param int|null $id     ID of the object or null.
	 * @param string   $object Object (user, group, blog, etc).
	 *
	 * @return Attachment|null
	 */
	public static function resolve_attachment( $id, $object = 'user' ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		$attachment = new \stdClass();

		foreach ( [ 'full', 'thumb' ] as $type ) {
			$args = [
				'item_id' => $id,
				'object'  => $object,
				'no_grav' => true,
				'html'    => false,
				'type'    => $type,
			];

			if ( 'blog' === $object ) {
				// Unset item ID and add correct item id key.
				unset( $args['item_id'] );
				$args['blog_id'] = $id;
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
	 * @param int|null $id     ID of the object or null.
	 * @param string   $object Object (members, groups, blogs, etc).
	 *
	 * @return Attachment|null
	 */
	public static function resolve_attachment_cover( $id, $object = 'members' ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		$url = bp_attachments_get_attachment(
			'url',
			[
				'object_dir' => $object,
				'item_id'    => $id,
			]
		);

		if ( empty( $url ) ) {
			return null;
		}

		$attachment       = new \stdClass();
		$attachment->full = $url;

		return new Attachment( $attachment );
	}

	/**
	 * Returns a Blog object.
	 *
	 * @throws UserError User error.
	 *
	 * @param int|null $id Blog ID or null.
	 * @return Blog|null
	 */
	public static function resolve_blog_object( $id ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		/**
		 * Get the blog object.
		 */
		$blogs       = current( bp_blogs_get_blogs( [ 'include_blog_ids' => $id ] ) );
		$blog_object = $blogs[0] ?? 0;

		if ( empty( $blog_object ) || ! is_object( $blog_object ) ) {
			throw new UserError(
				sprintf(
					// translators: Blog ID.
					__( 'No Blog was found with ID: %d', 'wp-graphql-buddypress' ),
					absint( $id )
				)
			);
		}

		return new Blog( $blog_object );
	}

	/**
	 * Returns a Friendship object.
	 *
	 * @throws UserError User error.
	 *
	 * @param int|null $id Friendship ID or null.
	 * @return Friendship|null
	 */
	public static function resolve_friendship_object( $id ) {
		if ( empty( $id ) || ! absint( $id ) ) {
			return null;
		}

		// Get the friendship object.
		$friendship = new \BP_Friends_Friendship( $id ); // This is cached.

		// Check if friendship exists.
		if ( false === FriendshipMutation::friendship_exists( $friendship ) ) {
			throw new UserError(
				sprintf(
					// translators: Friendship ID.
					__( 'No Friendship was found with ID: %d', 'wp-graphql-buddypress' ),
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
	 *
	 * @return array
	 */
	public static function resolve_blogs_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new BlogsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the XProfileGroupsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_xprofile_groups_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new XProfileGroupsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the XProfileFieldsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_xprofile_fields_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new XProfileFieldsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the GroupsConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_groups_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new GroupsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the GroupMembersConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Query args to pass to the connection resolver.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_group_members_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new GroupMembersConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the MembersConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_members_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new MembersConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}

	/**
	 * Wrapper for the FriendshipConnectionResolver class.
	 *
	 * @param mixed       $source  Source.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The context of the query to pass along.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return array
	 */
	public static function resolve_friendship_connection( $source, array $args, AppContext $context, ResolveInfo $info ) {
		return ( new FriendshipsConnectionResolver( $source, $args, $context, $info ) )->get_connection();
	}
}
