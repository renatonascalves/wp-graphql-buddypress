<?php
/**
 * Test_Member_Query Class.
 *
 * @group members
 */
class Test_Member_Query extends WPGraphQL_BuddyPress_UnitTestCase  {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_get_member_with_user_query() {

		// Set member type.
		bp_set_member_type( $this->user, 'foo' );

		// Add one friendship.
		friends_add_friend( $this->user, $this->random_user, true );

		$this->assertQuerySuccessful( $this->get_a_member() )
			->hasField( 'mentionName', bp_activity_get_user_mentionname( $this->user ) )
			->hasField( 'link', bp_core_get_user_domain( $this->user ) )
			->hasField( 'totalFriendCount', 1 )
			->hasField( 'latestUpdate', null )
			->hasField( 'attachmentAvatar', [ 'full'  => $this->get_avatar_image( 'full', 'user', absint( $this->user ) ) ] )
			->hasField( 'attachmentCover', null );
	}

	public function test_get_member_with_latest_update() {
		$this->bp->set_current_user( $this->user );

		bp_activity_post_update(
			[
				'type'    => 'activity_update',
				'user_id' => $this->user,
				'content' => 'The Joshua Tree',
			]
		);

		$this->assertQuerySuccessful( $this->get_a_member() )
			->hasField( 'latestUpdate', apply_filters( 'bp_get_activity_content', 'The Joshua Tree' ) )
			->hasField( 'totalFriendCount', null );
	}

	public function test_get_member_with_avatar_disabled() {
		$this->bp->set_current_user( $this->user );

		buddypress()->avatar->show_avatars = false;

		$this->assertQuerySuccessful( $this->get_a_member() )
			->hasField( 'attachmentAvatar', null );

		buddypress()->avatar->show_avatars = true;
	}

	/**
	 * Get a member.
	 *
	 * @param int|null $user_id User ID.
	 * @return array
	 */
	protected function get_a_member( $user_id = null ): array {
		$u         = $user_id ?? $this->user;
		$global_id = $this->toRelayId( 'user', (string) $u );
		$query     = "
			query {
				user(id: \"{$global_id}\") {
					link
					mentionName
					totalFriendCount
					latestUpdate
					attachmentAvatar {
						full
					}
					attachmentCover {
						full
					}
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
