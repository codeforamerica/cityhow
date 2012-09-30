<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'cityhowpagoda');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'ga48AM+Votf#v+nO.CgG+Br|r+P>J+tq5/#)6{v,j^/%JlGr[nz%BE5-O-d[FVJ)');
define('SECURE_AUTH_KEY',  '5pfnYI_Jl{@Btm99|>Nr%,Vjr?ga[P;6,A3XeWfMlM1p}+`%!E2IE6_J[-nFA5=x');
define('LOGGED_IN_KEY',    '>}-!Y*!u-y6hh>>u/wAm:yCR(v_5)aAGIr-!G~M{%9(ztJ`vgl}|7DD2/RS00,+J');
define('NONCE_KEY',        'Cp3Blaz1~0`/!cWLSl&Js|=;vR<(-/(MV?j2))>W~ceQx>+2)bi#e`3{zu7,)VgR');
define('AUTH_SALT',        '7hOS:~74.ccq3jm8F-7G3@hl &I[-JV-]@l(|6A=Br;-9hRuXICk+raer;7ZfM+C');
define('SECURE_AUTH_SALT', '[=b}8E;0AOEiK9 6jf,x%|k%E~:~&Plq1FqG%[wWIcTrNqP?Vks6Z$GjLC|{+m-u');
define('LOGGED_IN_SALT',   'vh8:/-E.%XyJ=%VZ%z8W+:J#H3~]MoQI!owjg=|!D9I9^8?d{:B[}B=E+3`1>S&{');
define('NONCE_SALT',       '!3{Q;)2h?!pXt,AZ|$Q+*t=8-p?!WxRZsMY3r~-)#z`O;OC)V1#>K7b2BRA]7@vC');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'nh_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
