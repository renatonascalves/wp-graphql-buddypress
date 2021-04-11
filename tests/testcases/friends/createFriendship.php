<?php

/**
 * Test_Friendship_createFriendship_Mutation Class.
 *
 * @group friends
 */
class Test_Friendship_createFriendship_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_create_friendship() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->create_friendship( $u1, $u2 ) )
			->hasField( 'isConfirmed', false )
			->hasField( 'initiator', [ 'userId' => $u1 ] )
			->hasField( 'friend', [ 'userId' => $u2 ] );
	}

	public function test_create_friendship_but_already_has_friendship_request() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->create_friendship( $u1, $u2 ) )
			->hasField( 'isConfirmed', false );

		// Already has a friendship request.
		$this->assertQueryFailed( $this->create_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'You already have a pending friendship request with this user.' );
	}

	public function test_can_not_create_item_to_myself_from_someone_else() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u2 );

		$this->assertQueryFailed( $this->create_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_admins_can_force_friendship_creation() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->create_friendship( $u1, $u2, true ) )
			->hasField( 'isConfirmed', true )
			->hasField( 'initiator', [ 'userId' => $u1 ] )
			->hasField( 'friend', [ 'userId' => $u2 ] );
	}

	public function test_regular_users_can_not_force_friendship_creation() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->create_friendship( $u1, $u2, true ) )
			->hasField( 'isConfirmed', false )
			->hasField( 'initiator', [ 'userId' => $u1 ] )
			->hasField( 'friend', [ 'userId' => $u2 ] );
	}

	public function test_regular_user_can_not_create_friendship_to_others() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$u3 = $this->bp_factory->user->create();

		$this->bp->set_current_user( $u3 );

		$this->assertQueryFailed( $this->create_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_create_friendship_user_not_logged_id() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();

		$this->assertQueryFailed( $this->create_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_create_friendship_with_invalid_friend_user() {
		$this->assertQueryFailed( $this->create_friendship( absint( $this->user ), GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}

	/**
	 * Create friendship.
	 *
	 * @param int $initiator Initiator ID.
	 * @param int $friend Friend ID.
	 * @param bool $force Force.
	 * @return array
	 */
	protected function create_friendship( int $initiator, int $friend, bool $force = false ): array {
		$query = '
			mutation createFriendshipTest(
				$clientMutationId: String!
				$initiatorId: Int
				$friendId: Int!
				$force: Boolean
			) {
				createFriendship(
					input: {
						clientMutationId: $clientMutationId
						initiatorId: $initiatorId
						friendId: $friendId
						force: $force
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
			'force'            => $force,
		];

		$operation_name = 'createFriendshipTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
