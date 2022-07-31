<?php

/**
 * Test_Invitation_rejectGroupRequest_Mutation Class.
 *
 * @group invite
 * @group invitation
 * @group request
 */
class Test_Invitation_rejectGroupRequest_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Private Group ID.
	 *
	 * @var int
	 */
	public $private_group_id;

	/**
	 * Request ID.
	 *
	 * @var int
	 */
	public $request_id;

	/**
	 * Set up.
	 */
	public function set_up() {
		parent::set_up();

		$this->private_group_id = $this->bp_factory->group->create(
			[
				'creator_id' => $this->user_id,
				'status'     => 'private',
			]
		);

		$this->request_id = groups_send_membership_request(
			[
				'group_id' => $this->private_group_id,
				'user_id'  => $this->random_user,
			]
		);
	}

	public function test_reject_request_invalid_id() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->reject_request( [ 'databaseId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'This group membership request does not exist.' );
	}

	public function test_invited_can_reject_request() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->reject_request() )
			->hasField( 'deleted', true )
			->hasField( 'invite', [ 'databaseId' => $this->request_id ] );
	}

	public function test_admin_can_reject_request() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->reject_request() )
			->hasField( 'deleted', true );
	}

	public function test_group_creator_can_reject_request() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQuerySuccessful( $this->reject_request() )
			->hasField( 'deleted', true );
	}

	public function test_group_admin_can_reject_request() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->reject_request() )
			->hasField( 'deleted', true );
	}

	public function test_group_moderator_can_reject_request() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->reject_request() )
			->hasField( 'deleted', true );
	}

	public function test_group_member_can_not_accept_someone_else_invitation() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id );

		$this->bp->set_current_user( $u );

		$this->assertQueryFailed( $this->reject_request() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_non_group_member_can_not_reject_request() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->reject_request() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Reject request mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function reject_request( array $args = [] ): array {
		$query = '
			mutation rejectRequestTest(
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
				'databaseId'       => $this->request_id,
				'type'             => 'REQUEST',
			]
		);

		$operation_name = 'rejectRequestTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
