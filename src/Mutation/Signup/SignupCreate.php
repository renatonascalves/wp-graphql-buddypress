<?php
/**
 * SignupCreate Mutation.
 *
 * @package WPGraphQL\Extensions\BuddyPress\Mutation\Signup
 * @since 0.1.0
 */

namespace WPGraphQL\Extensions\BuddyPress\Mutation\Signup;

use GraphQL\Error\UserError;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\BuddyPress\Data\Factory;
use WPGraphQL\Extensions\BuddyPress\Data\SignupHelper;
use BP_Signup;

/**
 * SignupCreate Class.
 */
class SignupCreate {

	/**
	 * Registers the SignupCreate mutation.
	 */
	public static function register_mutation(): void {
		register_graphql_mutation(
			'createSignup',
			[
				'inputFields'         => self::get_input_fields(),
				'outputFields'        => self::get_output_fields(),
				'mutateAndGetPayload' => self::mutate_and_get_payload(),
			]
		);
	}

	/**
	 * Defines the mutation input field configuration.
	 *
	 * @return array
	 */
	public static function get_input_fields(): array {
		$multisite_fields = [];
		$fields           = [
			'userLogin' => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'The login for the new member.', 'wp-graphql-buddypress' ),
			],
			'userEmail' => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'The email address for the new member.', 'wp-graphql-buddypress' ),
			],
			'userName'  => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'Full name for the new member.', 'wp-graphql-buddypress' ),
			],
			'password'  => [
				'type'        => [ 'non_null' => 'String' ],
				'description' => __( 'The password for the new member.', 'wp-graphql-buddypress' ),
			],
		];

		if ( is_multisite() ) {
			$multisite_fields = [
				'siteName'     => [
					'type'        => 'String',
					'description' => __( 'Unique name (slug) of the new member\'s child site.', 'wp-graphql-buddypress' ),
				],
				'siteTitle'    => [
					'type'        => 'String',
					'description' => __( 'Title of the new member\'s child site.', 'wp-graphql-buddypress' ),
				],
				'sitePublic'   => [
					'type'        => 'Boolean',
					'description' => __( 'Search engine visibility of the new member\'s site. true to be visible, false otherwise.', 'wp-graphql-buddypress' ),
				],
				'siteLanguage' => [
					'type'        => 'SiteLanguagesEnum',
					'description' => __( 'Language to use for the new member\'s site. Default: the Network main site\'s locale (eg: en_US)', 'wp-graphql-buddypress' ),
				],
			];
		}

		return array_merge( $fields, $multisite_fields );
	}

	/**
	 * Defines the mutation output field configuration.
	 *
	 * @return array
	 */
	public static function get_output_fields(): array {
		return [
			'signup' => [
				'type'        => 'Signup',
				'description' => __( 'The signup object that was created.', 'wp-graphql-buddypress' ),
				'resolve'     => function ( array $payload, array $args, AppContext $context ) {
					if ( empty( $payload['id'] ) ) {
						return null;
					}

					return Factory::resolve_signup_object( absint( $payload['id'] ), $context );
				},
			],
		];
	}

	/**
	 * Defines the mutation data modification closure.
	 *
	 * @return callable
	 */
	public static function mutate_and_get_payload() {
		return function ( array $input ) {

			// Validate user signup.
			$signup_validation = bp_core_validate_user_signup( $input['userLogin'], $input['userEmail'] );

			if ( is_wp_error( $signup_validation['errors'] ) && $signup_validation['errors']->get_error_messages() ) {
				throw new UserError( esc_html( $signup_validation['errors']->get_error_message() ) );
			}

			// Init the signup meta.
			$meta = [];

			// Use the validated login and email.
			$user_login = $signup_validation['user_name'];
			$user_email = $signup_validation['user_email'];

			// Init some MU specific variables.
			$domain        = '';
			$path          = '';
			$site_title    = '';
			$site_name     = '';
			$wp_key_suffix = '';

			if ( is_multisite() ) {
				$user_login    = preg_replace( '/\s+/', '', sanitize_user( $user_login, true ) );
				$user_email    = sanitize_email( $user_email );
				$wp_key_suffix = $user_email;

				if ( SignupHelper::is_blog_signup_enabled() ) {
					$site_title = $input['siteTitle'];
					$site_name  = $input['siteName'];

					if ( $site_title && $site_name ) {
						// Validate the blog signup.
						$blog_signup_validation = bp_core_validate_blog_signup( $site_name, $site_title );
						if ( is_wp_error( $blog_signup_validation['errors'] ) && $blog_signup_validation['errors']->get_error_messages() ) {
							throw new UserError( esc_html( $blog_signup_validation['errors']->get_error_message() ) );
						}

						$domain        = $blog_signup_validation['domain'];
						$wp_key_suffix = $domain;
						$path          = $blog_signup_validation['path'];
						$site_title    = $blog_signup_validation['blog_title'];
						$site_language = wp_unslash( sanitize_text_field( $input['siteLanguage'] ) );
						$meta          = [
							'lang_id' => 1,
							'public'  => (bool) $input['sitePublic'] ? 1 : 0,
						];

						if ( ! empty( $site_language ) ) {
							$meta['WPLANG'] = $site_language;
						}
					}
				} elseif ( ! empty( $input['siteTitle'] ) && ! empty( $input['siteName'] ) ) {
					throw new UserError( esc_html__( 'You are trying to create a blog but blog signup is disabled.', 'wp-graphql-buddypress' ) );
				}
			}

			$password = $input['password'];

			if ( empty( $password ) || false !== strpos( $password, '\\' ) ) {
				throw new UserError( esc_html__( 'Passwords cannot be empty or contain the "\\" character.', 'wp-graphql-buddypress' ) );
			}

			// Hash and store the password.
			$meta['password'] = wp_hash_password( $password );

			if ( ! empty( $input['userName'] ) ) {
				$meta['field_1']           = $input['userName'];
				$meta['profile_field_ids'] = 1;
			}

			if ( is_multisite() ) {
				// On Multisite, use the WordPress way to generate the activation key.
				$activation_key = substr( md5( time() . wp_rand() . $wp_key_suffix ), 0, 16 );

				if ( $site_title && $site_name ) {
					/** This filter is documented in wp-includes/ms-functions.php */
					$meta = apply_filters( 'signup_site_meta', $meta, $domain, $path, $site_title, $user_login, $user_email, $activation_key );
				} else {
					/** This filter is documented in wp-includes/ms-functions.php */
					$meta = apply_filters( 'signup_user_meta', $meta, $user_login, $user_email, $activation_key );
				}
			} else {
				$activation_key = wp_generate_password( 32, false );
			}

			/**
			 * Filter here to add custom signup meta.
			 *
			 * @since 0.1.0
			 *
			 * @param array $meta The signup meta.
			 */
			$meta = apply_filters( 'bp_graphql_signup_create_meta', $meta );

			$signup_args = [
				'user_login'     => $user_login,
				'user_email'     => $user_email,
				'activation_key' => $activation_key,
				'domain'         => $domain,
				'path'           => $path,
				'title'          => $site_title,
				'meta'           => $meta,
			];

			// Add signup.
			$id = BP_Signup::add( $signup_args );

			if ( ! is_numeric( $id ) ) {
				throw new UserError( esc_html__( 'Could not create signup.', 'wp-graphql-buddypress' ) );
			}

			$signup = SignupHelper::get_signup( $id );

			if ( empty( $signup->id ) ) {
				throw new UserError( esc_html__( 'Could not create signup.', 'wp-graphql-buddypress' ) );
			}

			if ( is_multisite() ) {
				if ( $site_title && $site_name ) {
					/** This action is documented in wp-includes/ms-functions.php */
					do_action( 'after_signup_site', $signup->domain, $signup->path, $signup->title, $signup->user_login, $signup->user_email, $signup->activation_key, $signup->meta );
				} else {
					/** This action is documented in wp-includes/ms-functions.php */
					do_action( 'after_signup_user', $signup->user_login, $signup->user_email, $signup->activation_key, $signup->meta );
				}
			} elseif ( apply_filters( 'bp_core_signup_send_activation_key', true, false, $signup->user_email, $signup->activation_key, $signup->meta ) ) {
				/** This filter is documented in bp-members/bp-members-functions.php */
				$salutation = $signup->user_login;
				if ( ! empty( $signup->user_name ) ) {
					$salutation = $signup->user_name;
				}

				bp_core_signup_send_validation_email( false, $signup->user_email, $signup->activation_key, $salutation );
			}

			// Return the signup ID.
			return [
				'id' => $signup->id,
			];
		};
	}
}
