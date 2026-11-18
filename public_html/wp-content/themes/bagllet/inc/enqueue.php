<?php
/**
 * Styles and scripts.
 *
 * @package Bagllet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues front-end assets.
 */
function bagllet_enqueue_assets() {
	$ver = BAGLLET_VERSION;

	wp_enqueue_style(
		'bagllet-tokens',
		BAGLLET_URI . '/assets/css/tokens.css',
		array(),
		$ver
	);

	wp_enqueue_style(
		'bagllet-base',
		BAGLLET_URI . '/assets/css/base.css',
		array( 'bagllet-tokens' ),
		$ver
	);

	wp_enqueue_style(
		'bagllet-style',
		get_stylesheet_uri(),
		array( 'bagllet-base' ),
		$ver
	);

	$custom_css = bagllet_get_custom_font_css();
	if ( $custom_css ) {
		wp_add_inline_style( 'bagllet-tokens', $custom_css );
	}

	wp_enqueue_script(
		'bagllet-theme',
		BAGLLET_URI . '/assets/js/theme.js',
		array(),
		$ver,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bagllet_enqueue_assets' );
