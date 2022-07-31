<?php

/**
 * Test_Invitation_acceptGroupRequest_Mutation Class.
 *
 * @group invite
 * @group invitation
 * @group request
 */
class Test_Invitation_acceptGroupRequest_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

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

	public function test_accept_request_invalid_id() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->accept_request( [ 'databaseId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'This group membership request does not exist.' );
	}

	public function test_invited_can_accept_request() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQuerySuccessful( $this->accept_request() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_admin_can_accept_request() {
		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->accept_request() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_group_creator_can_accept_request() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQuerySuccessful( $this->accept_request() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_group_admin_can_accept_request() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->accept_request() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_group_moderator_can_accept_request() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->accept_request() )
			->hasField( 'databaseId', $this->random_user );
	}

	public function test_group_member_can_not_accept_someone_else_invitation() {
		$u = $this->bp_factory->user->create();
		$this->bp->add_user_to_group( $u, $this->private_group_id );

		$this->bp->set_current_user( $u );

		$this->assertQueryFailed( $this->accept_request() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_non_group_member_can_not_accept_request() {
		$this->bp->set_current_user( $this->bp_factory->user->create() );

		$this->assertQueryFailed( $this->accept_request() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	/**
	 * Accept request mutation.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function accept_request( array $args = [] ): array {
		$query = '
			mutation acceptRequestTest(
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
				'databaseId'       => $this->request_id,
				'type'             => 'REQUEST',
			]
		);

		$operation_name = 'acceptRequestTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
