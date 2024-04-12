<?php
/**
 * Blog Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use WPGraphQL\Utils\Utils;
use WPGraphQL\Extensions\BuddyPress\Data\BlogHelper;
use stdClass;
use BP_Signup;

/**
 * Class Blog - Models the data for the Blog object type.
 *
 * @property string $id ID.
 * @property int    $databaseId Blog ID.
 * @property int    $admin ID of the blog admin.
 * @property string $name Blog name.
 * @property string $description Blog description.
 * @property string $uri Blog permalink.
 * @property string $path Blog path.
 * @property string $domain Blog domain.
 * @property string $lastActivity Blog's last activity.
 * @property bool   $public Blog status.
 * @property string $language Blog language.
 * @property int    $latestPostId Latest post ID from Blog.
 */
class Blog extends Model {

	/**
	 * Stores the Blog object for the incoming data.
	 *
	 * @var stdClass|BP_Signup
	 */
	protected $data;

	/**
	 * Blog constructor.
	 *
	 * @param stdClass|BP_Signup $blog The Blog or signup object.
	 */
	public function __construct( $blog ) {
		$this->data = $blog;
		parent::__construct();
	}

	/**
	 * Method for determining if the data should be considered private or not.
	 *
	 * @return bool
	 */
	protected function is_private(): bool {

		// If the visibility is set to members only, make the object private.
		if ( ! bp_current_user_can( 'bp_view', [ 'bp_component' => 'blogs' ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize the Blog object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id'           => function () {
					return ! empty( $this->data->blog_id )
						? Relay::toGlobalId( 'blog', $this->data->blog_id )
						: null;
				},
				'databaseId'   => function () {
					return $this->data->blog_id ?? null;
				},
				'admin'        => function () {
					return $this->data->admin_user_id ?? null;
				},
				'name'         => function () {
					return $this->data->name ?? $this->data->title ?? null;
				},
				'description'  => function () {
					return $this->data->description ?? null;
				},
				'uri'          => function () {
					return BlogHelper::get_blog_uri( $this->data );
				},
				'path'         => function () {
					return $this->data->path ?? null;
				},
				'domain'       => function () {
					return $this->data->domain ?? null;
				},
				'lastActivity' => function () {
					return Utils::prepare_date_response( $this->data->last_activity );
				},
				'public'       => function () {
					return wp_validate_boolean( $this->data->meta['public'] );
				},
				'language'     => function () {
					return $this->data->meta['WPLANG'] ?? null;
				},
				// @todo Pending implementation.
				'latestPostId' => function () {
					return $this->data->latest_post->ID ?? null;
				},
			];
		}
	}
}
