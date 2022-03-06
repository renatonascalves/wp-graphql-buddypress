<?php

/**
 * Test_Friendship_deleteFriendship_Mutation Class.
 *
 * @group friends
 */
class Test_Friendship_deleteFriendship_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_initiator_withdraw_friendship() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->delete_friendship( $u1, $u2 ) )
			->hasField( 'deleted', true )
			->hasField( 'initiator', [ 'userId' => $u1 ] )
			->hasField( 'friend', [ 'userId' => $u2 ] );
	}

	public function test_friend_reject_friendship() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u2 );

		$this->assertQuerySuccessful( $this->delete_friendship( $u1, $u2 ) )
			->hasField( 'deleted', true )
			->hasField( 'initiator', [ 'userId' => $u1 ] )
			->hasField( 'friend', [ 'userId' => $u2 ] );
	}

	public function test_reject_and_remove_friendship_from_database_using_initiator() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->delete_friendship( $u1, $u2, true ) )
			->hasField( 'deleted', true )
			->hasField( 'initiator', [ 'userId' => $u1 ] )
			->hasField( 'friend', [ 'userId' => $u2 ] );
	}

	public function test_reject_and_remove_friendship_from_database_using_friend() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u2 );

		$this->assertQuerySuccessful( $this->delete_friendship( $u1, $u2, true ) )
			->hasField( 'deleted', true )
			->hasField( 'initiator', [ 'userId' => $u1 ] )
			->hasField( 'friend', [ 'userId' => $u2 ] );
	}

	public function test_reject_friendship_without_logged_in_user() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->assertQueryFailed( $this->delete_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_user_can_not_delete_or_reject_other_user_friendship_request() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u3 );

		$this->assertQueryFailed( $this->delete_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_admin_can_not_delete_or_reject_other_user_friendship_request() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->delete_friendship( $u1, $u2, true ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_reject_friendship_with_invalid_initiator() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		// Invalid initiator.
		$this->assertQueryFailed( $this->delete_friendship( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER, $u2 ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}

	public function test_reject_friendship_with_invalid_friend() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		// Invalid friend.
		$this->assertQueryFailed( $this->delete_friendship( $u1, GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}

	/**
	 * Delete friendship.
	 *
	 * @param int  $initiator Initiator ID.
	 * @param int  $friend Friend ID.
	 * @param bool $force Force.
	 * @return array
	 */
	protected function delete_friendship( int $initiator, int $friend, bool $force = false ): array {
		$query = '
			mutation deleteFriendshipTest(
				$clientMutationId: String!
				$initiatorId: Int!
				$friendId: Int!
				$force: Boolean
			) {
				deleteFriendship(
					input: {
						clientMutationId: $clientMutationId
						initiatorId: $initiatorId
						friendId: $friendId
						force: $force
					}
				)
				{
					clientMutationId
					deleted
					friendship {
						initiator {
							userId
						}
						friend {
							userId
						}
					}
				}
			}
		';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'initiatorId'      => $initiator,
			'friendId'         => $friend,
			'force'            => $force,
		];

		$operation_name = 'deleteFriendshipTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
