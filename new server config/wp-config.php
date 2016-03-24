<?php
define('WP_MEMORY_LIMIT', '64M' /* WebSharks™ Core auto-fix. */);
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
define('DB_NAME', 'bta_mysql_5119');

/** MySQL database username */
define('DB_USER', 'bta_mysql_5119');

/** MySQL database password */
define('DB_PASSWORD', 't3mp19xd$');

/** MySQL hostname */
define('DB_HOST', '172.19.52.21');

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
define('AUTH_KEY',         '?$2XOHt8@I1Tbh(9Iz$@;F<K-&OKL{yt:OW`9S(Z:!=pt)r6&Z]_nPQEfP_2laid');
define('SECURE_AUTH_KEY',  'xlWpIMd/uT0_uW5>_xUaf^4gN7Jh0648q%>xWUKF<FF%E?jY]]%%c503bQ4-GNlw');
define('LOGGED_IN_KEY',    'MAhupPb*0%FeT`[X$])(_| ]fzR!roS^-<$W>veN>ls(By}*XFoq^_dX]yj|B=31');
define('NONCE_KEY',        'N;F_#yFV~h1z9s5MqAI-/PS2R.|PS h|j^Gn+BPN/j?#-I-Dmerol#g+di{j|0`R');
define('AUTH_SALT',        '2wvunvKLF@76hwo|[Or Jf18:LbW7(0o=G8y+dM=|CB{N_>w~oZ92XBqE8nj@jKg');
define('SECURE_AUTH_SALT', 'wws=]q*.tJ CCQGZZxXMZ@nI :#PGi&%UZ,>#5,G+|{?|8w?hihT/wD@-&vivlDt');
define('LOGGED_IN_SALT',   '!AZo+L JW//v?5r`YqiR5P-+!CLBajO(@jI6FRu8[fq;)GgD+XE%[Qc.pb-|8!f}');
define('NONCE_SALT',       '58kZiL,P1ypP8C18|M,-/JLel4?yU9CImV{WwUJnWyJ]PV&=m]aV+R-|C:O/ngH+');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'evolver_';

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
 // Enable WP_DEBUG mode
/*define('WP_DEBUG', true);

// Enable Debug logging to the /wp-content/debug.log file
define('WP_DEBUG_LOG', true);

// Disable display of errors and warnings 
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors',0);

// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
define('SCRIPT_DEBUG', true);*/



/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

