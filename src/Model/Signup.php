<?php
/**
 * Signup Model Class.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Model\Model;
use BP_Signup;
use WPGraphQL\Extensions\BuddyPress\Data\SignupHelper;

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
 * @property int    $blogId Blog ID.
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
	 * Method for determining if the data should be considered private or not.
	 *
	 * @return bool
	 */
	protected function is_private(): bool {

		if ( SignupHelper::can_see() ) {
			return false;
		}

		return true;
	}

	/**
	 * Initialize the Activity object.
	 */
	protected function init() : void {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'            => function() {
					return ! empty( $this->data->id )
						? Relay::toGlobalId( 'signup', (string) $this->data->id )
						: null;
				},
				'databaseId'    => function() {
					return ! empty( $this->data->id ) ? absint( $this->data->id ) : null;
				},
				'userLogin'     => function() {
					return $this->data->user_login ?? null;
				},
				'userEmail'     => function() {
					return $this->data->user_email ?? null;
				},
				'userName'      => function() {
					return $this->data->user_name ?? null;
				},
				// @todo Pending implementation.
				'blogId'        => function() {
					return 0;
				},
				'registered'    => function() {
					return Utils::prepare_date_response( $this->data->registered, get_date_from_gmt( $this->data->registered ) );
				},
				'registeredGmt' => function() {
					return Utils::prepare_date_response( $this->data->registered );
				},
				'dateSent'      => function() {
					return Utils::prepare_date_response( $this->data->date_sent, get_date_from_gmt( $this->data->date_sent ) );
				},
				'dateSentGmt'   => function() {
					return Utils::prepare_date_response( $this->data->date_sent );
				},
				'countSent'     => function() {
					return $this->data->count_sent ?? null;
				},
			];
		}
	}
}
