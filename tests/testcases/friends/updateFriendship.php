<?php

/**
 * Test_Friendship_updateFriendship_Mutation Class.
 *
 * @group friends
 */
class Test_Friendship_updateFriendship_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_accept_friendship() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u2 );

		$this->assertQuerySuccessful( $this->update_friendship( $u1, $u2 ) )
			->hasField( 'isConfirmed', true )
			->hasField( 'initiator', [ 'userId' => $u1 ] )
			->hasField( 'friend', [ 'userId' => $u2 ] );
	}

	public function test_initiator_can_not_accept_his_own_friendship_request() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		$this->assertQueryFailed( $this->update_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'There was a problem accepting the friendship. Try again.' );
	}

	public function test_user_can_not_accept_other_user_friendship_request() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->update_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_accept_friendship_with_user_not_logged_in() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->assertQueryFailed( $this->update_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_accept_friendship_with_invalid_friend() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		// Invalid friend.
		$this->assertQueryFailed( $this->update_friendship( $u1, GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}

	public function test_accept_friendship_with_invalid_initiator() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u2 );

		// Invalid initiator.
		$this->assertQueryFailed( $this->update_friendship( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER, $u2 ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}

	/**
	 * Update friendship.
	 *
	 * @param int $initiator Initiator ID.
	 * @param int $friend Friend ID.
	 * @return array
	 */
	protected function update_friendship( int $initiator, int $friend ): array {
		$query = '
			mutation updateFriendshipTest(
				$clientMutationId: String!
				$initiatorId: Int!
				$friendId: Int!
			) {
				updateFriendship(
					input: {
						clientMutationId: $clientMutationId
						initiatorId: $initiatorId
						friendId: $friendId
					}
				)
				{
					clientMutationId
					friendship {
						isConfirmed
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
		];

		$operation_name = 'updateFriendshipTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) ) ;
	}
}
