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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'drishtiq_db');

/** MySQL database username */
define('DB_USER', 'drishtiq_user');

/** MySQL database password */
define('DB_PASSWORD', 'Atustr29!');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Ope5~SZ<.U,Y2B*4LR+n`>yCA2WK7LsH]#_+~;kiUgFuyTu2ev?wc1qEJ;lfj)$a');
define('SECURE_AUTH_KEY',  'kx=Wovf,9xz%cOyv9x]`}M`yKi]Hr2lL4(EI0)a6FYPW6}B+p,Wk7|c9cufwz!}1');
define('LOGGED_IN_KEY',    'cQ)`Ud$CG//-hx8UaUs8ZY7A%V9HX[)XeDhtYZ-|(=i7glM/LDnb*vIppKtJ~40^');
define('NONCE_KEY',        ')L{Z&-BVFQND+fL)PD@Z>|@_j7s8d@z[j/Y]5?1B<A#I)B0e0,[+@vk{sZE6!Xp-');
define('AUTH_SALT',        ' qU+%+qm*tr*|`]+3V>_TUUc<M,{zD1Lq6BMrKif%/Fdv~[^F.>yYK*YFZDaE)II');
define('SECURE_AUTH_SALT', '.79eg4)7Ayz4+jLpa}/k|/-yF+& duF+fn/_5-hF|Hs|+{|dOJ]=MP!(d+(yj5(L');
define('LOGGED_IN_SALT',   '*Q#s2x.-*U+)xp$;4r=F`88Vx1Zd2b-1h51X*D*!(N5%uk4**T|$bp:hk;|/diHX');
define('NONCE_SALT',       'B5|f<h<nk!.&ez4LKeAJ:cG]7%=><Azz8Y,UMfFYi# 7-jSX#ywpBne&qvL`krKv');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
