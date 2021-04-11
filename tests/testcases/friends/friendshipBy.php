<?php

/**
 * Test_Friendship_friendshipBy_Queries Class.
 *
 * @group friends
 */
class Test_Friendship_friendshipBy_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_getting_friendship_with_initiator() {
		$u = $this->bp_factory->user->create();
		$f = $this->create_friendship_object( $u, $this->user );

		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->get_friendship( $f ) )
			->hasField( 'friendshipId', $f )
			->hasField( 'isConfirmed', false )
			->hasField( 'friend', [ 'userId' => $this->user ] )
			->hasField( 'initiator', [ 'userId' => $u ] );
	}

	public function test_getting_friendship_with_invited_friend() {
		$u1 = $this->bp_factory->user->create();
		$u2 = $this->bp_factory->user->create();
		$f  = $this->create_friendship_object( $u1, $u2 );

		$this->bp->set_current_user( $u1 );

		$this->assertQuerySuccessful( $this->get_friendship( $f ) )
			->hasField( 'friendshipId', $f )
			->hasField( 'isConfirmed', false )
			->hasField( 'friend', [ 'userId' => $u2 ] )
			->hasField( 'initiator', [ 'userId' => $u1 ] );
	}

	public function test_getting_friendship_with_invalid_id() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->get_friendship( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage(
				sprintf(
					'No Friendship was found with ID: %d',
					GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER
				)
			);
	}

	public function test_getting_friendship_with_non_logged_in_user() {
		$f = $this->create_friendship_object();

		$this->assertQueryFailed( $this->get_friendship( $f ) )
			->expectedErrorMessage( 'Sorry, you need to be logged in to perform this action.' );
	}

	public function test_friendship_with_unauthorized_member() {
		$f = $this->create_friendship_object();

		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->get_friendship( $f ) )
			->expectedErrorMessage( 'Sorry, you don\'t have permission to see this friendship.' );
	}

	/**
	 * Get friendship.
	 *
	 * @param int $id ID.
	 * @return array
	 */
	protected function get_friendship( int $id ): array {
		$global_id = $this->toRelayId( 'friendship', $id );
		$query     = "{
			friendshipBy(id: \"{$global_id}\") {
				id
				friendshipId
				isConfirmed
				friend {
					userId
				}
				initiator {
					userId
				}
			}
		}";

		return $this->graphql( compact( 'query' ) );
	}
}
