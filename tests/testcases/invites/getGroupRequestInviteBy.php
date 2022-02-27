<?php
/**
 * Test_Invitation_getGroupRequestInviteBy_Queries Class.
 *
 * @group invite
 * @group invitation
 * @group groups
 * @group group-membership
 */
class Test_Invitation_getGroupRequestInviteBy_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Private Group ID.
	 *
	 * @var int
	 */
	public $private_group_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->private_group_id = $this->bp_factory->group->create(
			[
				'creator_id' => $this->user,
				'status'     => 'private',
			]
		);
	}

	public function test_group_membership_request_as_requester() {
		$request_id = groups_send_membership_request(
			[
				'group_id' => $this->private_group_id,
				'user_id'  => $this->random_user,
			]
		);

		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->get_a_group_membership_request( $request_id ) )
			->hasField( 'id', $this->toRelayId( 'invitation', (string) $request_id ) )
			->hasField( 'databaseId', $request_id )
			->hasField( 'itemId', $this->private_group_id )
			->hasField( 'accepted', false )
			->hasField( 'type', 'REQUEST' )
			->hasField( 'inviteSent', true )
			->hasField( 'inviter', null )
			->hasField( 'invitee', [ 'databaseId' => $this->random_user ] )
			->hasField( 'group', null )
			->hasField( 'message', null );
	}

	public function test_get_group_membership_request_from_another_user_as_admin() {
		$request_id = groups_send_membership_request(
			[
				'group_id' => $this->private_group_id,
				'user_id'  => $this->random_user,
			]
		);

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->get_a_group_membership_request( $request_id ) )
			->hasField( 'id', $this->toRelayId( 'invitation', (string) $request_id ) )
			->hasField( 'databaseId', $request_id )
			->hasField( 'itemId', $this->private_group_id )
			->hasField( 'type', 'REQUEST' )
			->hasField( 'group', [ 'databaseId' => $this->private_group_id ] )
			->hasField( 'invitee', [ 'databaseId' => $this->random_user ] );
	}

	public function test_get_group_membership_request_from_another_user_as_group_admin() {
		$u          = $this->bp_factory->user->create();
		$request_id = groups_send_membership_request(
			[
				'group_id' => $this->private_group_id,
				'user_id'  => $this->random_user,
			]
		);

		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->get_a_group_membership_request( $request_id ) )
			->hasField( 'id', $this->toRelayId( 'invitation', (string) $request_id ) )
			->hasField( 'databaseId', $request_id )
			->hasField( 'itemId', $this->private_group_id )
			->hasField( 'type', 'REQUEST' )
			->hasField( 'group', [ 'databaseId' => $this->private_group_id ] )
			->hasField( 'invitee', [ 'databaseId' => $this->random_user ] );
	}

	public function test_get_group_membership_request_from_another_user_as_group_moderator() {
		$u          = $this->bp_factory->user->create();
		$request_id = groups_send_membership_request(
			[
				'group_id' => $this->private_group_id,
				'user_id'  => $this->random_user,
			]
		);

		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->get_a_group_membership_request( $request_id ) )
			->hasField( 'id', $this->toRelayId( 'invitation', (string) $request_id ) )
			->hasField( 'databaseId', $request_id )
			->hasField( 'itemId', $this->private_group_id )
			->hasField( 'type', 'REQUEST' )
			->hasField( 'group', [ 'databaseId' => $this->private_group_id ] )
			->hasField( 'invitee', [ 'databaseId' => $this->random_user ] );
	}

	public function test_get_group_membership_request_from_another_user_as_group_member() {
		$u          = $this->bp_factory->user->create();
		$request_id = groups_send_membership_request(
			[
				'group_id' => $this->private_group_id,
				'user_id'  => $this->random_user,
			]
		);

		$this->bp->add_user_to_group( $u, $this->private_group_id );

		$this->bp->set_current_user( $u );

		$response = $this->get_a_group_membership_request( $request_id );

		$this->assertEmpty( $response['data']['getInviteBy'] );
	}

	public function test_get_group_membership_request_unauthenticated() {
		$request_id = groups_send_membership_request(
			[
				'group_id' => $this->private_group_id,
				'user_id'  => $this->random_user,
			]
		);

		$response = $this->get_a_group_membership_request( $request_id );

		$this->assertEmpty( $response['data']['getInviteBy'] );
	}

	public function test_get_group_membership_request_with_invalid_id() {
		$this->assertQueryFailed( $this->get_a_group_membership_request( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This group membership request does not exist.' );
	}

	/**
	 * Get a group membership request.
	 *
	 * @param int $request_id Request ID.
	 * @return array
	 */
	protected function get_a_group_membership_request( int $request_id ): array {
		$query = "
			query {
				getInviteBy(inviteId: {$request_id}, type: REQUEST) {
					id
					databaseId
					itemId
					type
					inviteSent
					accepted
					invitee {
						databaseId
					}
					inviter {
						databaseId
					}
					group {
						databaseId
					}
					message
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
