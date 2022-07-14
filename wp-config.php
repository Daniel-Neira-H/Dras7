<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_dras7' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '2SBj$m^n]O1MNZSH6He]? Uxq?nV7s^xyd*-b)ZbsS<`9aDemD1ao0U+%8/D6pkn' );
define( 'SECURE_AUTH_KEY',  'M:$bGME)]d?XVwf!JK@W%*3:PdcS4Qi MGF4f 9_C)}jpce3t?8BsS+,eX;;RLEg' );
define( 'LOGGED_IN_KEY',    'nb//mI}[F,g;/`*j|`CZ9*2oTR_R2UUQ{.7{!@a<y(wjPg4}*Tf`AyM_!FSt4Qx^' );
define( 'NONCE_KEY',        '}OJ[pF|mlMuaIUC]2Nt`opVE|/!x^pe 4>u,`A[M9asf_K~O8I^lj3Ga@@M|e&xr' );
define( 'AUTH_SALT',        'D|)i|/MW{V>+:c>BR:Wq<!j0a( =pzf<`CCFDsc!9M<pTb@P+|+~oM`kJSuiIg~ ' );
define( 'SECURE_AUTH_SALT', '[$uQT2#ewtg.4-3R@+F[9{(6(?~[Nv1Kl8CEz*QFGz;2+ f=w=?!Zl2h{C;s,{?E' );
define( 'LOGGED_IN_SALT',   '{zX#G-EaU!~~*rK6M4OtZ`#{{OdT[et-ToKx:kX`tByutvn~kfG^*U1cXk-1zv$,' );
define( 'NONCE_SALT',       'N*H>|D_Q~mc6:LG]1O wyE5GFMc}eXu]zdBEfhmL3dyY?s[*)b054n@RFeG8EbA;' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
