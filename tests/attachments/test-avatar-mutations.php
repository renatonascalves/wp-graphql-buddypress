<?php

/**
 * Test_Attachment_Avatar_Mutations Class.
 *
 * @group attachment-avatar
 */
class Test_Attachment_Avatar_Mutations extends WP_UnitTestCase {

	public $admin;
	public $bp_factory;
	public $bp;
	public $client_mutation_id;

	public function setUp() {
		parent::setUp();

		$this->client_mutation_id = 'someUniqueId';
		$this->bp_factory         = new BP_UnitTest_Factory();
		$this->bp                 = new BP_UnitTestCase();
		$this->admin              = $this->factory->user->create(
			[
				'role'       => 'administrator',
				'user_email' => 'admin@example.com',
			]
		);
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function delete_avatar() {
		$u = $this->factory->user->create();

		$this->bp->set_current_user( $u );

		$mutation = '
		mutation deleteAvatarTest( $clientMutationId: String!, $objectId: Int ) {
			attachmentAvatarDelete(
		    	input: {
		      		clientMutationId: $clientMutationId
              		objectId: $objectId
		    	}
          	)
          	{
            	clientMutationId
            	deleted
          	}
        }
        ';

		$variables = [
			'clientMutationId' => $this->client_mutation_id,
			'objectId'          => $u,
		];

		$this->assertEquals(
			[
				'data' => [
					'attachmentAvatarDelete' => [
						'clientMutationId' => $this->client_mutation_id,
						'deleted'          => true,
					],
				],
			],
			do_graphql_request( $mutation, 'deleteAvatarTest', $variables )
		);
	}
}
