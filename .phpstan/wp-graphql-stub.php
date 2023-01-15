<?php

namespace WPGraphQL\Type\Connection {
    /**
     * Class Comments
     *
     * This class organizes the registration of connections to Comments
     *
     * @package WPGraphQL\Type\Connection
     */
    class Comments
    {
        /**
         * Register connections to Comments.
         *
         * Connections from Post Objects to Comments are handled in \Registry\Utils\PostObject.
         *
         * @return void
         * @throws Exception
         */
        public static function register_connections()
        {
        }
        /**
         * Given an array of $args, this returns the connection config, merging the provided args
         * with the defaults
         *
         * @param array $args
         *
         * @return array
         */
        public static function get_connection_config($args = [])
        {
        }
        /**
         * This returns the connection args for the Comment connection
         *
         * @return array
         */
        public static function get_connection_args()
        {
        }
    }
}
namespace WPGraphQL\Connection {
    /**
     * Deprecated class for backwards compatibility.
     */
    class Comments extends \WPGraphQL\Type\Connection\Comments
    {
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function register_connections()
        {
        }
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function get_connection_config($args = [])
        {
        }
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function get_connection_args()
        {
        }
    }
}
namespace WPGraphQL\Type\Connection {
    /**
     * Class MenuItems
     *
     * This class organizes registration of connections to MenuItems
     *
     * @package WPGraphQL\Type\Connection
     */
    class MenuItems
    {
        /**
         * Register connections to MenuItems
         *
         * @return void
         * @throws Exception
         */
        public static function register_connections()
        {
        }
        /**
         * Given an array of $args, returns the args for the connection with the provided args merged
         *
         * @param array $args
         *
         * @return array
         */
        public static function get_connection_config($args = [])
        {
        }
    }
}
namespace WPGraphQL\Connection {
    /**
     * Deprecated class for backwards compatibility.
     */
    class MenuItems extends \WPGraphQL\Type\Connection\MenuItems
    {
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function register_connections()
        {
        }
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function get_connection_config($args = [])
        {
        }
    }
}
namespace WPGraphQL\Type\Connection {
    /**
     * Class TermObjects
     *
     * This class organizes the registration of connections to TermObjects
     *
     * @package WPGraphQL\Type\Connection
     */
    class TermObjects
    {
        /**
         * Register connections to TermObjects
         *
         * @return void
         */
        public static function register_connections()
        {
        }
        /**
         * Given the Taxonomy Object and an array of args, this returns an array of args for use in
         * registering a connection.
         *
         * @param \WP_Taxonomy $tax_object        The taxonomy object for the taxonomy having a
         *                                        connection registered to it
         * @param array        $args              The custom args to modify the connection registration
         *
         * @return array
         */
        public static function get_connection_config($tax_object, $args = [])
        {
        }
        /**
         * Given an optional array of args, this returns the args to be used in the connection
         *
         * @param array $args The args to modify the defaults
         *
         * @return array
         */
        public static function get_connection_args($args = [])
        {
        }
    }
}
namespace WPGraphQL\Connection {
    /**
     * Deprecated class for backwards compatibility.
     */
    class TermObjects extends \WPGraphQL\Type\Connection\TermObjects
    {
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function register_connections()
        {
        }
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function get_connection_config($tax_object, $args = [])
        {
        }
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function get_connection_args($args = [])
        {
        }
    }
}
namespace WPGraphQL\Type\Connection {
    /**
     * Class Users
     *
     * This class organizes the registration of connections to Users
     *
     * @package WPGraphQL\Type\Connection
     */
    class Users
    {
        /**
         * Register connections to Users
         *
         * @return void
         */
        public static function register_connections()
        {
        }
        /**
         * Returns the connection args for use in the connection
         *
         * @return array
         */
        public static function get_connection_args()
        {
        }
    }
}
namespace WPGraphQL\Connection {
    /**
     * Deprecated class for backwards compatibility.
     */
    class Users extends \WPGraphQL\Type\Connection\Users
    {
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function register_connections()
        {
        }
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function get_connection_args()
        {
        }
    }
}
namespace WPGraphQL\Type\Connection {
    /**
     * Class PostObjects
     *
     * This class organizes the registration of connections to PostObjects
     *
     * @package WPGraphQL\Type\Connection
     */
    class PostObjects
    {
        /**
         * Registers the various connections from other Types to PostObjects
         *
         * @return void
         * @throws Exception
         */
        public static function register_connections()
        {
        }
        /**
         * Given the Post Type Object and an array of args, this returns an array of args for use in
         * registering a connection.
         *
         * @param mixed|WP_Post_Type|WP_Taxonomy $graphql_object The post type object for the post_type having a
         *                                        connection registered to it
         * @param array                          $args           The custom args to modify the connection registration
         *
         * @return array
         */
        public static function get_connection_config($graphql_object, $args = [])
        {
        }
        /**
         * Given an optional array of args, this returns the args to be used in the connection
         *
         * @param array         $args             The args to modify the defaults
         * @param mixed|WP_Post_Type|WP_Taxonomy $post_type_object The post type the connection is going to
         *
         * @return array
         */
        public static function get_connection_args($args = [], $post_type_object = null)
        {
        }
    }
}
namespace WPGraphQL\Connection {
    /**
     * Deprecated class for backwards compatibility.
     */
    class PostObjects extends \WPGraphQL\Type\Connection\PostObjects
    {
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function register_connections()
        {
        }
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function get_connection_config($graphql_object, $args = [])
        {
        }
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function get_connection_args($args = [], $post_type_object = null)
        {
        }
    }
}
namespace WPGraphQL\Type\Connection {
    class Taxonomies
    {
        /**
         * Registers connections to the Taxonomy type
         *
         * @return void
         */
        public static function register_connections()
        {
        }
    }
}
namespace WPGraphQL\Connection {
    /**
     * Deprecated class for backwards compatibility.
     */
    class Taxonomies extends \WPGraphQL\Type\Connection\Taxonomies
    {
        /**
         * {@inheritDoc}
         *
         * @deprecated 1.13.0
         */
        public static function register_connections()
        {
        }
    }
}
namespace WPGraphQL\Mutation {
    class MediaItemCreate
    {
        /**
         * Registers the MediaItemCreate mutation.
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    /**
     * Class UpdateSettings
     *
     * @package WPGraphQL\Mutation
     */
    class UpdateSettings
    {
        /**
         * Registers the CommentCreate mutation.
         *
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return void
         */
        public static function register_mutation(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return array
         */
        public static function get_input_fields(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return array
         */
        public static function get_output_fields(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @param array $input The mutation input
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return array
         *
         * @throws UserError
         */
        public static function mutate_and_get_payload(array $input, \WPGraphQL\Registry\TypeRegistry $type_registry) : array
        {
        }
    }
    /**
     * Class TermObjectUpdate
     *
     * @package WPGraphQL\Mutation
     */
    class TermObjectUpdate
    {
        /**
         * Registers the TermObjectUpdate mutation.
         *
         * @param WP_Taxonomy $taxonomy The Taxonomy the mutation is registered for.
         *
         * @return void
         */
        public static function register_mutation(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @param WP_Taxonomy $taxonomy    The taxonomy type of the mutation.
         *
         * @return array
         */
        public static function get_input_fields(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @param WP_Taxonomy $taxonomy    The taxonomy type of the mutation.
         *
         * @return array
         */
        public static function get_output_fields(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @param WP_Taxonomy $taxonomy       The taxonomy type of the mutation.
         * @param string       $mutation_name  The name of the mutation.
         *
         * @return callable
         */
        public static function mutate_and_get_payload(\WP_Taxonomy $taxonomy, $mutation_name)
        {
        }
    }
    /**
     * Class UserCreate
     *
     * @package WPGraphQL\Mutation
     */
    class UserCreate
    {
        /**
         * Registers the CommentCreate mutation.
         *
         * @return void
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    class PostObjectDelete
    {
        /**
         * Registers the PostObjectDelete mutation.
         *
         * @param WP_Post_Type $post_type_object The post type of the mutation.
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation(\WP_Post_Type $post_type_object)
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @param WP_Post_Type $post_type_object The post type of the mutation.
         *
         * @return array
         */
        public static function get_input_fields($post_type_object)
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @param WP_Post_Type $post_type_object The post type of the mutation.
         *
         * @return array
         */
        public static function get_output_fields(\WP_Post_Type $post_type_object)
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @param WP_Post_Type $post_type_object The post type of the mutation.
         * @param string       $mutation_name    The mutation name.
         *
         * @return callable
         */
        public static function mutate_and_get_payload(\WP_Post_Type $post_type_object, string $mutation_name)
        {
        }
    }
    class SendPasswordResetEmail
    {
        /**
         * Registers the sendPasswordResetEmail Mutation
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields() : array
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields() : array
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload() : callable
        {
        }
        /**
         * Was a username or email address provided?
         *
         * @param array $input The input args.
         *
         * @return bool
         */
        private static function was_username_provided($input)
        {
        }
        /**
         * Get WP_User object representing this user
         *
         * @param  string $username The user's username or email address.
         *
         * @return WP_User|false WP_User object on success, false on failure.
         */
        private static function get_user_data($username)
        {
        }
        /**
         * Get the error message indicating why the user wasn't found
         *
         * @param  string $username The user's username or email address.
         *
         * @return string
         */
        private static function get_user_not_found_error_message(string $username)
        {
        }
        /**
         * Is the provided username arg an email address?
         *
         * @param  string $username The user's username or email address.
         *
         * @return bool
         */
        private static function is_email_address(string $username)
        {
        }
        /**
         * Get the subject of the password reset email
         *
         * @param WP_User $user_data User data
         *
         * @return string
         */
        private static function get_email_subject($user_data)
        {
        }
        /**
         * Get the site name.
         *
         * @return string
         */
        private static function get_site_name()
        {
        }
        /**
         * Get the message body of the password reset email
         *
         * @param WP_User $user_data User data
         * @param string   $key       Password reset key
         *
         * @return string
         */
        private static function get_email_message($user_data, $key)
        {
        }
    }
    class TermObjectCreate
    {
        /**
         * Registers the TermObjectCreate mutation.
         *
         * @param WP_Taxonomy $taxonomy The taxonomy type of the mutation.
         *
         * @return void
         */
        public static function register_mutation(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @param WP_Taxonomy $taxonomy The taxonomy type of the mutation.
         *
         * @return array
         */
        public static function get_input_fields(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @param WP_Taxonomy $taxonomy The taxonomy type of the mutation.
         *
         * @return array
         */
        public static function get_output_fields(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @param WP_Taxonomy $taxonomy      The taxonomy type of the mutation.
         * @param string      $mutation_name The name of the mutation.
         *
         * @return callable
         */
        public static function mutate_and_get_payload(\WP_Taxonomy $taxonomy, string $mutation_name)
        {
        }
    }
    class UserUpdate
    {
        /**
         * Registers the CommentCreate mutation.
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    class CommentDelete
    {
        /**
         * Registers the CommentDelete mutation.
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    class MediaItemUpdate
    {
        /**
         * Registers the MediaItemUpdate mutation.
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    /**
     * Class TermObjectDelete
     *
     * @package WPGraphQL\Mutation
     */
    class TermObjectDelete
    {
        /**
         * Registers the TermObjectDelete mutation.
         *
         * @param WP_Taxonomy $taxonomy The taxonomy type of the mutation.
         *
         * @return void
         */
        public static function register_mutation(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @param WP_Taxonomy $taxonomy The taxonomy type of the mutation.
         *
         * @return array
         */
        public static function get_input_fields(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @param WP_Taxonomy $taxonomy The taxonomy type of the mutation.
         *
         * @return array
         */
        public static function get_output_fields(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @param WP_Taxonomy $taxonomy      The taxonomy type of the mutation.
         * @param string      $mutation_name The name of the mutation.
         *
         * @return callable
         */
        public static function mutate_and_get_payload(\WP_Taxonomy $taxonomy, string $mutation_name)
        {
        }
    }
    /**
     * Class PostObjectCreate
     *
     * @package WPGraphQL\Mutation
     */
    class PostObjectCreate
    {
        /**
         * Registers the PostObjectCreate mutation.
         *
         * @param WP_Post_Type $post_type_object The post type of the mutation.
         *
         * @return void
         */
        public static function register_mutation(\WP_Post_Type $post_type_object)
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @param WP_Post_Type $post_type_object The post type of the mutation.
         *
         * @return array
         */
        public static function get_input_fields($post_type_object)
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @param WP_Post_Type $post_type_object The post type of the mutation.
         *
         * @return array
         */
        public static function get_output_fields(\WP_Post_Type $post_type_object)
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @param WP_Post_Type $post_type_object The post type of the mutation.
         * @param string       $mutation_name    The mutation name.
         *
         * @return callable
         */
        public static function mutate_and_get_payload($post_type_object, $mutation_name)
        {
        }
    }
    /**
     * Class CommentRestore
     *
     * @package WPGraphQL\Mutation
     */
    class CommentRestore
    {
        /**
         * Registers the CommentRestore mutation.
         *
         * @return void
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    class CommentCreate
    {
        /**
         * Registers the CommentCreate mutation.
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    class ResetUserPassword
    {
        /**
         * Registers the ResetUserPassword mutation.
         *
         * @return void
         * @throws \Exception
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    class MediaItemDelete
    {
        /**
         * Registers the MediaItemDelete mutation.
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    class UserRegister
    {
        /**
         * Registers the CommentCreate mutation.
         *
         * @return void
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
        /**
         * @return bool False.
         */
        public static function return_false() : bool
        {
        }
    }
    class PostObjectUpdate
    {
        /**
         * Registers the PostObjectUpdate mutation.
         *
         * @param WP_Post_Type $post_type_object The post type of the mutation.
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation(\WP_Post_Type $post_type_object)
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @param WP_Post_Type $post_type_object   The post type of the mutation.
         *
         * @return array
         */
        public static function get_input_fields($post_type_object)
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @param WP_Post_Type $post_type_object   The post type of the mutation.
         *
         * @return array
         */
        public static function get_output_fields($post_type_object)
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @param WP_Post_Type $post_type_object   The post type of the mutation.
         * @param string        $mutation_name      The mutation name.
         *
         * @return callable
         */
        public static function mutate_and_get_payload($post_type_object, $mutation_name)
        {
        }
    }
    /**
     * Class UserDelete
     *
     * @package WPGraphQL\Mutation
     */
    class UserDelete
    {
        /**
         * Registers the CommentCreate mutation.
         *
         * @return void
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
    /**
     * Class CommentUpdate
     *
     * @package WPGraphQL\Mutation
     */
    class CommentUpdate
    {
        /**
         * Registers the CommentUpdate mutation.
         *
         * @return void
         * @throws Exception
         */
        public static function register_mutation()
        {
        }
        /**
         * Defines the mutation input field configuration.
         *
         * @return array
         */
        public static function get_input_fields()
        {
        }
        /**
         * Defines the mutation output field configuration.
         *
         * @return array
         */
        public static function get_output_fields()
        {
        }
        /**
         * Defines the mutation data modification closure.
         *
         * @return callable
         */
        public static function mutate_and_get_payload()
        {
        }
    }
}
namespace WPGraphQL\Admin\Settings {
    /**
     * Class Settings
     *
     * @package WPGraphQL\Admin\Settings
     */
    class Settings
    {
        /**
         * @var SettingsRegistry
         */
        public $settings_api;
        /**
         * WP_ENVIRONMENT_TYPE
         *
         * @var string The WordPress environment.
         */
        protected $wp_environment;
        /**
         * Initialize the WPGraphQL Settings Pages
         *
         * @return void
         */
        public function init()
        {
        }
        /**
         * Return the environment. Default to production.
         *
         * @return string The environment set using WP_ENVIRONMENT_TYPE.
         */
        protected function get_wp_environment()
        {
        }
        /**
         * Add the options page to the WP Admin
         *
         * @return void
         */
        public function add_options_page()
        {
        }
        /**
         * Registers the initial settings for WPGraphQL
         *
         * @return void
         */
        public function register_settings()
        {
        }
        /**
         * Initialize the settings admin page
         *
         * @return void
         */
        public function initialize_settings_page()
        {
        }
        /**
         * Initialize the styles and scripts used on the settings admin page
         *
         * @param string $hook_suffix The current admin page.
         */
        public function initialize_settings_page_scripts(string $hook_suffix) : void
        {
        }
        /**
         * Render the settings page in the admin
         *
         * @return void
         */
        public function render_settings_page()
        {
        }
    }
    /**
     * Class SettingsRegistry
     *
     * This settings class is based on the WordPress Settings API Class v1.3 from Tareq Hasan of WeDevs
     *
     * @see     https://github.com/tareq1988/wordpress-settings-api-class
     * @author  Tareq Hasan <tareq@weDevs.com>
     * @link    https://tareq.co Tareq Hasan
     *
     * @package WPGraphQL\Admin\Settings
     */
    class SettingsRegistry
    {
        /**
         * Settings sections array
         *
         * @var array
         */
        protected $settings_sections = [];
        /**
         * Settings fields array
         *
         * @var array
         */
        protected $settings_fields = [];
        /**
         * @return array
         */
        public function get_settings_sections()
        {
        }
        /**
         * @return array
         */
        public function get_settings_fields()
        {
        }
        /**
         * Enqueue scripts and styles
         *
         * @param string $hook_suffix The current admin page.
         *
         * @return void
         */
        public function admin_enqueue_scripts(string $hook_suffix)
        {
        }
        /**
         * Set settings sections
         *
         * @param string $slug    Setting Section Slug
         * @param array  $section setting section config
         *
         * @return SettingsRegistry
         */
        public function register_section(string $slug, array $section)
        {
        }
        /**
         * Register fields to a section
         *
         * @param string $section The slug of the section to register a field to
         * @param array  $fields  settings fields array
         *
         * @return SettingsRegistry
         */
        public function register_fields(string $section, array $fields)
        {
        }
        /**
         * Register a field to a section
         *
         * @param string $section The slug of the section to register a field to
         * @param array  $field   The config for the field being registered
         *
         * @return SettingsRegistry
         */
        public function register_field(string $section, array $field)
        {
        }
        /**
         * Initialize and registers the settings sections and fileds to WordPress
         *
         * Usually this should be called at `admin_init` hook.
         *
         * This function gets the initiated settings sections and fields. Then
         * registers them to WordPress and ready for use.
         *
         * @return void
         */
        public function admin_init()
        {
        }
        /**
         * Get field description for display
         *
         * @param array $args settings field args
         *
         * @return string
         */
        public function get_field_description(array $args) : string
        {
        }
        /**
         * Displays a text field for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_text(array $args)
        {
        }
        /**
         * Displays a url field for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_url(array $args)
        {
        }
        /**
         * Displays a number field for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_number(array $args)
        {
        }
        /**
         * Displays a checkbox for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_checkbox(array $args)
        {
        }
        /**
         * Displays a multicheckbox for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_multicheck(array $args)
        {
        }
        /**
         * Displays a radio button for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_radio(array $args)
        {
        }
        /**
         * Displays a selectbox for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_select(array $args)
        {
        }
        /**
         * Displays a textarea for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_textarea(array $args)
        {
        }
        /**
         * Displays the html for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_html(array $args)
        {
        }
        /**
         * Displays a rich text textarea for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_wysiwyg(array $args)
        {
        }
        /**
         * Displays a file upload field for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_file(array $args)
        {
        }
        /**
         * Displays a password field for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_password(array $args)
        {
        }
        /**
         * Displays a color picker field for a settings field
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_color($args)
        {
        }
        /**
         * Displays a select box for creating the pages select box
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_pages(array $args)
        {
        }
        /**
         * Displays a select box for user roles
         *
         * @param array $args settings field args
         *
         * @return void
         */
        public function callback_user_role_select(array $args)
        {
        }
        /**
         * Sanitize callback for Settings API
         *
         * @param array $options
         *
         * @return mixed
         */
        public function sanitize_options(array $options)
        {
        }
        /**
         * Get sanitization callback for given option slug
         *
         * @param string $slug option slug
         *
         * @return mixed string or bool false
         */
        public function get_sanitize_callback($slug = '')
        {
        }
        /**
         * Get the value of a settings field
         *
         * @param string $option  settings field name
         * @param string $section the section name this field belongs to
         * @param string $default default text if it's not found
         *
         * @return string
         */
        public function get_option($option, $section, $default = '')
        {
        }
        /**
         * Show navigations as tab
         *
         * Shows all the settings section labels as tab
         *
         * @return void
         */
        public function show_navigation()
        {
        }
        /**
         * Show the section settings forms
         *
         * This function displays every sections in a different form
         *
         * @return void
         */
        public function show_forms()
        {
        }
        /**
         * Tabbable JavaScript codes & Initiate Color Picker
         *
         * This code uses localstorage for displaying active tabs
         *
         * @return void
         */
        public function script()
        {
        }
        /**
         * Add styles to adjust some settings
         *
         * @return void
         */
        public function _style_fix()
        {
        }
    }
}
namespace WPGraphQL\Admin\GraphiQL {
    /**
     * Class GraphiQL
     *
     * Sets up GraphiQL in the WordPress Admin
     *
     * @package WPGraphQL\Admin\GraphiQL
     */
    class GraphiQL
    {
        /**
         * @var bool Whether GraphiQL is enabled
         */
        protected $is_enabled = false;
        /**
         * Initialize Admin functionality for WPGraphQL
         *
         * @return void
         */
        public function init()
        {
        }
        /**
         * Registers admin bar menu
         *
         * @param WP_Admin_Bar $admin_bar The Admin Bar Instance
         *
         * @return void
         */
        public function register_admin_bar_menu(\WP_Admin_Bar $admin_bar)
        {
        }
        /**
         * Register the admin page as a subpage
         *
         * @return void
         */
        public function register_admin_page()
        {
        }
        /**
         * Render the markup to load GraphiQL to.
         *
         * @return void
         */
        public function render_graphiql_admin_page()
        {
        }
        /**
         * Enqueues the stylesheet and js for the WPGraphiQL app
         *
         * @return void
         */
        public function enqueue_graphiql()
        {
        }
        /**
         * Enqueue the GraphiQL Auth Switch extension, which adds a button to the GraphiQL toolbar
         * that allows the user to switch between the logged in user and the current user
         *
         * @return void
         */
        public function graphiql_enqueue_auth_switch()
        {
        }
        /**
         * Enqueue the Query Composer extension, which adds a button to the GraphiQL toolbar
         * that allows the user to open the Query Composer and compose a query with a form-based UI
         *
         * @return void
         */
        public function graphiql_enqueue_query_composer()
        {
        }
        /**
         * Enqueue the GraphiQL Fullscreen Toggle extension, which adds a button to the GraphiQL toolbar
         * that allows the user to toggle the fullscreen mode
         *
         * @return void
         */
        public function graphiql_enqueue_fullscreen_toggle()
        {
        }
    }
}
namespace WPGraphQL\Admin {
    /**
     * Class Admin
     *
     * @package WPGraphQL\Admin
     */
    class Admin
    {
        /**
         * Whether Admin Pages are enabled or not
         *
         * @var boolean
         */
        protected $admin_enabled;
        /**
         * Whether GraphiQL is enabled or not
         *
         * @var boolean
         */
        protected $graphiql_enabled;
        /**
         * @var Settings
         */
        protected $settings;
        /**
         * Initialize Admin functionality for WPGraphQL
         *
         * @return void
         */
        public function init()
        {
        }
    }
}
namespace WPGraphQL\Server {
    /**
     * Extends GraphQL\Server\Helper to apply filters and parse query extensions.
     *
     * @package WPGraphQL\Server
     */
    class WPHelper extends \GraphQL\Server\Helper
    {
        /**
         * Parses normalized request params and returns instance of OperationParams
         * or array of OperationParams in case of batch operation.
         *
         * @param string $method The method of the request (GET, POST, etc).
         * @param array  $bodyParams The params passed to the body of the request.
         * @param array  $queryParams The query params passed to the request.
         * @return OperationParams|OperationParams[]
         * @throws RequestError Throws RequestError.
         */
        public function parseRequestParams($method, array $bodyParams, array $queryParams)
        {
        }
        /**
         * Parse parameters and proxy to parse_extensions.
         *
         * @param  array $params Request parameters.
         * @return array
         */
        private function parse_params($params)
        {
        }
        /**
         * Parse query extensions.
         *
         * @param  array $params Request parameters.
         * @return array
         */
        private function parse_extensions($params)
        {
        }
    }
}
namespace WPGraphQL\Server\ValidationRules {
    /**
     * Class QueryDepth
     *
     * @package WPGraphQL\Server\ValidationRules
     */
    class QueryDepth extends \GraphQL\Validator\Rules\QuerySecurityRule
    {
        /**
         * @var int
         */
        private $maxQueryDepth;
        /**
         * QueryDepth constructor.
         */
        public function __construct()
        {
        }
        /**
         * @param ValidationContext $context
         *
         * @return callable[]|mixed[]
         */
        public function getVisitor(\GraphQL\Validator\ValidationContext $context)
        {
        }
        /**
         * Determine field depth
         *
         * @param mixed $node The node being analyzed
         * @param int $depth The depth of the field
         * @param int $maxDepth The max depth allowed
         *
         * @return int|mixed
         */
        private function fieldDepth($node, $depth = 0, $maxDepth = 0)
        {
        }
        /**
         * Determine node depth
         *
         * @param Node $node The node being analyzed in the operation
         * @param int  $depth The depth of the operation
         * @param int  $maxDepth The Max Depth of the operation
         *
         * @return int|mixed
         */
        private function nodeDepth(\GraphQL\Language\AST\Node $node, $depth = 0, $maxDepth = 0)
        {
        }
        /**
         * Return the maxQueryDepth allowed
         *
         * @return int
         */
        public function getMaxQueryDepth()
        {
        }
        /**
         * Set max query depth. If equal to 0 no check is done. Must be greater or equal to 0.
         *
         * @param int $maxQueryDepth The max query depth to allow for GraphQL operations
         *
         * @return void
         */
        public function setMaxQueryDepth(int $maxQueryDepth)
        {
        }
        /**
         * Return the max query depth error message
         *
         * @param int $max The max number of levels to allow in GraphQL operation
         * @param int $count The number of levels in the current operation
         *
         * @return string
         */
        public function errorMessage($max, $count)
        {
        }
        /**
         * Determine whether the rule should be enabled
         *
         * @return bool
         */
        protected function isEnabled()
        {
        }
    }
    /**
     * Class RequireAuthentication
     *
     * @package WPGraphQL\Server\ValidationRules
     */
    class RequireAuthentication extends \GraphQL\Validator\Rules\QuerySecurityRule
    {
        /**
         * @return bool
         */
        protected function isEnabled()
        {
        }
        /**
         * @param ValidationContext $context
         *
         * @return callable[]|mixed[]
         */
        public function getVisitor(\GraphQL\Validator\ValidationContext $context)
        {
        }
    }
    /**
     * Class DisableIntrospection
     *
     * @package WPGraphQL\Server\ValidationRules
     */
    class DisableIntrospection extends \GraphQL\Validator\Rules\DisableIntrospection
    {
        /**
         * @return bool
         */
        public function isEnabled()
        {
        }
    }
}
namespace WPGraphQL\Utils {
    class Preview
    {
        /**
         * This filters the post meta for previews. Since WordPress core does not save meta for
         * revisions this resolves calls to get_post_meta() using the meta of the revisions parent (the
         * published version of the post).
         *
         * For plugins (such as ACF) that do store meta on revisions, the filter
         * "graphql_resolve_revision_meta_from_parent" can be used to opt-out of this default behavior
         * and instead return meta from the revision object instead of the parent.
         *
         * @param mixed       $default_value The default value of the meta
         * @param int         $object_id     The ID of the object the meta is for
         * @param string|null $meta_key      The meta key
         * @param bool|null   $single        Whether the meta is a single value
         *
         * @return mixed
         */
        public static function filter_post_meta_for_previews($default_value, int $object_id, ?string $meta_key = null, ?bool $single = false)
        {
        }
    }
    /**
     * Class InstrumentSchema
     *
     * @package WPGraphQL\Data
     */
    class InstrumentSchema
    {
        /**
         * @param Type $type Instance of the Schema.
         * @param string $type_name Name of the Type
         *
         * @return Type
         */
        public static function instrument_resolvers(\GraphQL\Type\Definition\Type $type, string $type_name) : \GraphQL\Type\Definition\Type
        {
        }
        /**
         * Wrap Fields
         *
         * This wraps fields to provide sanitization on fields output by introspection queries
         * (description/deprecation reason) and provides hooks to resolvers.
         *
         * @param array  $fields    The fields configured for a Type
         * @param string $type_name The Type name
         *
         * @return mixed
         */
        protected static function wrap_fields(array $fields, string $type_name)
        {
        }
        /**
         * Check field permissions when resolving.
         *
         * This takes into account auth params defined in the Schema
         *
         * @param mixed                 $source         The source passed down the Resolve Tree
         * @param array                 $args           The args for the field
         * @param AppContext            $context        The AppContext passed down the ResolveTree
         * @param ResolveInfo           $info           The ResolveInfo passed down the ResolveTree
         * @param mixed|callable|string $field_resolver The Resolve function for the field
         * @param string                $type_name      The name of the type the fields belong to
         * @param string                $field_key      The name of the field
         * @param FieldDefinition       $field          The Field Definition for the resolving field
         *
         * @return bool|mixed
         */
        public static function check_field_permissions($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info, $field_resolver, string $type_name, string $field_key, \GraphQL\Type\Definition\FieldDefinition $field)
        {
        }
    }
    /**
     * Class QueryLog
     *
     * @package WPGraphQL\Utils
     */
    class QueryLog
    {
        /**
         * Whether Query Logs are enabled
         *
         * @var boolean
         */
        protected $query_logs_enabled;
        /**
         * The user role query logs should be limited to
         *
         * @var string
         */
        protected $query_log_user_role;
        /**
         * Initialize Query Logging
         *
         * @return void
         */
        public function init()
        {
        }
        /**
         * Tell WordPress to start saving queries.
         *
         * NOTE: This will affect all requests, not just GraphQL requests.
         *
         * @return void
         */
        public function init_save_queries()
        {
        }
        /**
         * Determine if the requesting user can see logs
         *
         * @return boolean
         */
        public function user_can_see_logs()
        {
        }
        /**
         * Filter the results of the GraphQL Response to include the Query Log
         *
         * @param mixed    $response
         * @param WPSchema $schema         The WPGraphQL Schema
         * @param string   $operation_name The operation name being executed
         * @param string   $request        The GraphQL Request being made
         * @param array    $variables      The variables sent with the request
         *
         * @return array
         */
        public function show_results($response, $schema, $operation_name, $request, $variables)
        {
        }
        /**
         * Return the query log produced from the logs stored by WPDB.
         *
         * @return array
         */
        public function get_query_log()
        {
        }
    }
    /**
     * This class is used to identify "keys" relevant to the GraphQL Request.
     *
     * These keys can be used to identify common patterns across documents.
     *
     * A common use case would be for caching a GraphQL request and tagging the cached
     * object with these keys, then later using these keys to evict the cached
     * document.
     *
     * These keys can also be used by loggers to identify patterns, etc.
     */
    class QueryAnalyzer
    {
        /**
         * @var Schema
         */
        protected $schema;
        /**
         * Types that are referenced in the query
         *
         * @var array
         */
        protected $queried_types = [];
        /**
         * @var string
         */
        protected $root_operation = 'Query';
        /**
         * Models that are referenced in the query
         *
         * @var array
         */
        protected $models = [];
        /**
         * Types in the query that are lists
         *
         * @var array
         */
        protected $list_types = [];
        /**
         * @var array
         */
        protected $runtime_nodes = [];
        /**
         * @var array
         */
        protected $runtime_nodes_by_type = [];
        /**
         * @var string
         */
        protected $query_id;
        /**
         * @var Request
         */
        protected $request;
        /**
         * @var Int The character length limit for headers
         */
        protected $header_length_limit;
        /**
         * @var string The keys that were skipped from being returned in the X-GraphQL-Keys header.
         */
        protected $skipped_keys = '';
        /**
         * @var array The GraphQL keys to return in the X-GraphQL-Keys header.
         */
        protected $graphql_keys = [];
        /**
         * @param Request $request The GraphQL request being executed
         */
        public function __construct(\WPGraphQL\Request $request)
        {
        }
        /**
         * @return Request
         */
        public function get_request() : \WPGraphQL\Request
        {
        }
        /**
         * @return void
         */
        public function init() : void
        {
        }
        /**
         * Determine the keys associated with the GraphQL document being executed
         *
         * @param ?string         $query     The GraphQL query
         * @param ?string         $operation The name of the operation
         * @param ?array          $variables Variables to be passed to your GraphQL request
         * @param OperationParams $params    The Operation Params. This includes any extra params, such
         *                                   as extenions or any other modifications to the request
         *                                   body
         *
         * @return void
         * @throws Exception
         */
        public function determine_graphql_keys(?string $query, ?string $operation, ?array $variables, \GraphQL\Server\OperationParams $params) : void
        {
        }
        /**
         * @return array
         */
        public function get_list_types() : array
        {
        }
        /**
         * @return array
         */
        public function get_query_types() : array
        {
        }
        /**
         * @return array
         */
        public function get_query_models() : array
        {
        }
        /**
         * @return array
         */
        public function get_runtime_nodes() : array
        {
        }
        /**
         * @return string
         */
        public function get_root_operation() : string
        {
        }
        /**
         * Returns the operation name of the query, if there is one
         *
         * @return string|null
         */
        public function get_operation_name() : ?string
        {
        }
        /**
         * @return string|null
         */
        public function get_query_id() : ?string
        {
        }
        /**
         * Given the Schema and a query string, return a list of GraphQL Types that are being asked for
         * by the query.
         *
         * @param ?Schema $schema The WPGraphQL Schema
         * @param ?string $query  The query string
         *
         * @return array
         * @throws SyntaxError|Exception
         */
        public function set_list_types(?\GraphQL\Type\Schema $schema, ?string $query) : array
        {
        }
        /**
         * Given the Schema and a query string, return a list of GraphQL Types that are being asked for
         * by the query.
         *
         * @param ?Schema $schema The WPGraphQL Schema
         * @param ?string $query  The query string
         *
         * @return array
         * @throws Exception
         */
        public function set_query_types(?\GraphQL\Type\Schema $schema, ?string $query) : array
        {
        }
        /**
         * Given the Schema and a query string, return a list of GraphQL model names that are being
         * asked for by the query.
         *
         * @param ?Schema $schema The WPGraphQL Schema
         * @param ?string $query  The query string
         *
         * @return array
         * @throws SyntaxError|Exception
         */
        public function set_query_models(?\GraphQL\Type\Schema $schema, ?string $query) : array
        {
        }
        /**
         * Track the nodes that were resolved by ensuring the Node's model
         * matches one of the models asked for in the query
         *
         * @param mixed $model The Model to be returned by the loader
         *
         * @return mixed
         */
        public function track_nodes($model)
        {
        }
        /**
         * Returns graphql keys for use in debugging and headers.
         *
         * @return array
         */
        public function get_graphql_keys()
        {
        }
        /**
         * Return headers
         *
         * @param array $headers The array of headers being returned
         *
         * @return array
         */
        public function get_headers(array $headers = []) : array
        {
        }
        /**
         * Outputs Query Analyzer data in the extensions response
         *
         * @param mixed       $response
         * @param WPSchema    $schema         The WPGraphQL Schema
         * @param string|null $operation_name The operation name being executed
         * @param string|null $request        The GraphQL Request being made
         * @param array|null  $variables      The variables sent with the request
         *
         * @return array|object|null
         */
        public function show_query_analyzer_in_extensions($response, \WPGraphQL\WPSchema $schema, ?string $operation_name, ?string $request, ?array $variables)
        {
        }
    }
    /**
     * Class Tracing
     *
     * Sets up trace data to track how long individual fields take to resolve in WPGraphQL
     *
     * @package WPGraphQL\Utils
     */
    class Tracing
    {
        /**
         * Whether Tracing is enabled
         *
         * @var boolean
         */
        public $tracing_enabled;
        /**
         * Stores the logs for the trace
         *
         * @var array
         */
        public $trace_logs = [];
        /**
         * The start microtime
         *
         * @var float
         */
        public $request_start_microtime;
        /**
         * The start timestamp
         *
         * @var float
         */
        public $request_start_timestamp;
        /**
         * The end microtime
         *
         * @var float
         */
        public $request_end_microtime;
        /**
         * The end timestamp
         *
         * @var float
         */
        public $request_end_timestamp;
        /**
         * The trace for the current field being resolved
         *
         * @var array
         */
        public $field_trace = [];
        /**
         * The version of the Apollo Tracing Spec
         *
         * @var int
         */
        public $trace_spec_version = 1;
        /**
         * The user role tracing is limited to
         *
         * @var string
         */
        public $tracing_user_role;
        /**
         * Initialize tracing
         *
         * @return void
         */
        public function init()
        {
        }
        /**
         * Sets the timestamp and microtime for the start of the request
         *
         * @return float
         */
        public function init_trace()
        {
        }
        /**
         * Sets the timestamp and microtime for the end of the request
         *
         * @return float
         */
        public function end_trace()
        {
        }
        /**
         * Initialize tracing for an individual field
         *
         * @param mixed               $source         The source passed down the Resolve Tree
         * @param array               $args           The args for the field
         * @param AppContext          $context        The AppContext passed down the ResolveTree
         * @param ResolveInfo         $info           The ResolveInfo passed down the ResolveTree
         *
         * @return void
         */
        public function init_field_resolver_trace($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * End the tracing for a resolver
         *
         * @return void
         */
        public function end_field_resolver_trace()
        {
        }
        /**
         * Given a resolver start time, returns the duration of a resolver
         *
         * @return float|int
         */
        public function get_field_resolver_duration()
        {
        }
        /**
         * Get the offset between the start of the request and now
         *
         * @return float|int
         */
        public function get_start_offset()
        {
        }
        /**
         * Given a trace, sanitizes the values and returns the sanitized_trace
         *
         * @param array $trace
         *
         * @return mixed
         */
        public function sanitize_resolver_trace(array $trace)
        {
        }
        /**
         * Given input from a Resolver Path, this sanitizes the input for output in the trace
         *
         * @param mixed $input The input to sanitize
         *
         * @return int|null|string
         */
        public static function sanitize_trace_resolver_path($input)
        {
        }
        /**
         * Formats a timestamp to be RFC 3339 compliant
         *
         * @see https://github.com/apollographql/apollo-tracing
         *
         * @param mixed|string|float|int $time The timestamp to format
         *
         * @return float
         */
        public function format_timestamp($time)
        {
        }
        /**
         * Filter the headers that WPGraphQL returns to include headers that indicate the WPGraphQL
         * server supports Apollo Tracing and Credentials
         *
         * @param array $headers The headers to return
         *
         * @return array
         */
        public function return_tracing_headers(array $headers)
        {
        }
        /**
         * Filter the results of the GraphQL Response to include the Query Log
         *
         * @param mixed|array|object $response       The response of the GraphQL Request
         *
         * @return mixed $response
         */
        public function add_tracing_to_response_extensions($response)
        {
        }
        /**
         * Returns the request duration calculated from the start and end times
         *
         * @return float|int
         */
        public function get_request_duration()
        {
        }
        /**
         * Determine if the requesting user can see trace data
         *
         * @return boolean
         */
        public function user_can_see_trace_data() : bool
        {
        }
        /**
         * Get the trace to add to the response
         *
         * @return array
         */
        public function get_trace() : array
        {
        }
    }
    /**
     * Class DebugLog
     *
     * @package WPGraphQL\Utils
     */
    class DebugLog
    {
        /**
         * @var array
         */
        protected $logs;
        /**
         * Whether logs are enabled
         *
         * @var boolean
         */
        protected $logs_enabled;
        /**
         * DebugLog constructor.
         */
        public function __construct()
        {
        }
        /**
         * Given a message and a config, a log entry is added to the log
         *
         * @param mixed|string|array $message The debug log message
         * @param array  $config Config for the debug log. Set type and any additional information to log
         *
         * @return array
         */
        public function add_log_entry($message, $config = [])
        {
        }
        /**
         * Returns the debug log
         *
         * @return array
         */
        public function get_logs()
        {
        }
    }
    class Utils
    {
        /**
         * Given a GraphQL Query string, return a hash
         *
         * @param string $query The Query String to hash
         *
         * @return string|null
         */
        public static function get_query_id(string $query)
        {
        }
        /**
         * Maps new input query args and sa nitizes the input
         *
         * @param mixed|array|string $args The raw query args from the GraphQL query
         * @param mixed|array|string $map  The mapping of where each of the args should go
         *
         * @return array
         * @since  0.5.0
         */
        public static function map_input($args, $map)
        {
        }
        /**
         * Checks the post_date_gmt or modified_gmt and prepare any post or
         * modified date for single post output.
         *
         * @param string $date_gmt GMT publication time.
         * @param mixed|string|null $date Optional. Local publication time. Default null.
         *
         * @return string|null ISO8601/RFC3339 formatted datetime.
         * @since 4.7.0
         */
        public static function prepare_date_response(string $date_gmt, $date = null)
        {
        }
        /**
         * Given a field name, formats it for GraphQL
         *
         * @param string $field_name The field name to format
         *
         * @return string
         */
        public static function format_field_name(string $field_name)
        {
        }
        /**
         * Given a type name, formats it for GraphQL
         *
         * @param string $type_name The type name to format
         *
         * @return string
         */
        public static function format_type_name($type_name)
        {
        }
        /**
         * Helper function that defines the allowed HTML to use on the Settings pages
         *
         * @return array
         */
        public static function get_allowed_wp_kses_html()
        {
        }
        /**
         * Helper function to get the WordPress database ID from a GraphQL ID type input.
         *
         * Returns false if not a valid ID.
         *
         * @param int|string $id The ID from the input args. Can be either the database ID (as either a string or int) or the global Relay ID.
         *
         * @return int|false
         */
        public static function get_database_id_from_id($id)
        {
        }
        /**
         * Get the node type from the ID
         *
         * @param int|string $id The encoded Node ID.
         *
         * @return bool|null
         */
        public static function get_node_type_from_id($id)
        {
        }
    }
}
namespace WPGraphQL {
    /**
     * Class Request
     *
     * Proxies a request to graphql-php, applying filters and transforming request
     * data as needed.
     *
     * @package WPGraphQL
     */
    class Request
    {
        /**
         * App context for this request.
         *
         * @var AppContext
         */
        public $app_context;
        /**
         * Request data.
         *
         * @var mixed|array|OperationParams
         */
        public $data;
        /**
         * Cached global post.
         *
         * @var ?WP_Post
         */
        public $global_post;
        /**
         * Cached global wp_the_query.
         *
         * @var ?WP_Query
         */
        private $global_wp_the_query;
        /**
         * GraphQL operation parameters for this request. Can also be an array of
         * OperationParams.
         *
         * @var mixed|array|OperationParams|OperationParams[]
         */
        public $params;
        /**
         * Schema for this request.
         *
         * @var WPSchema
         */
        public $schema;
        /**
         * Debug log for WPGraphQL Requests
         *
         * @var DebugLog
         */
        public $debug_log;
        /**
         * The Type Registry the Schema is built with
         *
         * @var Registry\TypeRegistry
         */
        public $type_registry;
        /**
         * Validation rules for execution.
         *
         * @var array
         */
        protected $validation_rules;
        /**
         * The default field resolver function. Default null
         *
         * @var mixed|callable|null
         */
        protected $field_resolver;
        /**
         * The root value of the request. Default null;
         *
         * @var mixed
         */
        protected $root_value;
        /**
         * @var QueryAnalyzer
         */
        protected $query_analyzer;
        /**
         * Constructor
         *
         * @param array $data The request data (for non-HTTP requests).
         *
         * @return void
         *
         * @throws Exception
         */
        public function __construct(array $data = [])
        {
        }
        /**
         * @return QueryAnalyzer
         */
        public function get_query_analyzer() : \WPGraphQL\Utils\QueryAnalyzer
        {
        }
        /**
         * @return mixed
         */
        protected function get_field_resolver()
        {
        }
        /**
         * Return the validation rules to use in the request
         *
         * @return array
         */
        protected function get_validation_rules() : array
        {
        }
        /**
         * Returns the root value to use in the request.
         *
         * @return mixed|null
         */
        protected function get_root_value()
        {
        }
        /**
         * Apply filters and do actions before GraphQL execution
         *
         * @return void
         * @throws Error
         */
        private function before_execute() : void
        {
        }
        /**
         * Checks authentication errors.
         *
         * False will mean there are no detected errors and
         * execution will continue.
         *
         * Anything else (true, WP_Error, thrown exception, etc) will prevent execution of the GraphQL
         * request.
         *
         * @return boolean
         * @throws Exception
         */
        protected function has_authentication_errors()
        {
        }
        /**
         * Filter Authentication errors. Allows plugins that authenticate to hook in and prevent
         * execution if Authentication errors exist.
         *
         * @param boolean $authentication_errors Whether there are authentication errors with the
         *                                       request
         *
         * @return boolean
         */
        protected function filtered_authentication_errors($authentication_errors = false)
        {
        }
        /**
         * Performs actions and runs filters after execution completes
         *
         * @param mixed|array|object $response The response from execution. Array for batch requests,
         *                                     single object for individual requests
         *
         * @return array
         *
         * @throws Exception
         */
        private function after_execute($response)
        {
        }
        /**
         * Apply filters and do actions after GraphQL execution
         *
         * @param mixed|array|object $response The response for your GraphQL request
         * @param mixed|Int|null     $key      The array key of the params for batch requests
         *
         * @return array
         */
        private function after_execute_actions($response, $key = null)
        {
        }
        /**
         * Run action for a request.
         *
         * @param OperationParams $params OperationParams for the request.
         *
         * @return void
         */
        private function do_action(\GraphQL\Server\OperationParams $params)
        {
        }
        /**
         * Execute an internal request (graphql() function call).
         *
         * @return array
         * @throws Exception
         */
        public function execute()
        {
        }
        /**
         * Execute an HTTP request.
         *
         * @return array
         * @throws Exception
         */
        public function execute_http()
        {
        }
        /**
         * Get the operation params for the request.
         *
         * @return OperationParams|OperationParams[]
         */
        public function get_params()
        {
        }
        /**
         * Returns the debug flag value
         *
         * @return int
         */
        public function get_debug_flag()
        {
        }
        /**
         * Determines if batch queries are enabled for the server.
         *
         * Default is to have batch queries enabled.
         *
         * @return bool
         */
        private function is_batch_queries_enabled()
        {
        }
        /**
         * Create the GraphQL server that will process the request.
         *
         * @return StandardServer
         */
        private function get_server()
        {
        }
    }
}
namespace WPGraphQL\Type {
    /**
     * Class WPConnectionType
     *
     * @package WPGraphQL\Type
     */
    class WPConnectionType
    {
        /**
         * Configuration for how auth should be handled on the connection field
         *
         * @var array
         */
        protected $auth;
        /**
         * The config for the connection
         *
         * @var array
         */
        protected $config;
        /**
         * The args configured for the connection
         *
         * @var array
         */
        protected $connection_args;
        /**
         * The fields to show on the connection
         *
         * @var array
         */
        protected $connection_fields;
        /**
         * @var array|null
         */
        protected $connection_interfaces;
        /**
         * The name of the connection
         *
         * @var mixed|string
         */
        protected $connection_name;
        /**
         * The fields to expose on the edge of the connection
         *
         * @var array
         */
        protected $edge_fields;
        /**
         * The name of the field the connection will be exposed as
         *
         * @var string
         */
        protected $from_field_name;
        /**
         * The name of the GraphQL Type the connection stems from
         *
         * @var string
         */
        protected $from_type;
        /**
         * Whether the connection is a one-to-one connection (default is false)
         *
         * @var bool
         */
        protected $one_to_one;
        /**
         * The Query Class that is used to resolve the connection.
         *
         * @var string
         */
        protected $query_class;
        /**
         * The resolver function to resolve the connection
         *
         * @var callable|Closure
         */
        protected $resolve_connection;
        /**
         * @var mixed|null
         */
        protected $resolve_cursor;
        /**
         * Whether to  include and generate the default GraphQL interfaces on the connection Object types.
         *
         * @var bool
         */
        protected $include_default_interfaces;
        /**
         * The name of the GraphQL Type the connection connects to
         *
         * @var string
         */
        protected $to_type;
        /**
         * The WPGraphQL TypeRegistry
         *
         * @var TypeRegistry
         */
        protected $type_registry;
        /**
         * The where args for the connection
         *
         * @var array
         */
        protected $where_args;
        /**
         * WPConnectionType constructor.
         *
         * @param array        $config The config array for the connection
         * @param TypeRegistry $type_registry Instance of the WPGraphQL Type Registry
         */
        public function __construct(array $config, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Validates that essential key/value pairs are passed to the connection config.
         *
         * @param array $config
         *
         * @return void
         */
        protected function validate_config(array $config) : void
        {
        }
        /**
         * Get edge interfaces
         *
         * @param array $interfaces
         *
         * @return array
         */
        protected function get_edge_interfaces(array $interfaces = []) : array
        {
        }
        /**
         * Utility method that formats the connection name given the name of the from Type and the to
         * Type
         *
         * @param string $from_type        Name of the Type the connection is coming from
         * @param string $to_type          Name of the Type the connection is going to
         * @param string $from_field_name  Acts as an alternative "toType" if connection type already defined using $to_type.
         *
         * @return string
         */
        public function get_connection_name(string $from_type, string $to_type, string $from_field_name) : string
        {
        }
        /**
         * If the connection includes connection args in the config, this registers the input args
         * for the connection
         *
         * @return void
         *
         * @throws Exception
         */
        protected function register_connection_input()
        {
        }
        /**
         * Registers the One to One Connection Edge type to the Schema
         *
         * @return void
         *
         * @throws Exception
         */
        protected function register_one_to_one_connection_edge_type() : void
        {
        }
        /**
         * Registers the PageInfo type for the connection
         *
         * @return void
         *
         * @throws Exception
         */
        public function register_connection_page_info_type() : void
        {
        }
        /**
         * Registers the Connection Edge type to the Schema
         *
         * @return void
         *
         * @throws Exception
         */
        protected function register_connection_edge_type() : void
        {
        }
        /**
         * Registers the Connection Type to the Schema
         *
         * @return void
         *
         * @throws Exception
         */
        protected function register_connection_type() : void
        {
        }
        /**
         * Returns fields to be used on the connection
         *
         * @return array
         */
        protected function get_connection_fields() : array
        {
        }
        /**
         * Get the args used for pagination on connections
         *
         * @return array|array[]
         */
        protected function get_pagination_args() : array
        {
        }
        /**
         * Registers the connection in the Graph
         *
         * @return void
         */
        public function register_connection_field() : void
        {
        }
        /**
         * @return void
         * @throws Exception
         */
        public function register_connection_interfaces() : void
        {
        }
        /**
         * Registers the connection Types and field to the Schema.
         *
         * @todo change to 'Protected'. This is public for now to allow for backwards compatibility.
         *
         * @return void
         *
         * @throws Exception
         */
        public function register_connection() : void
        {
        }
    }
}
namespace WPGraphQL\Type\Input {
    class PostObjectsConnectionOrderbyInput
    {
        /**
         * Register the PostObjectsConnectionOrderbyInput Input
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class DateInput
    {
        /**
         * Register the DateInput Input
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class DateQueryInput
    {
        /**
         * Register the DateQueryInput Input
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class UsersConnectionOrderbyInput
    {
        /**
         * Register the UsersConnectionOrderbyInput Input
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
}
namespace WPGraphQL\Type\ObjectType {
    class CommentAuthor
    {
        /**
         * Register the CommentAuthor Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class Taxonomy
    {
        /**
         * Register the Taxonomy type to the GraphQL Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class EnqueuedScript
     *
     * @package WPGraphQL\Type\Object
     */
    class EnqueuedScript
    {
        /**
         * Register the EnqueuedScript Type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class TermObject
     *
     * @package WPGraphQL\Type\Object
     * @deprecated 1.12.0
     */
    class TermObject
    {
        /**
         * Register the Type for each kind of Taxonomy
         *
         * @param WP_Taxonomy $tax_object The taxonomy being registered
         *
         * @return void
         * @throws Exception
         * @deprecated 1.12.0
         */
        public static function register_taxonomy_object_type(\WP_Taxonomy $tax_object)
        {
        }
    }
    /**
     * Class Settings
     *
     * @package WPGraphQL\Type\Object
     */
    class Settings
    {
        /**
         * Registers a Settings Type with fields for all settings based on settings
         * registered using the core register_setting API
         *
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Returns an array of fields for all settings based on the `register_setting` WordPress API
         *
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return array
         */
        public static function get_fields(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    /**
     * Class PostTypeLabelDetails
     *
     * @package WPGraphQL\Type\Object
     */
    class PostTypeLabelDetails
    {
        /**
         * Register the PostTypeLabelDetails type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * WPObject - PostObject
     *
     * @package WPGraphQL\Type
     * @deprecated 1.12.0
     */
    class PostObject
    {
        /**
         * Registers a post_type WPObject type to the schema.
         *
         * @param WP_Post_Type $post_type_object Post type.
         * @param TypeRegistry $type_registry    The Type Registry
         *
         * @return void
         * @throws Exception
         * @deprecated 1.12.0
         */
        public static function register_post_object_types(\WP_Post_Type $post_type_object, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Registers common post type fields on schema type corresponding to provided post type object.
         *
         * @param WP_Post_Type $post_type_object Post type.
         * @param TypeRegistry $type_registry    The Type Registry
         *
         * @deprecated 1.12.0
         *
         * @return array
         */
        public static function get_fields($post_type_object, $type_registry)
        {
        }
    }
    /**
     * Class Plugin
     *
     * @package WPGraphQL\Type\Object
     */
    class Plugin
    {
        /**
         * Registers the Plugin Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class Theme
     *
     * @package WPGraphQL\Type\Object
     */
    class Theme
    {
        /**
         * Register the Theme Type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class User
     *
     * @package WPGraphQL\Type\Object
     */
    class User
    {
        /**
         * Registers the User type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class MediaSize
    {
        /**
         * Register the MediaSize
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class Avatar
    {
        /**
         * Register the Avatar Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class SettingGroup
    {
        /**
         * Register each settings group to the GraphQL Schema
         *
         * @param string       $group_name    The name of the setting group
         * @param string       $group         The settings group config
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return string|null
         * @throws Exception
         */
        public static function register_settings_group(string $group_name, string $group, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Given the name of a registered settings group, retrieve GraphQL fields for the group
         *
         * @param string $group_name Name of the settings group to retrieve fields for
         * @param string $group      The settings group config
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return array
         */
        public static function get_settings_group_fields(string $group_name, string $group, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class Menu
    {
        /**
         * Register the Menu object type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class ContentType
    {
        /**
         * Register the ContentType Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class MenuItem
    {
        /**
         * Register the MenuItem Type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class Comment
     *
     * @package WPGraphQL\Type\Object
     */
    class Comment
    {
        /**
         * Register Comment Type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class MediaItemMeta
     *
     * @package WPGraphQL\Type\ObjectType
     */
    class MediaItemMeta
    {
        /**
         * Register the MediaItemMeta Type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class RootQuery
     *
     * @package WPGraphQL\Type\Object
     */
    class RootQuery
    {
        /**
         * Register the RootQuery type
         *
         * @return void
         */
        public static function register_type()
        {
        }
        /**
         * Register RootQuery fields for Post Objects of supported post types
         *
         * @return void
         */
        public static function register_post_object_fields()
        {
        }
        /**
         * Register RootQuery fields for Term Objects of supported taxonomies
         *
         * @return void
         */
        public static function register_term_object_fields()
        {
        }
    }
    class UserRole
    {
        /**
         * Register the UserRole Type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class RootMutation
    {
        /**
         * Register RootMutation type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class MediaDetails
    {
        /**
         * Register the MediaDetails type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class EnqueuedStylesheet
     *
     * @package WPGraphQL\Type\Object
     */
    class EnqueuedStylesheet
    {
        /**
         * Register the EnqueuedStylesheet Type
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
}
namespace WPGraphQL\Type {
    /**
     * Trait WPInterfaceTrait
     *
     * This Trait includes methods to help Interfaces and ObjectTypes ensure they implement
     * the proper inherited interfaces
     *
     * @package WPGraphQL\Type
     */
    trait WPInterfaceTrait
    {
        /**
         * Given an array of interfaces, this gets the Interfaces the Type should implement including
         * inherited interfaces.
         *
         * @return array
         */
        protected function get_implemented_interfaces() : array
        {
        }
    }
    class WPInterfaceType extends \GraphQL\Type\Definition\InterfaceType
    {
        use \WPGraphQL\Type\WPInterfaceTrait;
        /**
         * Instance of the TypeRegistry as an Interface needs knowledge of available Types
         *
         * @var TypeRegistry
         */
        public $type_registry;
        /**
         * @var array
         */
        public $config;
        /**
         * WPInterfaceType constructor.
         *
         * @param array        $config
         * @param TypeRegistry $type_registry
         *
         * @throws Exception
         */
        public function __construct(array $config, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Get interfaces implemented by this Interface
         *
         * @return array
         */
        public function getInterfaces() : array
        {
        }
        /**
         * This function sorts the fields and applies a filter to allow for easily
         * extending/modifying the shape of the Schema for the type.
         *
         * @param array  $fields
         * @param string $type_name
         *
         * @return mixed
         * @since 0.0.5
         */
        public function prepare_fields(array $fields, string $type_name)
        {
        }
    }
    /**
     * Class WPMutationType
     *
     * @package WPGraphQL\Type
     */
    class WPMutationType
    {
        /**
         * Configuration for how auth should be handled on the connection field
         *
         * @var array
         */
        protected $auth;
        /**
         * The config for the connection
         *
         * @var array
         */
        protected $config;
        /**
         * The name of the mutation field
         *
         * @var string
         */
        protected $mutation_name;
        /**
         * Whether the user must be authenticated to use the mutation.
         *
         * @var bool
         */
        protected $is_private;
        /**
         * The mutation input field config.
         *
         * @var array
         */
        protected $input_fields;
        /**
         * The mutation output field config.
         *
         * @var array
         */
        protected $output_fields;
        /**
         * The resolver function to resole the connection
         *
         * @var callable|Closure
         */
        protected $resolve_mutation;
        /**
         * The WPGraphQL TypeRegistry
         *
         * @var TypeRegistry
         */
        protected $type_registry;
        /**
         * WPMutationType constructor.
         *
         * @param array        $config        The config array for the mutation
         * @param TypeRegistry $type_registry Instance of the WPGraphQL Type Registry
         *
         * @throws Exception
         */
        public function __construct(array $config, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Validates that essential key/value pairs are passed to the connection config.
         *
         * @param array $config
         *
         * @return bool
         */
        protected function is_config_valid(array $config) : bool
        {
        }
        /**
         * Gets the mutation input fields.
         */
        protected function get_input_fields() : array
        {
        }
        /**
         * Gets the mutation output fields.
         */
        protected function get_output_fields() : array
        {
        }
        protected function get_resolver() : callable
        {
        }
        /**
         * Registers the input args for the mutation.
         */
        protected function register_mutation_input() : void
        {
        }
        protected function register_mutation_payload() : void
        {
        }
        /**
         * Registers the mutation in the Graph.
         */
        protected function register_mutation_field() : void
        {
        }
        /**
         * Registers the Mutation Types and field to the Schema.
         *
         * @throws Exception
         */
        protected function register_mutation() : void
        {
        }
    }
}
namespace WPGraphQL\Type\Enum {
    class AvatarRatingEnum
    {
        /**
         * Register the AvatarRatingEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class MenuLocationEnum
    {
        /**
         * Register the MenuLocationEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class UsersConnectionSearchColumnEnum
    {
        /**
         * Register the UsersConnectionSearchColumnEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class TaxonomyIdTypeEnum
    {
        /**
         * Register the TaxonomyIdTypeEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class MenuNodeIdTypeEnum
     *
     * @package WPGraphQL\Type\Enum
     */
    class MenuNodeIdTypeEnum
    {
        /**
         * Register the MenuNodeIdTypeEnum
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class TermObjectsConnectionOrderbyEnum
    {
        /**
         * Register the TermObjectsConnectionOrderbyEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class MenuItemNodeIdTypeEnum
     *
     * @package WPGraphQL\Type\Enum
     */
    class MenuItemNodeIdTypeEnum
    {
        /**
         * Register the MenuItemNodeIdTypeEnum
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class PostObjectsConnectionOrderbyEnum
    {
        /**
         * Register the PostObjectsConnectionOrderbyEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class TaxonomyEnum
    {
        /**
         * Register the TaxonomyEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class MediaItemSizeEnum
    {
        /**
         * Register the MediaItemSizeEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class UserRoleEnum
    {
        /**
         * Register the UserRoleEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class TimezoneEnum
    {
        /**
         * Register the TimezoneEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class PostObjectFieldFormatEnum
    {
        /**
         * Register the PostObjectFieldFormatEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class UsersConnectionOrderbyEnum
    {
        /**
         * Register the UsersConnectionOrderbyEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class CommentsConnectionOrderbyEnum
    {
        /**
         * Register the CommentsConnectionOrderbyEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class PostObjectsConnectionDateColumnEnum
    {
        /**
         * Register the PostObjectsConnectionDateColumnEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class TermNodeIdTypeEnum
    {
        /**
         * Register the Enum used for setting the field to identify term nodes by
         *
         * @return void
         */
        public static function register_type()
        {
        }
        /**
         * Get the values for the Enum definitions
         *
         * @return array
         */
        public static function get_values()
        {
        }
    }
    class OrderEnum
    {
        /**
         * Register the OrderEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class ContentTypeEnum
    {
        /**
         * Register the ContentTypeEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class ContentTypeIdTypeEnum
    {
        /**
         * Register the ContentTypeIdTypeEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class MimeTypeEnum
    {
        /**
         * Register the MimeTypeEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class CommentStatusEnum
    {
        /**
         * Register the CommentStatusEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class UserNodeIdTypeEnum
    {
        /**
         * Register the Enum used for setting the field to identify User nodes by
         *
         * @return void
         */
        public static function register_type()
        {
        }
        /**
         * Returns the values for the Enum.
         *
         * @return array
         */
        public static function get_values()
        {
        }
    }
    class MediaItemStatusEnum
    {
        /**
         * Register the MediaItemStatusEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    /**
     * Class CommentNodeIdTypeEnum
     *
     * @package WPGraphQL\Type\Enum
     */
    class CommentNodeIdTypeEnum
    {
        /**
         * Register the CommentNodeIdTypeEnum
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class RelationEnum
    {
        /**
         * Register the RelationEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class PostStatusEnum
    {
        /**
         * Register the PostStatusEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class PluginStatusEnum
    {
        /**
         * Register the PluginStatusEnum Type to the Schema
         *
         * @return void
         */
        public static function register_type()
        {
        }
        /**
         * Returns the array configuration for the GraphQL enum values.
         *
         * @return array
         */
        protected static function get_enum_values()
        {
        }
    }
    class ContentNodeIdTypeEnum
    {
        /**
         * Register the Enum used for setting the field to identify content nodes by
         *
         * @return void
         */
        public static function register_type()
        {
        }
        /**
         * Get the values for the Enum definitions
         *
         * @return array
         */
        public static function get_values()
        {
        }
    }
}
namespace WPGraphQL\Type {
    /**
     * Class WPInputObjectType
     *
     * Input types should extend this class to take advantage of the helper methods for formatting
     * and adding consistent filters.
     *
     * @package WPGraphQL\Type
     * @since 0.0.5
     */
    class WPInputObjectType extends \GraphQL\Type\Definition\InputObjectType
    {
        /**
         * WPInputObjectType constructor.
         *
         * @param array        $config
         */
        public function __construct(array $config, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Prepare_fields
         *
         * This function sorts the fields and applies a filter to allow for easily
         * extending/modifying the shape of the Schema for the type.
         *
         * @param array        $fields
         * @param string       $type_name
         * @param array        $config
         * @param TypeRegistry $type_registry
         * @return mixed
         * @since 0.0.5
         */
        public function prepare_fields(array $fields, string $type_name, array $config, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    /**
     * Class WPUnionType
     *
     * Union Types should extend this class to take advantage of the helper methods
     * and consistent filters.
     *
     * @package WPGraphQL\Type\Union
     * @since   0.0.30
     */
    class WPUnionType extends \GraphQL\Type\Definition\UnionType
    {
        /**
         * @var TypeRegistry
         */
        public $type_registry;
        /**
         * WPUnionType constructor.
         *
         * @param array        $config The Config to setup a Union Type
         * @param TypeRegistry $type_registry
         *
         * @since 0.0.30
         */
        public function __construct(array $config, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    /**
     * Class WPScalar
     *
     * @package WPGraphQL\Type
     */
    class WPScalar extends \GraphQL\Type\Definition\CustomScalarType
    {
        /**
         * WPScalar constructor.
         *
         * @param array        $config
         * @param TypeRegistry $type_registry
         */
        public function __construct(array $config, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
}
namespace WPGraphQL\Type\InterfaceType {
    class Connection
    {
        /**
         * Register the Connection Interface
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
    }
    class Previewable
    {
        /**
         * Adds the Previewable Type to the WPGraphQL Registry
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
    }
    class NodeWithTitle
    {
        /**
         * Registers the NodeWithTitle Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class PageInfo
    {
        /**
         * Register the PageInfo Interface
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
        /**
         * Get the fields for the PageInfo Type
         *
         * @return array[]
         */
        public static function get_fields() : array
        {
        }
    }
    class NodeWithComments
    {
        /**
         * Registers the NodeWithComments Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class ContentTemplate
    {
        /**
         * Register the ContentTemplate Interface
         *
         * @return void
         */
        public static function register_type()
        {
        }
        /**
         * Register individual GraphQL objects for supported theme templates.
         *
         * @return void
         */
        public static function register_content_template_types()
        {
        }
    }
    /**
     * Class HierarchicalContentNode
     *
     * @package WPGraphQL\Type\InterfaceType
     */
    class HierarchicalContentNode
    {
        /**
         * Register the HierarchicalContentNode Interface Type
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
    }
    class NodeWithTrackbacks
    {
        /**
         * Registers the NodeWithTrackbacks Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    /**
     * Class DatabaseIdentifier
     *
     * @package WPGraphQL\Type\InterfaceType
     */
    class DatabaseIdentifier
    {
        /**
         * Register the DatabaseIdentifier Interface.
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class NodeWithAuthor
    {
        /**
         * Registers the NodeWithAuthor Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class Edge
    {
        /**
         * Register the Connection Interface
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
    }
    class ContentNode
    {
        /**
         * Adds the ContentNode Type to the WPGraphQL Registry
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class MenuItemLinkable
    {
        /**
         * Registers the MenuItemLinkable Interface Type
         *
         * @param TypeRegistry $type_registry Instance of the WPGraphQL Type Registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
    }
    class NodeWithTemplate
    {
        /**
         * Registers the NodeWithTemplate Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class Node
    {
        /**
         * Register the Node interface
         *
         * @return void
         */
        public static function register_type()
        {
        }
    }
    class NodeWithPageAttributes
    {
        /**
         * Registers the NodeWithPageAttributes Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class TermNode
    {
        /**
         * Register the TermNode Interface
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class NodeWithFeaturedImage
    {
        /**
         * Registers the NodeWithFeaturedImage Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    /**
     * Class HierarchicalNode
     *
     * @package WPGraphQL\Type\InterfaceType
     */
    class HierarchicalNode
    {
        /**
         * Register the HierarchicalNode Interface Type
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
    }
    class NodeWithContentEditor
    {
        /**
         * Registers the NodeWithContentEditor Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class OneToOneConnection
    {
        /**
         * Register the Connection Interface
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
    }
    class UniformResourceIdentifiable
    {
        /**
         * Registers the UniformResourceIdentifiable Interface to the Schema.
         *
         * @param TypeRegistry $type_registry
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    /**
     * Class CommenterInterface
     *
     * @package WPGraphQL\Type\InterfaceType
     */
    class Commenter
    {
        /**
         * Register the Commenter Interface
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class NodeWithRevisions
    {
        /**
         * Registers the NodeWithRevisions Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    class NodeWithExcerpt
    {
        /**
         * Registers the NodeWithExcerpt Type to the Schema
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    /**
     * Class EnqueuedAsset
     *
     * @package WPGraphQL\Type
     */
    class EnqueuedAsset
    {
        /**
         * Register the Enqueued Script Type
         *
         * @param TypeRegistry $type_registry The WPGraphQL Type Registry
         *
         * @return void
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
    /**
     * Class HierarchicalTermNode
     *
     * @package WPGraphQL\Type\InterfaceType
     */
    class HierarchicalTermNode
    {
        /**
         * Register the HierarchicalTermNode Interface Type
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
    }
}
namespace WPGraphQL\Type\Union {
    /**
     * Class MenuItemObjectUnion
     *
     * @package WPGraphQL\Type\Union
     * @deprecated
     */
    class MenuItemObjectUnion
    {
        /**
         * Registers the Type
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Returns a list of possible types for the union
         *
         * @return array
         */
        public static function get_possible_types()
        {
        }
    }
    /**
     * Class PostObjectUnion
     *
     * @package WPGraphQL\Type\Union
     * @deprecated use ContentNode interface instead
     */
    class PostObjectUnion
    {
        /**
         * Registers the Type
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
        /**
         * Returns a list of possible types for the union
         *
         * @return array
         */
        public static function get_possible_types()
        {
        }
    }
    /**
     * Class TermObjectUnion
     *
     * @package WPGraphQL\Type\Union
     * @deprecated use TermNode interface instead
     */
    class TermObjectUnion
    {
        /**
         * Registers the Type
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public static function register_type(\WPGraphQL\Registry\TypeRegistry $type_registry) : void
        {
        }
        /**
         * Returns a list of possible types for the union
         *
         * @return array
         */
        public static function get_possible_types()
        {
        }
    }
}
namespace WPGraphQL\Type {
    /**
     * Class WPEnumType
     *
     * EnumTypes should extend this class to have filters and sorting applied, etc.
     *
     * @package WPGraphQL\Type
     */
    class WPEnumType extends \GraphQL\Type\Definition\EnumType
    {
        /**
         * WPEnumType constructor.
         *
         * @param array $config
         */
        public function __construct($config)
        {
        }
        /**
         * Generate a safe / sanitized name from a menu location slug.
         *
         * @param  string $value Enum value.
         * @return string
         */
        public static function get_safe_name(string $value)
        {
        }
        /**
         * This function sorts the values and applies a filter to allow for easily
         * extending/modifying the shape of the Schema for the enum.
         *
         * @param array  $values
         * @param string $type_name
         * @return mixed
         * @since 0.0.5
         */
        private static function prepare_values($values, $type_name)
        {
        }
    }
    /**
     * Class WPObjectType
     *
     * Object Types should extend this class to take advantage of the helper methods
     * and consistent filters.
     *
     * @package WPGraphQL\Type
     * @since   0.0.5
     */
    class WPObjectType extends \GraphQL\Type\Definition\ObjectType
    {
        use \WPGraphQL\Type\WPInterfaceTrait;
        /**
         * Holds the node_interface definition allowing WPObjectTypes
         * to easily define themselves as a node type by implementing
         * self::$node_interface
         *
         * @var array|Node $node_interface
         * @since 0.0.5
         */
        private static $node_interface;
        /**
         * Instance of the Type Registry
         *
         * @var TypeRegistry
         */
        public $type_registry;
        /**
         * @var array
         */
        public $config;
        /**
         * WPObjectType constructor.
         *
         * @param array        $config
         * @param TypeRegistry $type_registry
         *
         * @throws \Exception
         * @since 0.0.5
         */
        public function __construct($config, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Get the interfaces implemented by the ObjectType
         *
         * @return array
         */
        public function getInterfaces() : array
        {
        }
        /**
         * Node_interface
         *
         * This returns the node_interface definition allowing
         * WPObjectTypes to easily implement the node_interface
         *
         * @return array|Node
         * @since 0.0.5
         */
        public static function node_interface()
        {
        }
        /**
         * This function sorts the fields and applies a filter to allow for easily
         * extending/modifying the shape of the Schema for the type.
         *
         * @param array  $fields
         * @param string $type_name
         * @param array  $config
         *
         * @return mixed
         * @since 0.0.5
         */
        public function prepare_fields($fields, $type_name, $config)
        {
        }
    }
}
namespace WPGraphQL\Model {
    /**
     * Class Model - Abstract class for modeling data for all core types
     *
     * @package WPGraphQL\Model
     */
    abstract class Model
    {
        /**
         * Stores the name of the type the child class extending this one represents
         *
         * @var string $model_name
         */
        protected $model_name;
        /**
         * Stores the raw data passed to the child class when it's instantiated before it's transformed
         *
         * @var array|object|mixed $data
         */
        protected $data;
        /**
         * Stores the capability name for what to check on the user if the data should be considered
         * "Restricted"
         *
         * @var string $restricted_cap
         */
        protected $restricted_cap;
        /**
         * Stores the array of allowed fields to show if the data is restricted
         *
         * @var array $allowed_restricted_fields
         */
        protected $allowed_restricted_fields;
        /**
         * Stores the DB ID of the user that owns this piece of data, or null if there is no owner
         *
         * @var int|null $owner
         */
        protected $owner;
        /**
         * Stores the WP_User object for the current user in the session
         *
         * @var \WP_User $current_user
         */
        protected $current_user;
        /**
         * Stores the visibility value for the current piece of data
         *
         * @var string
         */
        protected $visibility;
        /**
         * The fields for the modeled object. This will be populated in the child class
         *
         * @var array $fields
         */
        public $fields;
        /**
         * Model constructor.
         *
         * @param string   $restricted_cap            The capability to check against to determine if
         *                                            the data should be restricted or not
         * @param array    $allowed_restricted_fields The allowed fields if the data is in fact
         *                                            restricted
         * @param null|int $owner                     Database ID of the user that owns this piece of
         *                                            data to compare with the current user ID
         *
         * @return void
         * @throws Exception Throws Exception.
         */
        protected function __construct($restricted_cap = '', $allowed_restricted_fields = [], $owner = null)
        {
        }
        /**
         * Magic method to re-map the isset check on the child class looking for properties when
         * resolving the fields
         *
         * @param string $key The name of the field you are trying to retrieve
         *
         * @return bool
         */
        public function __isset($key)
        {
        }
        /**
         * Magic method to re-map setting new properties to the class inside of the $fields prop rather
         * than on the class in unique properties
         *
         * @param string                    $key   Name of the key to set the data to
         * @param callable|int|string|mixed $value The value to set to the key
         *
         * @return void
         */
        public function __set($key, $value)
        {
        }
        /**
         * Magic method to re-map where external calls go to look for properties on the child objects.
         * This is crucial to let objects modeled through this class work with the default field
         * resolver.
         *
         * @param string $key Name of the property that is trying to be accessed
         *
         * @return mixed|null
         */
        public function __get($key)
        {
        }
        /**
         * Generic model setup before the resolver function executes
         *
         * @return void
         */
        public function setup()
        {
        }
        /**
         * Generic model tear down after the fields are setup. This can be used
         * to reset state to where it was before the model was setup.
         *
         * @return void
         */
        public function tear_down()
        {
        }
        /**
         * Returns the name of the model, built from the child className
         *
         * @return string
         */
        protected function get_model_name()
        {
        }
        /**
         * Return the visibility state for the current piece of data
         *
         * @return string|null
         */
        public function get_visibility()
        {
        }
        /**
         * Method to return the private state of the object. Can be overwritten in classes extending
         * this one.
         *
         * @return bool
         */
        protected function is_private()
        {
        }
        /**
         * Whether or not the owner of the data matches the current user
         *
         * @return bool
         */
        protected function owner_matches_current_user()
        {
        }
        /**
         * Restricts fields for the data to only return the allowed fields if the data is restricted
         *
         * @return void
         */
        protected function restrict_fields()
        {
        }
        /**
         * Wraps all fields with another callback layer so we can inject hooks & filters into them
         *
         * @return void
         */
        protected function wrap_fields()
        {
        }
        /**
         * Adds the model visibility fields to the data
         *
         * @return void
         */
        private function add_model_visibility()
        {
        }
        /**
         * Returns instance of the data fully modeled
         *
         * @return void
         */
        protected function prepare_fields()
        {
        }
        /**
         * Given a string, and optional context, this decodes html entities if html_entity_decode is
         * enabled.
         *
         * @param string $string     The string to decode
         * @param string $field_name The name of the field being encoded
         * @param bool   $enabled    Whether decoding is enabled by default for the string passed in
         *
         * @return string
         */
        public function html_entity_decode($string, $field_name, $enabled = false)
        {
        }
        /**
         * Filter the fields returned for the object
         *
         * @param null|string|array $fields The field or fields to build in the modeled object. You can
         *                                  pass null to build all of the fields, a string to only
         *                                  build an object with one field, or an array of field keys
         *                                  to build an object with those keys and their respective
         *                                  values.
         *
         * @return void
         */
        public function filter($fields)
        {
        }
        /**
         * @return mixed
         */
        protected abstract function init();
    }
    /**
     * Class CommentAuthor - Models the CommentAuthor object
     *
     * @property string $id
     * @property int    $databaseId
     * @property string $name
     * @property string $email
     * @property string $url
     *
     * @package WPGraphQL\Model
     */
    class CommentAuthor extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the comment author to be modeled
         *
         * @var WP_Comment $data The raw data passed to he model
         */
        protected $data;
        /**
         * CommentAuthor constructor.
         *
         * @param WP_Comment $comment_author The incoming comment author array to be modeled
         *
         * @throws Exception
         */
        public function __construct(\WP_Comment $comment_author)
        {
        }
        /**
         * Initializes the object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class Taxonomy - Models data for taxonomies
     *
     * @property string $id
     * @property array  $object_type
     * @property string $name
     * @property string $label
     * @property string $description
     * @property bool   $public
     * @property bool   $hierarchical
     * @property bool   $showUi
     * @property bool   $showInMenu
     * @property bool   $showInNavMenus
     * @property bool   $showCloud
     * @property bool   $showInQuickEdit
     * @property bool   $showInAdminColumn
     * @property bool   $showInRest
     * @property string $restBase
     * @property string $restControllerClass
     * @property bool   $showInGraphql
     * @property string $graphqlSingleName
     * @property string $graphql_single_name
     * @property string $graphqlPluralName
     * @property string $graphql_plural_name
     *
     * @package WPGraphQL\Model
     */
    class Taxonomy extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming WP_Taxonomy object to be modeled
         *
         * @var \WP_Taxonomy $data
         */
        protected $data;
        /**
         * Taxonomy constructor.
         *
         * @param \WP_Taxonomy $taxonomy The incoming Taxonomy to model
         *
         * @throws \Exception
         */
        public function __construct(\WP_Taxonomy $taxonomy)
        {
        }
        /**
         * Method for determining if the data should be considered private or not
         *
         * @return bool
         */
        protected function is_private()
        {
        }
        /**
         * Initializes the object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class PostType - Models data for PostTypes
     *
     * @property string $id
     * @property string $name
     * @property object $labels
     * @property string $description
     * @property bool   $public
     * @property bool   $hierarchical
     * @property bool   $excludeFromSearch
     * @property bool   $publiclyQueryable
     * @property bool   $showUi
     * @property bool   $showInMenu
     * @property bool   $showInNavMenus
     * @property bool   $showInAdminBar
     * @property int    $menuPosition
     * @property string $menuIcon
     * @property bool   $hasArchive
     * @property bool   $canExport
     * @property bool   $deleteWithUser
     * @property bool   $showInRest
     * @property string $restBase
     * @property string $restControllerClass
     * @property bool   $showInGraphql
     * @property string $graphqlSingleName
     * @property string $graphql_single_name
     * @property string $graphqlPluralName
     * @property string $graphql_plural_name
     * @property string $taxonomies
     *
     * @package WPGraphQL\Model
     */
    class PostType extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming WP_Post_Type to be modeled
         *
         * @var \WP_Post_Type $data
         */
        protected $data;
        /**
         * PostType constructor.
         *
         * @param \WP_Post_Type $post_type The incoming post type to model
         *
         * @throws \Exception
         */
        public function __construct(\WP_Post_Type $post_type)
        {
        }
        /**
         * Method for determining if the data should be considered private or not
         *
         * @return bool
         */
        protected function is_private()
        {
        }
        /**
         * Initializes the object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class Post - Models data for the Post object type
     *
     * @property int     $ID
     * @property string  $post_author
     * @property string  $id
     * @property string  $post_type
     * @property string  $authorId
     * @property string  $authorDatabaseId
     * @property int     $databaseId
     * @property string  $date
     * @property string  $dateGmt
     * @property string  $contentRendered
     * @property string  $contentRaw
     * @property string  $titleRendered
     * @property string  $titleRaw
     * @property string  $excerptRendered
     * @property string  $excerptRaw
     * @property string  $post_status
     * @property string  $status
     * @property string  $commentStatus
     * @property string  $pingStatus
     * @property string  $slug
     * @property array   $template
     * @property boolean $isFrontPage
     * @property boolean $isPrivacyPage
     * @property boolean $isPostsPage
     * @property boolean $isPreview
     * @property boolean $isRevision
     * @property boolean $isSticky
     * @property string  $toPing
     * @property string  $pinged
     * @property string  $modified
     * @property string  $modifiedGmt
     * @property string  $parentId
     * @property int     $parentDatabaseId
     * @property int     $editLastId
     * @property array   $editLock
     * @property string  $enclosure
     * @property string  $guid
     * @property int     $menuOrder
     * @property string  $link
     * @property string  $uri
     * @property int     $commentCount
     * @property string  $featuredImageId
     * @property int     $featuredImageDatabaseId
     * @property string  $pageTemplate
     * @property int     $previewRevisionDatabaseId
     *
     * @property string  $captionRaw
     * @property string  $captionRendered
     * @property string  $altText
     * @property string  $descriptionRaw
     * @property string  $descriptionRendered
     * @property string  $mediaType
     * @property string  $sourceUrl
     * @property string  $mimeType
     * @property array   $mediaDetails
     *
     * @package WPGraphQL\Model
     */
    class Post extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming post data
         *
         * @var WP_Post $data
         */
        protected $data;
        /**
         * Store the global post to reset during model tear down
         *
         * @var WP_Post
         */
        protected $global_post;
        /**
         * Stores the incoming post type object for the post being modeled
         *
         * @var null|WP_Post_Type $post_type_object
         */
        protected $post_type_object;
        /**
         * Store the instance of the WP_Query
         *
         * @var WP_Query
         */
        protected $wp_query;
        /**
         * Post constructor.
         *
         * @param WP_Post $post The incoming WP_Post object that needs modeling.
         *
         * @return void
         * @throws Exception
         */
        public function __construct(\WP_Post $post)
        {
        }
        /**
         * Setup the global data for the model to have proper context when resolving
         *
         * @return void
         */
        public function setup()
        {
        }
        /**
         * Retrieve the cap to check if the data should be restricted for the post
         *
         * @return string
         */
        protected function get_restricted_cap()
        {
        }
        /**
         * Determine if the model is private
         *
         * @return bool
         */
        public function is_private()
        {
        }
        /**
         * Method for determining if the data should be considered private or not
         *
         * @param WP_Post $post_object The object of the post we need to verify permissions for
         *
         * @return bool
         */
        protected function is_post_private($post_object = null)
        {
        }
        /**
         * Initialize the Post object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class Plugin - Models the Plugin object
     *
     * @property string $id
     * @property string $name
     * @property string $pluginUri
     * @property string $description
     * @property string $author
     * @property string $authorUri
     * @property string $version
     * @property string $path
     *
     * @package WPGraphQL\Model
     */
    class Plugin extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming plugin data to be modeled
         *
         * @var array $data
         */
        protected $data;
        /**
         * Plugin constructor.
         *
         * @param array $plugin The incoming Plugin data to be modeled
         *
         * @throws \Exception
         */
        public function __construct($plugin)
        {
        }
        /**
         * Method for determining if the data should be considered private or not
         *
         * @return bool
         */
        protected function is_private()
        {
        }
        /**
         * Initializes the object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class Theme - Models data for themes
     *
     * @property string     $id
     * @property string     $slug
     * @property string     $name
     * @property string     $screenshot
     * @property string     $themeUri
     * @property string     $description
     * @property string     $author
     * @property string     $authorUri
     * @property array      $tags
     * @property string|int $version
     *
     * @package WPGraphQL\Model
     */
    class Theme extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming WP_Theme to be modeled
         *
         * @var \WP_Theme $data
         */
        protected $data;
        /**
         * Theme constructor.
         *
         * @param \WP_Theme $theme The incoming WP_Theme to be modeled
         *
         * @return void
         * @throws \Exception
         */
        public function __construct(\WP_Theme $theme)
        {
        }
        /**
         * Method for determining if the data should be considered private or not
         *
         * @return bool
         */
        protected function is_private()
        {
        }
        /**
         * Initialize the object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class Term - Models data for Terms
     *
     * @property string $id
     * @property int    $term_id
     * @property int    $databaseId
     * @property int    $count
     * @property string $description
     * @property string $name
     * @property string $slug
     * @property int    $termGroupId
     * @property int    $termTaxonomyId
     * @property string $taxonomyName
     * @property string $link
     * @property string $parentId
     * @property int    $parentDatabaseId
     * @property array  $ancestors
     *
     * @package WPGraphQL\Model
     */
    class Term extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming WP_Term object
         *
         * @var WP_Term $data
         */
        protected $data;
        /**
         * Stores the taxonomy object for the term being modeled
         *
         * @var null|WP_Taxonomy $taxonomy_object
         */
        protected $taxonomy_object;
        /**
         * The global Post instance
         *
         * @var WP_Post
         */
        protected $global_post;
        /**
         * Term constructor.
         *
         * @param WP_Term $term The incoming WP_Term object that needs modeling
         *
         * @return void
         * @throws Exception
         */
        public function __construct(\WP_Term $term)
        {
        }
        /**
         * Setup the global state for the model to have proper context when resolving
         *
         * @return void
         */
        public function setup()
        {
        }
        /**
         * Reset global state after the model fields
         * have been generated
         *
         * @return void
         */
        public function tear_down()
        {
        }
        /**
         * Initializes the Term object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class User - Models the data for the User object type
     *
     * @property string $id
     * @property int    $databaseId
     * @property array  $capabilities
     * @property string $capKey
     * @property array  $roles
     * @property string $email
     * @property string $firstName
     * @property string $lastName
     * @property array  $extraCapabilities
     * @property string $description
     * @property string $username
     * @property string $name
     * @property string $registeredDate
     * @property string $nickname
     * @property string $url
     * @property string $slug
     * @property string $nicename
     * @property string $locale
     * @property int    $userId
     * @property string $uri
     * @property string $enqueuedScriptsQueue
     * @property string $enqueuedStylesheetsQueue
     *
     * @package WPGraphQL\Model
     */
    class User extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the WP_User object for the incoming data
         *
         * @var WP_User $data
         */
        protected $data;
        /**
         * The Global Post at time of Model generation
         *
         * @var WP_Post
         */
        protected $global_post;
        /**
         * The global authordata at time of Model generation
         *
         * @var WP_User
         */
        protected $global_authordata;
        /**
         * User constructor.
         *
         * @param WP_User $user The incoming WP_User object that needs modeling
         *
         * @return void
         * @throws Exception
         */
        public function __construct(\WP_User $user)
        {
        }
        /**
         * Setup the global data for the model to have proper context when resolving
         *
         * @return void
         */
        public function setup()
        {
        }
        /**
         * Reset global state after the model fields
         * have been generated
         *
         * @return void
         */
        public function tear_down()
        {
        }
        /**
         * Method for determining if the data should be considered private or not
         *
         * @return bool
         */
        protected function is_private()
        {
        }
        /**
         * Initialize the User object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class Avatar - Models data for avatars
     *
     * @property int    $size
     * @property int    $height
     * @property int    $width
     * @property string $default
     * @property bool   $forceDefault
     * @property string $rating
     * @property string $scheme
     * @property string $extraAttr
     * @property bool   $foundAvatar
     * @property string $url
     *
     * @package WPGraphQL\Model
     */
    class Avatar extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming avatar to be modeled
         *
         * @var array $data
         */
        protected $data;
        /**
         * Avatar constructor.
         *
         * @param array $avatar The incoming avatar to be modeled
         *
         * @throws Exception Throws Exception.
         */
        public function __construct(array $avatar)
        {
        }
        /**
         * Initializes the object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class Menu - Models data for Menus
     *
     * @property string $id
     * @property int    $count
     * @property int    $menuId
     * @property int    $databaseId
     * @property string $name
     * @property string $slug
     *
     * @package WPGraphQL\Model
     */
    class Menu extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming WP_Term object
         *
         * @var \WP_Term $data
         */
        protected $data;
        /**
         * Menu constructor.
         *
         * @param \WP_Term $term The incoming WP_Term object that needs modeling
         *
         * @return void
         * @throws \Exception
         */
        public function __construct(\WP_Term $term)
        {
        }
        /**
         * Determines whether a Menu should be considered private.
         *
         * If a Menu is not connected to a menu that's assigned to a location
         * it's not considered a public node
         *
         * @return bool
         * @throws \Exception
         */
        public function is_private()
        {
        }
        /**
         * Initializes the Menu object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class MenuItem - Models the data for the MenuItem object type
     *
     * @property string $id
     * @property array  $cssClasses
     * @property string $description
     * @property string $label
     * @property string $linkRelationship
     * @property int    $menuItemId
     * @property int    $databaseId
     * @property int    $objectId
     * @property string $target
     * @property string $title
     * @property string $url
     * @property string $menuId
     * @property int    $menuDatabaseId
     * @property array  $locations
     *
     * @package WPGraphQL\Model
     */
    class MenuItem extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming post data
         *
         * @var mixed|object $data
         */
        protected $data;
        /**
         * MenuItem constructor.
         *
         * @param WP_Post $post The incoming WP_Post object that needs modeling
         *
         * @return void
         * @throws Exception
         */
        public function __construct(\WP_Post $post)
        {
        }
        /**
         * Determines whether a MenuItem should be considered private.
         *
         * If a MenuItem is not connected to a menu that's assigned to a location
         * it's not considered a public node
         *
         * @return bool
         * @throws Exception
         */
        public function is_private()
        {
        }
        /**
         * Initialize the MenuItem object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class Comment - Models data for Comments
     *
     * @property int    $comment_ID
     * @property int    $comment_parent_id
     * @property int    $commentId
     * @property int    $parentDatabaseId
     * @property int    $userId
     * @property string $agent
     * @property string $authorIp
     * @property string $comment_author
     * @property string $comment_author_url
     * @property string $commentAuthorEmail
     * @property string $contentRaw
     * @property string $contentRendered
     * @property string $date
     * @property string $dateGmt
     * @property string $id
     * @property string $karma
     * @property string $parentId
     * @property string $status
     * @property string $type
     *
     * @package WPGraphQL\Model
     */
    class Comment extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming WP_Comment object to be modeled
         *
         * @var WP_Comment $data
         */
        protected $data;
        /**
         * Comment constructor.
         *
         * @param WP_Comment $comment The incoming WP_Comment to be modeled
         *
         * @throws Exception
         */
        public function __construct(\WP_Comment $comment)
        {
        }
        /**
         * Method for determining if the data should be considered private or not
         *
         * @return bool
         * @throws Exception
         */
        protected function is_private()
        {
        }
        /**
         * Initializes the object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
    /**
     * Class UserRole - Models data for user roles
     *
     * @property string $displayName
     * @property string $id
     * @property string $name
     * @property array  $capabilities
     *
     * @package WPGraphQL\Model
     */
    class UserRole extends \WPGraphQL\Model\Model
    {
        /**
         * Stores the incoming user role to be modeled
         *
         * @var array $data
         */
        protected $data;
        /**
         * UserRole constructor.
         *
         * @param array $user_role The incoming user role to be modeled
         *
         * @return void
         * @throws \Exception
         */
        public function __construct($user_role)
        {
        }
        /**
         * Method for determining if the data should be considered private or not
         *
         * @return bool
         */
        protected function is_private()
        {
        }
        /**
         * Initializes the object
         *
         * @return void
         */
        protected function init()
        {
        }
    }
}
namespace WPGraphQL\Registry {
    /**
     * Class SchemaRegistry
     *
     * @package WPGraphQL\Registry
     */
    class SchemaRegistry
    {
        /**
         * @var TypeRegistry
         */
        protected $type_registry;
        /**
         * SchemaRegistry constructor.
         *
         * @throws Exception
         */
        public function __construct()
        {
        }
        /**
         * Returns the Schema to use for execution of the GraphQL Request
         *
         * @return WPSchema
         * @throws Exception
         */
        public function get_schema()
        {
        }
    }
    /**
     * Class TypeRegistry
     *
     * This class maintains the registry of Types used in the GraphQL Schema
     *
     * @package WPGraphQL\Registry
     */
    class TypeRegistry
    {
        /**
         * The registered Types
         *
         * @var array
         */
        protected $types;
        /**
         * The loaders needed to register types
         *
         * @var array
         */
        protected $type_loaders;
        /**
         * Stores a list of Types that need to be eagerly loaded instead of lazy loaded.
         *
         * Types that exist in the Schema but are only part of a Union/Interface ResolveType but not
         * referenced directly need to be eagerly loaded.
         *
         * @var array
         */
        protected $eager_type_map;
        /**
         * Stores a list of Types that should be excluded from the schema.
         *
         * Type names are filtered by `graphql_excluded_types` and normalized using strtolower(), to avoid case sensitivity issues.
         *
         * @var array
         */
        protected $excluded_types = null;
        /**
         * TypeRegistry constructor.
         */
        public function __construct()
        {
        }
        /**
         * Formats the array key to a more friendly format
         *
         * @param string $key Name of the array key to format
         *
         * @return string
         */
        protected function format_key(string $key)
        {
        }
        /**
         * Returns the eager type map, an array of Type definitions for Types that
         * are not directly referenced in the schema.
         *
         * Types can add "eagerlyLoadType => true" when being registered to be included
         * in the eager_type_map.
         *
         * @return array
         */
        protected function get_eager_type_map()
        {
        }
        /**
         * Initialize the TypeRegistry
         *
         * @throws Exception
         *
         * @return void
         */
        public function init()
        {
        }
        /**
         * Initialize the Type Registry
         *
         * @param TypeRegistry $type_registry
         *
         * @return void
         * @throws Exception
         */
        public function init_type_registry(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Given a config for a custom Scalar, this adds the Scalar for use in the Schema.
         *
         * @param string $type_name The name of the Type to register
         * @param array  $config    The config for the scalar type to register
         *
         * @throws Exception
         *
         * @return void
         */
        public function register_scalar(string $type_name, array $config)
        {
        }
        /**
         * Registers connections that were passed through the Type registration config
         *
         * @param array $config Type config
         *
         * @return void
         *
         * @throws Exception
         */
        protected function register_connections_from_config(array $config)
        {
        }
        /**
         * Add a Type to the Registry
         *
         * @param string $type_name The name of the type to register
         * @param mixed|array|Type $config The config for the type
         *
         * @throws Exception
         *
         * @return void
         */
        public function register_type(string $type_name, $config)
        {
        }
        /**
         * Add an Object Type to the Registry
         *
         * @param string $type_name The name of the type to register
         * @param array $config The configuration of the type
         *
         * @throws Exception
         * @return void
         */
        public function register_object_type(string $type_name, array $config)
        {
        }
        /**
         * Add an Interface Type to the registry
         *
         * @param string $type_name The name of the type to register
         * @param array $config he configuration of the type
         *
         * @throws Exception
         * @return void
         */
        public function register_interface_type(string $type_name, array $config)
        {
        }
        /**
         * Add an Enum Type to the registry
         *
         * @param string $type_name The name of the type to register
         * @param array $config he configuration of the type
         *
         * @return void
         * @throws Exception
         */
        public function register_enum_type(string $type_name, array $config)
        {
        }
        /**
         * Add an Input Type to the Registry
         *
         * @param string $type_name The name of the type to register
         * @param array $config he configuration of the type
         *
         * @return void
         * @throws Exception
         */
        public function register_input_type(string $type_name, array $config)
        {
        }
        /**
         * Add a Union Type to the Registry
         *
         * @param string $type_name The name of the type to register
         * @param array $config he configuration of the type
         *
         * @return void
         *
         * @throws Exception
         */
        public function register_union_type(string $type_name, array $config)
        {
        }
        /**
         * @param string $type_name The name of the type to register
         * @param mixed|array|Type $config he configuration of the type
         *
         * @return mixed|array|Type|null
         * @throws Exception
         */
        public function prepare_type(string $type_name, $config)
        {
        }
        /**
         * Given a type name, returns the type or null if not found
         *
         * @param string $type_name The name of the Type to get from the registry
         *
         * @return mixed
         * |null
         */
        public function get_type(string $type_name)
        {
        }
        /**
         * Given a type name, determines if the type is already present in the Type Loader
         *
         * @param string $type_name The name of the type to check the registry for
         *
         * @return bool
         */
        public function has_type(string $type_name)
        {
        }
        /**
         * Return the Types in the registry
         *
         * @return array
         */
        public function get_types()
        {
        }
        /**
         * Wrapper for prepare_field to prepare multiple fields for registration at once
         *
         * @param array  $fields    Array of fields and their settings to register on a Type
         * @param string $type_name Name of the Type to register the fields to
         *
         * @return array
         * @throws Exception
         */
        public function prepare_fields(array $fields, string $type_name)
        {
        }
        /**
         * Prepare the field to be registered on the type
         *
         * @param string $field_name   Friendly name of the field
         * @param array  $field_config Config data about the field to prepare
         * @param string $type_name    Name of the type to prepare the field for
         *
         * @return array|null
         * @throws Exception
         */
        protected function prepare_field($field_name, $field_config, $type_name)
        {
        }
        /**
         * Processes type modifiers (e.g., "non-null"). Loads types immediately, so do
         * not call before types are ready to be loaded.
         *
         * @param mixed|string|array $type The type definition
         *
         * @return mixed
         * @throws Exception
         */
        public function setup_type_modifiers($type)
        {
        }
        /**
         * Wrapper for the register_field method to register multiple fields at once
         *
         * @param string $type_name Name of the type in the Type Registry to add the fields to
         * @param array  $fields    Fields to register
         *
         * @return void
         */
        public function register_fields(string $type_name, array $fields = [])
        {
        }
        /**
         * Add a field to a Type in the Type Registry
         *
         * @param string $type_name  Name of the type in the Type Registry to add the fields to
         * @param string $field_name Name of the field to add to the type
         * @param array  $config     Info about the field to register to the type
         *
         * @return void
         */
        public function register_field(string $type_name, string $field_name, array $config)
        {
        }
        /**
         * Remove a field from a type
         *
         * @param string $type_name  Name of the Type the field is registered to
         * @param string $field_name Name of the field you would like to remove from the type
         *
         * @return void
         */
        public function deregister_field(string $type_name, string $field_name)
        {
        }
        /**
         * Method to register a new connection in the Type registry
         *
         * @param array $config The info about the connection being registered
         *
         * @return void
         * @throws InvalidArgumentException
         * @throws Exception
         */
        public function register_connection(array $config)
        {
        }
        /**
         * Handles registration of a mutation to the Type registry
         *
         * @param string $mutation_name Name of the mutation being registered
         * @param array  $config        Info about the mutation being registered
         *
         * @return void
         * @throws Exception
         */
        public function register_mutation(string $mutation_name, array $config)
        {
        }
        /**
         * Given a Type, this returns an instance of a NonNull of that type
         *
         * @param mixed $type The Type being wrapped
         *
         * @return NonNull
         */
        public function non_null($type)
        {
        }
        /**
         * Given a Type, this returns an instance of a listOf of that type
         *
         * @param mixed $type The Type being wrapped
         *
         * @return ListOfType
         */
        public function list_of($type)
        {
        }
        /**
         * Get the list of GraphQL type names to exclude from the schema.
         *
         * Type names are normalized using `strtolower()`, to avoid case sensitivity issues.
         *
         * @since 1.13.0
         */
        public function get_excluded_types() : array
        {
        }
        /**
         * Gets the actual type name, stripped of possible NonNull and ListOf wrappers.
         *
         * Returns an empty string if the type modifiers are malformed.
         *
         * @param string|array $type The (possibly-wrapped) type name.
         */
        protected function get_unmodified_type_name($type) : string
        {
        }
    }
}
namespace WPGraphQL\Registry\Utils {
    /**
     * Class TermObjectType
     *
     * @package WPGraphQL\Data
     * @since   1.12.0
     */
    class TermObject
    {
        /**
         * Registers a taxonomy type to the schema as either a GraphQL object, interface, or union.
         *
         * @param WP_Taxonomy $tax_object Taxonomy.
         *
         * @return void
         * @throws Exception
         */
        public static function register_types(\WP_Taxonomy $tax_object)
        {
        }
        /**
         * Gets all the connections for the given post type.
         *
         * @param WP_Taxonomy $tax_object
         *
         * @return array
         */
        protected static function get_connections(\WP_Taxonomy $tax_object)
        {
        }
        /**
         * Gets all the interfaces for the given Taxonomy.
         *
         * @param WP_Taxonomy $tax_object Taxonomy.
         *
         * @return array
         */
        protected static function get_interfaces(\WP_Taxonomy $tax_object)
        {
        }
        /**
         * Registers common Taxonomy fields on schema type corresponding to provided Taxonomy object.
         *
         * @param WP_Taxonomy $tax_object Taxonomy.
         *
         * @return array
         */
        protected static function get_fields(\WP_Taxonomy $tax_object)
        {
        }
    }
    /**
     * Class PostObject
     *
     * @package WPGraphQL\Data
     * @since   1.12.0
     */
    class PostObject
    {
        /**
         * Registers a post_type type to the schema as either a GraphQL object, interface, or union.
         *
         * @param WP_Post_Type $post_type_object Post type.
         *
         * @return void
         * @throws Exception
         */
        public static function register_types(\WP_Post_Type $post_type_object)
        {
        }
        /**
         * Gets all the connections for the given post type.
         *
         * @param WP_Post_Type $post_type_object
         *
         * @return array
         */
        protected static function get_connections(\WP_Post_Type $post_type_object)
        {
        }
        /**
         * Gets all the interfaces for the given post type.
         *
         * @param WP_Post_Type $post_type_object Post type.
         *
         * @return array
         */
        protected static function get_interfaces(\WP_Post_Type $post_type_object)
        {
        }
        /**
         * Registers common post type fields on schema type corresponding to provided post type object.
         *
         * @param WP_Post_Type $post_type_object Post type.
         *
         * @return array
         * @todo make protected after \Type\ObjectType\PostObject::get_fields() is removed.
         */
        public static function get_fields(\WP_Post_Type $post_type_object)
        {
        }
        /**
         * Register fields to the Type used for attachments (MediaItem).
         *
         * @return void
         */
        private static function register_attachment_fields(\WP_Post_Type $post_type_object)
        {
        }
    }
}
namespace WPGraphQL {
    /**
     * Class Router
     * This sets up the /graphql endpoint
     *
     * @package WPGraphQL
     * @since   0.0.1
     */
    class Router
    {
        /**
         * Sets the route to use as the endpoint
         *
         * @var string $route
         */
        public static $route = 'graphql';
        /**
         * Holds the Global Post for later resetting
         *
         * @var string
         */
        protected static $global_post = '';
        /**
         * Set the default status code to 200.
         *
         * @var int
         */
        public static $http_status_code = 200;
        /**
         * @var Request
         */
        protected static $request;
        /**
         * Initialize the WPGraphQL Router
         *
         * @return void
         * @throws Exception
         */
        public function init()
        {
        }
        /**
         * Returns the GraphQL Request being executed
         *
         * @return Request
         */
        public static function get_request()
        {
        }
        /**
         * Adds rewrite rule for the route endpoint
         *
         * @return void
         * @since  0.0.1
         * @uses   add_rewrite_rule()
         */
        public static function add_rewrite_rule()
        {
        }
        /**
         * Determines whether the request is an API request to play nice with
         * application passwords and potential other WordPress core functionality
         * for APIs
         *
         * @param bool $is_api_request Whether the request is an API request
         *
         * @return bool
         */
        public function is_api_request($is_api_request)
        {
        }
        /**
         * Adds the query_var for the route
         *
         * @param array $query_vars The array of whitelisted query variables.
         *
         * @return array
         * @since  0.0.1
         */
        public static function add_query_var($query_vars)
        {
        }
        /**
         * Returns true when the current request is a GraphQL request coming from the HTTP
         *
         * NOTE: This will only indicate whether the GraphQL Request is an HTTP request. Many features
         * need to affect _all_ GraphQL requests, including internal requests using the `graphql()`
         * function, so be careful how you use this to check your conditions.
         *
         * @return boolean
         */
        public static function is_graphql_http_request()
        {
        }
        /**
         * DEPRECATED: Returns whether a request is a GraphQL Request. Deprecated
         * because it's name is a bit misleading. This will only return if the request
         * is a GraphQL request coming from the HTTP endpoint. Internal GraphQL requests
         * won't be able to use this to properly determine if the request is a GraphQL request
         * or not.
         *
         * @return boolean
         * @deprecated 0.4.1 Use Router::is_graphql_http_request instead. This now resolves to it
         */
        public static function is_graphql_request()
        {
        }
        /**
         * This resolves the http request and ensures that WordPress can respond with the appropriate
         * JSON response instead of responding with a template from the standard WordPress Template
         * Loading process
         *
         * @return void
         * @throws Exception Throws exception.
         * @throws \Throwable Throws exception.
         * @since  0.0.1
         */
        public static function resolve_http_request()
        {
        }
        /**
         * Sends an HTTP header.
         *
         * @param string $key   Header key.
         * @param string $value Header value.
         *
         * @return void
         * @since  0.0.5
         */
        public static function send_header($key, $value)
        {
        }
        /**
         * Sends an HTTP status code.
         *
         * @return void
         */
        protected static function set_status()
        {
        }
        /**
         * Returns an array of headers to send with the HTTP response
         *
         * @return array
         */
        protected static function get_response_headers()
        {
        }
        /**
         * Set the response headers
         *
         * @return void
         * @since  0.0.1
         */
        public static function set_headers()
        {
        }
        /**
         * Retrieves the raw request entity (body).
         *
         * @since  0.0.5
         *
         * @global string php://input Raw post data.
         *
         * @return string|false Raw request data.
         */
        public static function get_raw_data()
        {
        }
        /**
         * This processes the graphql requests that come into the /graphql endpoint via an HTTP request
         *
         * @return mixed
         * @throws Exception Throws Exception.
         * @throws \Throwable Throws Exception.
         * @global WP_User $current_user The currently authenticated user.
         * @since  0.0.1
         */
        public static function process_http_request()
        {
        }
        /**
         * Prepare headers for response
         *
         * @param mixed|array|ExecutionResult $response        The response of the GraphQL Request.
         * @param mixed|array|ExecutionResult $graphql_results The results of the GraphQL execution.
         * @param string                      $query           The GraphQL query.
         * @param string                      $operation_name  The operation name of the GraphQL
         *                                                     Request.
         * @param mixed|array|null            $variables       The variables applied to the GraphQL
         *                                                     Request.
         * @param mixed|WP_User|null          $user            The current user object.
         *
         * @return void
         */
        protected static function prepare_headers($response, $graphql_results, string $query, string $operation_name, $variables, $user = null)
        {
        }
    }
    /**
     * This class was used to access Type definitions pre v0.4.0, but is no longer used.
     * See upgrade guide vor v0.4.0 (https://github.com/wp-graphql/wp-graphql/releases/tag/v0.4.0) for
     * information on updating to use non-static TypeRegistry methods to get_type(), etc.
     *
     * @deprecated since v0.6.0. Old static methods can now be done by accessing the
     *             TypeRegistry class from within the `graphql_register_types` hook
     */
    class Types
    {
        /**
         * @deprecated since v0.6.0. Use Utils:map_input instead
         *
         * @param array $args The raw query args from the GraphQL query.
         * @param array $map  The mapping of where each of the args should go.
         *
         * @return array
         */
        public static function map_input($args, $map)
        {
        }
        /**
         * @deprecated since v0.6.0 use Utils::prepare_date_response(); instead
         * @param string      $date_gmt GMT publication time.
         * @param string|null $date     Optional. Local publication time. Default null.
         * @return string|null ISO8601/RFC3339 formatted datetime.
         */
        public static function prepare_date_response($date_gmt, $date = null)
        {
        }
    }
    /**
     * Class WPSchema
     *
     * Extends the Schema to make some properties accessible via hooks/filters
     *
     * @package WPGraphQL
     */
    class WPSchema extends \GraphQL\Type\Schema
    {
        /**
         * @var SchemaConfig
         */
        public $config;
        /**
         * Holds the $filterable_config which allows WordPress access to modifying the
         * $config that gets passed down to the Executable Schema
         *
         * @var SchemaConfig|null
         * @since 0.0.9
         */
        public $filterable_config;
        /**
         * WPSchema constructor.
         *
         * @param SchemaConfig $config The config for the Schema.
         * @param TypeRegistry $type_registry
         *
         * @since 0.0.9
         */
        public function __construct(\GraphQL\Type\SchemaConfig $config, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
    }
}
namespace WPGraphQL\Data\Connection {
    /**
     * Class AbstractConnectionResolver
     *
     * ConnectionResolvers should extend this to make returning data in proper shape for
     * connections easier, ensure data is passed through consistent filters, etc.
     *
     * @package WPGraphQL\Data\Connection
     */
    abstract class AbstractConnectionResolver
    {
        /**
         * The source from the field calling the connection
         *
         * @var mixed
         */
        protected $source;
        /**
         * The args input on the field calling the connection
         *
         * @var array
         */
        protected $args;
        /**
         * The AppContext for the GraphQL Request
         *
         * @var \WPGraphQL\AppContext
         */
        protected $context;
        /**
         * The ResolveInfo for the GraphQL Request
         *
         * @var \GraphQL\Type\Definition\ResolveInfo
         */
        protected $info;
        /**
         * The query args used to query for data to resolve the connection
         *
         * @var array
         */
        protected $query_args;
        /**
         * Whether the connection resolver should execute
         *
         * @var bool
         */
        protected $should_execute = true;
        /**
         * The loader the resolver is configured to use.
         *
         * @var AbstractDataLoader
         */
        protected $loader;
        /**
         * Whether the connection is a one to one connection. Default false.
         *
         * @var bool
         */
        public $one_to_one = false;
        /**
         * The Query class/array/object used to fetch the data.
         *
         * Examples:
         *   return new WP_Query( $this->query_args );
         *   return new WP_Comment_Query( $this->query_args );
         *   return new WP_Term_Query( $this->query_args );
         *
         * Whatever it is will be passed through filters so that fields throughout
         * have context from what was queried and can make adjustments as needed, such
         * as exposing `totalCount` in pageInfo, etc.
         *
         * @var mixed
         */
        protected $query;
        /**
         * @var array
         */
        protected $items;
        /**
         * @var array
         */
        protected $ids;
        /**
         * @var array
         */
        protected $nodes;
        /**
         * @var array
         */
        protected $edges;
        /**
         * @var int
         */
        protected $query_amount;
        /**
         * ConnectionResolver constructor.
         *
         * @param mixed       $source  source passed down from the resolve tree
         * @param array       $args    array of arguments input in the field as part of the GraphQL
         *                             query
         * @param AppContext  $context Object containing app context that gets passed down the resolve
         *                             tree
         * @param ResolveInfo $info    Info about fields passed down the resolve tree
         *
         * @throws Exception
         */
        public function __construct($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Returns the source of the connection
         *
         * @return mixed
         */
        public function getSource()
        {
        }
        /**
         * Get the loader name
         *
         * @return AbstractDataLoader
         * @throws Exception
         */
        protected function getLoader()
        {
        }
        /**
         * Returns the $args passed to the connection
         *
         * @deprecated Deprecated since v1.11.0 in favor of $this->get_args();
         *
         * @codeCoverageIgnore
         */
        public function getArgs() : array
        {
        }
        /**
         * Returns the $args passed to the connection.
         *
         * Useful for modifying the $args before they are passed to $this->get_query_args().
         *
         * @return array
         */
        public function get_args() : array
        {
        }
        /**
         * Returns the AppContext of the connection
         *
         * @return AppContext
         */
        public function getContext() : \WPGraphQL\AppContext
        {
        }
        /**
         * Returns the ResolveInfo of the connection
         *
         * @return ResolveInfo
         */
        public function getInfo() : \GraphQL\Type\Definition\ResolveInfo
        {
        }
        /**
         * Returns whether the connection should execute
         *
         * @return bool
         */
        public function getShouldExecute() : bool
        {
        }
        /**
         * @param string $key   The key of the query arg to set
         * @param mixed  $value The value of the query arg to set
         *
         * @return AbstractConnectionResolver
         *
         * @deprecated 0.3.0
         *
         * @codeCoverageIgnore
         */
        public function setQueryArg($key, $value)
        {
        }
        /**
         * Given a key and value, this sets a query_arg which will modify the query_args used by
         * the connection resolvers get_query();
         *
         * @param string $key   The key of the query arg to set
         * @param mixed  $value The value of the query arg to set
         *
         * @return AbstractConnectionResolver
         */
        public function set_query_arg($key, $value)
        {
        }
        /**
         * Whether the connection should resolve as a one-to-one connection.
         *
         * @return AbstractConnectionResolver
         */
        public function one_to_one()
        {
        }
        /**
         * Get_loader_name
         *
         * Return the name of the loader to be used with the connection resolver
         *
         * @return string
         */
        public abstract function get_loader_name();
        /**
         * Get_query_args
         *
         * This method is used to accept the GraphQL Args input to the connection and return args
         * that can be used in the Query to the datasource.
         *
         * For example, if the ConnectionResolver uses WP_Query to fetch the data, this
         * should return $args for use in `new WP_Query`
         *
         * @return array
         */
        public abstract function get_query_args();
        /**
         * Get_query
         *
         * The Query used to get items from the database (or even external datasource) are all
         * different.
         *
         * Each connection resolver should be responsible for defining the Query object that
         * is used to fetch items.
         *
         * @return mixed
         */
        public abstract function get_query();
        /**
         * Should_execute
         *
         * Determine whether or not the query should execute.
         *
         * Return true to exeucte, return false to prevent execution.
         *
         * Various criteria can be used to determine whether a Connection Query should
         * be executed.
         *
         * For example, if a user is requesting revisions of a Post, and the user doesn't have
         * permission to edit the post, they don't have permission to view the revisions, and therefore
         * we can prevent the query to fetch revisions from executing in the first place.
         *
         * @return bool
         */
        public abstract function should_execute();
        /**
         * Is_valid_offset
         *
         * Determine whether or not the the offset is valid, i.e the item corresponding to the offset
         * exists. Offset is equivalent to WordPress ID (e.g post_id, term_id). So this function is
         * equivalent to checking if the WordPress object exists for the given ID.
         *
         * @param mixed $offset The offset to validate. Typically a WordPress Database ID
         *
         * @return bool
         */
        public abstract function is_valid_offset($offset);
        /**
         * Return an array of ids from the query
         *
         * Each Query class in WP and potential datasource handles this differently, so each connection
         * resolver should handle getting the items into a uniform array of items.
         *
         * Note: This is not an abstract function to prevent backwards compatibility issues, so it
         * instead throws an exception. Classes that extend AbstractConnectionResolver should
         * override this method, instead of AbstractConnectionResolver::get_ids().
         *
         * @since 1.9.0
         *
         * @throws Exception if child class forgot to implement this.
         *
         * @return array the array of IDs.
         */
        public function get_ids_from_query()
        {
        }
        /**
         * Given an ID, return the model for the entity or null
         *
         * @param mixed $id The ID to identify the object by. Could be a database ID or an in-memory ID
         *                  (like post_type name)
         *
         * @return mixed|Model|null
         * @throws Exception
         */
        public function get_node_by_id($id)
        {
        }
        /**
         * Get_query_amount
         *
         * Returns the max between what was requested and what is defined as the $max_query_amount to
         * ensure that queries don't exceed unwanted limits when querying data.
         *
         * @return int
         * @throws Exception
         */
        public function get_query_amount()
        {
        }
        /**
         * Get_amount_requested
         *
         * This checks the $args to determine the amount requested, and if
         *
         * @return int|null
         * @throws Exception
         */
        public function get_amount_requested()
        {
        }
        /**
         * Gets the offset for the `after` cursor.
         *
         * @return int|string|null
         */
        public function get_after_offset()
        {
        }
        /**
         * Gets the offset for the `before` cursor.
         *
         * @return int|string|null
         */
        public function get_before_offset()
        {
        }
        /**
         * Gets the array index for the given offset.
         *
         * @param int|string|false $offset The cursor pagination offset.
         * @param array      $ids    The array of ids from the query.
         *
         * @return int|false $index The array index of the offset.
         */
        public function get_array_index_for_offset($offset, $ids)
        {
        }
        /**
         * Returns an array slice of IDs, per the Relay Cursor Connection spec.
         *
         * The resulting array should be overfetched by 1.
         *
         * @see https://relay.dev/graphql/connections.htm#sec-Pagination-algorithm
         *
         * @param array $ids The array of IDs from the query to slice, ordered as expected by the GraphQL query.
         *
         * @since 1.9.0
         *
         * @return array
         */
        public function apply_cursors_to_ids(array $ids)
        {
        }
        /**
         * Returns an array of IDs for the connection.
         *
         * These IDs have been fetched from the query with all the query args applied,
         * then sliced (overfetching by 1) by pagination args.
         *
         * @return array
         */
        public function get_ids()
        {
        }
        /**
         * Get_offset
         *
         * This returns the offset to be used in the $query_args based on the $args passed to the
         * GraphQL query.
         *
         * @deprecated 1.9.0
         *
         * @codeCoverageIgnore
         *
         * @return int|mixed
         */
        public function get_offset()
        {
        }
        /**
         * Returns the offset for a given cursor.
         *
         * Connections that use a string-based offset should override this method.
         *
         * @return int|mixed
         */
        public function get_offset_for_cursor(string $cursor = null)
        {
        }
        /**
         * Has_next_page
         *
         * Whether there is a next page in the connection.
         *
         * If there are more "items" than were asked for in the "first" argument
         * ore if there are more "items" after the "before" argument, has_next_page()
         * will be set to true
         *
         * @return boolean
         */
        public function has_next_page()
        {
        }
        /**
         * Has_previous_page
         *
         * Whether there is a previous page in the connection.
         *
         * If there are more "items" than were asked for in the "last" argument
         * or if there are more "items" before the "after" argument, has_previous_page()
         * will be set to true.
         *
         * @return boolean
         */
        public function has_previous_page()
        {
        }
        /**
         * Get_start_cursor
         *
         * Determine the start cursor from the connection
         *
         * @return mixed string|null
         */
        public function get_start_cursor()
        {
        }
        /**
         * Get_end_cursor
         *
         * Determine the end cursor from the connection
         *
         * @return mixed string|null
         */
        public function get_end_cursor()
        {
        }
        /**
         * Gets the IDs for the currently-paginated slice of nodes.
         *
         * We slice the array to match the amount of items that was asked for, as we over-fetched by 1 item to calculate pageInfo.
         *
         * @used-by AbstractConnectionResolver::get_nodes()
         *
         * @return array
         */
        public function get_ids_for_nodes()
        {
        }
        /**
         * Get_nodes
         *
         * Get the nodes from the query.
         *
         * @uses AbstractConnectionResolver::get_ids_for_nodes()
         *
         * @return array
         * @throws Exception
         */
        public function get_nodes()
        {
        }
        /**
         * Validates Model.
         *
         * If model isn't a class with a `fields` member, this function with have be overridden in
         * the Connection class.
         *
         * @param \WPGraphQL\Model\Model|mixed $model The model being validated
         *
         * @return bool
         */
        protected function is_valid_model($model)
        {
        }
        /**
         * Given an ID, a cursor is returned
         *
         * @param int $id
         *
         * @return string
         */
        protected function get_cursor_for_node($id)
        {
        }
        /**
         * Get_edges
         *
         * This iterates over the nodes and returns edges
         *
         * @return array
         */
        public function get_edges()
        {
        }
        /**
         * Get_page_info
         *
         * Returns pageInfo for the connection
         *
         * @return array
         */
        public function get_page_info()
        {
        }
        /**
         * Execute the resolver query and get the data for the connection
         *
         * @return array
         *
         * @throws Exception
         */
        public function execute_and_get_ids()
        {
        }
        /**
         * Get_connection
         *
         * Get the connection to return to the Connection Resolver
         *
         * @return mixed|array|Deferred
         *
         * @throws Exception
         */
        public function get_connection()
        {
        }
    }
    /**
     * Class UserConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class UserConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * {@inheritDoc}
         *
         * A custom class is assumed to have the same core functions as WP_User_Query.
         *
         * @var \WP_User_Query|object
         */
        protected $query;
        /**
         * Determines whether the query should execute at all. It's possible that in some
         * situations we may want to prevent the underlying query from executing at all.
         *
         * In those cases, this would be set to false.
         *
         * @return bool
         */
        public function should_execute()
        {
        }
        public function get_loader_name()
        {
        }
        /**
         * Converts the args that were input to the connection into args that can be executed
         * by WP_User_Query
         *
         * @return array
         * @throws \Exception
         */
        public function get_query_args()
        {
        }
        /**
         * Return an instance of the WP_User_Query with the args for the connection being executed
         *
         * @return object|\WP_User_Query
         * @throws \Exception
         */
        public function get_query()
        {
        }
        /**
         * Returns an array of ids from the query being executed.
         *
         * @return array
         */
        public function get_ids_from_query()
        {
        }
        /**
         * This sets up the "allowed" args, and translates the GraphQL-friendly keys to WP_User_Query
         * friendly keys.
         *
         * There's probably a cleaner/more dynamic way to approach this, but this was quick. I'd be
         * down to explore more dynamic ways to map this, but for now this gets the job done.
         *
         * @param array $args The query "where" args
         *
         * @return array
         * @since  0.0.5
         */
        protected function sanitize_input_fields(array $args)
        {
        }
        /**
         * Determine whether or not the the offset is valid, i.e the user corresponding to the offset
         * exists. Offset is equivalent to user_id. So this function is equivalent to checking if the
         * user with the given ID exists.
         *
         * @param int $offset The ID of the node used as the offset in the cursor
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
    }
    /**
     * Class ContentTypeConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class ContentTypeConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * {@inheritDoc}
         *
         * @var array
         */
        protected $query;
        /**
         * {@inheritDoc}
         */
        public function get_ids_from_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_query_args()
        {
        }
        /**
         * Get the items from the source
         *
         * @return array
         */
        public function get_query()
        {
        }
        /**
         * The name of the loader to load the data
         *
         * @return string
         */
        public function get_loader_name()
        {
        }
        /**
         * Determine if the offset used for pagination is valid
         *
         * @param mixed $offset
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
        /**
         * Determine if the query should execute
         *
         * @return bool
         */
        public function should_execute()
        {
        }
    }
    /**
     * Class PostObjectConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class PostObjectConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * The name of the post type, or array of post types the connection resolver is resolving for
         *
         * @var mixed string|array
         */
        protected $post_type;
        /**
         * {@inheritDoc}
         *
         * @var \WP_Query|object
         */
        protected $query;
        /**
         * PostObjectConnectionResolver constructor.
         *
         * @param mixed              $source    source passed down from the resolve tree
         * @param array              $args      array of arguments input in the field as part of the
         *                                      GraphQL query
         * @param AppContext         $context   Object containing app context that gets passed down the
         *                                      resolve tree
         * @param ResolveInfo        $info      Info about fields passed down the resolve tree
         * @param mixed|string|array $post_type The post type to resolve for
         *
         * @throws Exception
         */
        public function __construct($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info, $post_type = 'any')
        {
        }
        /**
         * Return the name of the loader
         *
         * @return string
         */
        public function get_loader_name()
        {
        }
        /**
         * Returns the query being executed
         *
         * @return \WP_Query|object
         *
         * @throws Exception
         */
        public function get_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_ids_from_query()
        {
        }
        /**
         * Determine whether the Query should execute. If it's determined that the query should
         * not be run based on context such as, but not limited to, who the user is, where in the
         * ResolveTree the Query is, the relation to the node the Query is connected to, etc
         *
         * Return false to prevent the query from executing.
         *
         * @return bool
         */
        public function should_execute()
        {
        }
        /**
         * Here, we map the args from the input, then we make sure that we're only querying
         * for IDs. The IDs are then passed down the resolve tree, and deferred resolvers
         * handle batch resolution of the posts.
         *
         * @return array
         */
        public function get_query_args()
        {
        }
        /**
         * This sets up the "allowed" args, and translates the GraphQL-friendly keys to WP_Query
         * friendly keys. There's probably a cleaner/more dynamic way to approach this, but
         * this was quick. I'd be down to explore more dynamic ways to map this, but for
         * now this gets the job done.
         *
         * @param array $where_args The args passed to the connection
         *
         * @return array
         * @since  0.0.5
         */
        public function sanitize_input_fields(array $where_args)
        {
        }
        /**
         * Limit the status of posts a user can query.
         *
         * By default, published posts are public, and other statuses require permission to access.
         *
         * This strips the status from the query_args if the user doesn't have permission to query for
         * posts of that status.
         *
         * @param mixed $stati The status(es) to sanitize
         *
         * @return array|null
         */
        public function sanitize_post_stati($stati)
        {
        }
        /**
         * Filters the GraphQL args before they are used in get_query_args().
         *
         * @return array
         */
        public function get_args() : array
        {
        }
        /**
         * Determine whether or not the the offset is valid, i.e the post corresponding to the offset
         * exists. Offset is equivalent to post_id. So this function is equivalent to checking if the
         * post with the given ID exists.
         *
         * @param int $offset The ID of the node used in the cursor offset
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
    }
    /**
     * Class MenuItemConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class MenuItemConnectionResolver extends \WPGraphQL\Data\Connection\PostObjectConnectionResolver
    {
        /**
         * MenuItemConnectionResolver constructor.
         *
         * @param mixed       $source     source passed down from the resolve tree
         * @param array       $args       array of arguments input in the field as part of the GraphQL query
         * @param AppContext  $context    Object containing app context that gets passed down the resolve tree
         * @param ResolveInfo $info       Info about fields passed down the resolve tree
         *
         * @throws Exception
         */
        public function __construct($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Returns the query args for the connection to resolve with
         *
         * @return array
         */
        public function get_query_args()
        {
        }
        /**
         * Filters the GraphQL args before they are used in get_query_args().
         *
         * @return array
         */
        public function get_args() : array
        {
        }
    }
    /**
     * Class TaxonomyConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class TaxonomyConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * {@inheritDoc}
         *
         * @var array
         */
        protected $query;
        /**
         * {@inheritDoc}
         */
        public function get_ids_from_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_query_args()
        {
        }
        /**
         * Get the items from the source
         *
         * @return array
         */
        public function get_query()
        {
        }
        /**
         * The name of the loader to load the data
         *
         * @return string
         */
        public function get_loader_name()
        {
        }
        /**
         * Determine if the offset used for pagination is valid
         *
         * @param mixed $offset
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
        /**
         * Determine if the query should execute
         *
         * @return bool
         */
        public function should_execute()
        {
        }
    }
    /**
     * Class PluginConnectionResolver - Connects plugins to other objects
     *
     * @package WPGraphQL\Data\Resolvers
     * @since   0.0.5
     */
    class UserRoleConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * {@inheritDoc}
         *
         * @var array
         */
        protected $query;
        /**
         * {@inheritDoc}
         */
        public function get_ids_from_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_query_args()
        {
        }
        /**
         * {@inheritDoc}
         *
         * @return array
         */
        public function get_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_loader_name()
        {
        }
        /**
         * @param mixed $offset Whether the provided offset is valid for the connection
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
        /**
         * @return bool
         */
        public function should_execute()
        {
        }
    }
    /**
     * Class PluginConnectionResolver - Connects plugins to other objects
     *
     * @package WPGraphQL\Data\Resolvers
     * @since 0.0.5
     */
    class PluginConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * {@inheritDoc}
         *
         * @var array
         */
        protected $query;
        /**
         * {@inheritDoc}
         */
        public function get_ids_from_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_query_args()
        {
        }
        /**
         * {@inheritDoc}
         *
         * @return array
         */
        public function get_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_loader_name()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function is_valid_offset($offset)
        {
        }
        /**
         * @return bool
         */
        public function should_execute()
        {
        }
    }
    /**
     * Class EnqueuedScriptsConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class EnqueuedScriptsConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * EnqueuedScriptsConnectionResolver constructor.
         *
         * @param mixed       $source     source passed down from the resolve tree
         * @param array       $args       array of arguments input in the field as part of the GraphQL query
         * @param AppContext  $context    Object containing app context that gets passed down the resolve tree
         * @param ResolveInfo $info       Info about fields passed down the resolve tree
         *
         * @throws Exception
         */
        public function __construct($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_ids_from_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_query_args()
        {
        }
        /**
         * Get the items from the source
         *
         * @return array
         */
        public function get_query()
        {
        }
        /**
         * The name of the loader to load the data
         *
         * @return string
         */
        public function get_loader_name()
        {
        }
        /**
         * Determine if the model is valid
         *
         * @param ?\_WP_Dependency $model
         *
         * @return bool
         */
        protected function is_valid_model($model)
        {
        }
        /**
         * Determine if the offset used for pagination is valid
         *
         * @param mixed $offset
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
        /**
         * Determine if the query should execute
         *
         * @return bool
         */
        public function should_execute()
        {
        }
    }
    /**
     * Class TermObjectConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class TermObjectConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * {@inheritDoc}
         *
         * @var \WP_Term_Query
         */
        protected $query;
        /**
         * The name of the Taxonomy the resolver is intended to be used for
         *
         * @var string
         */
        protected $taxonomy;
        /**
         * TermObjectConnectionResolver constructor.
         *
         * @param mixed       $source     source passed down from the resolve tree
         * @param array       $args       array of arguments input in the field as part of the GraphQL query
         * @param AppContext  $context    Object containing app context that gets passed down the resolve tree
         * @param ResolveInfo $info       Info about fields passed down the resolve tree
         * @param mixed|string|null $taxonomy The name of the Taxonomy the resolver is intended to be used for
         *
         * @throws Exception
         */
        public function __construct($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info, $taxonomy = null)
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_query_args()
        {
        }
        /**
         * Return an instance of WP_Term_Query with the args mapped to the query
         *
         * @return \WP_Term_Query
         * @throws Exception
         */
        public function get_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_ids_from_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_loader_name()
        {
        }
        /**
         * Whether the connection query should execute. Certain contexts _may_ warrant
         * restricting the query to execute at all. Default is true, meaning any time
         * a TermObjectConnection resolver is asked for, it will execute.
         *
         * @return bool
         */
        public function should_execute()
        {
        }
        /**
         * This maps the GraphQL "friendly" args to get_terms $args.
         * There's probably a cleaner/more dynamic way to approach this, but this was quick. I'd be down
         * to explore more dynamic ways to map this, but for now this gets the job done.
         *
         * @since  0.0.5
         * @return array
         */
        public function sanitize_input_fields()
        {
        }
        /**
         * Filters the GraphQL args before they are used in get_query_args().
         *
         * @return array
         */
        public function get_args() : array
        {
        }
        /**
         * Determine whether or not the the offset is valid, i.e the term corresponding to the offset
         * exists. Offset is equivalent to term_id. So this function is equivalent to checking if the
         * term with the given ID exists.
         *
         * @param int $offset The ID of the node used in the cursor for offset
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
    }
    /**
     * Class MenuConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class MenuConnectionResolver extends \WPGraphQL\Data\Connection\TermObjectConnectionResolver
    {
        /**
         * Get the connection args for use in WP_Term_Query to query the menus
         *
         * @return array
         * @throws Exception
         */
        public function get_query_args()
        {
        }
    }
    /**
     * Class CommentConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class CommentConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * {@inheritDoc}
         *
         * @var WP_Comment_Query
         */
        protected $query;
        /**
         * {@inheritDoc}
         */
        public function get_query_args()
        {
        }
        /**
         * Get_query
         *
         * Return the instance of the WP_Comment_Query
         *
         * @return WP_Comment_Query
         * @throws Exception
         */
        public function get_query()
        {
        }
        /**
         * Return the name of the loader
         *
         * @return string
         */
        public function get_loader_name()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_ids_from_query()
        {
        }
        /**
         * This can be used to determine whether the connection query should even execute.
         *
         * For example, if the $source were a post_type that didn't support comments, we could prevent
         * the connection query from even executing. In our case, we prevent comments from even showing
         * in the Schema for post types that don't have comment support, so we don't need to worry
         * about that, but there may be other situations where we'd need to prevent it.
         *
         * @return boolean
         */
        public function should_execute()
        {
        }
        /**
         * Filters the GraphQL args before they are used in get_query_args().
         *
         * @return array
         */
        public function get_args() : array
        {
        }
        /**
         * This sets up the "allowed" args, and translates the GraphQL-friendly keys to
         * WP_Comment_Query friendly keys.
         *
         * There's probably a cleaner/more dynamic way to approach this, but this was quick. I'd be
         * down to explore more dynamic ways to map this, but for now this gets the job done.
         *
         * @param array $args The array of query arguments
         *
         * @since  0.0.5
         * @return array
         */
        public function sanitize_input_fields(array $args)
        {
        }
        /**
         * Determine whether or not the the offset is valid, i.e the comment corresponding to the
         * offset exists. Offset is equivalent to comment_id. So this function is equivalent to
         * checking if the comment with the given ID exists.
         *
         * @param int $offset The ID of the node used for the cursor offset
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
    }
    /**
     * Class EnqueuedStylesheetConnectionResolver
     *
     * @package WPGraphQL\Data\Connection
     */
    class EnqueuedStylesheetConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * {@inheritDoc}
         *
         * @var array
         */
        protected $query;
        /**
         * EnqueuedStylesheetConnectionResolver constructor.
         *
         * @param mixed       $source     source passed down from the resolve tree
         * @param array       $args       array of arguments input in the field as part of the GraphQL query
         * @param AppContext  $context    Object containing app context that gets passed down the resolve tree
         * @param ResolveInfo $info       Info about fields passed down the resolve tree
         *
         * @throws Exception
         */
        public function __construct($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Get the IDs from the source
         *
         * @return array
         */
        public function get_ids_from_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_query_args()
        {
        }
        /**
         * Get the items from the source
         *
         * @return array
         */
        public function get_query()
        {
        }
        /**
         * The name of the loader to load the data
         *
         * @return string
         */
        public function get_loader_name()
        {
        }
        /**
         * Determine if the model is valid
         *
         * @param ?\_WP_Dependency $model
         *
         * @return bool
         */
        protected function is_valid_model($model)
        {
        }
        /**
         * Determine if the offset used for pagination is valid
         *
         * @param mixed $offset
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
        /**
         * Determine if the query should execute
         *
         * @return bool
         */
        public function should_execute()
        {
        }
    }
    /**
     * Class ThemeConnectionResolver
     *
     * @package WPGraphQL\Data\Resolvers
     * @since 0.5.0
     */
    class ThemeConnectionResolver extends \WPGraphQL\Data\Connection\AbstractConnectionResolver
    {
        /**
         * {@inheritDoc}
         *
         * @var array
         */
        protected $query;
        /**
         * {@inheritDoc}
         */
        public function get_ids_from_query()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_query_args()
        {
        }
        /**
         * Get the items from the source
         *
         * @return array
         */
        public function get_query()
        {
        }
        /**
         * The name of the loader to load the data
         *
         * @return string
         */
        public function get_loader_name()
        {
        }
        /**
         * Determine if the offset used for pagination is valid
         *
         * @param mixed $offset
         *
         * @return bool
         */
        public function is_valid_offset($offset)
        {
        }
        /**
         * Determine if the query should execute
         *
         * @return bool
         */
        public function should_execute()
        {
        }
    }
}
namespace WPGraphQL\Data\Cursor {
    /**
     * Abstract Cursor
     *
     * @package WPGraphQL\Data\Loader
     * @since 1.9.0
     */
    abstract class AbstractCursor
    {
        /**
         * The global WordPress Database instance
         *
         * @var wpdb $wpdb
         */
        public $wpdb;
        /**
         * @var CursorBuilder
         */
        public $builder;
        /**
         * @var string
         */
        public $compare;
        /**
         * Our current cursor offset.
         * For example, the term, post, user, or comment ID.
         *
         * @var int
         */
        public $cursor_offset;
        /**
         * @var string|null
         */
        public $cursor;
        /**
         * The WP object instance for the cursor.
         *
         * @var mixed
         */
        public $cursor_node;
        /**
         * Copy of query vars so we can modify them safely
         *
         * @var array
         */
        public $query_vars = [];
        /**
         * The constructor
         *
         * @param array $query_vars
         * @param string|null $cursor
         */
        public function __construct($query_vars, $cursor = 'after')
        {
        }
        /**
         * Get the query variable for the provided name.
         *
         * @param string $name .
         *
         * @return mixed|null
         */
        public function get_query_var(string $name)
        {
        }
        /**
         * Get the direction pagination is going in.
         *
         * @return string
         */
        public function get_cursor_compare()
        {
        }
        /**
         * Ensure the cursor_offset is a positive integer and we have a valid object for our cursor node.
         *
         * @return bool
         */
        protected function is_valid_offset_and_node()
        {
        }
        /**
         * Get the WP Object instance for the cursor.
         *
         * This is cached internally so it should not generate additionl queries.
         *
         * @return mixed|null;
         */
        public abstract function get_cursor_node();
        /**
         * Return the additional AND operators for the where statement
         *
         * @return string
         */
        public abstract function get_where();
        /**
         * Generate the final SQL string to be appended to WHERE clause
         *
         * @return string
         */
        public abstract function to_sql();
    }
    class TermObjectCursor extends \WPGraphQL\Data\Cursor\AbstractCursor
    {
        /**
         * @var ?WP_Term;
         */
        public $cursor_node;
        /**
         * Counter for meta value joins
         *
         * @var integer
         */
        public $meta_join_alias = 0;
        /**
         * @param string $name The name of the query var to get
         *
         * @deprecated 1.9.0
         *
         * @return mixed|null
         */
        public function get_query_arg(string $name)
        {
        }
        /**
         * {@inheritDoc}
         *
         * @return ?WP_Term;
         */
        public function get_cursor_node()
        {
        }
        /**
         * @return ?WP_Term;
         * @deprecated 1.9.0
         */
        public function get_cursor_term()
        {
        }
        /**
         * Build and return the SQL statement to add to the Query
         *
         * @param array|null $fields The fields from the CursorBuilder to convert to SQL
         *
         * @return string
         */
        public function to_sql($fields = null)
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_where()
        {
        }
        /**
         * Get AND operator for given order by key
         *
         * @param string $by    The order by key
         * @param string $order The order direction ASC or DESC
         *
         * @return void
         */
        private function compare_with(string $by, string $order)
        {
        }
        /**
         * Compare with meta key field
         *
         * @param string $meta_key meta key
         * @param string $order    The comparison string
         *
         * @return void
         */
        private function compare_with_meta_field(string $meta_key, string $order)
        {
        }
        /**
         * Get the actual meta key if any
         *
         * @param string $by The order by key
         *
         * @return string|null
         */
        private function get_meta_key(string $by)
        {
        }
    }
    /**
     * User Cursor
     *
     * This class generates the SQL AND operators for cursor based pagination for users
     *
     * @package WPGraphQL\Data\Cursor
     */
    class UserCursor extends \WPGraphQL\Data\Cursor\AbstractCursor
    {
        /**
         * @var ?WP_User
         */
        public $cursor_node;
        /**
         * Counter for meta value joins
         *
         * @var integer
         */
        public $meta_join_alias = 0;
        /**
         * UserCursor constructor.
         *
         * @param array|WP_User_Query $query_vars The query vars to use when building the SQL statement.
         * @param string|null         $cursor     Whether to generate the before or after cursor
         *
         * @return void
         */
        public function __construct($query_vars, $cursor = 'after')
        {
        }
        /**
         * {@inheritDoc}
         *
         * Unlike most queries, users by default are in ascending order.
         */
        public function get_cursor_compare()
        {
        }
        /**
         * {@inheritDoc}
         *
         * @return ?WP_User
         */
        public function get_cursor_node()
        {
        }
        /**
         * @return ?WP_User
         * @deprecated 1.9.0
         */
        public function get_cursor_user()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function to_sql()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_where()
        {
        }
        /**
         * Get AND operator for given order by key
         *
         * @param string $by    The order by key
         * @param string $order The order direction ASC or DESC
         *
         * @return void
         */
        private function compare_with($by, $order)
        {
        }
        /**
         * Use user login based comparison
         *
         * @return void
         */
        private function compare_with_login()
        {
        }
        /**
         * Compare with meta key field
         *
         * @param string $meta_key user meta key
         * @param string $order    The comparison string
         *
         * @return void
         */
        private function compare_with_meta_field(string $meta_key, string $order)
        {
        }
        /**
         * Get the actual meta key if any
         *
         * @param string $by The order by key
         *
         * @return string|null
         */
        private function get_meta_key($by)
        {
        }
    }
    /**
     * Comment Cursor
     *
     * This class generates the SQL and operators for cursor based pagination for comments
     *
     * @package WPGraphQL\Data\Cursor
     */
    class CommentObjectCursor extends \WPGraphQL\Data\Cursor\AbstractCursor
    {
        /**
         * @var ?\WP_Comment
         */
        public $cursor_node;
        /**
         * @param array|\WP_Comment_Query $query_vars The query vars to use when building the SQL statement.
         * @param string|null            $cursor Whether to generate the before or after cursor. Default "after"
         *
         * @return void
         */
        public function __construct($query_vars, $cursor = 'after')
        {
        }
        /**
         * {@inheritDoc}
         *
         * @return ?WP_Comment
         */
        public function get_cursor_node()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_where()
        {
        }
        /**
         * Get AND operator for given order by key
         *
         * @param string $by    The order by key
         * @param string $order The order direction ASC or DESC
         *
         * @return void
         */
        public function compare_with($by, $order)
        {
        }
        /**
         * Use comment date based comparison
         *
         * @return void
         */
        private function compare_with_date()
        {
        }
        /**
         *{@inheritDoc}
         */
        public function to_sql()
        {
        }
    }
    /**
     * Generic class for building AND&OR operators for cursor based paginators
     */
    class CursorBuilder
    {
        /**
         * The field by which the cursor should order the results
         *
         * @var array
         */
        public $fields;
        /**
         * Default comparison operator. < or >
         *
         * @var string
         */
        public $compare;
        /**
         * CursorBuilder constructor.
         *
         * @param string $compare
         *
         * @return void
         */
        public function __construct($compare = '>')
        {
        }
        /**
         * Add ordering field. The order you call this method matters. First field
         * will be the primary field and latter ones will be used if the primary
         * field has duplicate values
         *
         * @param string           $key           database column
         * @param mixed|string|int $value         value from the current cursor
         * @param string|null      $type          type cast
         * @param string|null      $order         custom order
         * @param object|null      $object_cursor The Cursor class
         *
         * @return void
         */
        public function add_field(string $key, $value, string $type = null, string $order = null, $object_cursor = null)
        {
        }
        /**
         * Returns true at least one ordering field has been added
         *
         * @return boolean
         */
        public function has_fields()
        {
        }
        /**
         * Generate the final SQL string to be appended to WHERE clause
         *
         * @param mixed|array|null $fields
         *
         * @return string
         */
        public function to_sql($fields = null)
        {
        }
        /**
         * Copied from
         * https://github.com/WordPress/WordPress/blob/c4f8bc468db56baa2a3bf917c99cdfd17c3391ce/wp-includes/class-wp-meta-query.php#L272-L296
         *
         * It's an instance method. No way to call it without creating the instance?
         *
         * Return the appropriate alias for the given meta type if applicable.
         *
         * @param string $type MySQL type to cast meta_value.
         *
         * @return string MySQL type.
         */
        public function get_cast_for_type($type = '')
        {
        }
    }
    /**
     * Post Cursor
     *
     * This class generates the SQL AND operators for cursor based pagination for posts
     *
     * @package WPGraphQL\Data\Cursor
     */
    class PostObjectCursor extends \WPGraphQL\Data\Cursor\AbstractCursor
    {
        /**
         * @var ?\WP_Post
         */
        public $cursor_node;
        /**
         * Counter for meta value joins
         *
         * @var integer
         */
        public $meta_join_alias = 0;
        /**
         * PostCursor constructor.
         *
         * @param array|\WP_Query $query_vars The query vars to use when building the SQL statement.
         * @param string|null     $cursor Whether to generate the before or after cursor. Default "after"
         */
        public function __construct($query_vars, $cursor = 'after')
        {
        }
        /**
         * {@inheritDoc}
         *
         * @return ?\WP_Post
         */
        public function get_cursor_node()
        {
        }
        /**
         * @deprecated 1.9.0
         *
         * @return ?\WP_Post
         */
        public function get_cursor_post()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function to_sql()
        {
        }
        /**
         * {@inheritDoc}
         */
        public function get_where()
        {
        }
        /**
         * Get AND operator for given order by key
         *
         * @param string $by    The order by key
         * @param string $order The order direction ASC or DESC
         *
         * @return void
         */
        private function compare_with($by, $order)
        {
        }
        /**
         * Use post date based comparison
         *
         * @return void
         */
        private function compare_with_date()
        {
        }
        /**
         * Compare with meta key field
         *
         * @param string $meta_key post meta key
         * @param string $order    The comparison string
         *
         * @return void
         */
        private function compare_with_meta_field(string $meta_key, string $order)
        {
        }
        /**
         * Get the actual meta key if any
         *
         * @param string $by The order by key
         *
         * @return string|null
         */
        private function get_meta_key($by)
        {
        }
    }
}
namespace WPGraphQL\Data {
    class TermObjectMutation
    {
        /**
         * This prepares the object to be mutated - ensures data is safe to be saved,
         * and mapped from input args to WordPress $args
         *
         * @throws UserError User error for invalid term.
         *
         * @param array        $input         The input from the GraphQL Request
         * @param WP_Taxonomy  $taxonomy      The Taxonomy object for the type of term being mutated
         * @param string       $mutation_name The name of the mutation (create, update, etc)
         *
         * @return mixed
         */
        public static function prepare_object(array $input, \WP_Taxonomy $taxonomy, string $mutation_name)
        {
        }
    }
    /**
     * Class Config
     *
     * This class contains configurations for various data-related things, such as query filters for
     * cursor pagination.
     *
     * @package WPGraphQL\Data
     */
    class Config
    {
        /**
         * Config constructor.
         */
        public function __construct()
        {
        }
        /**
         * When posts are ordered by fields that have duplicate values, we need to consider
         * another field to "stabilize" the query order. We use IDs as they're always unique.
         *
         * This allows for posts with the same title or same date or same meta value to exist
         * and for their cursors to properly go forward/backward to the proper place in the database.
         *
         * @param string    $orderby  The ORDER BY clause of the query.
         * @param WP_Query $wp_query The WP_Query instance executing
         *
         * @return string
         */
        public function graphql_wp_query_cursor_pagination_stability(string $orderby, \WP_Query $wp_query)
        {
        }
        /**
         * This filters the WPQuery 'where' $args, enforcing the query to return results before or
         * after the referenced cursor
         *
         * @param string   $where The WHERE clause of the query.
         * @param WP_Query $query The WP_Query instance (passed by reference).
         *
         * @return string
         */
        public function graphql_wp_query_cursor_pagination_support(string $where, \WP_Query $query)
        {
        }
        /**
         * When users are ordered by a meta query the order might be random when
         * the meta values have same values multiple times. This filter adds a
         * secondary ordering by the post ID which forces stable order in such cases.
         *
         * @param string $orderby The ORDER BY clause of the query.
         *
         * @return string
         */
        public function graphql_wp_user_query_cursor_pagination_stability($orderby)
        {
        }
        /**
         * This filters the WP_User_Query 'where' $args, enforcing the query to return results before or
         * after the referenced cursor
         *
         * @param string         $where The WHERE clause of the query.
         * @param \WP_User_Query $query The WP_User_Query instance (passed by reference).
         *
         * @return string
         */
        public function graphql_wp_user_query_cursor_pagination_support($where, \WP_User_Query $query)
        {
        }
        /**
         * This filters the term_clauses in the WP_Term_Query to support cursor based pagination, where
         * we can move forward or backward from a particular record, instead of typical offset
         * pagination which can be much more expensive and less accurate.
         *
         * @param array $pieces     Terms query SQL clauses.
         * @param array $taxonomies An array of taxonomies.
         * @param array $args       An array of terms query arguments.
         *
         * @return array $pieces
         */
        public function graphql_wp_term_query_cursor_pagination_support(array $pieces, array $taxonomies, array $args)
        {
        }
        /**
         * This returns a modified version of the $pieces of the comment query clauses if the request
         * is a GraphQL Request and before or after cursors are passed to the query
         *
         * @param array            $pieces A compacted array of comment query clauses.
         * @param WP_Comment_Query $query  Current instance of WP_Comment_Query, passed by reference.
         *
         * @return array $pieces
         */
        public function graphql_wp_comments_query_cursor_pagination_support(array $pieces, \WP_Comment_Query $query)
        {
        }
    }
    /**
     * Class MediaItemMutation
     *
     * @package WPGraphQL\Type\MediaItem
     */
    class MediaItemMutation
    {
        /**
         * This prepares the media item for insertion
         *
         * @param array        $input            The input for the mutation from the GraphQL request
         * @param WP_Post_Type $post_type_object The post_type_object for the mediaItem (attachment)
         * @param string       $mutation_name    The name of the mutation being performed (create,
         *                                       update, etc.)
         * @param mixed        $file             The mediaItem (attachment) file
         *
         * @return array $media_item_args
         */
        public static function prepare_media_item(array $input, \WP_Post_Type $post_type_object, string $mutation_name, $file)
        {
        }
        /**
         * This updates additional data related to a mediaItem, such as postmeta.
         *
         * @param int          $media_item_id    The ID of the media item being mutated
         * @param array        $input            The input on the mutation
         * @param WP_Post_Type $post_type_object The Post Type Object for the item being mutated
         * @param string       $mutation_name    The name of the mutation
         * @param AppContext   $context          The AppContext that is passed down the resolve tree
         * @param ResolveInfo  $info             The ResolveInfo that is passed down the resolve tree
         *
         * @return void
         */
        public static function update_additional_media_item_data(int $media_item_id, array $input, \WP_Post_Type $post_type_object, string $mutation_name, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
    }
    /**
     * Class UserMutation
     *
     * @package WPGraphQL\Type\User\Mutation
     */
    class UserMutation
    {
        /**
         * Stores the input fields static definition
         *
         * @var array $input_fields
         */
        private static $input_fields = [];
        /**
         * Defines the accepted input arguments
         *
         * @return array|null
         */
        public static function input_fields()
        {
        }
        /**
         * Maps the GraphQL input to a format that the WordPress functions can use
         *
         * @param array  $input         Data coming from the GraphQL mutation query input
         * @param string $mutation_name Name of the mutation being performed
         *
         * @return array
         */
        public static function prepare_user_object($input, $mutation_name)
        {
        }
        /**
         * This updates additional data related to the user object after the initial mutation has
         * happened
         *
         * @param int         $user_id       The ID of the user being mutated
         * @param array       $input         The input data from the GraphQL query
         * @param string      $mutation_name Name of the mutation currently being run
         * @param AppContext  $context       The AppContext passed down the resolve tree
         * @param ResolveInfo $info          The ResolveInfo passed down the Resolve Tree
         *
         * @return void
         * @throws Exception
         */
        public static function update_additional_user_object_data($user_id, $input, $mutation_name, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Method to add user roles to a user object
         *
         * @param int   $user_id The ID of the user
         * @param array $roles   List of roles that need to get added to the user
         *
         * @return void
         * @throws Exception
         */
        private static function add_user_roles($user_id, $roles)
        {
        }
        /**
         * Method to check if the user role is valid, and if the current user has permission to add, or
         * remove it from a user.
         *
         * @param string $role    Name of the role trying to get added to a user object
         * @param int    $user_id The ID of the user being mutated
         *
         * @return mixed|bool|\WP_Error
         */
        private static function verify_user_role($role, $user_id)
        {
        }
    }
}
namespace WPGraphQL\Data\Loader {
    /**
     * Class AbstractDataLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    abstract class AbstractDataLoader
    {
        /**
         * Whether the loader should cache results or not. In some cases the loader may be used to just
         * get content but not bother with caching it.
         *
         * Default: true
         *
         * @var bool
         */
        private $shouldCache = true;
        /**
         * This stores an array of items that have already been loaded
         *
         * @var array
         */
        private $cached = [];
        /**
         * This stores an array of IDs that need to be loaded
         *
         * @var array
         */
        private $buffer = [];
        /**
         * This stores a reference to the AppContext for the loader to make use of
         *
         * @var \WPGraphQL\AppContext
         */
        protected $context;
        /**
         * AbstractDataLoader constructor.
         *
         * @param \WPGraphQL\AppContext $context
         */
        public function __construct(\WPGraphQL\AppContext $context)
        {
        }
        /**
         * Given a Database ID, the particular loader will buffer it and resolve it deferred.
         *
         * @param mixed|int|string $database_id The database ID for a particular loader to load an
         *                                      object
         *
         * @return Deferred|null
         * @throws Exception
         */
        public function load_deferred($database_id)
        {
        }
        /**
         * Add keys to buffer to be loaded in single batch later.
         *
         * @param array $keys The keys of the objects to buffer
         *
         * @return $this
         * @throws Exception
         */
        public function buffer(array $keys)
        {
        }
        /**
         * Loads a key and returns value represented by this key.
         * Internally this method will load all currently buffered items and cache them locally.
         *
         * @param mixed $key
         *
         * @return mixed
         * @throws Exception
         */
        public function load($key)
        {
        }
        /**
         * Adds the provided key and value to the cache. If the key already exists, no
         * change is made. Returns itself for method chaining.
         *
         * @param mixed $key
         * @param mixed $value
         *
         * @return $this
         * @throws Exception
         */
        public function prime($key, $value)
        {
        }
        /**
         * Clears the value at `key` from the cache, if it exists. Returns itself for
         * method chaining.
         *
         * @param array $keys
         *
         * @return $this
         */
        public function clear(array $keys)
        {
        }
        /**
         * Clears the entire cache. To be used when some event results in unknown
         * invalidations across this particular `DataLoader`. Returns itself for
         * method chaining.
         *
         * @return AbstractDataLoader
         * @deprecated in favor of clear_all
         */
        public function clearAll()
        {
        }
        /**
         * Clears the entire cache. To be used when some event results in unknown
         * invalidations across this particular `DataLoader`. Returns itself for
         * method chaining.
         *
         * @return AbstractDataLoader
         */
        public function clear_all()
        {
        }
        /**
         * Loads multiple keys. Returns generator where each entry directly corresponds to entry in
         * $keys. If second argument $asArray is set to true, returns array instead of generator
         *
         * @param array $keys
         * @param bool  $asArray
         *
         * @return array|Generator
         * @throws Exception
         *
         * @deprecated Use load_many instead
         */
        public function loadMany(array $keys, $asArray = false)
        {
        }
        /**
         * Loads multiple keys. Returns generator where each entry directly corresponds to entry in
         * $keys. If second argument $asArray is set to true, returns array instead of generator
         *
         * @param array $keys
         * @param bool  $asArray
         *
         * @return array|Generator
         * @throws Exception
         */
        public function load_many(array $keys, $asArray = false)
        {
        }
        /**
         * Given an array of keys, this yields the object from the cached results
         *
         * @param array $keys   The keys to generate results for
         * @param array $result The results for all keys
         *
         * @return Generator
         */
        private function generate_many(array $keys, array $result)
        {
        }
        /**
         * This checks to see if any items are in the buffer, and if there are this
         * executes the loaders `loadKeys` method to load the items and adds them
         * to the cache if necessary
         *
         * @return array
         * @throws Exception
         */
        private function load_buffered()
        {
        }
        /**
         * This helps to ensure null values aren't being loaded by accident.
         *
         * @param mixed $key
         *
         * @return string
         */
        private function get_scalar_key_hint($key)
        {
        }
        /**
         * For loaders that need to decode keys, this method can help with that.
         * For example, if we wanted to accept a list of RELAY style global IDs and pass them
         * to the loader, we could have the loader centrally decode the keys into their
         * integer values in the PostObjectLoader by overriding this method.
         *
         * @param mixed $key
         *
         * @return mixed
         */
        protected function key_to_scalar($key)
        {
        }
        /**
         * @param mixed $key
         *
         * @return mixed
         * @deprecated Use key_to_scalar instead
         */
        protected function keyToScalar($key)
        {
        }
        /**
         * @param mixed $entry The entry loaded from the dataloader to be used to generate a Model
         * @param mixed $key   The Key used to identify the loaded entry
         *
         * @return null|Model
         */
        protected function normalize_entry($entry, $key)
        {
        }
        /**
         * Returns a cached data object by key.
         *
         * @param mixed $key  Key.
         *
         * @return mixed
         */
        protected function get_cached($key)
        {
        }
        /**
         * Caches a data object by key.
         *
         * @param mixed $key    Key.
         * @param mixed $value  Data object.
         *
         * @return mixed
         */
        protected function set_cached($key, $value)
        {
        }
        /**
         * If the loader needs to do any tweaks between getting raw data from the DB and caching,
         * this can be overridden by the specific loader and used for transformations, etc.
         *
         * @param mixed $entry The User Role object
         * @param mixed $key   The Key to identify the user role by
         *
         * @return Model
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
         * values
         *
         * Note that order of returned values must match exactly the order of keys.
         * If some entry is not available for given key - it must include null for the missing key.
         *
         * For example:
         * loadKeys(['a', 'b', 'c']) -> ['a' => 'value1, 'b' => null, 'c' => 'value3']
         *
         * @param array $keys
         *
         * @return array
         */
        protected abstract function loadKeys(array $keys);
    }
    /**
     * Class EnqueuedScriptLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class EnqueuedScriptLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * Given an array of enqueued script handles ($keys) load the associated
         * enqueued scripts from the $wp_scripts registry.
         *
         * @param array $keys
         *
         * @return array
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class PostTypeLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class PostTypeLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return mixed|PostType
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * @param array $keys
         *
         * @return array
         * @throws Exception
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class EnqueuedStylesheetLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class EnqueuedStylesheetLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * Given an array of enqueued stylesheet handles ($keys) load the associated
         * enqueued stylesheets from the $wp_styles registry.
         *
         * @param array $keys
         *
         * @return array
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class UserRoleLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class UserRoleLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return mixed|UserRole
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * @param array $keys
         *
         * @return array
         * @throws Exception
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class TermObjectLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class TermObjectLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return mixed|Term
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
         * posts as the values
         *
         * Note that order of returned values must match exactly the order of keys.
         * If some entry is not available for given key - it must include null for the missing key.
         *
         * For example:
         * loadKeys(['a', 'b', 'c']) -> ['a' => 'value1, 'b' => null, 'c' => 'value3']
         *
         * @param int[] $keys
         *
         * @return array
         * @throws Exception
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class PluginLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class PluginLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return Model|Plugin
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * Given an array of plugin names, load the associated plugins from the plugin registry.
         *
         * @param array $keys
         *
         * @return array
         * @throws Exception
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class UserLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class UserLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return mixed|User
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * The data loader always returns a user object if it exists, but we need to
         * separately determine whether the user should be considered private. The
         * WordPress frontend does not expose authors without published posts, so our
         * privacy model follows that same convention.
         *
         * Example return format for input "[ 1, 2 ]":
         *
         * [
         *   2 => true,  // User 2 is public (has published posts)
         * ]
         *
         * In this example, user 1 is not public (has no published posts) and is
         * omitted from the returned array.
         *
         * @param array $keys Array of author IDs (int).
         *
         * @return array
         */
        public function get_public_users(array $keys)
        {
        }
        /**
         * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
         * values
         *
         * Note that order of returned values must match exactly the order of keys.
         * If some entry is not available for given key - it must include null for the missing key.
         *
         * For example:
         * loadKeys(['a', 'b', 'c']) -> ['a' => 'value1, 'b' => null, 'c' => 'value3']
         *
         * @param array $keys
         *
         * @return array
         * @throws Exception
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class ThemeLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class ThemeLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return Model|Theme
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * @param array $keys
         *
         * @return array
         * @throws Exception
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class CommentAuthorLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class CommentAuthorLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return mixed|CommentAuthor
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * @param array $keys
         *
         * @return array
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class CommentLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class CommentLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return mixed|Comment|null
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
         * comments as the values
         *
         * Note that order of returned values must match exactly the order of keys.
         * If some entry is not available for given key - it must include null for the missing key.
         *
         * For example:
         * loadKeys(['a', 'b', 'c']) -> ['a' => 'value1, 'b' => null, 'c' => 'value3']
         *
         * @param array $keys
         *
         * @return array
         * @throws Exception
         */
        public function loadKeys(array $keys = [])
        {
        }
    }
    /**
     * Class PostObjectLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class PostObjectLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return mixed|Post
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * Given array of keys, loads and returns a map consisting of keys from `keys` array and loaded
         * posts as the values
         *
         * Note that order of returned values must match exactly the order of keys.
         * If some entry is not available for given key - it must include null for the missing key.
         *
         * For example:
         * loadKeys(['a', 'b', 'c']) -> ['a' => 'value1, 'b' => null, 'c' => 'value3']
         *
         * @param array $keys
         *
         * @return array
         * @throws Exception
         */
        public function loadKeys(array $keys)
        {
        }
    }
    /**
     * Class TaxonomyLoader
     *
     * @package WPGraphQL\Data\Loader
     */
    class TaxonomyLoader extends \WPGraphQL\Data\Loader\AbstractDataLoader
    {
        /**
         * @param mixed $entry The User Role object
         * @param mixed $key The Key to identify the user role by
         *
         * @return mixed|Taxonomy
         * @throws Exception
         */
        protected function get_model($entry, $key)
        {
        }
        /**
         * @param array $keys
         *
         * @return array
         * @throws Exception
         */
        public function loadKeys(array $keys)
        {
        }
    }
}
namespace WPGraphQL\Data {
    class NodeResolver
    {
        /**
         * @var WP
         */
        protected $wp;
        /**
         * @var AppContext
         */
        protected $context;
        /**
         * NodeResolver constructor.
         *
         * @param AppContext $context
         *
         * @return void
         */
        public function __construct(\WPGraphQL\AppContext $context)
        {
        }
        /**
         * Given a Post object, validates it before returning it.
         *
         * @param WP_Post $post
         *
         * @return WP_Post|null
         */
        public function validate_post(\WP_Post $post)
        {
        }
        /**
         * Given the URI of a resource, this method attempts to resolve it and return the
         * appropriate related object
         *
         * @param string       $uri              The path to be used as an identifier for the
         *                                             resource.
         * @param mixed|array|string $extra_query_vars Any extra query vars to consider
         *
         * @return mixed
         * @throws Exception
         */
        public function resolve_uri(string $uri, $extra_query_vars = '')
        {
        }
        /**
         * Parses a URL to produce an array of query variables.
         *
         * Mimics WP::parse_request()
         *
         * @param string $uri
         * @param array|string $extra_query_vars
         *
         * @return string|null The parsed uri.
         */
        public function parse_request(string $uri, $extra_query_vars = '')
        {
        }
    }
    /**
     * Class DataSource
     *
     * This class serves as a factory for all the resolvers for queries and mutations. This layer of
     * abstraction over the actual resolve functions allows easier, granular control over versioning as
     * we can change big things behind the scenes if/when needed, and we just need to ensure the
     * call to the DataSource method returns the expected data later on. This should make it easy
     * down the road to version resolvers if/when changes to the WordPress API are rolled out.
     *
     * @package WPGraphQL\Data
     * @since   0.0.4
     */
    class DataSource
    {
        /**
         * Stores an array of node definitions
         *
         * @var array $node_definition
         * @since  0.0.4
         */
        protected static $node_definition;
        /**
         * Retrieves a WP_Comment object for the id that gets passed
         *
         * @param int        $id      ID of the comment we want to get the object for.
         * @param AppContext $context The context of the request.
         *
         * @return Deferred object
         * @throws UserError Throws UserError.
         * @throws Exception Throws UserError.
         *
         * @since      0.0.5
         *
         * @deprecated Use the Loader passed in $context instead
         */
        public static function resolve_comment($id, $context)
        {
        }
        /**
         * Retrieves a WP_Comment object for the ID that gets passed
         *
         * @param int $comment_id The ID of the comment the comment author is associated with.
         *
         * @return mixed|CommentAuthor|null
         * @throws Exception Throws Exception.
         */
        public static function resolve_comment_author(int $comment_id)
        {
        }
        /**
         * Wrapper for the CommentsConnectionResolver class
         *
         * @param mixed       $source  The object the connection is coming from
         * @param array       $args    Query args to pass to the connection resolver
         * @param AppContext  $context The context of the query to pass along
         * @param ResolveInfo $info    The ResolveInfo object
         *
         * @return mixed
         * @throws Exception
         * @since 0.0.5
         */
        public static function resolve_comments_connection($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Wrapper for PluginsConnectionResolver::resolve
         *
         * @param mixed       $source  The object the connection is coming from
         * @param array       $args    Array of arguments to pass to resolve method
         * @param AppContext  $context AppContext object passed down
         * @param ResolveInfo $info    The ResolveInfo object
         *
         * @return array
         * @throws Exception
         * @since  0.0.5
         */
        public static function resolve_plugins_connection($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Returns the post object for the ID and post type passed
         *
         * @param int        $id      ID of the post you are trying to retrieve
         * @param AppContext $context The context of the GraphQL Request
         *
         * @return Deferred
         *
         * @throws UserError
         * @throws Exception
         *
         * @since      0.0.5
         * @deprecated Use the Loader passed in $context instead
         */
        public static function resolve_post_object(int $id, \WPGraphQL\AppContext $context)
        {
        }
        /**
         * @param int        $id      The ID of the menu item to load
         * @param AppContext $context The context of the GraphQL request
         *
         * @return Deferred|null
         * @throws Exception
         *
         * @deprecated Use the Loader passed in $context instead
         */
        public static function resolve_menu_item(int $id, \WPGraphQL\AppContext $context)
        {
        }
        /**
         * Wrapper for PostObjectsConnectionResolver
         *
         * @param mixed              $source    The object the connection is coming from
         * @param array              $args      Arguments to pass to the resolve method
         * @param AppContext         $context   AppContext object to pass down
         * @param ResolveInfo        $info      The ResolveInfo object
         * @param mixed|string|array $post_type Post type of the post we are trying to resolve
         *
         * @return mixed
         * @throws Exception
         * @since  0.0.5
         */
        public static function resolve_post_objects_connection($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info, $post_type)
        {
        }
        /**
         * Retrieves the taxonomy object for the name of the taxonomy passed
         *
         * @param string $taxonomy Name of the taxonomy you want to retrieve the taxonomy object for
         *
         * @return Taxonomy object
         * @throws UserError | Exception
         * @since  0.0.5
         */
        public static function resolve_taxonomy($taxonomy)
        {
        }
        /**
         * Get the term object for a term
         *
         * @param int        $id      ID of the term you are trying to retrieve the object for
         * @param AppContext $context The context of the GraphQL Request
         *
         * @return mixed
         * @throws Exception
         * @since      0.0.5
         *
         * @deprecated Use the Loader passed in $context instead
         */
        public static function resolve_term_object($id, \WPGraphQL\AppContext $context)
        {
        }
        /**
         * Wrapper for TermObjectConnectionResolver::resolve
         *
         * @param mixed       $source   The object the connection is coming from
         * @param array       $args     Array of args to be passed to the resolve method
         * @param AppContext  $context  The AppContext object to be passed down
         * @param ResolveInfo $info     The ResolveInfo object
         * @param string      $taxonomy The name of the taxonomy the term belongs to
         *
         * @return array
         * @throws Exception
         * @since  0.0.5
         */
        public static function resolve_term_objects_connection($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info, string $taxonomy)
        {
        }
        /**
         * Retrieves the theme object for the theme you are looking for
         *
         * @param string $stylesheet Directory name for the theme.
         *
         * @return Theme object
         * @throws UserError
         * @throws Exception
         * @since  0.0.5
         */
        public static function resolve_theme($stylesheet)
        {
        }
        /**
         * Wrapper for the ThemesConnectionResolver::resolve method
         *
         * @param mixed       $source  The object the connection is coming from
         * @param array       $args    Passes an array of arguments to the resolve method
         * @param AppContext  $context The AppContext object to be passed down
         * @param ResolveInfo $info    The ResolveInfo object
         *
         * @return array
         * @throws Exception
         * @since  0.0.5
         */
        public static function resolve_themes_connection($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Gets the user object for the user ID specified
         *
         * @param int        $id      ID of the user you want the object for
         * @param AppContext $context The AppContext
         *
         * @return Deferred
         * @throws Exception
         *
         * @since      0.0.5
         * @deprecated Use the Loader passed in $context instead
         */
        public static function resolve_user($id, \WPGraphQL\AppContext $context)
        {
        }
        /**
         * Wrapper for the UsersConnectionResolver::resolve method
         *
         * @param mixed       $source  The object the connection is coming from
         * @param array       $args    Array of args to be passed down to the resolve method
         * @param AppContext  $context The AppContext object to be passed down
         * @param ResolveInfo $info    The ResolveInfo object
         *
         * @return array
         * @throws Exception
         * @since  0.0.5
         */
        public static function resolve_users_connection($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Returns an array of data about the user role you are requesting
         *
         * @param string $name Name of the user role you want info for
         *
         * @return UserRole
         * @throws Exception
         * @since  0.0.30
         */
        public static function resolve_user_role($name)
        {
        }
        /**
         * Resolve the avatar for a user
         *
         * @param int   $user_id ID of the user to get the avatar data for
         * @param array $args    The args to pass to the get_avatar_data function
         *
         * @return Avatar|null
         * @throws Exception
         */
        public static function resolve_avatar(int $user_id, array $args)
        {
        }
        /**
         * Resolve the connection data for user roles
         *
         * @param array       $source  The Query results
         * @param array       $args    The query arguments
         * @param AppContext  $context The AppContext passed down to the query
         * @param ResolveInfo $info    The ResloveInfo object
         *
         * @return array
         * @throws Exception
         */
        public static function resolve_user_role_connection($source, array $args, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Format the setting group name to our standard.
         *
         * @param string $group
         *
         * @return string $group
         */
        public static function format_group_name(string $group)
        {
        }
        /**
         * Get all of the allowed settings by group and return the
         * settings group that matches the group param
         *
         * @param string $group
         *
         * @return array $settings_groups[ $group ]
         */
        public static function get_setting_group_fields(string $group, \WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Get all of the allowed settings by group
         *
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return array $allowed_settings_by_group
         */
        public static function get_allowed_settings_by_group(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * Get all of the $allowed_settings
         *
         * @param TypeRegistry $type_registry The WPGraphQL TypeRegistry
         *
         * @return array $allowed_settings
         */
        public static function get_allowed_settings(\WPGraphQL\Registry\TypeRegistry $type_registry)
        {
        }
        /**
         * We get the node interface and field from the relay library.
         *
         * The first method is the way we resolve an ID to its object. The second is the way we resolve
         * an object that implements node to its type.
         *
         * @return array
         * @throws UserError
         */
        public static function get_node_definition()
        {
        }
        /**
         * Given a node, returns the GraphQL Type
         *
         * @param mixed $node The node to resolve the type of
         *
         * @return string
         */
        public static function resolve_node_type($node)
        {
        }
        /**
         * Given the ID of a node, this resolves the data
         *
         * @param string      $global_id The Global ID of the node
         * @param AppContext  $context   The Context of the GraphQL Request
         * @param ResolveInfo $info      The ResolveInfo for the GraphQL Request
         *
         * @return null|string
         * @throws Exception
         */
        public static function resolve_node($global_id, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Returns array of nav menu location names
         *
         * @return array
         */
        public static function get_registered_nav_menu_locations()
        {
        }
        /**
         * This resolves a resource, given a URI (the path / permalink to a resource)
         *
         * Based largely on the core parse_request function in wp-includes/class-wp.php
         *
         * @param string      $uri     The URI to fetch a resource from
         * @param AppContext  $context The AppContext passed through the GraphQL Resolve Tree
         * @param ResolveInfo $info    The ResolveInfo passed through the GraphQL Resolve tree
         *
         * @return mixed
         * @throws Exception
         */
        public static function resolve_resource_by_uri($uri, $context, $info)
        {
        }
    }
    /**
     * Class CommentMutation
     *
     * @package WPGraphQL\Type\Comment\Mutation
     */
    class CommentMutation
    {
        /**
         * This handles inserting the comment and creating
         *
         * @param array  $input         The input for the mutation
         * @param array  $output_args   The output args
         * @param string $mutation_name The name of the mutation being performed
         * @param bool   $update        Whether it's an update action
         *
         * @return array $output_args
         * @throws Exception
         */
        public static function prepare_comment_object(array $input, array &$output_args, string $mutation_name, $update = false)
        {
        }
        /**
         * This updates commentmeta.
         *
         * @param int         $comment_id    The ID of the postObject the comment is connected to
         * @param array       $input         The input for the mutation
         * @param string      $mutation_name The name of the mutation ( ex: create, update, delete )
         * @param AppContext  $context       The AppContext passed down to all resolvers
         * @param ResolveInfo $info          The ResolveInfo passed down to all resolvers
         *
         * @return void
         */
        public static function update_additional_comment_data(int $comment_id, array $input, string $mutation_name, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info)
        {
        }
        /**
         * Gets the user object for the comment author.
         *
         * @param ?string $author_email The authorEmail provided to the mutation input.
         *
         * @return \WP_User|false
         */
        protected static function get_comment_author(string $author_email = null)
        {
        }
    }
    /**
     * Class PostObjectMutation
     *
     * @package WPGraphQL\Type\PostObject
     */
    class PostObjectMutation
    {
        /**
         * This handles inserting the post object
         *
         * @param array        $input             The input for the mutation
         * @param WP_Post_Type $post_type_object  The post_type_object for the type of post being
         *                                        mutated
         * @param string       $mutation_name     The name of the mutation being performed
         *
         * @return array $insert_post_args
         * @throws \Exception
         */
        public static function prepare_post_object($input, $post_type_object, $mutation_name)
        {
        }
        /**
         * This updates additional data related to a post object, such as postmeta, term relationships,
         * etc.
         *
         * @param int          $post_id               $post_id      The ID of the postObject being
         *                                            mutated
         * @param array        $input                 The input for the mutation
         * @param WP_Post_Type $post_type_object      The Post Type Object for the type of post being
         *                                            mutated
         * @param string       $mutation_name         The name of the mutation (ex: create, update,
         *                                            delete)
         * @param AppContext   $context               The AppContext passed down to all resolvers
         * @param ResolveInfo  $info                  The ResolveInfo passed down to all resolvers
         * @param string       $intended_post_status  The intended post_status the post should have
         *                                            according to the mutation input
         * @param string       $default_post_status   The default status posts should use if an
         *                                            intended status wasn't set
         *
         * @return void
         */
        public static function update_additional_post_object_data($post_id, $input, $post_type_object, $mutation_name, \WPGraphQL\AppContext $context, \GraphQL\Type\Definition\ResolveInfo $info, $default_post_status = null, $intended_post_status = null)
        {
        }
        /**
         * Given a $post_id and $input from the mutation, check to see if any term associations are
         * being made, and properly set the relationships
         *
         * @param int          $post_id           The ID of the postObject being mutated
         * @param array        $input             The input for the mutation
         * @param WP_Post_Type $post_type_object  The Post Type Object for the type of post being
         *                                        mutated
         * @param string       $mutation_name     The name of the mutation (ex: create, update, delete)
         *
         * @return void
         */
        protected static function set_object_terms(int $post_id, array $input, \WP_Post_Type $post_type_object, string $mutation_name)
        {
        }
        /**
         * Given an array of Term properties (slug, name, description, etc), create the term and return
         * a term_id
         *
         * @param array  $node     The node input for the term
         * @param string $taxonomy The taxonomy the term input is for
         *
         * @return int $term_id The ID of the created term. 0 if no term was created.
         */
        protected static function create_term_to_connect($node, $taxonomy)
        {
        }
        /**
         * This is a copy of the wp_set_post_lock function that exists in WordPress core, but is not
         * accessible because that part of WordPress is never loaded for WPGraphQL executions
         *
         * Mark the post as currently being edited by the current user
         *
         * @param int $post_id ID of the post being edited.
         *
         * @return array|false Array of the lock time and user ID. False if the post does not exist, or
         *                     there is no current user.
         */
        public static function set_edit_lock($post_id)
        {
        }
        /**
         * Remove the edit lock for a post
         *
         * @param int $post_id ID of the post to delete the lock for
         *
         * @return bool
         */
        public static function remove_edit_lock(int $post_id)
        {
        }
    }
}
namespace {
    /**
     * Class WPGraphQL
     *
     * This is the one true WPGraphQL class
     *
     * @package WPGraphQL
     */
    final class WPGraphQL
    {
        /**
         * Stores the instance of the WPGraphQL class
         *
         * @var ?WPGraphQL The one true WPGraphQL
         * @since  0.0.1
         */
        private static $instance;
        /**
         * Holds the Schema def
         *
         * @var mixed|null|WPSchema $schema The Schema used for the GraphQL API
         */
        protected static $schema;
        /**
         * Holds the TypeRegistry instance
         *
         * @var mixed|null|TypeRegistry $type_registry The registry that holds all GraphQL Types
         */
        protected static $type_registry;
        /**
         * Stores an array of allowed post types
         *
         * @var ?WP_Post_Type[] allowed_post_types
         * @since  0.0.5
         */
        protected static $allowed_post_types;
        /**
         * Stores an array of allowed taxonomies
         *
         * @var ?WP_Taxonomy[] allowed_taxonomies
         * @since  0.0.5
         */
        protected static $allowed_taxonomies;
        /**
         * @var boolean
         */
        protected static $is_graphql_request;
        /**
         * The instance of the WPGraphQL object
         *
         * @return WPGraphQL - The one true WPGraphQL
         * @since  0.0.1
         */
        public static function instance()
        {
        }
        /**
         * Throw error on object clone.
         * The whole idea of the singleton design pattern is that there is a single object
         * therefore, we don't want the object to be cloned.
         *
         * @return void
         * @since  0.0.1
         */
        public function __clone()
        {
        }
        /**
         * Disable unserializing of the class.
         *
         * @return void
         * @since  0.0.1
         */
        public function __wakeup()
        {
        }
        /**
         * Setup plugin constants.
         *
         * @return void
         * @since  0.0.1
         */
        private function setup_constants()
        {
        }
        /**
         * Include required files.
         * Uses composer's autoload
         *
         * @return bool
         * @since  0.0.1
         */
        private function includes()
        {
        }
        /**
         * Set whether the request is a GraphQL request or not
         *
         * @param bool $is_graphql_request
         *
         * @return void
         */
        public static function set_is_graphql_request($is_graphql_request = \false)
        {
        }
        /**
         * @return bool
         */
        public static function is_graphql_request()
        {
        }
        /**
         * Sets up actions to run at certain spots throughout WordPress and the WPGraphQL execution
         * cycle
         *
         * @return void
         */
        private function actions()
        {
        }
        /**
         * Check if the minimum PHP version requirement is met before execution begins.
         *
         * If the server is running a lower version than required, throw an exception and prevent
         * further execution.
         *
         * @return void
         * @throws Exception
         */
        public function min_php_version_check()
        {
        }
        /**
         * Sets up the plugin url
         *
         * @return void
         */
        public function setup_plugin_url()
        {
        }
        /**
         * Determine the post_types and taxonomies, etc that should show in GraphQL
         *
         * @return void
         */
        public function setup_types()
        {
        }
        /**
         * Flush permalinks if the GraphQL Endpoint route isn't yet registered
         *
         * @return void
         */
        public function maybe_flush_permalinks()
        {
        }
        /**
         * Setup filters
         *
         * @return void
         */
        private function filters()
        {
        }
        /**
         * Initialize admin functionality
         *
         * @return void
         */
        public function init_admin()
        {
        }
        /**
         * This sets up built-in post_types and taxonomies to show in the GraphQL Schema
         *
         * @return void
         * @since  0.0.2
         */
        public static function show_in_graphql()
        {
        }
        /**
         * Sets up the default post types to show_in_graphql.
         *
         * @param array  $args      Array of arguments for registering a post type.
         * @param string $post_type Post type key.
         *
         * @return array
         */
        public static function setup_default_post_types($args, $post_type)
        {
        }
        /**
         * Sets up the default taxonomies to show_in_graphql.
         *
         * @param array  $args     Array of arguments for registering a taxonomy.
         * @param string $taxonomy Taxonomy key.
         *
         * @return array
         * @since 1.12.0
         */
        public static function setup_default_taxonomies($args, $taxonomy)
        {
        }
        /**
         * Set the GraphQL Post Type Args and pass them through a filter.
         *
         * @param array  $args           The graphql specific args for the post type
         * @param string $post_type_name The name of the post type being registered
         *
         * @return array
         * @throws Exception
         * @since 1.12.0
         */
        public static function register_graphql_post_type_args(array $args, string $post_type_name)
        {
        }
        /**
         * Set the GraphQL Taxonomy Args and pass them through a filter.
         *
         * @param array  $args          The graphql specific args for the taxonomy
         * @param string $taxonomy_name The name of the taxonomy being registered
         *
         * @return array
         * @throws Exception
         * @since 1.12.0
         */
        public static function register_graphql_taxonomy_args(array $args, string $taxonomy_name)
        {
        }
        /**
         * This sets the post type /taxonomy GraphQL properties.
         *
         * @since 1.12.0
         */
        public static function get_default_graphql_type_args() : array
        {
        }
        /**
         * Get the post types that are allowed to be used in GraphQL.
         * This gets all post_types that are set to show_in_graphql, but allows for external code
         * (plugins/theme) to filter the list of allowed_post_types to add/remove additional post_types
         *
         * @param string|array $output Optional. The type of output to return. Accepts post type
         *                             'names' or 'objects'. Default 'names'.
         * @param array        $args   Optional. Arguments to filter allowed post types
         *
         * @return array
         * @since  0.0.4
         * @since  1.8.1 adds $output as first param, and stores post type objects in class property.
         */
        public static function get_allowed_post_types($output = 'names', $args = [])
        {
        }
        /**
         * Get the taxonomies that are allowed to be used in GraphQL.
         * This gets all taxonomies that are set to "show_in_graphql" but allows for external code
         * (plugins/themes) to filter the list of allowed_taxonomies to add/remove additional
         * taxonomies
         *
         * @param string $output Optional. The type of output to return. Accepts taxonomy 'names' or 'objects'. Default 'names'.
         * @param array  $args   Optional. Arguments to filter allowed taxonomies.
         *
         * @return array
         * @since  0.0.4
         */
        public static function get_allowed_taxonomies($output = 'names', $args = [])
        {
        }
        /**
         * Allow Schema to be cleared
         *
         * @return void
         */
        public static function clear_schema()
        {
        }
        /**
         * Returns the Schema as defined by static registrations throughout
         * the WP Load.
         *
         * @return WPSchema
         *
         * @throws Exception
         */
        public static function get_schema()
        {
        }
        /**
         * Whether WPGraphQL is operating in Debug mode
         *
         * @return bool
         */
        public static function debug() : bool
        {
        }
        /**
         * Returns the Schema as defined by static registrations throughout
         * the WP Load.
         *
         * @return TypeRegistry
         *
         * @throws Exception
         */
        public static function get_type_registry()
        {
        }
        /**
         * Return the static schema if there is one
         *
         * @return null|string
         */
        public static function get_static_schema()
        {
        }
        /**
         * Get the AppContext for use in passing down the Resolve Tree
         *
         * @return AppContext
         */
        public static function get_app_context()
        {
        }
    }
}
namespace WPGraphQL {
    /**
     * Class AppContext
     * Creates an object that contains all of the context for the GraphQL query
     * This class gets instantiated and populated in the main WPGraphQL class.
     *
     * The context is passed to each resolver during execution.
     *
     * Resolvers have the ability to read and write to context to pass info to nested resolvers.
     *
     * @package WPGraphQL
     */
    class AppContext
    {
        /**
         * Stores the url string for the current site
         *
         * @var string $root_url
         */
        public $root_url;
        /**
         * Stores the WP_User object of the current user
         *
         * @var WP_User $viewer
         */
        public $viewer;
        /**
         * @var TypeRegistry
         */
        public $type_registry;
        /**
         * Stores everything from the $_REQUEST global
         *
         * @var mixed $request
         */
        public $request;
        /**
         * Stores additional $config properties
         *
         * @var mixed $config
         */
        public $config;
        /**
         * Passes context about the current connection being resolved
         *
         * @var mixed|String|null
         */
        public $currentConnection = null;
        /**
         * Passes context about the current connection
         *
         * @var array
         */
        public $connectionArgs = [];
        /**
         * Stores the loaders for the class
         *
         * @var array
         */
        public $loaders = [];
        /**
         * Instance of the NodeResolver class to resolve nodes by URI
         *
         * @var NodeResolver
         */
        public $node_resolver;
        /**
         * AppContext constructor.
         */
        public function __construct()
        {
        }
        /**
         * Retrieves loader assigned to $key
         *
         * @param string $key The name of the loader to get
         *
         * @return mixed
         *
         * @deprecated Use get_loader instead.
         */
        public function getLoader($key)
        {
        }
        /**
         * Retrieves loader assigned to $key
         *
         * @param string $key The name of the loader to get
         *
         * @return mixed
         */
        public function get_loader($key)
        {
        }
        /**
         * Returns the $args for the connection the field is a part of
         *
         * @deprecated use get_connection_args() instead
         * @return array|mixed
         */
        public function getConnectionArgs()
        {
        }
        /**
         * Returns the $args for the connection the field is a part of
         *
         * @return array|mixed
         */
        public function get_connection_args()
        {
        }
        /**
         * Returns the current connection
         *
         * @return mixed|null|String
         */
        public function get_current_connection()
        {
        }
        /**
         * @return mixed|null|String
         * @deprecated use get_current_connection instead.
         */
        public function getCurrentConnection()
        {
        }
    }
}
namespace GraphQLRelay\tests {
    class StarWarsMutationTest extends \PHPUnit\Framework\TestCase
    {
        public function testMutatesTheDataSet()
        {
        }
    }
}
namespace GraphQLRelay\Tests\Connection {
    class ArrayConnectionTest extends \PHPUnit\Framework\TestCase
    {
        protected $letters = ['A', 'B', 'C', 'D', 'E'];
        public function testReturnsAllElementsWithoutFilters()
        {
        }
        public function testRespectsASmallerFirst()
        {
        }
        public function testRespectsAnOverlyLargeFirst()
        {
        }
        public function testRespectsASmallerLast()
        {
        }
        public function testRespectsAnOverlyLargeLast()
        {
        }
        public function testRespectsFirstAndAfter()
        {
        }
        public function testRespectsFirstAndAfterWithLongFirst()
        {
        }
        public function testRespectsLastAndBefore()
        {
        }
        public function testRespectsLastAndBeforeWithLongLast()
        {
        }
        public function testRespectsFirstAndAfterAndBeforeTooFew()
        {
        }
        public function testRespectsFirstAndAfterAndBeforeTooMany()
        {
        }
        public function testRespectsFirstAndAfterAndBeforeExactlyRight()
        {
        }
        public function testRespectsLastAndAfterAndBeforeTooFew()
        {
        }
        public function testRespectsLastAndAfterAndBeforeTooMany()
        {
        }
        public function testRespectsLastAndAfterAndBeforeExactlyRight()
        {
        }
        public function testReturnsNoElementsIfFirstIs0()
        {
        }
        public function testReturnsAllElementsIfCursorsAreInvalid()
        {
        }
        public function testReturnsAllElementsIfCursorsAreOnTheOutside()
        {
        }
        public function testReturnsNoElementsIfCursorsCross()
        {
        }
        public function testReturnsAnEdgeCursorGivenAnArrayAndAMemberObject()
        {
        }
        public function testReturnsNullGivenAnArrayAndANonMemberObject()
        {
        }
        public function testWorksWithAJustRightArraySlice()
        {
        }
        public function testWorksWithAnOversizedArraySliceLeftSide()
        {
        }
        public function testWorksWithAnOversizedArraySliceRightSide()
        {
        }
        public function testWorksWithAnOversizedArraySliceBothSides()
        {
        }
        public function testWorksWithAnUndersizedArraySliceLeftSide()
        {
        }
        public function testWorksWithAnUndersizedArraySliceRightSide()
        {
        }
        public function testWorksWithAnUndersizedArraySliceBothSides()
        {
        }
    }
    class ConnectionTest extends \PHPUnit\Framework\TestCase
    {
        /**
         * @var array
         */
        protected $allUsers;
        /**
         * @var \GraphQL\Type\Definition\ObjectType
         */
        protected $userType;
        /**
         * @var array
         */
        protected $friendConnection;
        /**
         * @var array
         */
        protected $userConnection;
        /**
         * @var ObjectType
         */
        protected $queryType;
        /**
         * @var Schema
         */
        protected $schema;
        public function setup() : void
        {
        }
        public function testIncludesConnectionAndEdgeFields()
        {
        }
        public function testWorksWithForwardConnectionArgs()
        {
        }
        public function testWorksWithBackwardConnectionArgs()
        {
        }
        public function testEdgeTypeThrowsWithoutNodeType()
        {
        }
        public function testConnectionTypeThrowsWithoutNodeType()
        {
        }
        public function testConnectionDefinitionThrowsWithoutNodeType()
        {
        }
        /**
         * Helper function to test a query and the expected response.
         */
        protected function assertValidQuery($query, $expected)
        {
        }
    }
    class SeparateConnectionTest extends \PHPUnit\Framework\TestCase
    {
        /**
         * @var array
         */
        protected $allUsers;
        /**
         * @var ObjectType
         */
        protected $userType;
        /**
         * @var ObjectType
         */
        protected $friendEdge;
        /**
         * @var ObjectType
         */
        protected $friendConnection;
        /**
         * @var ObjectType
         */
        protected $userEdge;
        /**
         * @var ObjectType
         */
        protected $userConnection;
        /**
         * @var ObjectType
         */
        protected $queryType;
        /**
         * @var Schema
         */
        protected $schema;
        public function setup() : void
        {
        }
        public function testIncludesConnectionAndEdgeFields()
        {
        }
        public function testWorksWithForwardConnectionArgs()
        {
        }
        public function testWorksWithBackwardConnectionArgs()
        {
        }
        /**
         * Helper function to test a query and the expected response.
         */
        protected function assertValidQuery($query, $expected)
        {
        }
    }
}
namespace GraphQLRelay\tests {
    class RelayTest extends \PHPUnit\Framework\TestCase
    {
        public function testForwardConnectionArgs()
        {
        }
        public function testBackwardConnectionArgs()
        {
        }
        public function testConnectionArgs()
        {
        }
        public function testConnectionDefinitions()
        {
        }
        public function testConnectionType()
        {
        }
        public function testEdgeType()
        {
        }
    }
}
namespace GraphQLRelay\Tests\Mutation {
    class MutationTest extends \PHPUnit\Framework\TestCase
    {
        /**
         * @var ObjectType
         */
        protected $simpleMutation;
        /**
         * @var ObjectType
         */
        protected $simpleMutationWithDescription;
        /**
         * @var ObjectType
         */
        protected $simpleMutationWithDeprecationReason;
        /**
         * @var ObjectType
         */
        protected $simpleMutationWithThunkFields;
        /**
         * @var ObjectType
         */
        protected $mutation;
        /**
         * @var ObjectType
         */
        protected $edgeMutation;
        /**
         * @var Schema
         */
        protected $schema;
        public function setup() : void
        {
        }
        public function testRequiresAnArgument()
        {
        }
        public function testReturnsTheSameClientMutationID()
        {
        }
        public function testReturnsNullWithOmittedClientMutationID()
        {
        }
        public function testSupportsEdgeAsOutputField()
        {
        }
        public function testIntrospection()
        {
        }
        public function testContainsCorrectPayload()
        {
        }
        public function testContainsCorrectField()
        {
        }
        public function testContainsCorrectDescriptions()
        {
        }
        public function testContainsCorrectDeprecationReasons()
        {
        }
        /**
         * Helper function to test a query and the expected response.
         */
        protected function assertValidQuery($query, $expected)
        {
        }
    }
}
namespace GraphQLRelay\tests {
    class StarWarsObjectIdentificationTest extends \PHPUnit\Framework\TestCase
    {
        public function testFetchesTheIDAndNameOfTheRebels()
        {
        }
        public function testRefetchesTheRebels()
        {
        }
        public function testFetchesTheIDAndNameOfTheEmpire()
        {
        }
        public function testRefetchesTheEmpire()
        {
        }
        public function testRefetchesTheXWing()
        {
        }
        /**
         * Helper function to test a query and the expected response.
         */
        private function assertValidQuery($query, $expected)
        {
        }
    }
}
namespace GraphQLRelay\tests\Node {
    class PluralTest extends \PHPUnit\Framework\TestCase
    {
        protected static function getSchema()
        {
        }
        public function testAllowsFetching()
        {
        }
        public function testCorrectlyIntrospects()
        {
        }
        /**
         * Helper function to test a query and the expected response.
         */
        private function assertValidQuery($query, $expected)
        {
        }
    }
}
namespace GraphQLRelay\Tests\Node {
    class NodeTest extends \PHPUnit\Framework\TestCase
    {
        /**
         * Node definition, so that it is only created once
         *
         * @var array
         */
        protected static $nodeDefinition;
        /**
         * @var ObjectType
         */
        protected static $userType;
        /**
         * @var ObjectType
         */
        protected static $photoType;
        public function testGetsCorrectIDForUsers()
        {
        }
        public function testGetsCorrectIDForPhotos()
        {
        }
        public function testGetsCorrectNameForUsers()
        {
        }
        public function testGetsCorrectWidthForPhotos()
        {
        }
        public function testGetsCorrectTypeNameForUsers()
        {
        }
        public function testCorrectWidthForPhotos()
        {
        }
        public function testIgnoresPhotoFragmentsOnUser()
        {
        }
        public function testReturnsNullForBadIDs()
        {
        }
        public function testHasCorrectNodeInterface()
        {
        }
        public function testHasCorrectNodeRootField()
        {
        }
        /**
         * Returns test schema
         *
         * @return Schema
         */
        protected function getSchema()
        {
        }
        /**
         * Returns test query type
         *
         * @return ObjectType
         */
        protected function getQueryType()
        {
        }
        /**
         * Returns node definitions
         *
         * @return array
         */
        protected function getNodeDefinitions()
        {
        }
        /**
         * Returns photo data
         *
         * @return array
         */
        protected function getPhotoData()
        {
        }
        /**
         * Returns user data
         *
         * @return array
         */
        protected function getUserData()
        {
        }
        /**
         * Helper function to test a query and the expected response.
         */
        private function assertValidQuery($query, $expected)
        {
        }
    }
}
namespace GraphQLRelay\tests {
    class StarWarsConnectionTest extends \PHPUnit\Framework\TestCase
    {
        public function testFetchesTheFirstShipOfTheRebels()
        {
        }
        public function testFetchesTheFirstTwoShipsOfTheRebelsWithACursor()
        {
        }
        public function testFetchesTheNextThreeShipsOfTHeRebelsWithACursor()
        {
        }
        public function testFetchesNoShipsOfTheRebelsAtTheEndOfConnection()
        {
        }
        public function testIdentifiesTheEndOfTheList()
        {
        }
        /**
         * Helper function to test a query and the expected response.
         */
        private function assertValidQuery($query, $expected)
        {
        }
    }
    class StarWarsSchema
    {
        protected static $shipConnection;
        protected static $factionType;
        protected static $shipType;
        protected static $nodeDefinition;
        protected static $shipMutation;
        /**
         * This is a basic end-to-end test, designed to demonstrate the various
         * capabilities of a Relay-compliant GraphQL server.
         *
         * It is recommended that readers of this test be familiar with
         * the end-to-end test in GraphQL.js first, as this test skips
         * over the basics covered there in favor of illustrating the
         * key aspects of the Relay spec that this test is designed to illustrate.
         *
         * We will create a GraphQL schema that describes the major
         * factions and ships in the original Star Wars trilogy.
         *
         * NOTE: This may contain spoilers for the original Star
         * Wars trilogy.
         */
        /**
         * Using our shorthand to describe type systems, the type system for our
         * example will be the followng:
         *
         * interface Node {
         *   id: ID!
         * }
         *
         * type Faction : Node {
         *   id: ID!
         *   name: String
         *   ships: ShipConnection
         * }
         *
         * type Ship : Node {
         *   id: ID!
         *   name: String
         * }
         *
         * type ShipConnection {
         *   edges: [ShipEdge]
         *   pageInfo: PageInfo!
         * }
         *
         * type ShipEdge {
         *   cursor: String!
         *   node: Ship
         * }
         *
         * type PageInfo {
         *   hasNextPage: Boolean!
         *   hasPreviousPage: Boolean!
         *   startCursor: String
         *   endCursor: String
         * }
         *
         * type Query {
         *   rebels: Faction
         *   empire: Faction
         *   node(id: ID!): Node
         * }
         *
         * input IntroduceShipInput {
         *   clientMutationId: string!
         *   shipName: string!
         *   factionId: ID!
         * }
         *
         * input IntroduceShipPayload {
         *   clientMutationId: string!
         *   ship: Ship
         *   faction: Faction
         * }
         *
         * type Mutation {
         *   introduceShip(input IntroduceShipInput!): IntroduceShipPayload
         * }
         */
        /**
         * We get the node interface and field from the relay library.
         *
         * The first method is the way we resolve an ID to its object. The second is the
         * way we resolve an object that implements node to its type.
         */
        protected static function getNodeDefinition()
        {
        }
        /**
         * We define our basic ship type.
         *
         * This implements the following type system shorthand:
         *   type Ship : Node {
         *     id: String!
         *     name: String
         *   }
         *
         * @return ObjectType
         */
        protected static function getShipType()
        {
        }
        /**
         * We define our faction type, which implements the node interface.
         *
         * This implements the following type system shorthand:
         *   type Faction : Node {
         *     id: String!
         *     name: String
         *     ships: ShipConnection
         *   }
         *
         * @return ObjectType
         */
        protected static function getFactionType()
        {
        }
        /**
         * We define a connection between a faction and its ships.
         *
         * connectionType implements the following type system shorthand:
         *   type ShipConnection {
         *     edges: [ShipEdge]
         *     pageInfo: PageInfo!
         *   }
         *
         * connectionType has an edges field - a list of edgeTypes that implement the
         * following type system shorthand:
         *   type ShipEdge {
         *     cursor: String!
         *     node: Ship
         *   }
         */
        protected static function getShipConnection()
        {
        }
        /**
         * This will return a GraphQLFieldConfig for our ship
         * mutation.
         *
         * It creates these two types implicitly:
         *   input IntroduceShipInput {
         *     clientMutationId: string!
         *     shipName: string!
         *     factionId: ID!
         *   }
         *
         *   input IntroduceShipPayload {
         *     clientMutationId: string!
         *     ship: Ship
         *     faction: Faction
         *   }
         */
        public static function getShipMutation()
        {
        }
        /**
         * Returns the complete schema for StarWars tests
         *
         * @return Schema
         */
        public static function getSchema()
        {
        }
    }
    class StarWarsData
    {
        protected static $xwing = ['id' => '1', 'name' => 'X-Wing'];
        protected static $ywing = ['id' => '2', 'name' => 'Y-Wing'];
        protected static $awing = ['id' => '3', 'name' => 'A-Wing'];
        protected static $falcon = ['id' => '4', 'name' => 'Millenium Falcon'];
        protected static $homeOne = ['id' => '5', 'name' => 'Home One'];
        protected static $tieFighter = ['id' => '6', 'name' => 'TIE Fighter'];
        protected static $tieInterceptor = ['id' => '7', 'name' => 'TIE Interceptor'];
        protected static $executor = ['id' => '8', 'name' => 'TIE Interceptor'];
        protected static $rebels = ['id' => '1', 'name' => 'Alliance to Restore the Republic', 'ships' => ['1', '2', '3', '4', '5']];
        protected static $empire = ['id' => '2', 'name' => 'Galactic Empire', 'ships' => ['6', '7', '8']];
        protected static $nextShip = 9;
        protected static $data;
        /**
         * Returns the data object
         *
         * @return array $array
         */
        protected static function getData()
        {
        }
        /**
         * @param $shipName
         * @param $factionId
         * @return array
         */
        public static function createShip($shipName, $factionId)
        {
        }
        public static function getShip($id)
        {
        }
        public static function getFaction($id)
        {
        }
        public static function getRebels()
        {
        }
        public static function getEmpire()
        {
        }
    }
}
namespace GraphQLRelay {
    class Relay
    {
        /**
         * Returns a GraphQLFieldConfigArgumentMap appropriate to include on a field
         * whose return type is a connection type with forward pagination.
         *
         * @return array
         */
        public static function forwardConnectionArgs()
        {
        }
        /**
         * Returns a GraphQLFieldConfigArgumentMap appropriate to include on a field
         * whose return type is a connection type with backward pagination.
         *
         * @return array
         */
        public static function backwardConnectionArgs()
        {
        }
        /**
         * Returns a GraphQLFieldConfigArgumentMap appropriate to include on a field
         * whose return type is a connection type with bidirectional pagination.
         *
         * @return array
         */
        public static function connectionArgs()
        {
        }
        /**
         * Returns a GraphQLObjectType for a connection and its edge with the given name,
         * and whose nodes are of the specified type.
         *
         * @param array $config
         * @return array
         */
        public static function connectionDefinitions(array $config)
        {
        }
        /**
         * Returns a GraphQLObjectType for a connection with the given name,
         * and whose nodes are of the specified type.
         *
         * @param array $config
         * @return ObjectType
         */
        public static function connectionType(array $config)
        {
        }
        /**
         * Returns a GraphQLObjectType for a edge with the given name,
         * and whose nodes are of the specified type.
         *
         * @param array $config
         * @return ObjectType
         */
        public static function edgeType(array $config)
        {
        }
        /**
         * A simple function that accepts an array and connection arguments, and returns
         * a connection object for use in GraphQL. It uses array offsets as pagination,
         * so pagination will only work if the array is static.
         * @param array $data
         * @param $args
         *
         * @return array
         */
        public static function connectionFromArray(array $data, $args)
        {
        }
        /**
         * Given a slice (subset) of an array, returns a connection object for use in
         * GraphQL.
         *
         * This function is similar to `connectionFromArray`, but is intended for use
         * cases where you know the cardinality of the connection, consider it too large
         * to materialize the entire array, and instead wish pass in a slice of the
         * total result large enough to cover the range specified in `args`.
         *
         * @param array $arraySlice
         * @param $args
         * @param $meta
         * @return array
         */
        public static function connectionFromArraySlice(array $arraySlice, $args, $meta)
        {
        }
        /**
         * Return the cursor associated with an object in an array.
         *
         * @param array $data
         * @param $object
         * @return null|string
         */
        public static function cursorForObjectInConnection(array $data, $object)
        {
        }
        /**
         * Returns a GraphQLFieldConfig for the mutation described by the
         * provided MutationConfig.
         *
         * A description of a mutation consumable by mutationWithClientMutationId
         * to create a GraphQLFieldConfig for that mutation.
         *
         * The inputFields and outputFields should not include `clientMutationId`,
         * as this will be provided automatically.
         *
         * An input object will be created containing the input fields, and an
         * object will be created containing the output fields.
         *
         * mutateAndGetPayload will receieve an Object with a key for each
         * input field, and it should return an Object with a key for each
         * output field. It may return synchronously, or return a Promise.
         *
         * type MutationConfig = {
         *   name: string,
         *   inputFields: InputObjectConfigFieldMap,
         *   outputFields: GraphQLFieldConfigMap,
         *   mutateAndGetPayload: mutationFn,
         * }
         *
         * @param array $config
         * @return array
         */
        public static function mutationWithClientMutationId(array $config)
        {
        }
        /**
         * Given a function to map from an ID to an underlying object, and a function
         * to map from an underlying object to the concrete GraphQLObjectType it
         * corresponds to, constructs a `Node` interface that objects can implement,
         * and a field config for a `node` root field.
         *
         * If the typeResolver is omitted, object resolution on the interface will be
         * handled with the `isTypeOf` method on object types, as with any GraphQL
         * interface without a provided `resolveType` method.
         *
         * @param callable $idFetcher
         * @param callable $typeResolver
         * @return array
         */
        public static function nodeDefinitions(callable $idFetcher, callable $typeResolver = null)
        {
        }
        /**
         * Takes a type name and an ID specific to that type name, and returns a
         * "global ID" that is unique among all types.
         *
         * @param string $type
         * @param string $id
         * @return string
         */
        public static function toGlobalId($type, $id)
        {
        }
        /**
         * Takes the "global ID" created by self::toGlobalId, and returns the type name and ID
         * used to create it.
         *
         * @param $globalId
         * @return array
         */
        public static function fromGlobalId($globalId)
        {
        }
        /**
         * Creates the configuration for an id field on a node, using `self::toGlobalId` to
         * construct the ID from the provided typename. The type-specific ID is fetched
         * by calling idFetcher on the object, or if not provided, by accessing the `id`
         * property on the object.
         *
         * @param string|null $typeName
         * @param callable|null $idFetcher
         * @return array
         */
        public static function globalIdField($typeName = null, callable $idFetcher = null)
        {
        }
    }
}
namespace GraphQLRelay\Connection {
    class Connection
    {
        /**
         * @var ObjectType
         */
        protected static $pageInfoType;
        /**
         * Returns a GraphQLFieldConfigArgumentMap appropriate to include on a field
         * whose return type is a connection type with forward pagination.
         *
         * @return array
         */
        public static function forwardConnectionArgs()
        {
        }
        /**
         * Returns a GraphQLFieldConfigArgumentMap appropriate to include on a field
         * whose return type is a connection type with backward pagination.
         *
         * @return array
         */
        public static function backwardConnectionArgs()
        {
        }
        /**
         * Returns a GraphQLFieldConfigArgumentMap appropriate to include on a field
         * whose return type is a connection type with bidirectional pagination.
         *
         * @return array
         */
        public static function connectionArgs()
        {
        }
        /**
         * Returns a GraphQLObjectType for a connection with the given name,
         * and whose nodes are of the specified type.
         */
        public static function connectionDefinitions(array $config)
        {
        }
        /**
         * Returns a GraphQLObjectType for a connection with the given name,
         * and whose nodes are of the specified type.
         *
         * @return ObjectType
         */
        public static function createConnectionType(array $config)
        {
        }
        /**
         * Returns a GraphQLObjectType for an edge with the given name,
         * and whose nodes are of the specified type.
         *
         * @param array $config
         * @return ObjectType
         */
        public static function createEdgeType(array $config)
        {
        }
        /**
         * The common page info type used by all connections.
         *
         * @return ObjectType
         */
        public static function pageInfoType()
        {
        }
        protected static function resolveMaybeThunk($thinkOrThunk)
        {
        }
    }
    class ArrayConnection
    {
        const PREFIX = 'arrayconnection:';
        /**
         * Creates the cursor string from an offset.
         */
        public static function offsetToCursor($offset)
        {
        }
        /**
         * Rederives the offset from the cursor string.
         */
        public static function cursorToOffset($cursor)
        {
        }
        /**
         * Given an optional cursor and a default offset, returns the offset
         * to use; if the cursor contains a valid offset, that will be used,
         * otherwise it will be the default.
         */
        public static function getOffsetWithDefault($cursor, $defaultOffset)
        {
        }
        /**
         * A simple function that accepts an array and connection arguments, and returns
         * a connection object for use in GraphQL. It uses array offsets as pagination,
         * so pagination will only work if the array is static.
         * @param array $data
         * @param $args
         *
         * @return array
         */
        public static function connectionFromArray(array $data, $args)
        {
        }
        /**
         * Given a slice (subset) of an array, returns a connection object for use in
         * GraphQL.
         *
         * This function is similar to `connectionFromArray`, but is intended for use
         * cases where you know the cardinality of the connection, consider it too large
         * to materialize the entire array, and instead wish pass in a slice of the
         * total result large enough to cover the range specified in `args`.
         *
         * @return array
         */
        public static function connectionFromArraySlice(array $arraySlice, $args, $meta)
        {
        }
        /**
         * Return the cursor associated with an object in an array.
         *
         * @param array $data
         * @param $object
         * @return null|string
         */
        public static function cursorForObjectInConnection(array $data, $object)
        {
        }
        /**
         * Returns the value for the given array key, NULL, if it does not exist
         *
         * @param array $array
         * @param string $key
         * @return mixed
         */
        protected static function getArrayValueSafe(array $array, $key)
        {
        }
    }
}
namespace GraphQLRelay\Mutation {
    class Mutation
    {
        /**
         * Returns a GraphQLFieldConfig for the mutation described by the
         * provided MutationConfig.
         *
         * A description of a mutation consumable by mutationWithClientMutationId
         * to create a GraphQLFieldConfig for that mutation.
         *
         * The inputFields and outputFields should not include `clientMutationId`,
         * as this will be provided automatically.
         *
         * An input object will be created containing the input fields, and an
         * object will be created containing the output fields.
         *
         * mutateAndGetPayload will receieve an Object with a key for each
         * input field, and it should return an Object with a key for each
         * output field. It may return synchronously, or return a Promise.
         *
         * type MutationConfig = {
         *   name: string,
         *   description?: string,
         *   deprecationReason?: string,
         *   inputFields: InputObjectConfigFieldMap,
         *   outputFields: GraphQLFieldConfigMap,
         *   mutateAndGetPayload: mutationFn,
         * }
         */
        public static function mutationWithClientMutationId(array $config)
        {
        }
        /**
         * Returns the value for the given array key, NULL, if it does not exist
         *
         * @param array $array
         * @param string $key
         * @return mixed
         */
        protected static function getArrayValue(array $array, $key)
        {
        }
        protected static function resolveMaybeThunk($thinkOrThunk)
        {
        }
    }
}
namespace GraphQLRelay\Node {
    class Plural
    {
        /**
         * Returns configuration for Plural identifying root field
         *
         * type PluralIdentifyingRootFieldConfig = {
         *       argName: string,
         *       inputType: GraphQLInputType,
         *       outputType: GraphQLOutputType,
         *       resolveSingleInput: (input: any, info: GraphQLResolveInfo) => ?any,
         *       description?: ?string,
         * };
         *
         * @param array $config
         * @return array
         */
        public static function pluralIdentifyingRootField(array $config)
        {
        }
        /**
         * Returns the value for the given array key, NULL, if it does not exist
         *
         * @param array $array
         * @param string $key
         * @return mixed
         */
        protected static function getArrayValue(array $array, $key)
        {
        }
    }
    class Node
    {
        /**
         * Given a function to map from an ID to an underlying object, and a function
         * to map from an underlying object to the concrete GraphQLObjectType it
         * corresponds to, constructs a `Node` interface that objects can implement,
         * and a field config for a `node` root field.
         *
         * If the typeResolver is omitted, object resolution on the interface will be
         * handled with the `isTypeOf` method on object types, as with any GraphQL
         * interface without a provided `resolveType` method.
         *
         * @param callable $idFetcher
         * @param callable $typeResolver
         * @return array
         */
        public static function nodeDefinitions(callable $idFetcher, callable $typeResolver = null)
        {
        }
        /**
         * Takes a type name and an ID specific to that type name, and returns a
         * "global ID" that is unique among all types.
         *
         * @param string $type
         * @param string $id
         * @return string
         */
        public static function toGlobalId($type, $id)
        {
        }
        /**
         * Takes the "global ID" created by self::toGlobalId, and returns the type name and ID
         * used to create it.
         *
         * @param $globalId
         * @return array
         */
        public static function fromGlobalId($globalId)
        {
        }
        /**
         * Creates the configuration for an id field on a node, using `self::toGlobalId` to
         * construct the ID from the provided typename. The type-specific ID is fetched
         * by calling idFetcher on the object, or if not provided, by accessing the `id`
         * property on the object.
         *
         * @param string|null $typeName
         * @param callable|null $idFetcher
         * @return array
         */
        public static function globalIdField($typeName = null, callable $idFetcher = null)
        {
        }
    }
}
/**
 * @author: Ivo Meißner
 * Date: 22.02.16
 * Time: 12:45
 */
namespace GraphQLRelay\Node {
    const GLOBAL_ID_DELIMITER = ':';
}
namespace {
    /**
     * Formats the name of a field so that it plays nice with GraphiQL
     *
     * @param string $field_name Name of the field
     *
     * @return string Name of the field
     * @since  0.0.2
     */
    function graphql_format_field_name($field_name)
    {
    }
    /**
     * Formats the name of a Type so that it plays nice with GraphiQL
     *
     * @param string $type_name Name of the field
     *
     * @return string Name of the field
     * @since  0.0.2
     */
    function graphql_format_type_name($type_name)
    {
    }
    /**
     * Provides a simple way to run a GraphQL query without posting a request to the endpoint.
     *
     * @param array $request_data   The GraphQL request data (query, variables, operation_name).
     * @param bool  $return_request If true, return the Request object, else return the results of the request execution
     *
     * @return array | Request
     * @throws Exception
     * @since  0.2.0
     */
    function graphql(array $request_data = [], bool $return_request = \false)
    {
    }
    /**
     * Previous access function for running GraphQL queries directly. This function will
     * eventually be deprecated in favor of `graphql`.
     *
     * @param string $query          The GraphQL query to run
     * @param string $operation_name The name of the operation
     * @param array  $variables      Variables to be passed to your GraphQL request
     * @param bool   $return_requst If true, return the Request object, else return the results of the request execution
     *
     * @return array | Request
     * @throws Exception
     * @since  0.0.2
     */
    function do_graphql_request($query, $operation_name = '', $variables = [], $return_requst = \false)
    {
    }
    /**
     * Determine when to register types
     *
     * @return string
     */
    function get_graphql_register_action()
    {
    }
    /**
     * Given a type name and interface name, this applies the interface to the Type.
     *
     * Should be used at the `graphql_register_types` hook.
     *
     * @param mixed|string|array<string> $interface_names Array of one or more names of the GraphQL
     *                                                    Interfaces to apply to the GraphQL Types
     * @param mixed|string|array<string> $type_names      Array of one or more names of the GraphQL
     *                                                    Types to apply the interfaces to
     *
     * example:
     * The following would register the "MyNewInterface" interface to the Post and Page type in the
     * Schema.
     *
     * register_graphql_interfaces_to_types( [ 'MyNewInterface' ], [ 'Post', 'Page' ] );
     *
     * @return void
     */
    function register_graphql_interfaces_to_types($interface_names, $type_names)
    {
    }
    /**
     * Given a Type Name and a $config array, this adds a Type to the TypeRegistry
     *
     * @param string $type_name The name of the Type to register
     * @param array  $config    The Type config
     *
     * @throws Exception
     * @return void
     */
    function register_graphql_type(string $type_name, array $config)
    {
    }
    /**
     * Given a Type Name and a $config array, this adds an Interface Type to the TypeRegistry
     *
     * @param string $type_name The name of the Type to register
     * @param array  $config    The Type config
     *
     * @throws Exception
     * @return void
     */
    function register_graphql_interface_type(string $type_name, array $config)
    {
    }
    /**
     * Given a Type Name and a $config array, this adds an ObjectType to the TypeRegistry
     *
     * @param string $type_name The name of the Type to register
     * @param array  $config    The Type config
     *
     * @return void
     */
    function register_graphql_object_type(string $type_name, array $config)
    {
    }
    /**
     * Given a Type Name and a $config array, this adds an InputType to the TypeRegistry
     *
     * @param string $type_name The name of the Type to register
     * @param array  $config    The Type config
     *
     * @return void
     */
    function register_graphql_input_type(string $type_name, array $config)
    {
    }
    /**
     * Given a Type Name and a $config array, this adds an UnionType to the TypeRegistry
     *
     * @param string $type_name The name of the Type to register
     * @param array  $config    The Type config
     *
     * @throws Exception
     *
     * @return void
     */
    function register_graphql_union_type(string $type_name, array $config)
    {
    }
    /**
     * Given a Type Name and a $config array, this adds an EnumType to the TypeRegistry
     *
     * @param string $type_name The name of the Type to register
     * @param array  $config    The Type config
     *
     * @return void
     */
    function register_graphql_enum_type(string $type_name, array $config)
    {
    }
    /**
     * Given a Type Name, Field Name, and a $config array, this adds a Field to a registered Type in
     * the TypeRegistry
     *
     * @param string $type_name  The name of the Type to add the field to
     * @param string $field_name The name of the Field to add to the Type
     * @param array  $config     The Type config
     *
     * @return void
     * @since 0.1.0
     */
    function register_graphql_field(string $type_name, string $field_name, array $config)
    {
    }
    /**
     * Given a Type Name and an array of field configs, this adds the fields to the registered type in
     * the TypeRegistry
     *
     * @param string $type_name The name of the Type to add the fields to
     * @param array  $fields    An array of field configs
     *
     * @return void
     * @since 0.1.0
     */
    function register_graphql_fields(string $type_name, array $fields)
    {
    }
    /**
     * Adds a field to the Connection Edge between the provided 'From' Type Name and 'To' Type Name.
     *
     * @param string $from_type  The name of the Type the connection is coming from.
     * @param string $to_type    The name of the Type or Alias (the connection config's `FromFieldName`) the connection is going to.
     * @param string $field_name The name of the field to add to the connection edge.
     * @param array $config      The field config.
     *
     * @since 1.13.0
     */
    function register_graphql_edge_field(string $from_type, string $to_type, string $field_name, array $config) : void
    {
    }
    /**
     * Adds several fields to the Connection Edge between the provided 'From' Type Name and 'To' Type Name.
     *
     * @param string $from_type The name of the Type the connection is coming from.
     * @param string $to_type   The name of the Type or Alias (the connection config's `FromFieldName`) the connection is going to.
     * @param array  $fields    An array of field configs.
     *
     * @since 1.13.0
     */
    function register_graphql_edge_fields(string $from_type, string $to_type, array $fields) : void
    {
    }
    /**
     * Adds an input field to the Connection Where Args between the provided 'From' Type Name and 'To' Type Name.
     *
     * @param string $from_type  The name of the Type the connection is coming from.
     * @param string $to_type    The name of the Type or Alias (the connection config's `FromFieldName`) the connection is going to.
     * @param string $field_name The name of the field to add to the connection edge.
     * @param array $config      The field config.
     *
     * @since 1.13.0
     */
    function register_graphql_connection_where_arg(string $from_type, string $to_type, string $field_name, array $config) : void
    {
    }
    /**
     * Adds several input fields to the Connection Where Args between the provided 'From' Type Name and 'To' Type Name.
     *
     * @param string $from_type The name of the Type the connection is coming from.
     * @param string $to_type   The name of the Type or Alias (the connection config's `FromFieldName`) the connection is going to.
     * @param array  $fields    An array of field configs.
     *
     * @since 1.13.0
     */
    function register_graphql_connection_where_args(string $from_type, string $to_type, array $fields) : void
    {
    }
    /**
     * Renames a GraphQL field.
     *
     * @param string $type_name       Name of the Type to rename a field on.
     * @param string $field_name      Field name to be renamed.
     * @param string $new_field_name  New field name.
     *
     * @return void
     * @since 1.3.4
     */
    function rename_graphql_field(string $type_name, string $field_name, string $new_field_name)
    {
    }
    /**
     * Renames a GraphQL Type in the Schema.
     *
     * @param string $type_name The name of the Type in the Schema to rename.
     * @param string $new_type_name  The new name to give the Type.
     *
     * @return void
     * @throws Exception
     *
     * @since 1.3.4
     */
    function rename_graphql_type(string $type_name, string $new_type_name)
    {
    }
    /**
     * Given a config array for a connection, this registers a connection by creating all appropriate
     * fields and types for the connection
     *
     * @param array $config Array to configure the connection
     *
     * @throws Exception
     * @return void
     *
     * @since 0.1.0
     */
    function register_graphql_connection(array $config)
    {
    }
    /**
     * Given a config array for a custom Scalar, this registers a Scalar for use in the Schema
     *
     * @param string $type_name The name of the Type to register
     * @param array  $config    The config for the scalar type to register
     *
     * @throws Exception
     * @return void
     *
     * @since 0.8.4
     */
    function register_graphql_scalar(string $type_name, array $config)
    {
    }
    /**
     * Given a Type Name, this removes the type from the entire schema
     *
     * @param string $type_name The name of the Type to remove.
     *
     * @since 1.13.0
     */
    function deregister_graphql_type(string $type_name) : void
    {
    }
    /**
     * Given a Type Name and Field Name, this removes the field from the TypeRegistry
     *
     * @param string $type_name  The name of the Type to remove the field from
     * @param string $field_name The name of the field to remove
     *
     * @return void
     *
     * @since 0.1.0
     */
    function deregister_graphql_field(string $type_name, string $field_name)
    {
    }
    /**
     * Given a Mutation Name and Config array, this adds a Mutation to the Schema
     *
     * @param string $mutation_name The name of the Mutation to register
     * @param array  $config        The config for the mutation
     *
     * @throws Exception
     *
     * @return void
     * @since 0.1.0
     */
    function register_graphql_mutation(string $mutation_name, array $config)
    {
    }
    /**
     * Whether a GraphQL request is in action or not. This is determined by the WPGraphQL Request
     * class being initiated. True while a request is in action, false after a request completes.
     *
     * This should be used when a condition needs to be checked for ALL GraphQL requests, such
     * as filtering WP_Query for GraphQL requests, for example.
     *
     * Default false.
     *
     * @return bool
     * @since 0.4.1
     */
    function is_graphql_request()
    {
    }
    /**
     * Whether a GraphQL HTTP request is in action or not. This is determined by
     * checking if the request is occurring on the route defined for the GraphQL endpoint.
     *
     * This conditional should only be used for features that apply to HTTP requests. If you are going
     * to apply filters to underlying WordPress core functionality that should affect _all_ GraphQL
     * requests, you should use "is_graphql_request" but if you need to apply filters only if the
     * GraphQL request is an HTTP request, use this conditional.
     *
     * Default false.
     *
     * @return bool
     * @since 0.4.1
     */
    function is_graphql_http_request()
    {
    }
    /**
     * Registers a GraphQL Settings Section
     *
     * @param string $slug   The slug of the group being registered
     * @param array  $config Array configuring the section. Should include: title
     *
     * @return void
     * @since 0.13.0
     */
    function register_graphql_settings_section(string $slug, array $config)
    {
    }
    /**
     * Registers a GraphQL Settings Field
     *
     * @param string $group  The name of the group to register a setting field to
     * @param array  $config The config for the settings field being registered
     *
     * @return void
     * @since 0.13.0
     */
    function register_graphql_settings_field(string $group, array $config)
    {
    }
    /**
     * Given a message and an optional config array
     *
     * @param mixed|string|array $message The debug message
     * @param array              $config  The debug config. Should be an associative array of keys and
     *                                    values.
     *                                    $config['type'] will set the "type" of the log, default type
     *                                    is GRAPHQL_DEBUG. Other fields added to $config will be
     *                                    merged into the debug entry.
     *
     * @return void
     * @since 0.14.0
     */
    function graphql_debug($message, $config = [])
    {
    }
    /**
     * Check if the name is valid for use in GraphQL
     *
     * @param string $type_name The name of the type to validate
     *
     * @return bool
     * @since 0.14.0
     */
    function is_valid_graphql_name(string $type_name)
    {
    }
    /**
     * Registers a series of GraphQL Settings Fields
     *
     * @param string $group  The name of the settings group to register fields to
     * @param array  $fields Array of field configs to register to the group
     *
     * @return void
     * @since 0.13.0
     */
    function register_graphql_settings_fields(string $group, array $fields)
    {
    }
    /**
     * Get an option value from GraphQL settings
     *
     * @param string $option_name  The key of the option to return
     * @param mixed  $default      The default value the setting should return if no value is set
     * @param string $section_name The settings group section that the option belongs to
     *
     * @return mixed|string|int|boolean
     * @since 0.13.0
     */
    function get_graphql_setting(string $option_name, $default = '', $section_name = 'graphql_general_settings')
    {
    }
    /**
     * Get the endpoint route for the WPGraphQL API
     *
     * @return string
     * @since 1.12.0
     */
    function graphql_get_endpoint()
    {
    }
    /**
     * Return the full url for the GraphQL Endpoint.
     *
     * @return string
     * @since 1.12.0
     */
    function graphql_get_endpoint_url()
    {
    }
    /**
     * Runs when WPGraphQL is activated
     *
     * @return void
     */
    function graphql_activation_callback()
    {
    }
    /**
     * Runs when WPGraphQL is de-activated
     *
     * This cleans up data that WPGraphQL stores
     *
     * @return void
     */
    function graphql_deactivation_callback()
    {
    }
    /**
     * Delete data on deactivation
     *
     * @return void
     */
    function delete_graphql_data()
    {
    }
    /**
     * Function that instantiates the plugins main class
     *
     * @return object
     */
    function graphql_init()
    {
    }
    /**
     * Initialize the plugin tracker
     *
     * @return void
     */
    function graphql_init_appsero_telemetry()
    {
    }
}
