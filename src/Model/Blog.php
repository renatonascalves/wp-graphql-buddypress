<?php
/**
 * Blog Model Class
 *
 * @package WPGraphQL\Extensions\BuddyPress\Model
 * @since 0.0.1-alpha
 */

namespace WPGraphQL\Extensions\BuddyPress\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use WPGraphQL\Utils\Utils;
use stdClass;

/**
 * Class Blog - Models the data for the Blog object type.
 *
 * @property string $id ID.
 * @property int    $blogId Blog ID.
 * @property int    $admin ID of the group admin.
 * @property string $name Group name.
 * @property string $description Group description.
 * @property string $permalink Group permalink.
 * @property string $path Group path.
 * @property string $domain Group domain.
 * @property string $lastActivity Group's last activity.
 */
class Blog extends Model {

	/**
	 * Stores the Blog object for the incoming data.
	 *
	 * @var stdClass
	 */
	protected $data;

	/**
	 * Blog constructor.
	 *
	 * @param stdClass $blog The Blog object.
	 */
	public function __construct( stdClass $blog ) {
		$this->data = $blog;
		parent::__construct();
	}

	/**
	 * Initialize the Blog object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'id' => function() {
					return ! empty( $this->data->blog_id )
						? Relay::toGlobalId( 'blog', $this->data->blog_id )
						: null;
				},
				'blogId' => function() {
					return $this->data->blog_id ?? null;
				},
				'admin' => function() {
					return $this->data->admin_user_id ?? null;
				},
				'name' => function() {
					return $this->data->name ?? null;
				},
				'description' => function() {
					return $this->data->description ?? null;
				},
				'permalink' => function() {
					return $this->get_blog_domain( $this->data );
				},
				'path' => function() {
					return $this->data->path ?? null;
				},
				'domain' => function() {
					return $this->data->domain ?? null;
				},
				'lastActivity' => function() {
					return Utils::prepare_date_response( $this->data->last_activity );
				},
			];
		}
	}

	/**
	 * Get blog permalink.
	 *
	 * @param stdClass $blog Blog object.
	 * @return string|null
	 */
	protected function get_blog_domain( stdClass $blog ): ?string {

		// Bail early.
		if ( empty( $blog->domain ) && empty( $blog->path ) ) {
			return null;
		}

		if ( empty( $blog->domain ) && ! empty( $blog->path ) ) {
			return bp_get_root_domain() . $blog->path;
		}

		$protocol  = is_ssl() ? 'https://' : 'http://';
		$permalink = $protocol . $blog->domain . $blog->path;

		return apply_filters( 'bp_get_blog_permalink', $permalink );
	}
}
