<?php

use GraphQLRelay\Relay;

class groupByTest extends \Codeception\TestCase\WPTestCase {

	public function setUp() {
		parent::setUp();

		$this->user = $this->factory->user->create();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @group get_item
	 */
	public function testgroupBy() {
		$id = Relay::toGlobalId( 'group', 'id' );

		$query = '
			query {
				groupBy( id: $id ) {
					id
					name
					groupId
					enableForum
					description
					link
					dateCreated
					status
					lastActivity
					totalMemberCount
				}
			}
		';
	}
}
