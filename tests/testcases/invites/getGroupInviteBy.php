<?php
/**
 * Test_Invitation_getGroupInviteBy_Queries Class.
 *
 * @group invite
 * @group invitation
 * @group group
 */
class Test_Invitation_getGroupInviteBy_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Global ID.
	 *
	 * @var int
	 */
	public $global_id;

	/**
	 * Invitation ID.
	 *
	 * @var int
	 */
	public $invitation_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->invitation_id = groups_invite_user(
			[
				'user_id'     => $this->random_user,
				'group_id'    => $this->group,
				'inviter_id'  => $this->user,
				'send_invite' => 1,
			]
		);

		$this->global_id = $this->toRelayId( 'invitation', (string) $this->invitation_id );
	}

	public function test_group_invite_as_invitee() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->get_a_group_invite() )
			->hasField( 'id', $this->global_id )
			->hasField( 'databaseId', $this->invitation_id )
			->hasField( 'itemId', $this->group )
			->hasField( 'accepted', false )
			->hasField( 'type', 'INVITE' )
			->hasField( 'inviteSent', true )
			->hasField( 'inviter', [ 'databaseId' => $this->user ] )
			->hasField( 'invitee', [ 'databaseId' => $this->random_user ] )
			->hasField( 'group', [ 'databaseId' => $this->group ] )
			->hasField( 'message', null );
	}

	public function test_group_invite_as_inviter() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->get_a_group_invite() )
			->hasField( 'id', $this->global_id )
			->hasField( 'databaseId', $this->invitation_id )
			->hasField( 'itemId', $this->group )
			->hasField( 'accepted', false )
			->hasField( 'type', 'INVITE' )
			->hasField( 'inviteSent', true )
			->hasField( 'inviter', [ 'databaseId' => $this->user ] )
			->hasField( 'invitee', [ 'databaseId' => $this->random_user ] )
			->hasField( 'group', [ 'databaseId' => $this->group ] )
			->hasField( 'message', null );
	}

	public function test_group_invite_as_admin() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->get_a_group_invite() )
			->hasField( 'id', $this->global_id )
			->hasField( 'databaseId', $this->invitation_id );
	}

	public function test_group_invite_as_group_admin() {
		$u = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u, $this->group, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->get_a_group_invite() )
			->hasField( 'id', $this->global_id )
			->hasField( 'databaseId', $this->invitation_id );
	}

	public function test_group_invite_as_group_moderator() {
		$u = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u, $this->group, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->get_a_group_invite() )
			->hasField( 'id', $this->global_id )
			->hasField( 'databaseId', $this->invitation_id );
	}

	public function test_group_invite_as_group_member() {
		$u = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u, $this->group );

		$this->bp->set_current_user( $u );

		$response = $this->get_a_group_invite();

		$this->assertEmpty( $response['data']['getInviteBy'] );
	}

	public function test_group_invite_unauthenticated() {
		$response = $this->get_a_group_invite();

		$this->assertEmpty( $response['data']['getInviteBy'] );
	}

	public function test_group_invite_with_invalid_id() {
		$this->assertQueryFailed( $this->get_a_group_invite( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This invitation does not exist.' );
	}

	/**
	 * Get a group invitation.
	 *
	 * @param int|null $invitation_id Invitation ID.
	 * @return array
	 */
	protected function get_a_group_invite( $invitation_id = null ): array {
		$invitation_id = $invitation_id ?? $this->invitation_id;
		$query         = "
			query {
				getInviteBy(inviteId: {$invitation_id}, type: INVITE) {
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
