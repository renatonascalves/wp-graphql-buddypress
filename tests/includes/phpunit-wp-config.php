<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_unit_tests');

/** MySQL database username */
define( 'DB_USER', 'root');

/** MySQL database password */
define( 'DB_PASSWORD', '');

/** MySQL hostname */
define( 'DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'VVH>:(A|uvyjv1!-X02e3rJKN9_EinK^cz}_s`.gY5e$h6*-/2LrpDJ@*Ys^#/8I');
define('SECURE_AUTH_KEY',  'aaxnQol=@1 |W;LmdcC5)X7C0*Y4/q,+|(~$wS+|>_~{gP9L$/ZE=xRnq1CtE2-,');
define('LOGGED_IN_KEY',    '7yx]H@Vc8faLj`&gEk b51ZfCkE*GWA +=DLe&~s&4bEBsj1#>|a!r|.s:BZ84*e');
define('NONCE_KEY',        'a9zYng`bmfhCtRm@nMJ+Q+B pEy!s=_%!`@29f8wF~mj~Z;ocytw&O)O`C;|4-pZ');
define('AUTH_SALT',        '1-FF`n^FoB$s2A5j~hiD!s}VUy)Yd!irzOaGMi04`<q+6Jz5,ms7z|y<:E.;ti,@');
define('SECURE_AUTH_SALT', '[14EYan]}<CSvk-@lqKC|Bn+^}O6;4w^h%V$%Gw(9!Y%Db:+P.upl5#FsR1V]AZn');
define('LOGGED_IN_SALT',   'Wko@Lg|#T*E!%8Z]s!8q-Q#4RwgUH|L#ExTTvO0e:(-]g-&y;6SSCv5-X@)J^@|Y');
define('NONCE_SALT',       'N{M_y&/:)E7RS]FE#T|i~AWq|M]LU: L+zD$8JN#)vTU|=BI35=zulWf|BhED.n7');

/**#@-*/

/*
 * Path to the theme to test with.
 *
 * The 'default' theme is symlinked from test/phpunit/data/themedir1/default into
 * the themes directory of the WordPress installation defined above.
 */
defined( 'WP_DEFAULT_THEME' ) || define( 'WP_DEFAULT_THEME', 'default' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wptests_';

/**
 * Test with WordPress debug mode (default).
 */
define( 'WP_DEBUG', true );

// Set Site domain, email and title constants.
define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );

/** Absolute path to the WordPress directory. */
defined( 'ABSPATH' ) || define( 'ABSPATH', '/tmp/wordpress/' );
