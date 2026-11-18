<?php
/**
 * Plugin Name: Bagllet Preview Switcher
 * Description: Applies the bagllet theme only when a secret preview cookie/query is present. Development only — not in sales zip.
 * Version: 0.2.0
 * Author: Mehdi Diaromidi
 *
 * Key file (NOT in git): wp-content/mu-plugins/bagllet-preview-key.php
 * must define BAGLLET_PREVIEW_KEY. Generated from .env.local via tools/deploy.
 *
 * @package Bagllet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads secret key from sibling file if present.
 */
function bagllet_preview_bootstrap_key() {
	$key_file = WPMU_PLUGIN_DIR . '/bagllet-preview-key.php';
	if ( is_readable( $key_file ) ) {
		require_once $key_file;
	}
}
bagllet_preview_bootstrap_key();

/**
 * Whether the current request should see the bagllet theme.
 *
 * @return bool
 */
function bagllet_preview_is_active() {
	if ( ! defined( 'BAGLLET_PREVIEW_KEY' ) || ! BAGLLET_PREVIEW_KEY ) {
		return false;
	}
	$key = (string) BAGLLET_PREVIEW_KEY;

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- preview gate, read-only switch.
	$q = isset( $_GET['bagllet_preview'] ) ? sanitize_text_field( wp_unslash( $_GET['bagllet_preview'] ) ) : '';
	if ( $q && hash_equals( $key, $q ) ) {
		if ( ! headers_sent() ) {
			setcookie(
				'bagllet_preview',
				$key,
				array(
					'expires'  => time() + DAY_IN_SECONDS,
					'path'     => COOKIEPATH ? COOKIEPATH : '/',
					'domain'   => COOKIE_DOMAIN,
					'secure'   => is_ssl(),
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		}
		return true;
	}

	if ( isset( $_COOKIE['bagllet_preview'] ) ) {
		$cookie = sanitize_text_field( wp_unslash( $_COOKIE['bagllet_preview'] ) );
		return hash_equals( $key, $cookie );
	}

	return false;
}

/**
 * Forces stylesheet/template to bagllet in preview mode.
 *
 * @param string $value Current value.
 * @return string
 */
function bagllet_preview_force_theme( $value ) {
	if ( bagllet_preview_is_active() ) {
		return 'bagllet';
	}
	return $value;
}
add_filter( 'template', 'bagllet_preview_force_theme', 100 );
add_filter( 'stylesheet', 'bagllet_preview_force_theme', 100 );
add_filter( 'option_template', 'bagllet_preview_force_theme', 100 );
add_filter( 'option_stylesheet', 'bagllet_preview_force_theme', 100 );

/**
 * In preview, dequeue legacy bagllet-widgets assets written for Woodmart
 * so parity work is not polluted (logic moves into the theme in phases 3–7).
 */
function bagllet_preview_dequeue_legacy_assets() {
	if ( ! bagllet_preview_is_active() ) {
		return;
	}

	$handles = array(
		'site-theme',
		'bagllet-site-theme',
		'bagllet-widgets-site-theme',
		'bagllet-footer',
		'bagllet-footer-css',
		'bagllet-footer-js',
	);

	foreach ( $handles as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}

	// Catch common path-based enqueues from bagllet-widgets.
	global $wp_styles, $wp_scripts;
	foreach ( array( $wp_styles, $wp_scripts ) as $queue ) {
		if ( ! $queue || empty( $queue->registered ) ) {
			continue;
		}
		foreach ( $queue->registered as $handle => $obj ) {
			$src = isset( $obj->src ) ? (string) $obj->src : '';
			if ( false !== strpos( $src, 'bagllet-widgets' ) && ( false !== strpos( $src, 'site-theme' ) || false !== strpos( $src, '/footer/' ) ) ) {
				if ( $queue === $wp_styles ) {
					wp_dequeue_style( $handle );
					wp_deregister_style( $handle );
				} else {
					wp_dequeue_script( $handle );
					wp_deregister_script( $handle );
				}
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'bagllet_preview_dequeue_legacy_assets', 1000 );
