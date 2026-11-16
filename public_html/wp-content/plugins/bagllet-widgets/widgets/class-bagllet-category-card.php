<?php
namespace Bagllet_Widgets;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * کارت دسته‌بندی (ستون کنار اسلایدر محصولات).
 */
class Category_Card extends Widget_Base {

	public function get_name() {
		return 'bagllet-category-card';
	}

	public function get_title() {
		return esc_html__( 'بگلت: کارت دسته‌بندی', 'bagllet-widgets' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_categories() {
		return array( 'bagllet' );
	}

	public function get_keywords() {
		return array( 'bagllet', 'category', 'دسته', 'دسته‌بندی', 'بگلت', 'باگلت' );
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
			'image',
			array(
				'label'   => esc_html__( 'تصویر (نسبت پیشنهادی ۶:۵)', 'bagllet-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => BAGLLET_WIDGETS_URL . 'assets/images/men-products.webp' ),
			)
		);

		$this->add_control(
			'image_alt',
			array(
				'label'       => esc_html__( 'متن جایگزین تصویر', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'tag_text',
			array(
				'label'       => esc_html__( 'تگ روی تصویر (خالی = بدون تگ)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'مجموعه مردانه',
				'label_block' => true,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'چرم مردانه',
				'label_block' => true,
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'توضیح کوتاه', 'bagllet-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'کیف، کیف پول و کمربند از چرم طبیعی',
				'rows'    => 3,
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'   => esc_html__( 'متن دکمه', 'bagllet-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'مشاهده محصولات',
			)
		);

		$this->add_control(
			'cta_link',
			array(
				'label' => esc_html__( 'لینک دکمه', 'bagllet-widgets' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'min_height',
			array(
				'label'       => esc_html__( 'هم‌قد با اسلایدر (px، خالی = خودکار)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => esc_html__( 'اگر ستون المنتور هم‌قد نشد، ارتفاع اسلایدر را اینجا بنویس.', 'bagllet-widgets' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$style = '';
		if ( ! empty( $s['min_height'] ) && is_numeric( $s['min_height'] ) ) {
			$style = ' style="min-height:' . (int) $s['min_height'] . 'px"';
		}

		$this->add_render_attribute( 'cta', 'class', 'bgl-cat__cta' );
		if ( ! empty( $s['cta_link']['url'] ) ) {
			$this->add_link_attributes( 'cta', $s['cta_link'] );
		}

		echo '<div class="bgl-cat"' . $style . '>'; // phpcs:ignore WordPress.Security.EscapeOutput

		$img = '';
		if ( ! empty( $s['image']['id'] ) ) {
			$img = wp_get_attachment_image(
				$s['image']['id'],
				'large',
				false,
				array(
					'loading' => 'lazy',
					'alt'     => $s['image_alt'],
				)
			);
		} elseif ( ! empty( $s['image']['url'] ) ) {
			$img = '<img src="' . esc_url( $s['image']['url'] ) . '" alt="' . esc_attr( $s['image_alt'] ) . '" loading="lazy">';
		}
		if ( $img ) {
			$tag = ! empty( $s['tag_text'] ) ? '<span class="bgl-cat__tag">' . esc_html( $s['tag_text'] ) . '</span>' : '';
			echo '<div class="bgl-cat__media">' . $img . $tag . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		if ( ! empty( $s['title'] ) ) {
			echo '<h3 class="bgl-cat__title">' . esc_html( $s['title'] ) . '</h3>';
		}
		if ( ! empty( $s['description'] ) ) {
			echo '<p class="bgl-cat__desc">' . esc_html( $s['description'] ) . '</p>';
		}

		if ( ! empty( $s['cta_text'] ) ) {
			echo '<a ';
			$this->print_render_attribute_string( 'cta' );
			echo '><span>' . esc_html( $s['cta_text'] ) . '</span>' . bagllet_widgets_icon( 'arrow' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		echo '</div>';
	}
}
