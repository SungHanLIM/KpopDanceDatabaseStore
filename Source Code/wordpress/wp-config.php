<?php

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

 
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'k_pop_store');

/** MySQL database username */
define('DB_USER', 'k_pop_store');

/** MySQL database password */
define('DB_PASSWORD', 'k_pop_store');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('FS_METHOD', 'direct');


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
/*
* define('AUTH_KEY',         'put your unique phrase here');
* define('SECURE_AUTH_KEY',  'put your unique phrase here');
* define('LOGGED_IN_KEY',    'put your unique phrase here');
* define('NONCE_KEY',        'put your unique phrase here');
* define('AUTH_SALT',        'put your unique phrase here');
* define('SECURE_AUTH_SALT', 'put your unique phrase here');
* define('LOGGED_IN_SALT',   'put your unique phrase here');
* define('NONCE_SALT',       'put your unique phrase here');
*/

define('AUTH_KEY',         ')pGv+,eM%z|l1]`4ZK{n+.My8 |+[M PIW6$<@j+`n8:03q-+z#}G|~4nL;>.:;S');
define('SECURE_AUTH_KEY',  '4Y{/|rSDhr-*-la@uBH7SxV<yF3ivv@{|+:a+_nXH*qrg@z)]^Fr46cpnL9HpQiA');
define('LOGGED_IN_KEY',    'X6,:0_i!rl(d$A:}>|I)+ZDIo&?9]t*Oy`(3|.N<.7]RwfCe+h8s9J?ygW6n6T[I');
define('NONCE_KEY',        'zTSIr,WwDmXdcn3&K,?HqB7!rZ{+aswai85YnsWh;^7e~s|BGE,bAJMh@mm^jR]|');
define('AUTH_SALT',        '-]vf7WCnvc@;-0hM~9T[YrO*|P-!UFD@kY-2=6n/s8ms*`C7?+BF@ch/oRj:TP;+');
define('SECURE_AUTH_SALT', 'i2;Ap&DMr0G-qbzrMDlmTXW-&R|ns6.~n.^zpFTNa@JMH|&;xrQKYxp5)e.PT0,h');
define('LOGGED_IN_SALT',   'QT?n0(U_Ddgpn.8S@y%0cMFqq+PNtQF-aP_B1+>|i%A}@o<.(}Tc@(gXDU>,fz)[');
define('NONCE_SALT',       '%>`h^t|^yI>?UFeXB,^qR:-Du+^l7WTCod$E87VN-[C|PpUO;tY,XjI=1KMdj:d|');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

