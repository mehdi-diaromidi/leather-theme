<?php
/**
 * Custom font upload support (IRANYekan / IRANSansXFaNum on bagllet.ir).
 * Bundled default: Vazirmatn (OFL). Commercial fonts are never shipped in the sales zip.
 *
 * @package Bagllet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns theme_mod path for a custom woff2 font file, if set.
 *
 * @return string Absolute filesystem path or empty.
 */
function bagllet_get_custom_font_path() {
	$path = get_theme_mod( 'bagllet_custom_font_path', '' );
	if ( ! is_string( $path ) || '' === $path ) {
		return '';
	}
	$path    = wp_normalize_path( $path );
	$uploads = wp_upload_dir();
	$basedir = isset( $uploads['basedir'] ) ? wp_normalize_path( $uploads['basedir'] ) : '';
	if ( ! $basedir || 0 !== strpos( $path, $basedir ) ) {
		return '';
	}
	if ( ! is_readable( $path ) || ! preg_match( '/\.woff2$/i', $path ) ) {
		return '';
	}
	return $path;
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
		wp_normalize_path( $uploads['basedir'] ),
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
