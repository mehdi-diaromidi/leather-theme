<?php
/**
 * Bagllet theme bootstrap.
 *
 * @package Bagllet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BAGLLET_VERSION', '0.2.2' );
define( 'BAGLLET_DIR', get_template_directory() );
define( 'BAGLLET_URI', get_template_directory_uri() );

$bagllet_includes = array(
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/fonts.php',
	'inc/helpers.php',
);

foreach ( $bagllet_includes as $bagllet_file ) {
	require_once BAGLLET_DIR . '/' . $bagllet_file;
}
