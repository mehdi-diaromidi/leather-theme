<?php
namespace Bagllet_Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * موزاییک دسته‌بندی‌ها: دسته‌ها از بانک دسته‌بندی‌های ووکامرس انتخاب می‌شوند؛
 * نام، لینک و تصویر شاخص خودکار از خود دسته می‌آید.
 */
class Category_Grid extends Widget_Base {

	public function get_name() {
		return 'bagllet-category-grid';
	}

	public function get_title() {
		return esc_html__( 'بگلت: شبکه‌ی دسته‌بندی', 'bagllet-widgets' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'bagllet' );
	}

	public function get_keywords() {
		return array( 'bagllet', 'category', 'grid', 'دسته', 'دسته‌بندی', 'بگلت' );
	}

	public function get_style_depends() {
		return array( 'bagllet-widgets' );
	}

	/**
	 * فهرست همه‌ی دسته‌های محصول به‌صورت id => برچسب سلسله‌مراتبی.
	 */
	private function category_options() {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return array();
		}
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'orderby'    => 'name',
			)
		);
		if ( is_wp_error( $terms ) ) {
			return array();
		}
		$by_id = array();
		foreach ( $terms as $t ) {
			$by_id[ $t->term_id ] = $t;
		}
		$out = array();
		foreach ( $terms as $t ) {
			$label = $t->name;
			$p     = $t->parent;
			$guard = 0;
			while ( $p && isset( $by_id[ $p ] ) && $guard < 5 ) {
				$label = $by_id[ $p ]->name . ' › ' . $label;
				$p     = $by_id[ $p ]->parent;
				++$guard;
			}
			$out[ $t->term_id ] = $label;
		}
		asort( $out );
		return $out;
	}

	private function default_items() {
		$slugs = array(
			'mens-leather-briefcase',
			'mens-leather-shoulder-bag',
			'mens-leather-wallet',
			'leather-belt',
			'%d8%b3%d8%aa-%d9%85%d8%b1%d8%af%d8%a7%d9%86%d9%87',
			'womens-leather-shoulder-bag',
			'womens-leather-clutch',
			'womens-leather-wallet',
			'womens-leather-belt',
		);
		$items = array();
		if ( taxonomy_exists( 'product_cat' ) ) {
			foreach ( $slugs as $slug ) {
				$term = get_term_by( 'slug', $slug, 'product_cat' );
				if ( $term && ! is_wp_error( $term ) ) {
					$items[] = array( 'cat' => (string) $term->term_id );
				}
			}
		}
		return $items;
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
			'title',
			array(
				'label'       => esc_html__( 'عنوان بخش (خالی = بدون عنوان)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'خرید بر اساس دسته‌بندی',
				'label_block' => true,
			)
		);

		$r = new Repeater();
		$r->add_control(
			'cat',
			array(
				'label'       => esc_html__( 'دسته‌بندی', 'bagllet-widgets' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->category_options(),
				'label_block' => true,
				'multiple'    => false,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'دسته‌ها (چیدمان ۹ تایی: کوچک، کوچک، عریض؛ کوچک، کوچک، عریض؛ سه‌تایی)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ cat }}}',
				'default'     => $this->default_items(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * تصویر دسته: تصویر شاخص خود دسته، وگرنه تصویر جدیدترین محصول آن دسته.
	 */
	private function term_image( $term ) {
		$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );

		if ( ! $thumb && post_type_exists( 'product' ) ) {
			$q = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'fields'         => 'ids',
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
						array(
							'taxonomy'         => 'product_cat',
							'field'            => 'term_id',
							'terms'            => $term->term_id,
							'include_children' => true,
						),
					),
				)
			);
			if ( ! empty( $q ) ) {
				$thumb = (int) get_post_thumbnail_id( $q[0] );
			}
		}

		if ( $thumb ) {
			return wp_get_attachment_image(
				$thumb,
				'large',
				false,
				array(
					'alt'      => $term->name,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
		}

		return '';
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		if ( empty( $s['items'] ) || ! taxonomy_exists( 'product_cat' ) ) {
			return;
		}

		$cards = array();
		foreach ( $s['items'] as $it ) {
			if ( empty( $it['cat'] ) ) {
				continue;
			}
			$term = get_term( (int) $it['cat'], 'product_cat' );
			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}
			$link    = get_term_link( $term );
			$cards[] = array(
				'name' => $term->name,
				'url'  => is_wp_error( $link ) ? '' : $link,
				'img'  => $this->term_image( $term ),
			);
		}
		if ( ! $cards ) {
			return;
		}

		echo '<section class="bgl-cg-w">';
		if ( ! empty( $s['title'] ) ) {
			echo '<div class="bgl-cg-h"><h2>' . esc_html( $s['title'] ) . '</h2></div>';
		}
		echo '<div class="bgl-cg bgl-cg--mosaic">';

		foreach ( $cards as $c ) {
			$tag  = $c['url'] ? 'a' : 'div';
			$href = $c['url'] ? ' href="' . esc_url( $c['url'] ) . '"' : '';
			echo '<' . $tag . ' class="bgl-cg__t is-small"' . $href . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<span class="bgl-cg__ph">' . $c['img'] . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<span class="bgl-cg__in"><span class="bgl-cg__tx"><b>' . esc_html( $c['name'] ) . '</b></span></span>';
			echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		echo '</div></section>';
	}
}
