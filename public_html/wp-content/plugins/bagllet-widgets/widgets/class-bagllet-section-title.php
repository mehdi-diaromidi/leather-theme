<?php
namespace Bagllet_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * تیتر بخش: تیتر مشکی + خط تزئینی Warm Clay (۳۲×۲) روی hairline.
 */
class Section_Title extends Widget_Base {

	public function get_name() {
		return 'bagllet-section-title';
	}

	public function get_title() {
		return esc_html__( 'بگلت: تیتر بخش', 'bagllet-widgets' );
	}

	public function get_icon() {
		return 'eicon-heading';
	}

	public function get_categories() {
		return array( 'bagllet' );
	}

	public function get_keywords() {
		return array( 'bagllet', 'title', 'heading', 'تیتر', 'عنوان', 'بگلت', 'باگلت' );
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
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'جواهرات مردانه',
				'label_block' => true,
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'تگ HTML', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$tag = in_array( $s['html_tag'], array( 'h2', 'h3', 'h4' ), true ) ? $s['html_tag'] : 'h2';

		if ( empty( $s['title'] ) ) {
			return;
		}

		echo '<div class="bgl-sec"><' . $tag . ' class="bgl-sec__title">' . esc_html( $s['title'] ) . '</' . $tag . '></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
