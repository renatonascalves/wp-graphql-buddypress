<?php

/**
 * Test_Invitation_createGroupInvitation_Mutation Class.
 *
 * @group invite
 * @group invitation
 * @group groups
 */
class Test_Invitation_createGroupInvitation_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Inviter ID.
	 *
	 * @var int
	 */
	public $inviter;

	/**
	 * Invitee ID.
	 *
	 * @var int
	 */
	public $invitee;

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

		$this->inviter          = $this->bp_factory->user->create();
		$this->invitee          = $this->bp_factory->user->create();
		$this->private_group_id = $this->bp_factory->group->create(
			[
				'creator_id' => $this->user_id,
				'status'     => 'private',
			]
		);

		$this->bp->add_user_to_group( $this->inviter, $this->private_group_id );
	}

	public function test_invite_user_to_group() {
		$this->bp->set_current_user( $this->inviter );

		$response = $this->invite_user_to_group();

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
							'type'       => strtoupper( $invite->type ),
							'accepted'   => $invite->accepted,
							'inviteSent' => $invite->invite_sent,
							'invitee'    => [
								'databaseId' => $this->invitee,
							],
							'inviter'    => [
								'databaseId' => $this->inviter,
							],
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

	public function test_group_creator_invite_user_to_group() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQuerySuccessful( $this->invite_user_to_group() );
	}

	public function test_invite_user_to_group_with_invalid_invitee_id() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->invite_user_to_group( [ 'userId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'Invalid member ID.' );
	}

	public function test_invite_user_to_group_with_invalid_inviter_id() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->invite_user_to_group( [ 'inviterId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'Invalid member ID.' );
	}

	public function test_invite_user_to_group_with_invalid_group_id() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->invite_user_to_group( [ 'itemId' => GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ] ) )
			->expectedErrorMessage( 'Invalid group ID.' );
	}

	public function test_group_moderator_invite_user_to_group() {
		$u = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_mod' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->invite_user_to_group() );
	}

	public function test_group_admin_invite_user_to_group() {
		$u = $this->bp_factory->user->create();

		$this->bp->add_user_to_group( $u, $this->private_group_id, [ 'is_admin' => true ] );

		$this->bp->set_current_user( $u );

		$this->assertQuerySuccessful( $this->invite_user_to_group() );
	}

	public function test_invite_yourself_to_group() {
		$this->bp->set_current_user( $this->inviter );

		$this->assertQueryFailed( $this->invite_user_to_group( [ 'userId' => $this->inviter ] ) )
			->expectedErrorMessage( 'Invalid member ID.' );
	}

	public function test_invite_user_to_group_user_not_logged_in() {
		$this->assertQueryFailed( $this->invite_user_to_group() )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}

	public function test_invite_user_to_group_with_invalid_type() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->invite_user_to_group( [ 'type' => 'random-status' ] ) )
			->expectedErrorMessage( 'Variable "$type" got invalid value "random-status"; Expected type InvitationTypeEnum.' );
	}

	public function test_invite_user_to_group_with_null_user_id() {
		$this->bp->set_current_user( $this->user_id );

		$this->assertQueryFailed( $this->invite_user_to_group( [ 'userId' => null ] ) )
			->expectedErrorMessage( 'Variable "$userId" of non-null type "Int!" must not be null.' );
	}

	/**
	 * Invite user to group.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	protected function invite_user_to_group( array $args = [] ): array {
		$query = '
			mutation createGroupInvitationTest(
				$clientMutationId:String!
				$message:String
				$userId:Int!
				$itemId:Int!
				$inviterId:Int
				$sendInvite:Boolean
				$type:InvitationTypeEnum!
			) {
				createInvitation(
					input: {
						clientMutationId: $clientMutationId
						message: $message
						userId: $userId
						inviterId: $inviterId
						itemId: $itemId
						sendInvite: $sendInvite
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
				'message'          => 'Message',
				'type'             => 'INVITE',
				'sendInvite'       => true,
				'userId'           => $this->invitee,
				'inviterId'        => $this->inviter,
				'itemId'           => $this->private_group_id,
			]
		);

		$operation_name = 'createGroupInvitationTest';

		return $this->graphql( compact( 'query', 'operation_name', 'variables' ) );
	}
}
