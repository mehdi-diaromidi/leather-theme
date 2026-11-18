<?php
/**
 * Custom font upload support (IRANYekan / IRANSansXFaNum on bagllet.ir).
 * Bundled default: Vazirmatn (OFL) via base.css @font-face only (not duplicated in theme.json).
 *
 * @package Bagllet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns theme_mod path for a custom woff2 font file, if set and safe.
 *
 * @return string Absolute filesystem path or empty.
 */
function bagllet_get_custom_font_path() {
	$path = get_theme_mod( 'bagllet_custom_font_path', '' );
	if ( ! is_string( $path ) || '' === $path ) {
		return '';
	}

	$uploads = wp_upload_dir();
	if ( empty( $uploads['basedir'] ) ) {
		return '';
	}

	$basedir  = realpath( $uploads['basedir'] );
	$resolved = realpath( $path );
	if ( ! $basedir || ! $resolved ) {
		return '';
	}

	$basedir_n  = wp_normalize_path( $basedir );
	$resolved_n = wp_normalize_path( $resolved );
	$prefix     = trailingslashit( $basedir_n );
	if ( 0 !== strpos( $resolved_n, $prefix ) && $resolved_n !== $basedir_n ) {
		return '';
	}

	if ( ! is_readable( $resolved_n ) || ! preg_match( '/\.woff2$/i', $resolved_n ) ) {
		return '';
	}

	return $resolved_n;
}

/**
 * Builds @font-face CSS for an uploaded custom font.
 *
 * @return string CSS or empty.
 */
function bagllet_get_custom_font_css() {
	$path = bagllet_get_custom_font_path();
	if ( ! $path ) {
		return '';
	}

	$uploads = wp_upload_dir();
	$url     = str_replace(
		wp_normalize_path( realpath( $uploads['basedir'] ) ),
		$uploads['baseurl'],
		$path
	);
	$url     = esc_url_raw( set_url_scheme( $url ) );
	if ( ! $url ) {
		return '';
	}

	$family = sanitize_text_field( get_theme_mod( 'bagllet_custom_font_family', 'BaglletCustom' ) );
	if ( '' === $family ) {
		$family = 'BaglletCustom';
	}

	$css  = '@font-face{font-family:"' . esc_attr( $family ) . '";';
	$css .= 'src:url("' . esc_url( $url ) . '") format("woff2");';
	$css .= 'font-weight:100 900;font-style:normal;font-display:swap;}';
	$css .= ':root{--font-family-custom:"' . esc_attr( $family ) . '",var(--font-family-base);}';
	return $css;
}
