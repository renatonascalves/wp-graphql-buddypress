<?php

/**
 * Test_Invitation_acceptGroupInvitation_Mutation Class.
 *
 * @group invite
 * @group invitation
 * @group groups
 */
class Test_Invitation_acceptGroupInvitation_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

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

		$this->private_group_id = $this->bp_factory->group->create(
			[
				'creator_id' => $this->user,
				'status'     => 'private',
			]
		);

		$this->invitation_id = groups_invite_user(
			[
				'user_id'     => $this->random_user,
				'group_id'    => $this->private_group_id,
				'inviter_id'  => $this->user,
				'send_invite' => 1,
			]
		);
	}

	public function test_accept_invitation_invalid_id() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->accept_invitation( [ 'databaseId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'This invitation does not exist.' );
	}

	public function test_invited_can_accept_invitation() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->accept_invitation() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_admin_can_accept_invitation() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->accept_invitation() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_group_creator_can_accept_invitation() {
		$this->bp->set_current_user( $this->user );

		$this->assertQuerySuccessful( $this->accept_invitation() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_group_admin_can_accept_invitation() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->accept_invitation() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_group_moderator_can_accept_invitation() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->accept_invitation() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_group_member_can_not_accept_someone_else_invitation() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id );

		$this->bp->set_current_user( $u );

		$this->assertQueryFailed( $this->accept_invitation() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_non_group_member_can_not_accept_invitation() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->accept_invitation() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Accept invitation mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function accept_invitation( array $args = [] ): array {
		$query = '
			mutation acceptInvitationTest(
				$clientMutationId: String!
				$type:InvitationTypeEnum!
				$databaseId: Int
			) {
				acceptInvitation(
					input: {
						clientMutationId: $clientMutationId
						type: $type
						databaseId: $databaseId
					}
				)
				{
					clientMutationId
					user {
						id
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

		$operation_name = 'acceptInvitationTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
