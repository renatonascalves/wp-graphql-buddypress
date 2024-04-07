<?php
/**
 * ActivityCreate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Activity
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Activity;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\ActivityHelper;

/**
 * ActivityCreate Class.
 */
class ActivityCreate {

	/**
	 * Registers the ActivityCreate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'createActivity',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input field configuration.
	 *
	 * @return array
	 */
	public static function get_input_fields(): array {
		return [
			'userId'          => [
				'type'        => 'Int',
				'description' => __( 'The userId to assign as the activity creator.', 'wp-graphql-buddypress' ),
			],
			'type'            => [
				'type'        => 'ActivityTypeEnum',
				'description' => __( 'The type of the activity.', 'wp-graphql-buddypress' ),
			],
			'content'         => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'The content of the activity.', 'wp-graphql-buddypress' ),
			],
			'component'       => [
				'type'        => 'ActivityComponentEnum',
				'description' => __( 'The active BuddyPress component name the activity relates to.', 'wp-graphql-buddypress' ),
			],
			'primaryItemId'   => [
				'type'        => 'Int',
				'description' => __( 'The ID of some other object primarily associated with this one.', 'wp-graphql-buddypress' ),
			],
			'secondaryItemId' => [
				'type'        => 'Int',
				'description' => __( 'The ID of some other object also associated with this one.', 'wp-graphql-buddypress' ),
			],
			'hidden'          => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the activity was sitewide hidden from streams or not.', 'wp-graphql-buddypress' ),
			],
		];
	}

	/**
	 * Defines the mutation output field configuration.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'activity' => [
				'type'        => 'Activity',
				'description' => __( 'The activity object that was created.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_activity_object( absint( $payload['id'] ), $context );
				},
			],
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		return function ( array $input ) {

			// Check empty activity content.
			if ( empty( $input['content'] ) ) {
				throw new UserError( esc_html__( 'Please, enter the content of the activity.', 'wp-graphql-buddypress' ) );
			}

			$user_id   = $input['userId'] ?? 0;
			$item_id   = $input['primaryItemId'];
			$component = $input['component'];
			$error     = __( 'Sorry, you are not allowed to perform this action.', 'wp-graphql-buddypress' );

			if ( false === is_user_logged_in() || ( ! empty( $user_id ) && (int) bp_loggedin_user_id() !== (int) $user_id ) ) {
				throw new UserError( esc_html( $error ) );
			}

			if (
				bp_is_active( 'groups' )
				&& ! empty( $component )
				&& buddypress()->groups->id === $component
				&& ! empty( $item_id )
				&& false === ActivityHelper::show_hidden( $component, $item_id )
			) {
				throw new UserError( esc_html( $error ) );
			}

			$type              = $input['type'] ?? 'activity_update';
			$secondary_item_id = $input['secondaryItemId'] ?? false;
			$activity_id       = 0;
			$activity_args     = ActivityHelper::prepare_activity_args( $input, 'create' );

			// Post a regular activity update.
			if ( 'activity_update' === $type ) {
				if ( bp_is_active( 'groups' ) && ! is_null( $item_id ) ) {
					$activity_id = groups_post_update( $activity_args );
				} else {
					$activity_id = bp_activity_post_update( $activity_args );
				}

				// Post an activity comment.
			} elseif ( 'activity_comment' === $type ) {

				// ID of the root activity item.
				if ( isset( $item_id ) ) {
					$activity_args['activity_id'] = (int) $item_id;
				}

				// ID of a parent comment.
				if ( ! empty( $secondary_item_id ) ) {
					$activity_args['parent_id'] = $secondary_item_id;
				}

				$activity_id = bp_activity_new_comment( $activity_args );

				// Otherwise add an activity.
			} else {
				$activity_id = bp_activity_add( $activity_args );
			}

			// Throw an exception if the activity failed to be created.
			if ( empty( $activity_id ) || ! is_numeric( $activity_id ) ) {
				throw new UserError( esc_html__( 'Could not create activity.', 'wp-graphql-buddypress' ) );
			}

			// Update current user's last activity.
			bp_update_user_last_activity( $user_id );

			// Return the activity ID.
			return [
				'id' => $activity_id,
			];
		};
	}
}
