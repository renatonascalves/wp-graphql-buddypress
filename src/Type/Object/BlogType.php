<?php
/**
 * Register Blog object type and queries.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\Object
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\Object;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\Blog;

/**
 * BlogType Class.
 */
class BlogType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Blog';

	/**
	 * Register the blog type and queries to the WPGraphQL schema.
	 */
	public static function register() {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress Blog.', 'wp-graphql-buddypress' ),
				'fields'            => [
					'id' => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The globally unique identifier for the blog.', 'wp-graphql-buddypress' ),
					],
					'blogId' => [
						'type'        => 'Int',
						'description' => __( 'The id field that matches the BP_Blogs_Blog->blog_id field.', 'wp-graphql-buddypress' ),
					],
					'blogAdmin' => [
						'type'        => 'User',
						'description' => __( 'The admin of the blog.', 'wp-graphql-buddypress' ),
						'resolve'     => function( Blog $blog, array $args, AppContext $context ) {
							return ! empty( $blog->admin )
								? DataSource::resolve_user( $blog->admin, $context )
								: null;
						},
					],
					'name' => [
						'type'        => 'String',
						'description' => __( 'The name of the Blog.', 'wp-graphql-buddypress' ),
					],
					'permalink' => [
						'type'        => 'String',
						'description' => __( 'The permalink of the blog.', 'wp-graphql-buddypress' ),
					],
					'description' => [
						'type'        => 'String',
						'description' => __( 'The description of the blog.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output.', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function( Blog $blog, array $args ) {
							if ( empty( $blog->description ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $blog->description;
							}

							return stripslashes( $blog->description );
						},
					],
					'path' => [
						'type'        => 'String',
						'description' => __( 'The path of the blog.', 'wp-graphql-buddypress' ),
					],
					'domain' => [
						'type'        => 'String',
						'description' => __( 'The domain of the blog.', 'wp-graphql-buddypress' ),
					],
					'lastActivity' => [
						'type'        => 'String',
						'description' => __( 'The last activity date from the blog, in the site\'s timezone.', 'wp-graphql-buddypress' ),
					],
					'attachmentAvatar' => [
						'type'        => 'Attachment',
						'description' => __( 'Attachment Avatar of the blog.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Blog $blog ) {
							return Factory::resolve_attachment( $blog->blogId ?? 0, 'blog' );
						},
					],
					'attachmentCover' => [
						'type'        => 'Attachment',
						'description' => __( 'Attachment Cover of the blog.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Blog $blog ) {
							return Factory::resolve_attachment_cover( $blog->blogId ?? 0, 'blogs' );
						},
					],
				],
				'resolve_node'      => function( $node, $id, $type ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_blog_object( $id );
					}

					return $node;
				},
				'resolve_node_type' => function( $type, $node ) {
					if ( $node instanceof Blog ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'blogBy',
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress Blog object.', 'wp-graphql-buddypress' ),
				'args'        => [
					'id'           => [
						'type'        => 'ID',
						'description' => __( 'Get the object by its global ID.', 'wp-graphql-buddypress' ),
					],
					'blogId'      => [
						'type'        => 'Int',
						'description' => __( 'Get the object by its database ID.', 'wp-graphql-buddypress' ),
					],
				],
				'resolve'     => function ( $source, array $args ) {
					$blog_id = 0;

					if ( ! empty( $args['id'] ) ) {
						$id_components = Relay::fromGlobalId( $args['id'] );

						if ( empty( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
							throw new UserError( __( 'The "id" is invalid.', 'wp-graphql-buddypress' ) );
						}

						$blog_id = absint( $id_components['id'] );
					} elseif ( ! empty( $args['blogId'] ) ) {
						$blog_id = absint( $args['blogId'] );
					}

					return Factory::resolve_blog_object( $blog_id );
				},
			]
		);
	}
}
