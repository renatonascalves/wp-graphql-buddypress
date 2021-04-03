<?php

/**
 * Test_Friendship_Create_Mutation Class.
 *
 * @group friends
 */
class Test_Friendship_Create_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	public static $bp_factory;
	public static $user;
	public static $bp;
	public $client_mutation_id;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$bp         = new BP_UnitTestCase();
		self::$bp_factory = new BP_UnitTest_Factory();
		self::$user       = self::factory()->user->create();
	}

	public function setUp() {
		parent::setUp();

		$this->client_mutation_id = 'someUniqueId';
	}

	public function test_create_friendship() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();

		self::$bp->set_current_user( $u1 );

		$this->assertEquals(
			[
				'data' => [
					'createFriendship' => [
						'clientMutationId' => $this->client_mutation_id,
						'friendship' => [
							'isConfirmed' => false,
							'initiator' => [
								'userId' => $u1,
							],
							'friend' => [
								'userId' => $u2,
							],
						],
					],
				],
			],
			$this->create_friendship( $u1, $u2 )
		);
	}

	public function test_create_friendship_but_already_has_friendship_request() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();

		self::$bp->set_current_user( $u1 );

		$this->assertEquals(
			[
				'data' => [
					'createFriendship' => [
						'clientMutationId' => $this->client_mutation_id,
						'friendship' => [
							'isConfirmed' => false,
							'initiator' => [
								'userId' => $u1,
							],
							'friend' => [
								'userId' => $u2,
							],
						],
					],
				],
			],
			$this->create_friendship( $u1, $u2 )
		);

		// Already friends.
		$this->assertQueryFailed( $this->create_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'You already have a pending friendship request with this user.' );
	}

	public function test_user_can_not_create_friendship_to_other_people() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();
		$u3 = self::$bp_factory->user->create();

		self::$bp->set_current_user( $u3 );

		$this->assertQueryFailed( $this->create_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_create_friendship_user_not_logged_id() {
		$u1 = self::$bp_factory->user->create();
		$u2 = self::$bp_factory->user->create();

		$this->assertQueryFailed( $this->create_friendship( $u1, $u2 ) )
			->expectedErrorMessage( 'Sorry, you do not have permission to perform this action.' );
	}

	public function test_create_friendship_invalid_user() {
		$this->assertQueryFailed( $this->create_friendship( self::$bp_factory->user->create(), 111 ) )
			->expectedErrorMessage( 'There was a problem confirming if user is valid.' );
	}

	protected function create_friendship_object( $u = 0, $a = 0 ) {
		if ( empty( $u ) ) {
			$u = self::factory()->user->create();
		}

		if ( empty( $a ) ) {
			$a = self::factory()->user->create();
		}

		$friendship                    = new BP_Friends_Friendship();
		$friendship->initiator_user_id = $u;
		$friendship->friend_user_id    = $a;
		$friendship->is_confirmed      = 0;
		$friendship->date_created      = bp_core_current_time();
		$friendship->save();

		return $friendship->id;
	}
}
