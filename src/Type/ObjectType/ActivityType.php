<?php
/**
 * Register Activity object type and queries.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Type\ObjectType
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Type\ObjectType;

use stdClass;
use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Model\Activity;
use WPGraphQL\Extensions\BuddyPress\Data\ActivityHelper;
use WPGraphQL\Extensions\BuddyPress\Type\Enum\GeneralEnums;
use BP_Activity_Activity;

/**
 * ActivityType Class.
 */
class ActivityType {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type_name = 'Activity';

	/**
	 * Register the activity type and queries to the WPGraphQL schema.
	 */
	public static function register(): void {
		register_graphql_object_type(
			self::$type_name,
			[
				'description'       => __( 'Info about a BuddyPress Activity.', 'wp-graphql-buddypress' ),
				'interfaces'        => [ 'Node', 'DatabaseIdentifier', 'UniformResourceIdentifiable' ],
				'eagerlyLoadType'   => true,
				'fields'            => [
					'parent'           => [
						'type'        => self::$type_name,
						'description' => __( 'Parent activity of the current activity. Usually, the activity object of an activity comment.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Activity $activity, array $args, AppContext $context ) {
							return Factory::resolve_activity_object( $activity->parentDatabaseId, $context );
						},
					],
					'parentId'         => [
						'type'        => 'ID',
						'description' => __( 'The globally unique identifier of the parent activity node.', 'wp-graphql-buddypress' ),
					],
					'parentDatabaseId' => [
						'type'        => 'Int',
						'description' => __( 'The ID of the parent activity.', 'wp-graphql-buddypress' ),
					],
					'primaryItemId'    => [
						'type'        => 'Int',
						'description' => __( 'The ID of some other object primarily associated with this one.', 'wp-graphql-buddypress' ),
					],
					'secondaryItemId'  => [
						'type'        => 'Int',
						'description' => __( 'The ID of some other object also associated with this one.', 'wp-graphql-buddypress' ),
					],
					'creator'          => [
						'type'        => 'User',
						'description' => __( 'The creator of the activity.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Activity $activity, array $args, AppContext $context ) {
							return ! empty( $activity->userId )
								? $context->get_loader( 'user' )->load_deferred( $activity->userId )
								: null;
						},
					],
					'content'          => [
						'type'        => 'String',
						'description' => __( 'HTML content for the activity.', 'wp-graphql-buddypress' ),
						'args'        => [
							'format' => [
								'type'        => 'ContentFieldFormatEnum',
								'description' => __( 'Format of the field output', 'wp-graphql-buddypress' ),
							],
						],
						'resolve'     => function ( Activity $activity, array $args ) {

							if ( empty( $activity->data->content ) ) {
								return null;
							}

							if ( isset( $args['format'] ) && 'raw' === $args['format'] ) {
								return $activity->data->content;
							}

							return self::render_activity_content( $activity->data );
						},
					],
					'component'        => [
						'type'        => 'ActivityComponentEnum',
						'description' => __( 'The active BuddyPress component name the activity relates to.', 'wp-graphql-buddypress' ),
					],
					'status'           => [
						'type'        => 'ActivityStatusEnum',
						'description' => __( 'Whether the activity has been marked as spam or not.', 'wp-graphql-buddypress' ),
					],
					'type'             => [
						'type'        => 'ActivityTypeEnum',
						'description' => __( 'The type of the activity.', 'wp-graphql-buddypress' ),
					],
					'title'            => [
						'type'        => 'String',
						'description' => __( 'The description of the activity\'s type (eg: Username posted an update).', 'wp-graphql-buddypress' ),
					],
					'date'             => [
						'type'        => 'String',
						'description' => __( 'The date the activity was published, in the site\'s timezone.', 'wp-graphql-buddypress' ),
					],
					'dateGmt'          => [
						'type'        => 'String',
						'description' => __( 'The date the activity was published, as GMT.', 'wp-graphql-buddypress' ),
					],
					'hidden'           => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the activity was sitewide hidden from streams or not.', 'wp-graphql-buddypress' ),
					],
					'isFavorited'      => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the activity has been favorited by the current user.', 'wp-graphql-buddypress' ),
						'resolve'     => function ( Activity $activity ) {

							if ( false === is_user_logged_in() ) {
								return false;
							}

							$user_favorites = bp_activity_get_user_favorites( get_current_user_id() );

							return in_array( $activity->databaseId, array_values( array_filter( wp_parse_id_list( $user_favorites ) ) ), true );
						},
					],
				],
				'resolve_node'      => function ( $node, $id, string $type, AppContext $context ) {
					if ( self::$type_name === $type ) {
						$node = Factory::resolve_activity_object( $id, $context );
					}

					return $node;
				},
				'resolve_node_type' => function ( $type, $node ) {
					if ( $node instanceof Activity ) {
						$type = self::$type_name;
					}

					return $type;
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'activity',
			[
				'type'        => self::$type_name,
				'description' => __( 'Get a BuddyPress Activity object.', 'wp-graphql-buddypress' ),
				'args'        => GeneralEnums::id_type_args( self::$type_name ),
				'resolve'     => function ( $source, array $args, AppContext $context ) {
					$activity = ActivityHelper::get_activity_from_input( $args );

					if ( false === bp_activity_user_can_read( $activity ) ) {
						throw new UserError( esc_html__( 'Sorry, you are not allowed to see this activity.', 'wp-graphql-buddypress' ) );
					}

					return Factory::resolve_activity_object( $activity->id, $context );
				},
			]
		);
	}

	/**
	 * Renders the content of an activity.
	 *
	 * @param BP_Activity_Activity $activity Activity object.
	 * @return string The rendered activity content.
	 */
	public static function render_activity_content( BP_Activity_Activity $activity ): string {
		$rendered = '';

		if ( empty( $activity->content ) ) {
			return $rendered;
		}

		// Do not truncate activities.
		add_filter( 'bp_activity_maybe_truncate_entry', '__return_false' );

		if ( 'activity_comment' === $activity->type ) {
			$rendered = apply_filters( 'bp_get_activity_content', $activity->content );
		} else {
			$activities_template = null;

			if ( isset( $GLOBALS['activities_template'] ) ) {
				$activities_template = $GLOBALS['activities_template'];
			}

			// Set the `activities_template` global for the current activity.
			$GLOBALS['activities_template']           = new stdClass();
			$GLOBALS['activities_template']->activity = $activity;

			// Set up activity oEmbed cache.
			bp_activity_embed();

			$rendered = apply_filters( 'bp_get_activity_content_body', $activity->content, $activity );

			// Restore the `activities_template` global.
			$GLOBALS['activities_template'] = $activities_template;
		}

		// Restore the filter to truncate activities.
		remove_filter( 'bp_activity_maybe_truncate_entry', '__return_false' );

		return (string) $rendered;
	}
}
