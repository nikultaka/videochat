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
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Testing@123' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '0pn<:o$6OLX9Kh{1ZFy8WT Pj6nTAQ-p;)I>KK>!-aJ7zjYpA_|8=k~$:&p:=`9]' );
define( 'SECURE_AUTH_KEY',  '.}gJO#`R[$4%~RnjZEBgg(TzR#|VJTqYwgo]hsK[-Ax~lnjUxRKT!iS5&}B/Da1%' );
define( 'LOGGED_IN_KEY',    'yvPh|[%KRM4SmLxF nqmN.dxWJZp*PJqO`ujy6Bmv~%*z!FQoD_JdJLN[IE{cKQh' );
define( 'NONCE_KEY',        'r&DV&xCKy4@_CLZLf/`kn;^bB+Rvd8CP>[IU>g&n?f>?)Fy hx>8YD],Y{28 QZ%' );
define( 'AUTH_SALT',        'He9c=W}Z U2.Nf_;KWM9l-<Q8):CmO3TV_|6in%4gIQ||NAn/%3Ap?9I%t]q5d4]' );
define( 'SECURE_AUTH_SALT', 'XK,$-;;N*h)o^3Nl(x=m@m{hO.]BrZ%D*CZ&z+D$`y(Ju1EhfQ,?.&Us@|c>C>%5' );
define( 'LOGGED_IN_SALT',   'g,@>8>7i-QrT(F]KXSAi~1O,LEJ!jMA?D#BNFzG+4!qtTAi[8zt}=}c&2@ooyxQ(' );
define( 'NONCE_SALT',       'XocAM1/Nt9jR/;_@Rb`og/DH$Uk^O4X  :aSpYrF@*;e0/O VAYBZ;:-}nWg[uk{' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
define( 'WP_DEBUG', true );
define ('FS_METHOD','direct');      

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
