<?php
/**
 * Test_Activity_activityBy_Queries Class.
 *
 * @group activity
 */
class Test_Activity_activityBy_Queries extends WPGraphQL_BuddyPress_UnitTestCase {

	/**
	 * Global ID.
	 *
	 * @var int
	 */
	public $global_id;

	/**
	 * Activity object.
	 *
	 * @var stdClass
	 */
	public $activity;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->activity  = $this->create_activity_id();
		$this->global_id = $this->toRelayId( 'activity', (string) $this->activity );
	}

	public function test_activity_query() {
		$a = $this->create_activity_id( [ 'content' => 'Foo' ] );

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->get_an_activity( $a ) )
			->hasField( 'id', $this->toRelayId( 'activity', (string) $a ) )
			->hasField( 'databaseId', $a )
			->hasField( 'parentDatabaseId', 0 )
			->hasField( 'primaryItemId', 0 )
			->hasField( 'secondaryItemId', 0 )
			->hasField( 'parentId', null )
			->hasField( 'content', 'Foo' )
			->hasField( 'status', 'PUBLISHED' )
			->hasField( 'type', 'ACTIVITY_UPDATE' )
			->hasField( 'isFavorited', false )
			->hasField( 'uri', bp_activity_get_permalink( $a ) )
			->hasField( 'component', 'ACTIVITY' )
			->hasField( 'creator', [ 'userId' => $this->admin ] )
			->hasField( 'hidden', false );
	}

	public function test_activity_comments() {
		$a = $this->create_activity_id(
			[
				'component' => 'activity',
				'content'   => 'Foo',
				'type'      => 'activity_update',
			]
		);

		$c = bp_activity_new_comment(
			[
				'type'        => 'activity_comment',
				'user_id'     => $this->user,
				'activity_id' => $a, // Root activity
				'content'     => 'Activity comment',
			]
		);

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->get_an_activity( $a ) )
			->hasField( 'id', $this->toRelayId( 'activity', (string) $a ) )
			->hasField( 'databaseId', $a )
			->hasField( 'parentDatabaseId', 0 )
			->hasField( 'primaryItemId', 0 )
			->hasField( 'secondaryItemId', 0 )
			->hasField( 'parentId', null )
			->hasField( 'content', 'Foo' )
			->hasField( 'status', 'PUBLISHED' )
			->hasField( 'type', 'ACTIVITY_UPDATE' )
			->hasField( 'isFavorited', false )
			->hasField( 'uri', bp_activity_get_permalink( $a ) )
			->hasField( 'component', 'ACTIVITY' )
			->hasField( 'creator', [ 'userId' => $this->admin ] )
			->hasField(
				'comments',
				[
					'nodes' => [
						0 => [
							'id'               => $this->toRelayId( 'activity', (string) $c ),
							'databaseId'       => $c,
							'parentDatabaseId' => $a,
							'parentId'         => $this->toRelayId( 'activity', (string) $a ),
							'type'             => 'ACTIVITY_COMMENT',
							'primaryItemId'    => $a,
							'secondaryItemId'  => $a,
						]
					]
				]
			)
			->hasField( 'hidden', false );
	}

	public function test_activity_by_query_with_id_param() {
		$this->bp->set_current_user( $this->admin );

		$query = "
			query {
				activityBy(id: \"{$this->global_id}\") {
					databaseId
				}
			}
		";

		$this->assertQuerySuccessful( $this->graphql( compact( 'query' ) ) )
			->hasField( 'databaseId', $this->activity );
	}

	public function test_get_activity_with_invalid_activity_id() {
		$this->assertQueryFailed( $this->get_an_activity( GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER ) )
			->expectedErrorMessage( 'This activity does not exist.' );
	}

	public function test_get_activity_with_unkown_id_argument() {
		$id    = GRAPHQL_TESTS_IMPOSSIBLY_HIGH_NUMBER;
		$query = "{
			activityBy(groupID: \"{$id}\") {
				id
			}
		}";

		$this->assertQueryFailed( $this->graphql( compact( 'query' ) ) )
			->expectedErrorMessage( 'Unknown argument "groupID" on field "activityBy" of type "RootQuery".' );
	}

	public function test_get_group_activity_without_access() {
		$g1 = $this->create_group_id( [ 'status' => 'private' ] );
		$a1 = $this->create_activity_id(
			[
				'component'     => buddypress()->groups->id,
				'type'          => 'created_group',
				'user_id'       => $this->admin,
				'item_id'       => $g1,
				'hide_sitewide' => true,
			]
		);

		$this->bp->set_current_user( $this->user );

		$this->assertQueryFailed( $this->get_an_activity( $a1 ) )
			->expectedErrorMessage( 'Sorry, you are not allowed to see this activity.' );
	}

	public function test_group_activity() {
		$g1 = $this->create_group_id( [ 'status' => 'private' ] );
		$a1 = $this->create_activity_id(
			[
				'component'     => buddypress()->groups->id,
				'content'       => 'Foo',
				'type'          => 'created_group',
				'user_id'       => $this->admin,
				'item_id'       => $g1,
				'hide_sitewide' => true,
			]
		);

		$this->bp->set_current_user( $this->admin );

		$this->assertQuerySuccessful( $this->get_an_activity( $a1 ) )
			->hasField( 'id', $this->toRelayId( 'activity', (string) $a1 ) )
			->hasField( 'databaseId', $a1 )
			->hasField( 'parentDatabaseId', 0 )
			->hasField( 'primaryItemId', $g1 )
			->hasField( 'secondaryItemId', 0 )
			->hasField( 'parentId', null )
			->hasField( 'content', 'Foo' )
			->hasField( 'status', 'PUBLISHED' )
			->hasField( 'type', 'CREATED_GROUP' )
			->hasField( 'isFavorited', false )
			->hasField( 'uri', bp_activity_get_permalink( $a1 ) )
			->hasField( 'component', 'GROUPS' )
			->hasField( 'creator', [ 'userId' => $this->admin ] )
			->hasField( 'hidden', true );
	}

	/**
	 * Get an activity.
	 *
	 * @param int|null $activity_id Activity ID.
	 * @return array
	 */
	protected function get_an_activity( $activity_id = null ): array {
		$activity = $activity_id ?? $this->activity;
		$query    = "
			query {
				activityBy(activityId: {$activity}) {
					id,
					parentDatabaseId
					parentId
					databaseId
					status
					content(format: RAW)
					uri
					type
					primaryItemId
					secondaryItemId
					isFavorited
					hidden
					component
					creator {
						userId
					}
					comments {
						nodes {
							id
							databaseId
							parentDatabaseId
							parentId
							type
							primaryItemId
							secondaryItemId
						}
					}
				}
			}
		";

		return $this->graphql( compact( 'query' ) );
	}
}
