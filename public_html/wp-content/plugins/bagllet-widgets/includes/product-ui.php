<?php
/**
 * استایل صفحه‌ی تک‌محصول، سبد خرید، تسویه حساب و فیلترهای فروشگاه/دسته‌ها.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( function_exists( 'is_product' ) && is_product() ) {
			wp_enqueue_style( 'bgl-product-ui', BAGLLET_WIDGETS_URL . 'assets/footer/product-ui.css', array(), BAGLLET_WIDGETS_VERSION );
		}
		if ( function_exists( 'is_cart' ) && is_cart() ) {
			wp_enqueue_style( 'bgl-cart', BAGLLET_WIDGETS_URL . 'assets/footer/cart.css', array(), BAGLLET_WIDGETS_VERSION . '.' . filemtime( BAGLLET_WIDGETS_PATH . 'assets/footer/cart.css' ) );
		}
		if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
			wp_enqueue_style( 'bgl-checkout', BAGLLET_WIDGETS_URL . 'assets/footer/checkout.css', array(), BAGLLET_WIDGETS_VERSION . '.' . filemtime( BAGLLET_WIDGETS_PATH . 'assets/footer/checkout.css' ) );
		}
		if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
			wp_enqueue_style( 'bgl-shop', BAGLLET_WIDGETS_URL . 'assets/footer/shop.css', array(), BAGLLET_WIDGETS_VERSION . '.' . filemtime( BAGLLET_WIDGETS_PATH . 'assets/footer/shop.css' ) );
		}
	},
	50
);

/**
 * Elementor layouts imported from the Woodmart demo still link to the demo host (e.g. cart steps).
 * Filters the whole builder output so element-cached widgets are covered too.
 */
add_filter(
	'elementor/frontend/the_content',
	function ( $content ) {
		if ( false === strpos( $content, 'newnewdemoes.aramis-co.ir' ) ) {
			return $content;
		}
		return str_replace( 'https://newnewdemoes.aramis-co.ir/wp-gold', untrailingslashit( home_url() ), $content );
	}
);
