<?php

/**
 * Test_Invitation_rejectGroupInvitation_Mutation Class.
 *
 * @group invite
 * @group invitation
 * @group reject
 */
class Test_Invitation_rejectGroupInvitation_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Invitation ID.
	 *
	 * @var int
	 */
	public $invitation_id;

	/**
	 * Inviter.
	 *
	 * @var int
	 */
	public $inviter;

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

		$this->inviter          = $this->bp_factory->user->create();
		$this->private_group_id = $this->bp_factory->group->create(
			[
				'creator_id' => $this->user,
				'status'     => 'private',
			]
		);

		$this->bp->add_user_to_group( $this->inviter, $this->private_group_id );

		$this->invitation_id = groups_invite_user(
			[
				'user_id'     => $this->random_user,
				'group_id'    => $this->private_group_id,
				'inviter_id'  => $this->inviter,
				'send_invite' => 1,
			]
		);
	}

	public function test_reject_invitation_invalid_id() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->reject_invitation( [ 'databaseId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'This invitation does not exist.' );
	}

	public function test_invited_can_reject_invitation() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->reject_invitation() )
			->hasField( 'deleted', true )
			->hasField( 'invite', [ 'databaseId' => $this->invitation_id ] );
	}

	public function test_inviter_can_reject_invitation() {
		$this->bp->set_current_user( $this->inviter );

		$this->assertQuerySuccessful( $this->reject_invitation() )
			->hasField( 'deleted', true )
			->hasField( 'invite', [ 'databaseId' => $this->invitation_id ] );
	}

	public function test_admin_can_reject_invitation() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->reject_invitation() )
			->hasField( 'deleted', true );
	}

	public function test_group_creator_can_reject_invitation() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->reject_invitation() )
			->hasField( 'deleted', true );
	}

	public function test_group_admin_can_reject_invitation() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->reject_invitation() )
			->hasField( 'deleted', true );
	}

	public function test_group_moderator_can_reject_invitation() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->reject_invitation() )
			->hasField( 'deleted', true );
	}

	public function test_group_member_can_not_accept_someone_else_invitation() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id );

		$this->bp->set_current_user( $u );

		$this->assertQueryFailed( $this->reject_invitation() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_non_group_member_can_not_reject_invitation() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->reject_invitation() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Reject invitation mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function reject_invitation( array $args = [] ): array {
		$query = '
			mutation rejectInvitationTest(
				$clientMutationId: String!
				$type:InvitationTypeEnum!
				$databaseId: Int
			) {
				rejectInvitation(
					input: {
						clientMutationId: $clientMutationId
						type: $type
						databaseId: $databaseId
					}
				)
				{
					clientMutationId
					deleted
					invite {
						databaseId
					}
				}
			}
        ';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'databaseId'       => $this->invitation_id,
				'type'             => 'INVITE',
			]
		);

		$operation_name = 'rejectInvitationTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
