<?php

/**
 * Test_Invitation_createGroupMembershipRequest_Mutation Class.
 *
 * @group invite
 * @group invitation
 * @group group-membership
 * @group groups
 */
class Test_Invitation_createGroupMembershipRequest_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Requester ID.
	 *
	 * @var int
	 */
	public $requester_id;

	/**
	 * Private Group ID.
	 *
	 * @var int
	 */
	public $private_group_id;

	/**
	 * Set up.
	 */
	public function setUp() : void {
		parent::setUp();

		$this->requester_id     = $this->bp_factory->user->create();
		$this->private_group_id = $this->bp_factory->group->create(
			[
				'creator_id' => $this->user_id,
				'status'     => 'private',
			]
		);
	}

	public function test_request_membership_to_group() {
		$this->bp->set_current_user( $this->requester_id );

		$response = $this->request_membership_to_group();

		$this->assertQuerySuccessful( $response );

		$invite = new BP_Invitation( $response['data']['createInvitation']['invite']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createInvitation' => [
						'clientMutationId' => $this->client_mutation_id,
						'invite'           => [
							'id'         => $this->toRelayId( 'invitation', (string) $invite->id ),
							'databaseId' => $invite->id,
							'itemId'     => $invite->item_id,
							'type'       => 'REQUEST',
							'accepted'   => $invite->accepted,
							'inviteSent' => $invite->invite_sent,
							'invitee'    => [
								'databaseId' => $this->requester_id,
							],
							'inviter'    => null,
							'group'      => null,
							'message'    => $invite->content,
						],
					],
				],
			],
			$response
		);
	}

	public function test_admin_can_request_membership_to_group_from_another_user() {
		$this->bp->set_current_user( $this->admin );

		$response = $this->request_membership_to_group();

		$this->assertQuerySuccessful( $response );

		$invite = new BP_Invitation( $response['data']['createInvitation']['invite']['databaseId'] );

		$this->assertEquals(
			[
				'data' => [
					'createInvitation' => [
						'clientMutationId' => $this->client_mutation_id,
						'invite'           => [
							'id'         => $this->toRelayId( 'invitation', (string) $invite->id ),
							'databaseId' => $invite->id,
							'itemId'     => $invite->item_id,
							'type'       => 'REQUEST',
							'accepted'   => $invite->accepted,
							'inviteSent' => $invite->invite_sent,
							'invitee'    => [
								'databaseId' => $this->requester_id,
							],
							'inviter'    => null,
							'group'      => [
								'databaseId' => $this->private_group_id,
							],
							'message'    => $invite->content,
						],
					],
				],
			],
			$response
		);
	}

	public function test_request_membership_to_group_with_duplicate_requests() {
		groups_send_membership_request(
			[
				'group_id' => $this->private_group_id,
				'user_id'  => $this->requester_id,
			]
		);

		$this->bp->set_current_user( $this->requester_id );

		$this->assertQueryFailed( $this->request_membership_to_group() )
			->expectedErrorMessage( 'There is already a request to this member.' );
	}

	public function test_admin_request_membership_to_group_with_duplicate_requests() {
		groups_send_membership_request(
			[
				'group_id' => $this->private_group_id,
				'user_id'  => $this->requester_id,
			]
		);

		$this->bp->set_current_user( $this->admin );

		$this->assertQueryFailed( $this->request_membership_to_group() )
			->expectedErrorMessage( 'There is already a request to this member.' );
	}

	public function test_group_creator_request_membership_to_group() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->request_membership_to_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_request_membership_to_group_with_invalid_invitee_id() {
		$this->bp->set_current_user( $this->requester_id );

		$this->assertQueryFailed( $this->request_membership_to_group( [ 'userId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'Invalid member ID.' );
	}

	public function test_request_membership_to_group_with_invalid_group_id() {
		$this->bp->set_current_user( $this->requester_id );

		$this->assertQueryFailed( $this->request_membership_to_group( [ 'itemId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'Invalid group ID.' );
	}

	public function test_group_moderator_request_membership_to_group() {
		$u = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQueryFailed( $this->request_membership_to_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_group_admin_request_membership_to_group() {
		$u = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQueryFailed( $this->request_membership_to_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_request_membership_to_group_user_not_logged_in() {
		$this->assertQueryFailed( $this->request_membership_to_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_request_membership_to_group_with_invalid_type() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->request_membership_to_group( [ 'type' => 'random-status' ] ) )
			->expectedErrorMessage( 'Variable "$type" got invalid value "random-status"; Expected type InvitationTypeEnum.' );
	}

	public function test_request_membership_to_group_with_null_user_id() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->request_membership_to_group( [ 'userId' => null ] ) )
			->expectedErrorMessage( 'Variable "$userId" of non-null type "Int!" must not be null.' );
	}

	/**
	 * Request membership to a group.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function request_membership_to_group( array $args = [] ): array {
		$query = '
			mutation createGroupMembershipRequestTest(
				$clientMutationId:String!
				$message:String
				$userId:Int!
				$itemId:Int!
				$type:InvitationTypeEnum!
			) {
				createInvitation(
					input: {
						clientMutationId: $clientMutationId
						message: $message
						userId: $userId
						itemId: $itemId
						type: $type
					}
				)
				{
					clientMutationId
					invite {
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
						message (format: RAW)
					}
				}
			}
		';

		$variables = wp_parse_args(
			$args,
			[
				'clientMutationId' => $this->client_mutation_id,
				'message'          => 'Request',
				'type'             => 'REQUEST',
				'userId'           => $this->requester_id,
				'itemId'           => $this->private_group_id,
			]
		);

		return $this->graphql( compact( 'query', 'variables' ) );
	}
}
