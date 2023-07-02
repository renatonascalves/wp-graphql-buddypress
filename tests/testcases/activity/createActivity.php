<?php

use WPGraphQL\Extensions\BuddyPress\Data\ActivityHelper;

/**
 * Test_Activity_createActivity_Mutation Class.
 *
 * @group activity
 */
class Test_Activity_createActivity_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_create_activity_authenticated() {
		$this->bp->set_current_user( $this->admin );

		$response = $this->create_activity();

		$this->assertQuerySuccessful( $response );

		$activity = ActivityHelper::get_activity( $response['data']['createActivity']['activity']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createActivity' => [
						'clientMutationId' => $this->client_mutation_id,
						'activity'         => [
							'id'               => $this->toRelayId( 'activity', (string) $activity->id ),
							'databaseId'       => $activity->id,
							'parentDatabaseId' => 0,
							'primaryItemId'    => $activity->item_id,
							'content'          => $activity->content,
							'component'        => strtoupper( $activity->component ),
							'type'             => strtoupper( $activity->type ),
							'hidden'           => $activity->hide_sitewide,
						],
					],
				],
			],
			$response
		);
	}

	public function test_create_activity_unauthenticated() {
		$this->assertQueryFailed( $this->create_activity() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_activity_without_permission() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->create_activity( [ 'userId' => $this->random_user ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_create_activity_without_content() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->create_activity( [ 'content' => '' ] ) )
			->expectedErrorMessage( 'Please, enter the content of the activity.' );
	}

	public function test_create_activity_in_a_group() {
		$g = $this->create_group_id();

		$this->bp->set_current_user( $this->admin );

		$response = $this->create_activity(
			[
				'primaryItemId' => $g,
				'component'     => strtoupper( buddypress()->groups->id ),
			]
		);

		$this->assertQuerySuccessful( $response );

		$activity = ActivityHelper::get_activity( $response['data']['createActivity']['activity']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createActivity' => [
						'clientMutationId' => $this->client_mutation_id,
						'activity'         => [
							'id'               => $this->toRelayId( 'activity', (string) $activity->id ),
							'databaseId'       => $activity->id,
							'parentDatabaseId' => 0,
							'primaryItemId'    => $g,
							'content'          => $activity->content,
							'component'        => strtoupper( $activity->component ),
							'type'             => strtoupper( $activity->type ),
							'hidden'           => $activity->hide_sitewide,
						],
					],
				],
			],
			$response
		);
	}

	public function test_non_member_create_activity_in_a_public_group() {
		$g = $this->create_group_id( [ 'status' => 'public' ] );

		$this->bp->set_current_user( $this->user_id );

		$response = $this->create_activity(
			[
				'primaryItemId' => $g,
				'userId'        => $this->user_id,
				'component'     => strtoupper( buddypress()->groups->id ),
			]
		);

		$this->assertQueryFailed( $response )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_member_create_activity_in_a_public_group() {
		$g = $this->create_group_id( [ 'status' => 'public' ] );

		$this->bp->add_user_to_group( $this->user_id, $g );

		$this->bp->set_current_user( $this->user_id );

		$response = $this->create_activity(
			[
				'primaryItemId' => $g,
				'userId'        => $this->user_id,
				'component'     => strtoupper( buddypress()->groups->id ),
			]
		);

		$this->assertQuerySuccessful( $response );

		$activity = ActivityHelper::get_activity( $response['data']['createActivity']['activity']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createActivity' => [
						'clientMutationId' => $this->client_mutation_id,
						'activity'         => [
							'id'               => $this->toRelayId( 'activity', (string) $activity->id ),
							'databaseId'       => $activity->id,
							'parentDatabaseId' => 0,
							'primaryItemId'    => $g,
							'content'          => $activity->content,
							'component'        => strtoupper( buddypress()->groups->id ),
							'type'             => strtoupper( $activity->type ),
							'hidden'           => $activity->hide_sitewide,
						],
					],
				],
			],
			$response
		);
	}

	public function test_create_activity_in_a_private_group() {
		$g = $this->create_group_id( [ 'status' => 'private' ] );

		$this->bp->set_current_user( $this->admin );

		$response = $this->create_activity(
			[
				'primaryItemId' => $g,
				'component'     => strtoupper( buddypress()->groups->id ),
			]
		);

		$this->assertQuerySuccessful( $response );

		$activity = ActivityHelper::get_activity( $response['data']['createActivity']['activity']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createActivity' => [
						'clientMutationId' => $this->client_mutation_id,
						'activity'         => [
							'id'               => $this->toRelayId( 'activity', (string) $activity->id ),
							'databaseId'       => $activity->id,
							'parentDatabaseId' => 0,
							'primaryItemId'    => $g,
							'content'          => $activity->content,
							'component'        => strtoupper( buddypress()->groups->id ),
							'type'             => strtoupper( $activity->type ),
							'hidden'           => $activity->hide_sitewide,
						],
					],
				],
			],
			$response
		);
	}

	public function test_create_activity_in_a_hidden_group() {
		$g = $this->create_group_id( [ 'status' => 'hidden' ] );

		$this->bp->set_current_user( $this->admin );

		$response = $this->create_activity(
			[
				'primaryItemId' => $g,
				'component'     => strtoupper( buddypress()->groups->id ),
			]
		);

		$this->assertQuerySuccessful( $response );

		$activity = ActivityHelper::get_activity( $response['data']['createActivity']['activity']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createActivity' => [
						'clientMutationId' => $this->client_mutation_id,
						'activity'         => [
							'id'               => $this->toRelayId( 'activity', (string) $activity->id ),
							'databaseId'       => $activity->id,
							'parentDatabaseId' => 0,
							'primaryItemId'    => $g,
							'content'          => $activity->content,
							'component'        => strtoupper( $activity->component ),
							'type'             => strtoupper( $activity->type ),
							'hidden'           => $activity->hide_sitewide,
						],
					],
				],
			],
			$response
		);
	}

	public function test_create_activity_comment() {
		$a = $this->create_activity_id( [ 'user_id' => $this->random_user ] );

		$this->bp->set_current_user( $this->admin );

		$response = $this->create_activity(
			[
				'primaryItemId' => $a,
				'content'       => 'Activity comment',
				'type'          => strtoupper( 'activity_comment' ),
			]
		);

		$this->assertQuerySuccessful( $response );

		$activity = ActivityHelper::get_activity( $response['data']['createActivity']['activity']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createActivity' => [
						'clientMutationId' => $this->client_mutation_id,
						'activity'         => [
							'id'               => $this->toRelayId( 'activity', (string) $activity->id ),
							'databaseId'       => $activity->id,
							'parentDatabaseId' => $a,
							'primaryItemId'    => $a,
							'content'          => $activity->content,
							'component'        => strtoupper( $activity->component ),
							'type'             => strtoupper( 'activity_comment' ),
							'hidden'           => $activity->hide_sitewide,
						],
					],
				],
			],
			$response
		);
	}

	public function test_create_activity_comment_in_a_group() {
		$g = $this->create_group_id( [ 'status' => 'hidden' ] );
		$a = $this->create_activity_id(
			[
				'item_id' => $g,
				'user_id' => $this->user_id,
			]
		);

		$this->bp->add_user_to_group( $this->random_user, $g );
		$this->bp->set_current_user( $this->random_user );

		$response = $this->create_activity(
			[
				'primaryItemId' => $a,
				'userId'        => $this->random_user,
				'content'       => 'Activity comment',
				'type'          => strtoupper( 'activity_comment' ),
			]
		);

		$this->assertQuerySuccessful( $response );

		$activity = ActivityHelper::get_activity( $response['data']['createActivity']['activity']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createActivity' => [
						'clientMutationId' => $this->client_mutation_id,
						'activity'         => [
							'id'               => $this->toRelayId( 'activity', (string) $activity->id ),
							'databaseId'       => $activity->id,
							'parentDatabaseId' => $a,
							'primaryItemId'    => $a,
							'content'          => $activity->content,
							'component'        => strtoupper( $activity->component ),
							'type'             => strtoupper( 'activity_comment' ),
							'hidden'           => $activity->hide_sitewide,
						],
					],
				],
			],
			$response
		);
	}

	public function test_create_new_blog_post_activity() {
		$p       = $this->factory->post->create();
		$b       = get_current_blog_id();
		$content = 'Blog post';

		$this->bp->set_current_user( $this->admin );

		$response = $this->create_activity(
			[
				'primaryItemId'   => $b,
				'secondaryItemId' => $p,
				'type'            => strtoupper( 'new_blog_post' ),
				'component'       => strtoupper( buddypress()->blogs->id, ),
				'hidden'          => true,
				'content'         => $content,
			]
		);

		$this->assertQuerySuccessful( $response );

		$activity = ActivityHelper::get_activity( $response['data']['createActivity']['activity']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createActivity' => [
						'clientMutationId' => $this->client_mutation_id,
						'activity'         => [
							'id'               => $this->toRelayId( 'activity', (string) $activity->id ),
							'databaseId'       => $activity->id,
							'parentDatabaseId' => 0,
							'primaryItemId'    => $b,
							'content'          => $activity->content,
							'component'        => strtoupper( buddypress()->blogs->id, ),
							'type'             => strtoupper( 'new_blog_post' ),
							'hidden'           => false,
						],
					],
				],
			],
			$response
		);

		// Check in another way.
		$query = bp_activity_get(
			[
				'show_hidden'  => true,
				'search_terms' => $content,
				'filter'       => [
					'object'       => buddypress()->blogs->id,
					'primary_id'   => get_current_blog_id(),
					'secondary_id' => $p,
				],
			]
		);

		$this->assertCount( 1, $query['activities'] );
		$this->assertSame( $activity->id, reset( $query['activities'] )->id );
	}

	/**
	 * Create activity mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function create_activity( array $args = [] ): array {
		$query = '
			mutation createActivityTest(
				$clientMutationId:String!
				$content:String!
				$primaryItemId:Int
				$userId:Int
				$secondaryItemId:Int
				$type:ActivityTypeEnum
				$component:ActivityComponentEnum
			) {
				createActivity(
					input: {
						clientMutationId: $clientMutationId
						primaryItemId: $primaryItemId
						secondaryItemId: $secondaryItemId
						userId: $userId
						content: $content
						type: $type
						component: $component
					}
				)
				{
					clientMutationId
					activity {
						id
						databaseId
						parentDatabaseId
						primaryItemId
						content(format: RAW)
						component
						type
						hidden
					}
				}
			}
		';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'userId'           => $this->admin,
				'primaryItemId'    => null,
				'secondaryItemId'  => null,
				'content'          => 'New activity content',
				'type'             => 'ACTIVITY_UPDATE',
				'component'        => strtoupper( buddypress()->activity->id ),
			]
		);

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
