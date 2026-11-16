<?php
/**
 * Temporary read-only export for bagllet theme conversion (phase 1).
 * Upload to wp-content/mu-plugins/, run once as admin, then DELETE this file.
 *
 * Usage (while logged in as admin):
 *   /wp-admin/?bagllet_export=1&_wpnonce=...
 * Nonce action: bagllet_export
 *
 * @package Bagllet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Emit JSON export for theme migration inventory.
 */
function bagllet_export_handle() {
	if ( ! isset( $_GET['bagllet_export'] ) || '1' !== $_GET['bagllet_export'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Forbidden', 'bagllet' ), 403 );
	}

	check_admin_referer( 'bagllet_export' );

	$option_keys = array(
		'template',
		'stylesheet',
		'woodmart_options',
		'wd_header_builder',
		'elementor_active_kit',
		'elementor_experiment-container',
		'woocommerce_shop_page_id',
		'woocommerce_cart_page_id',
		'woocommerce_checkout_page_id',
		'woocommerce_myaccount_page_id',
		'woocommerce_terms_page_id',
		'page_on_front',
		'show_on_front',
		'active_plugins',
	);

	$options = array();
	foreach ( $option_keys as $key ) {
		$options[ $key ] = get_option( $key );
	}

	// Header builder layouts (Woodmart CPT / options vary by version).
	$header_posts = get_posts(
		array(
			'post_type'      => array( 'woodmart_layout', 'cms_block', 'elementor_library' ),
			'post_status'    => 'any',
			'posts_per_page' => 100,
			'fields'         => 'ids',
		)
	);

	$layouts = array();
	foreach ( $header_posts as $pid ) {
		$layouts[] = array(
			'ID'         => $pid,
			'post_type'  => get_post_type( $pid ),
			'post_title' => get_the_title( $pid ),
			'post_name'  => get_post_field( 'post_name', $pid ),
			'meta_keys'  => array_keys( get_post_meta( $pid ) ),
		);
	}

	// Product attribute term meta sample (swatches).
	$swatch_sample = array();
	$attrs         = wc_get_attribute_taxonomies();
	if ( is_array( $attrs ) ) {
		foreach ( array_slice( $attrs, 0, 10 ) as $attr ) {
			$tax   = 'pa_' . $attr->attribute_name;
			$terms = get_terms(
				array(
					'taxonomy'   => $tax,
					'hide_empty' => false,
					'number'     => 5,
				)
			);
			$term_data = array();
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_data[] = array(
						'term_id' => $term->term_id,
						'name'    => $term->name,
						'slug'    => $term->slug,
						'meta'    => get_term_meta( $term->term_id ),
					);
				}
			}
			$swatch_sample[] = array(
				'attribute' => $attr,
				'taxonomy'  => $tax,
				'terms'     => $term_data,
			);
		}
	}

	// Menu item meta sample (mega menu).
	$menu_meta = array();
	$menus     = wp_get_nav_menus();
	foreach ( $menus as $menu ) {
		$items = wp_get_nav_menu_items( $menu->term_id );
		$sample_items = array();
		if ( is_array( $items ) ) {
			foreach ( array_slice( $items, 0, 15 ) as $item ) {
				$meta = get_post_meta( $item->ID );
				$sample_items[] = array(
					'ID'         => $item->ID,
					'title'      => $item->title,
					'url'        => $item->url,
					'menu_order' => $item->menu_order,
					'meta_keys'  => array_keys( (array) $meta ),
					'meta'       => $meta,
				);
			}
		}
		$menu_meta[] = array(
			'menu'  => $menu->name,
			'slug'  => $menu->slug,
			'count' => $menu->count,
			'items' => $sample_items,
		);
	}

	$theme = wp_get_theme();

	$data = array(
		'exported_at'   => gmdate( 'c' ),
		'siteurl'       => site_url(),
		'home'          => home_url(),
		'wp_version'    => get_bloginfo( 'version' ),
		'php_version'   => PHP_VERSION,
		'theme'         => array(
			'name'       => $theme->get( 'Name' ),
			'version'    => $theme->get( 'Version' ),
			'stylesheet' => $theme->get_stylesheet(),
			'template'   => $theme->get_template(),
		),
		'woo_version'   => defined( 'WC_VERSION' ) ? WC_VERSION : null,
		'options'       => $options,
		'layouts'       => $layouts,
		'swatch_sample' => $swatch_sample,
		'menus'         => $menu_meta,
		'plugins'       => get_plugins(),
	);

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="bagllet-export.json"' );
	echo wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
	exit;
}
add_action( 'admin_init', 'bagllet_export_handle' );

/**
 * Admin notice with one-click export link (nonce included).
 */
function bagllet_export_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$url = wp_nonce_url( admin_url( '?bagllet_export=1' ), 'bagllet_export' );
	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Bagllet export mu-plugin is active (temporary). Download JSON then delete this mu-plugin.', 'bagllet' );
	echo ' <a class="button button-primary" href="' . esc_url( $url ) . '">';
	echo esc_html__( 'Download export JSON', 'bagllet' );
	echo '</a></p></div>';
}
add_action( 'admin_notices', 'bagllet_export_notice' );
