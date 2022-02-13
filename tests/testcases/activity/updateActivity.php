<?php

/**
 * Test_Activity_updateActivity_Mutation Class.
 *
 * @group activity
 */
class Test_Activity_updateActivity_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_update_activity_authenticated() {
		$a = $this->create_activity_object();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_activity( [ 'activityId' => $a ] ) )
			->hasField( 'content', 'Updated Activity content' )
			->hasField( 'databaseId', $a );
	}

	public function test_update_activity_component() {
		$a = $this->create_activity_object();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_activity( [ 'activityId' => $a, 'component' => 'GROUPS' ] ) )
			->hasField( 'component', 'GROUPS' )
			->hasField( 'databaseId', $a );
	}

	public function test_update_activity_type() {
		$a = $this->create_activity_object();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_activity( [ 'activityId' => $a, 'type' => 'ACTIVITY_COMMENT' ] ) )
			->hasField( 'type', 'ACTIVITY_COMMENT' )
			->hasField( 'primaryItemId', 0 )
			->hasField( 'databaseId', $a );
	}

	public function test_update_activity_into_activity_comment() {
		$c = $this->create_activity_object();
		$a = $this->create_activity_object();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_activity(
			[
				'activityId'    => $c,
				'type'          => 'ACTIVITY_COMMENT',
				'primaryItemId' => $a
			]
		) )
			->hasField( 'type', 'ACTIVITY_COMMENT' )
			->hasField( 'primaryItemId', $a )
			->hasField( 'databaseId', $c );
	}

	public function test_update_activity_without_content_or_type() {
		$a = $this->create_activity_object();

		$this->bp->set_current_user( $this->admin );

		// Test without content.
		$this->assertQueryFailed( $this->update_activity( [ 'activityId' => $a, 'content' => '' ] ) )
			->expectedErrorMessage( 'Please, enter the content of the activity.' );

		// Test without a type.
		$this->assertQueryFailed( $this->update_activity( [ 'activityId' => $a, 'type' => '' ] ) )
			->expectedErrorMessage( 'Variable "$type" got invalid value (empty string); Expected type ActivityTypeEnum.' );
	}

	public function test_update_with_invalid_activity_id() {
		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->update_activity( [ 'activityId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'This activity does not exist.' );
	}

	public function test_update_activity_unauthenticated() {
		$a = $this->create_activity_object();

		$this->assertQueryFailed( $this->update_activity( [ 'activityId' => $a ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_activity_without_permission() {
		$a = $this->create_activity_object();

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->update_activity( [ 'activityId' => $a ] ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_activity_posted_in_a_group() {
		$g = $this->create_group_object();
		$a = $this->create_activity_object(
			[
				'component' => buddypress()->groups->id,
				'type'      => 'activity_update',
				'item_id'   => $g,
			]
		);

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_activity( [ 'activityId' => $a, 'primaryItemId' => $g ] ) )
			->hasField( 'content', 'Updated Activity content' )
			->hasField( 'primaryItemId', $g )
			->hasField( 'databaseId', $a );
	}

	public function test_creator_can_update_activity_comment() {
		$a = $this->create_activity_object();
		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->update_activity(
			[
				'activityId'    => $c,
				'primaryItemId' => $a,
				'type'          => 'ACTIVITY_COMMENT',
				'content'       => 'Updated commment',
			]
		) )
			->hasField( 'content', 'Updated commment' )
			->hasField( 'primaryItemId', $a )
			->hasField( 'parentDatabaseId', $a )
			->hasField( 'databaseId', $c );
	}

	public function test_update_activity_comment_unauthenticated() {
		$a = $this->create_activity_object( [ 'user_id' => $this->random_user ] );
		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->assertQueryFailed( $this->update_activity(
			[
				'activityId'    => $c,
				'primaryItemId' => $a,
				'type'          => 'ACTIVITY_COMMENT',
				'content'       => 'Updated commment',
			]
		) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_admin_can_update_activity_comment() {
		$a = $this->create_activity_object( [ 'user_id' => $this->random_user ] );
		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->update_activity(
			[
				'activityId'    => $c,
				'primaryItemId' => $a,
				'type'          => 'ACTIVITY_COMMENT',
				'content'       => 'Updated commment',
			]
		) )
			->hasField( 'content', 'Updated commment' )
			->hasField( 'primaryItemId', $a )
			->hasField( 'parentDatabaseId', $a )
			->hasField( 'databaseId', $c );
	}

	public function test_activity_creator_can_not_update_activity_comment() {
		$a = $this->create_activity_object( [ 'user_id' => $this->random_user ] );
		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->update_activity(
			[
				'activityId'    => $c,
				'primaryItemId' => $a,
				'type'          => 'ACTIVITY_COMMENT',
				'content'       => 'Updated commment',
			]
		) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_update_activity_comment_without_content_or_type() {
		$a = $this->create_activity_object( [ 'user_id' => $this->random_user ] );
		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->bp->set_current_user( $this->user );

		// Test without content.
		$this->assertQueryFailed( $this->update_activity( [ 'activityId' => $c, 'content' => '' ] ) )
			->expectedErrorMessage( 'Please, enter the content of the activity.' );

		// Test without a type.
		$this->assertQueryFailed( $this->update_activity( [ 'activityId' => $c, 'type' => '' ] ) )
			->expectedErrorMessage( 'Variable "$type" got invalid value (empty string); Expected type ActivityTypeEnum.' );
	}

	/**
	 * Update activity mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function update_activity( array $args = [] ): array {
		$query = '
			mutation updateActivityTest(
				$clientMutationId:String!
				$content:String!
				$activityId:Int
				$primaryItemId:Int
				$secondaryItemId:Int
				$type:ActivityTypeEnum!
				$component:ActivityComponentEnum
			) {
				updateActivity(
					input: {
						clientMutationId: $clientMutationId
						activityId: $activityId
						primaryItemId: $primaryItemId
						secondaryItemId: $secondaryItemId
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
					}
				}
			}
        ';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'primaryItemId'    => 0,
				'secondaryItemId'  => 0,
				'content'          => 'Updated Activity content',
				'type'             => 'ACTIVITY_UPDATE',
				'component'        => strtoupper( buddypress()->activity->id ),
			]
		);

		$operation_name = 'updateActivityTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
