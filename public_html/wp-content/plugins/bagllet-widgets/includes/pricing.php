<?php
/**
 * نمایش قیمت محصولات متغیر: فقط پایین‌ترین قیمت (به‌جای بازه‌ی «از ... تا ...») در همه‌ی جاها.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'woocommerce_variable_price_html',
	function ( $html, $product ) {
		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return $html;
		}

		$min_price = $product->get_variation_price( 'min', true );
		$min_reg   = $product->get_variation_regular_price( 'min', true );

		if ( '' === $min_price || null === $min_price ) {
			return $html;
		}

		// اگر پایین‌ترین قیمت حراج است، قیمت عادی خط‌خورده کنارش بیاید.
		if ( $product->is_on_sale() && (float) $min_reg > (float) $min_price ) {
			return wc_format_sale_price( wc_price( $min_reg ), wc_price( $min_price ) ) . $product->get_price_suffix();
		}

		return wc_price( $min_price ) . $product->get_price_suffix();
	},
	20,
	2
);

// کش قیمت‌های متغیر هنگام تغییر محصول بازسازی شود.
add_filter( 'woocommerce_get_variation_prices_hash', function ( $hash ) {
	$hash[] = 'bgl-min-price';
	return $hash;
} );
