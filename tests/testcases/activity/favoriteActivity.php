<?php

/**
 * Test_Activity_favoriteActivity_Mutation Class.
 *
 * @group activity
 * @group favorite
 */
class Test_Activity_favoriteActivity_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_favorite_activity_authenticated() {
		$a = $this->create_activity_id();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->favorite_activity( $a ) )
			->hasField( 'databaseId', $a )
			->hasField( 'isFavorited', true );

		$this->assertTrue(
			in_array(
				$a,
				array_values( array_filter( wp_parse_id_list( bp_activity_get_user_favorites( $this->admin ) ) ) ),
				true
			)
		);
	}

	public function test_unfavorite_activity_authenticated() {
		$a = $this->create_activity_id();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->favorite_activity( $a ) )
			->hasField( 'databaseId', $a )
			->hasField( 'isFavorited', true );

		$this->assertTrue(
			in_array(
				$a,
				array_values( array_filter( wp_parse_id_list( bp_activity_get_user_favorites( $this->admin ) ) ) ),
				true
			)
		);

		$this->assertQuerySuccessful( $this->favorite_activity( $a ) )
			->hasField( 'databaseId', $a )
			->hasField( 'isFavorited', false );

		$this->assertFalse(
			in_array(
				$a,
				array_values( array_filter( wp_parse_id_list( bp_activity_get_user_favorites( $this->admin ) ) ) ),
				true
			)
		);
	}

	public function test_favorite_activity_comment_authenticated() {
		$a = $this->create_activity_id();
		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->admin,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->favorite_activity( $a ) )
			->hasField( 'databaseId', $a )
			->hasField( 'isFavorited', true );

		$this->assertQuerySuccessful( $this->favorite_activity( $c ) )
			->hasField( 'databaseId', $c )
			->hasField( 'isFavorited', true );

		$favorites = array_values( array_filter( wp_parse_id_list( bp_activity_get_user_favorites( $this->admin ) ) ) );

		$this->assertTrue( in_array( $a, $favorites, true ) );
		$this->assertTrue( in_array( $c, $favorites, true ) );
	}

	public function test_favorite_activity_unauthenticated() {
		$a = $this->create_activity_id();

		$this->assertQueryFailed( $this->favorite_activity( $a ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_favorite_activity_with_invalid_id() {
		$this->assertQueryFailed( $this->favorite_activity( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This activity does not exist.' );
	}

	public function test_unfavorite_activity_directly() {
		$a = $this->create_activity_id();

		bp_activity_add_user_favorite( $a, $this->admin );

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->favorite_activity( $a ) )
			->hasField( 'databaseId', $a )
			->hasField( 'isFavorited', false );
	}

	public function test_favorite_activity_when_disable() {
		$a = $this->create_activity_id();

		$this->bp->set_current_user( $this->admin );

		add_filter( 'bp_activity_can_favorite', '__return_false' );

		$this->assertQueryFailed( $this->favorite_activity( $a ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );

		remove_filter( 'bp_activity_can_favorite', '__return_false' );
	}

	public function test_favorite_activity_from_hidden_group() {
		$g = $this->create_group_id( [ 'status' => 'hidden' ] );
		$a = $this->create_activity_id(
			[
				'component' => buddypress()->groups->id,
				'type'      => 'activity_update',
				'user_id'   => $this->admin,
				'item_id'   => $g,
			]
		);

		$this->bp->add_user_to_group( $this->random_user, $g );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->favorite_activity( $a ) )
			->hasField( 'databaseId', $a )
			->hasField( 'isFavorited', true );
	}

	public function test_favorite_activity_from_hidden_group_without_permission() {
		$g = $this->create_group_id( [ 'status' => 'hidden' ] );
		$a = $this->create_activity_id(
			[
				'component' => buddypress()->groups->id,
				'type'      => 'activity_update',
				'user_id'   => $this->admin,
				'item_id'   => $g,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->favorite_activity( $a ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * (Un)favorite an activity.
	 *
	 * @param int $activity_id Activity ID.
	 * @return array
	 */
	protected function favorite_activity( int $activity_id ): array {
		$query = '
			mutation favoriteActivityTest(
				$clientMutationId:String!
				$activityId:Int
			) {
				favoriteActivity(
					input: {
						clientMutationId:$clientMutationId
						activityId:$activityId
					}
				)
				{
					clientMutationId
					activity {
						id
						databaseId
						isFavorited
					}
				}
			}
		';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'activityId'       => $activity_id,
		];

		$operation_name = 'favoriteActivityTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
