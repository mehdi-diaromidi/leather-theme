<?php
/**
 * Theme supports, menus, sidebars.
 *
 * @package Bagllet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up theme defaults.
 */
function bagllet_setup() {
	load_theme_textdomain( 'bagllet', BAGLLET_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/tokens.css' );
	add_editor_style( 'assets/css/base.css' );

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary'   => __( 'منوی اصلی', 'bagllet' ),
			'footer'    => __( 'منوی فوتر', 'bagllet' ),
			'mobile'    => __( 'منوی موبایل', 'bagllet' ),
			'cat_women' => __( 'دسته‌بندی زنانه', 'bagllet' ),
			'cat_men'   => __( 'دسته‌بندی مردانه', 'bagllet' ),
		)
	);
}
add_action( 'after_setup_theme', 'bagllet_setup' );

/**
 * Registers widget areas.
 */
function bagllet_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'سایدبار فروشگاه', 'bagllet' ),
			'id'            => 'shop-sidebar',
			'description'   => __( 'ویجت‌های کنار آرشیو فروشگاه.', 'bagllet' ),
			'before_widget' => '<section id="%1$s" class="bagllet-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="bagllet-widget__title h4">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'فوتر — ستون ۱', 'bagllet' ),
			'id'            => 'footer-1',
			'before_widget' => '<section id="%1$s" class="bagllet-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="bagllet-widget__title h4">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'فوتر — ستون ۲', 'bagllet' ),
			'id'            => 'footer-2',
			'before_widget' => '<section id="%1$s" class="bagllet-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="bagllet-widget__title h4">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'bagllet_widgets_init' );

/**
 * Content width.
 */
function bagllet_content_width() {
	$GLOBALS['content_width'] = 1280;
}
add_action( 'after_setup_theme', 'bagllet_content_width', 0 );
