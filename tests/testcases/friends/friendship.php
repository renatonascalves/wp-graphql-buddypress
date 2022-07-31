<?php

/**
 * Test_Friendship_friendship_Queries Class.
 *
 * @group friends
 */
class Test_Friendship_friendship_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	public function test_getting_friendship_with_initiator() {
		$friendship = $this->create_friendship_object( absint( $this->random_user ), absint( $this->user_id ) );

		$this->bp->set_current_user( $this->user_id );

		$this->assertQuerySuccessful( $this->get_friendship( $friendship ) )
			->hasField( 'databaseId', $friendship )
			->hasField( 'isConfirmed', false )
			->hasField( 'friend', [ 'userId' => $this->user_id ] )
			->hasField( 'initiator', [ 'userId' => $this->random_user ] );
	}

	public function test_getting_friendship_with_friendship_initiator() {
		$friendship = $this->create_friendship_object( absint( $this->random_user ), absint( $this->user_id ) );

		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->get_friendship( $friendship ) )
			->hasField( 'databaseId', $friendship )
			->hasField( 'isConfirmed', false )
			->hasField( 'friend', [ 'userId' => $this->user_id ] )
			->hasField( 'initiator', [ 'userId' => $this->random_user ] );
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

	public function test_getting_friendship_without_logged_in_user() {
		$friendship = $this->create_friendship_object();

		$this->assertQueryFailed( $this->get_friendship( $friendship ) )
			->expectedErrorMessage( 'Sorry, you need to be logged in to perform this action.' );
	}

	public function test_friendship_with_unauthorized_member() {
		$friendship = $this->create_friendship_object();

		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->get_friendship( $friendship ) )
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
		$query     = "
			query {
				friendship(id: \"{$global_id}\", idType: ID) {
					id
					databaseId
					isConfirmed
					friend {
						userId
					}
					initiator {
						userId
					}
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
