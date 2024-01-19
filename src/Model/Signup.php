<?php
/**
 * Signup Model Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\Model;
use BP_Signup;

/**
 * Class Signup - Models the data for the Signup object type.
 *
 * @property string $id ID.
 * @property int    $databaseId Database ID.
 * @property string $userLogin User login.
 * @property string $userEmail User email.
 * @property string $userName User name.
 * @property string $registered Registered date.
 * @property string $registeredGmt Registered date as GMT.
 * @property string $dateSent Date sent.
 * @property string $dateSentGmt Date as GMT.
 * @property int    $countSent Count sent.
 * @property bool   $active Active status.
 * @property BP_Signup $blog Signup object.
 */
class Signup extends Model {

	/**
	 * Stores the Signup object for the incoming data.
	 *
	 * @var BP_Signup
	 */
	protected $data;

	/**
	 * Signup constructor.
	 *
	 * @param BP_Signup $signup The signup object.
	 */
	public function __construct( BP_Signup $signup ) {
		$this->data = $signup;
		parent::__construct();
	}

	/**
	 * Initialize the Activity object.
	 */
	protected function init(): void {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'            => function () {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'signup', (string) $this->data->id )
						: null;
				},
				'databaseId'    => function () {
					return ! empty( $this->data->id ) ? absint( $this->data->id ) : null;
				},
				'userLogin'     => function () {
					return ! empty( $this->data->user_login ) ? $this->data->user_login : null;
				},
				'userEmail'     => function () {
					return ! empty( $this->data->user_email ) ? $this->data->user_email : null;
				},
				'userName'      => function () {
					return ! empty( $this->data->user_name ) ? $this->data->user_name : null;
				},
				'blog'          => function () {
					return $this->data;
				},
				'active'        => function () {
					return wp_validate_boolean( $this->data->active );
				},
				'registered'    => function () {
					return Utils::prepare_date_response( $this->data->registered, get_date_from_gmt( $this->data->registered ) );
				},
				'registeredGmt' => function () {
					return Utils::prepare_date_response( $this->data->registered );
				},
				'dateSent'      => function () {
					return Utils::prepare_date_response( $this->data->date_sent, get_date_from_gmt( $this->data->date_sent ) );
				},
				'dateSentGmt'   => function () {
					return Utils::prepare_date_response( $this->data->date_sent );
				},
				'countSent'     => function () {
					return ! empty( $this->data->count_sent ) ? $this->data->count_sent : null;
				},
			];
		}
	}
}
