<?php

/**
 * Test_Attachment_deleteMemberAvatar_Mutation Class.
 *
 * @group attachment-avatar
 * @group member-avatar
 */
class Test_Attachment_deleteMemberAvatar_Mutation extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();
	}

	public function test_delete_member_avatar_without_permissions() {
		$this->bp->set_current_user( $this->random_user );

		$this->assertQueryFailed( $this->delete_avatar( 'USER', absint( $this->user ) ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to perform this action.' );
	}
}
