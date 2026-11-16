<?php
namespace Bagllet_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * جعبه‌ی اقساط (اسنپ‌پی) برای صفحه‌ی تک محصول:
 * مبلغ هر قسط = قیمت محصول ÷ تعداد اقساط، و با انتخاب تنوع محصول خودکار به‌روز می‌شود.
 */
class Installment extends Widget_Base {

	public function get_name() {
		return 'bagllet-installment';
	}

	public function get_title() {
		return esc_html__( 'بگلت: اقساط اسنپ‌پی', 'bagllet-widgets' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return array( 'bagllet' );
	}

	public function get_keywords() {
		return array( 'bagllet', 'installment', 'snapp', 'اقساط', 'اسنپ', 'قسط', 'بگلت' );
	}

	public function get_style_depends() {
		return array( 'bagllet-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'محتوا', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => esc_html__( 'تعداد اقساط', 'bagllet-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 2,
				'max'     => 24,
			)
		);

		$this->add_control(
			'label',
			array(
				'label'       => esc_html__( 'عنوان (قبل از مبلغ)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'هر قسط اسنپ پی:',
				'label_block' => true,
			)
		);

		$this->add_control(
			'text',
			array(
				'label'       => esc_html__( 'توضیح', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '۴ قسط ماهانه بدون سود، چک و ضامن',
				'label_block' => true,
			)
		);

		$this->add_control(
			'logo',
			array(
				'label'       => esc_html__( 'لوگو (اختیاری؛ خالی = نشان متنی)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'برای نمایش لوگوی رسمی، فایل آن را اینجا بارگذاری کنید.', 'bagllet-widgets' ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => esc_html__( 'لینک (اختیاری)', 'bagllet-widgets' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * قیمت پایه‌ی محصول (برای تنوع‌دار: کمترین قیمت).
	 */
	private function base_price() {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null;
		if ( ! $product ) {
			global $product;
		}
		if ( ! $product || ! is_object( $product ) ) {
			return 2745740; // نمونه برای پیش‌نمایش ویرایشگر
		}
		if ( $product->is_type( 'variable' ) ) {
			$p = $product->get_variation_price( 'min', true );
		} else {
			$p = wc_get_price_to_display( $product );
		}
		return (float) $p;
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$count = max( 2, (int) $s['count'] );
		$price = $this->base_price();
		if ( $price <= 0 ) {
			return;
		}
		$per      = (int) round( $price / $count );
		$currency = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : 'تومان';

		$tag  = ! empty( $s['link']['url'] ) ? 'a' : 'div';
		$attr = '';
		if ( 'a' === $tag ) {
			$attr = ' href="' . esc_url( $s['link']['url'] ) . '"';
			if ( ! empty( $s['link']['is_external'] ) ) {
				$attr .= ' target="_blank" rel="noopener"';
			}
		}

		echo '<' . $tag . $attr . ' class="bgl-ins" data-count="' . (int) $count . '" data-base="' . esc_attr( $per ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput

		echo '<span class="bgl-ins__logo" aria-hidden="true">';
		if ( ! empty( $s['logo']['url'] ) ) {
			echo '<img src="' . esc_url( $s['logo']['url'] ) . '" alt="" loading="lazy">';
		} else {
			echo '<b>Snapp!</b><b>Pay</b>';
		}
		echo '</span>';

		echo '<span class="bgl-ins__tx"><b class="bgl-ins__t">';
		if ( ! empty( $s['label'] ) ) {
			echo esc_html( $s['label'] ) . ' ';
		}
		echo '<bdi><span class="bgl-ins__amt">' . esc_html( number_format( $per ) ) . '</span> ' . esc_html( $currency ) . '</bdi></b>';
		if ( ! empty( $s['text'] ) ) {
			echo '<small>' . esc_html( $s['text'] ) . '</small>';
		}
		echo '</span></' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput

		static $script = false;
		if ( ! $script ) {
			$script = true;
			?>
			<script>
			(function () {
				function fmt(n) { return Math.round(n).toLocaleString('en-US'); }
				function setAll(fn) {
					document.querySelectorAll('.bgl-ins').forEach(function (b) {
						var c = parseInt(b.getAttribute('data-count'), 10) || 4;
						var a = b.querySelector('.bgl-ins__amt');
						if (a) { a.textContent = fmt(fn(b, c)); }
					});
				}
				function bind() {
					if (!window.jQuery) { return; }
					var $f = window.jQuery('form.variations_form');
					$f.on('found_variation', function (e, v) {
						if (v && v.display_price) { setAll(function (b, c) { return v.display_price / c; }); }
					});
					$f.on('reset_data hide_variation', function () {
						setAll(function (b) { return parseFloat(b.getAttribute('data-base')) || 0; });
					});
				}
				if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', bind); } else { bind(); }
			})();
			</script>
			<?php
		}
	}
}
