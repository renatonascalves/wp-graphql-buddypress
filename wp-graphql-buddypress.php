<?php
/**
 * WPGraphQL BuddyPress
 *
 * @package  WPGraphQL\Extensions\BuddyPress
 * @author   Renato Alves
 * @version  0.0.1-alpha
 * @license  GPL-3
 *
 * @wordpress-plugin
 * Plugin Name:       WPGraphQL BuddyPress
 * Plugin URI:        https://github.com/wp-graphql/wp-graphql-buddypress
 * GitHub Plugin URI: https://github.com/wp-graphql/wp-graphql-buddypress
 * Description:       Adds BuddyPress functionality to the WPGraphQL schema.
 * Version:           0.0.1-alpha
 * Author:            Renato Alves
 * Author URI:        https://ralv.es
 * Text Domain:       wp-graphql-buddypress
 * Domain Path:       /languages/
 * Requires PHP:      7.1
 * Requires WP:       4.9
 * Tested up to:      5.8
 * License:           GPL-3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_GraphQL_BuddyPress' ) ) :

	/**
	 * This is the one true WP_GraphQL_BuddyPress class
	 */
	final class WP_GraphQL_BuddyPress {

		/**
		 * Stores the instance of the WP_GraphQL_BuddyPress class
		 *
		 * @since 0.0.1-alpha
		 *
		 * @var WP_GraphQL_BuddyPress The one true WP_GraphQL_BuddyPress
		 */
		private static $instance;

		/**
		 * The instance of the WP_GraphQL_BuddyPress object
		 *
		 * @since 0.0.1-alpha
		 *
		 * @return WP_GraphQL_BuddyPress The one true WP_GraphQL_BuddyPress
		 */
		public static function instance(): self {

			if ( ! isset( self::$instance ) && ! ( is_a( self::$instance, __CLASS__ ) ) ) {
				self::$instance = new self();
				self::$instance->setup_constants();
				self::$instance->dependencies();
				if ( self::$instance->includes() ) {
					self::$instance->actions();
					self::$instance->filters();
				}
			}

			/**
			 * Fire off init action.
			 *
			 * @param WP_GraphQL_BuddyPress $instance The instance of the WP_GraphQL_BuddyPress class
			 */
			do_action( 'graphql_buddypress_init', self::$instance );

			// Return the WP_GraphQL_BuddyPress Instance.
			return self::$instance;
		}

		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single object
		 * therefore, we don't want the object to be cloned.
		 *
		 * @since 0.0.1-alpha
		 */
		public function __clone() {

			// Cloning instances of the class is forbidden.
			_doing_it_wrong(
				__FUNCTION__,
				esc_html__(
					'The WP_GraphQL_BuddyPress class should not be cloned.',
					'wp-graphql-buddypress'
				),
				'0.0.1-alpha'
			);
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since 0.0.1-alpha
		 */
		public function __wakeup() {

			// De-serializing instances of the class is forbidden.
			_doing_it_wrong(
				__FUNCTION__,
				esc_html__(
					'De-serializing instances of the WP_GraphQL_BuddyPress class is not allowed.',
					'wp-graphql-buddypress'
				),
				'0.0.1-alpha'
			);
		}

		/**
		 * Setup plugin constants.
		 *
		 * @since 0.0.1-alpha
		 */
		private function setup_constants(): void {

			// Plugin version.
			if ( ! defined( 'WPGRAPHQL_BUDDYPRESS_VERSION' ) ) {
				define( 'WPGRAPHQL_BUDDYPRESS_VERSION', '0.0.1-alpha' );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPGRAPHQL_BUDDYPRESS_PLUGIN_DIR' ) ) {
				define( 'WPGRAPHQL_BUDDYPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPGRAPHQL_BUDDYPRESS_PLUGIN_URL' ) ) {
				define( 'WPGRAPHQL_BUDDYPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WPGRAPHQL_BUDDYPRESS_PLUGIN_FILE' ) ) {
				define( 'WPGRAPHQL_BUDDYPRESS_PLUGIN_FILE', __FILE__ );
			}

			// Whether to autoload the files or not.
			if ( ! defined( 'WPGRAPHQL_BUDDYPRESS_AUTOLOAD' ) ) {
				define( 'WPGRAPHQL_BUDDYPRESS_AUTOLOAD', true );
			}
		}

		/**
		 * Uses composer's autoload to include required files.
		 *
		 * @since 0.0.1-alpha
		 *
		 * @return bool
		 */
		private function includes(): bool {

			// Autoload Required Classes.
			if ( defined( 'WPGRAPHQL_BUDDYPRESS_AUTOLOAD' ) && false !== WPGRAPHQL_BUDDYPRESS_AUTOLOAD ) {

				if ( file_exists( WPGRAPHQL_BUDDYPRESS_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
					// Autoload Required Classes.
					require_once WPGRAPHQL_BUDDYPRESS_PLUGIN_DIR . 'vendor/autoload.php';
				}

				// Bail if installed incorrectly.
				if ( ! class_exists( '\WPGraphQL\Extensions\BuddyPress\TypeRegistry' ) ) {
					add_action( 'admin_notices', array( $this, 'wp_graphql_buddypress_missing_notice' ) );
					return false;
				}
			}

			return true;
		}

		/**
		 * Class dependencies.
		 *
		 * @since 0.0.1-alpha
		 */
		private function dependencies(): void {

			// Checks if BuddyPress is installed.
			if ( ! class_exists( 'BuddyPress' ) ) {
				add_action( 'admin_notices', array( $this, 'buddypress_missing_notice' ) );
				return;
			}

			// Checks if WPGraphQL is installed.
			if ( ! class_exists( 'WPGraphQL' ) ) {
				add_action( 'admin_notices', array( $this, 'wpgraphql_missing_notice' ) );
				return;
			}
		}

		/**
		 * WPGraphQL BuddyPress missing notice.
		 *
		 * @since 0.0.1-alpha
		 */
		public function wp_graphql_buddypress_missing_notice(): void {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			?>
			<div class="notice notice-error">
				<p>
					<?php esc_html_e( 'WPGraphQL BuddyPress appears to have been installed without its dependencies. It will not work properly until dependencies are installed. This likely means you have cloned WPGraphQL BuddyPress from Github and need to run the command `composer install`.', 'wp-graphql-buddypress' ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * BuddyPress missing notice.
		 *
		 * @since 0.0.1-alpha
		 */
		public function buddypress_missing_notice(): void {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			?>
			<div class="notice notice-error">
				<p><strong><?php esc_html_e( 'WP GraphQL BuddyPress', 'wp-graphql-buddypress' ); ?></strong> <?php esc_html_e( 'depends on the lastest version of Buddypress to work!', 'wp-graphql-buddypress' ); ?></p>
			</div>
			<?php
		}

		/**
		 * WPGraphQL missing notice.
		 *
		 * @since 0.0.1-alpha
		 */
		public function wpgraphql_missing_notice(): void {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			?>
			<div class="notice notice-error">
				<p><strong><?php esc_html_e( 'WP GraphQL BuddyPress', 'wp-graphql-buddypress' ); ?></strong> <?php esc_html_e( 'depends on the lastest version of WPGraphQL to work!', 'wp-graphql-buddypress' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Sets up actions.
		 *
		 * @since 0.0.1-alpha
		 */
		private function actions(): void {

			// Setup actions.
			\WPGraphQL\Extensions\BuddyPress\TypeRegistry::add_actions();
		}

		/**
		 * Set up filters.
		 *
		 * @since 0.0.1-alpha
		 */
		private function filters(): void {

			// Setup filters.
			\WPGraphQL\Extensions\BuddyPress\TypeRegistry::add_filters();

			/**
			 * Allow regular BuddyPress members to delete their own account, if allowed.
			 *
			 * Multisite uses this hook.
			 */
			add_filter(
				'map_meta_cap',
				function( $caps, $cap, $user_id ) {

					// Apply to GraphQL request only.
					if ( false === is_graphql_request() ) {
						return $caps;
					}

					// Check for settings to confirm if users can delete their own accounts.
					if ( true === bp_disable_account_deletion() ) {
						return $caps;
					}

					// Check if user is logged in.
					if ( false === is_user_logged_in() ) {
						return $caps;
					}

					// Confirm user to check against with logged in user.
					if ( bp_loggedin_user_id() !== $user_id ) {
						return $caps;
					}

					foreach ( $caps as $key => $capability ) {
						if ( 'do_not_allow' !== $capability ) {
							continue;
						}

						switch ( $cap ) {
							case 'delete_user':
							case 'delete_users':
								$caps[ $key ] = 'delete_users';
								break;
						}
					}

					return $caps;
				},
				1,
				3
			);

			/**
			 * Allow regular BuddyPress members to delete their own account, if allowed.
			 */
			add_filter(
				'user_has_cap',
				function ( $caps, $cap, $args ) {

					// Apply to GraphQL request only.
					if ( false === is_graphql_request() ) {
						return $caps;
					}

					// Required field.
					if ( empty( $args[0] ) ) {
						return $caps;
					}

					// Bail if not checking the 'delete_users' cap.
					if ( 'delete_users' !== $args[0] ) {
						return $caps;
					}

					// Bail if already with permissions (eg.: admins).
					if ( isset( $caps['delete_users'] ) && true === $caps['delete_users'] ) {
						return $caps;
					}

					// Check for settings to confirm if users can delete their own accounts.
					if ( true === bp_disable_account_deletion() ) {
						return $caps;
					}

					// Check if user is logged in.
					if ( false === is_user_logged_in() ) {
						return $caps;
					}

					// Confirm user to check against with logged in user.
					if ( isset( $args[2] ) && bp_loggedin_user_id() !== absint( $args[2] ) ) {
						return $caps;
					}

					// Allow for this one.
					$caps[ $args[0] ] = true;

					return $caps;
				},
				10,
				3
			);
		}
	}

endif;

/**
 * Function that instantiates the plugin main class.
 *
 * @since 0.0.1-alpha
 *
 * @return WP_GraphQL_BuddyPress The one true WP_GraphQL_BuddyPress
 */
function wp_graphql_buddypress_init(): \WP_GraphQL_BuddyPress {

	// Return an instance of the action.
	return \WP_GraphQL_BuddyPress::instance();
}
add_action( 'graphql_init', 'wp_graphql_buddypress_init' );
