<?php
namespace Bagllet_Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * هیرو اسلایدر سبک: اسلایدها + باکس‌های شناور روی هر اسلاید.
 * بدون کتابخانه؛ فقط CSS + یک اسکریپت کوچک.
 */
class Hero extends Widget_Base {

	public function get_name() {
		return 'bagllet-hero';
	}

	public function get_title() {
		return esc_html__( 'بگلت: هیرو اسلایدر', 'bagllet-widgets' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return array( 'bagllet' );
	}

	public function get_keywords() {
		return array( 'bagllet', 'hero', 'slider', 'banner', 'هیرو', 'اسلایدر', 'بنر', 'بگلت', 'باگلت' );
	}

	public function get_style_depends() {
		return array( 'bagllet-widgets' );
	}

	public function get_script_depends() {
		return array( 'bagllet-hero' );
	}

	protected function register_controls() {

		/* ---------- اسلایدها ---------- */
		$this->start_controls_section(
			'section_slides',
			array(
				'label' => esc_html__( 'اسلایدها', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$r = new Repeater();

		$r->add_control(
			'image',
			array(
				'label'       => esc_html__( 'تصویر دسکتاپ (پیشنهاد: ۳۰۰۰×۲۰۰۰، نسبت ۳:۲)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => '' ),
				'label_block' => true,
			)
		);
		$r->add_control(
			'image_mobile',
			array(
				'label'       => esc_html__( 'تصویر موبایل (اختیاری، نسبت ۴:۵)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => '' ),
				'label_block' => true,
			)
		);
		$r->add_control(
			'image_alt',
			array(
				'label'       => esc_html__( 'متن جایگزین تصویر', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'pos_d',
			array(
				'label'   => esc_html__( 'کادر تصویر در دسکتاپ (۰ چپ ... ۱۰۰ راست)', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
				'default' => array( 'unit' => '%', 'size' => 50 ),
			)
		);
		$r->add_control(
			'pos_y',
			array(
				'label'   => esc_html__( 'کادر عمودی تصویر (۰ بالا ... ۱۰۰ پایین؛ برای نیفتادن سر مدل عدد کم بگذار)', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
				'default' => array( 'unit' => '%', 'size' => 8 ),
			)
		);
		$r->add_control(
			'pos_m',
			array(
				'label'   => esc_html__( 'کادر تصویر در موبایل (۰ چپ ... ۱۰۰ راست)', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
				'default' => array( 'unit' => '%', 'size' => 40 ),
			)
		);
		$r->add_control(
			'text_side',
			array(
				'label'   => esc_html__( 'جای متن', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'start',
				'options' => array(
					'start' => esc_html__( 'راست', 'bagllet-widgets' ),
					'end'   => esc_html__( 'چپ', 'bagllet-widgets' ),
				),
			)
		);
		$r->add_control(
			'tag',
			array(
				'label'       => esc_html__( 'تگ کوچک (Sand)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'title_1',
			array(
				'label'       => esc_html__( 'تیتر، خط اول (سفید)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'title_2',
			array(
				'label'       => esc_html__( 'تیتر، خط دوم (Clay)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'description',
			array(
				'label' => esc_html__( 'توضیح کوتاه', 'bagllet-widgets' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);
		$r->add_control(
			'facts',
			array(
				'label'       => esc_html__( 'نکته‌های کوتاه زیر توضیح (با ویرگول جدا کن، خالی = بدون نکته)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'چرم طبیعی، دوخت دقیق، ضمانت اصالت',
			)
		);
		$r->add_control(
			'cta_text',
			array(
				'label'       => esc_html__( 'متن دکمه اصلی (Clay)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'cta_link',
			array(
				'label' => esc_html__( 'لینک دکمه اصلی', 'bagllet-widgets' ),
				'type'  => Controls_Manager::URL,
			)
		);
		$r->add_control(
			'link2_text',
			array(
				'label'       => esc_html__( 'متن لینک دوم (اختیاری)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$r->add_control(
			'link2_link',
			array(
				'label' => esc_html__( 'لینک دوم', 'bagllet-widgets' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => esc_html__( 'اسلایدها', 'bagllet-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'title_field' => '{{{ title_1 }}} {{{ title_2 }}}',
				'default'     => array(
					array(
						'tag'         => 'مجموعه مردانه',
						'title_1'     => 'کیف چرمی مردانه',
						'title_2'     => 'با دوخت ماندگار',
						'description' => 'کیف، کیف پول و کمربند از چرم طبیعی؛ برای روزهای کاری و روزهای خاص.',
						'cta_text'    => 'مشاهده مردانه',
						'text_side'   => 'start',
					),
					array(
						'tag'         => 'مجموعه زنانه',
						'title_1'     => 'کیف چرمی زنانه',
						'title_2'     => 'شیک و ماندگار',
						'description' => 'کیف دستی، کیف پول و کمربند از چرم طبیعی، با رنگ‌های گرم.',
						'cta_text'    => 'مشاهده زنانه',
						'text_side'   => 'start',
					),
					array(
						'tag'         => 'کلکسیون جدید',
						'title_1'     => 'ست کامل چرم',
						'title_2'     => 'برای هر سبک',
						'description' => 'از کیف تا کمربند و کیف پول؛ یک کلکسیون هماهنگ از چرم طبیعی.',
						'cta_text'    => 'مشاهده کلکسیون',
						'text_side'   => 'start',
					),
				),
			)
		);

		$this->end_controls_section();

		/* ---------- باکس‌های شناور ---------- */
		$this->start_controls_section(
			'section_boxes',
			array(
				'label' => esc_html__( 'باکس‌های شناور روی بنر', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$b = new Repeater();
		$b->add_control(
			'slide',
			array(
				'label'   => esc_html__( 'روی کدام اسلاید', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
			)
		);
		$b->add_control(
			'pos',
			array(
				'label'   => esc_html__( 'جایگاه (دسکتاپ)', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'tl',
				'options' => array(
					'tl' => esc_html__( 'بالا چپ', 'bagllet-widgets' ),
					'tr' => esc_html__( 'بالا راست', 'bagllet-widgets' ),
					'ml' => esc_html__( 'وسط چپ', 'bagllet-widgets' ),
					'mr' => esc_html__( 'وسط راست', 'bagllet-widgets' ),
					'bl' => esc_html__( 'پایین چپ', 'bagllet-widgets' ),
					'br' => esc_html__( 'پایین راست', 'bagllet-widgets' ),
				),
			)
		);
		$b->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'آیکون', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'leaf',
				'options' => bagllet_widgets_icon_options(),
			)
		);
		$b->add_control( 'title', array( 'label' => esc_html__( 'عنوان باکس', 'bagllet-widgets' ), 'type' => Controls_Manager::TEXT, 'label_block' => true ) );
		$b->add_control( 'text', array( 'label' => esc_html__( 'متن کوچک', 'bagllet-widgets' ), 'type' => Controls_Manager::TEXT, 'label_block' => true ) );
		$b->add_control( 'link', array( 'label' => esc_html__( 'لینک (اختیاری)', 'bagllet-widgets' ), 'type' => Controls_Manager::URL ) );

		$this->add_control(
			'boxes',
			array(
				'label'       => esc_html__( 'باکس‌ها (در موبایل فقط با روشن‌کردن گزینه‌ی تنظیمات نمایش داده می‌شوند)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $b->get_controls(),
				'title_field' => '{{{ slide }}} · {{{ title }}}',
				'default'     => array(
					array( 'slide' => '1', 'pos' => 'tl', 'icon' => 'layers', 'title' => 'چرم طبیعی', 'text' => 'دانه‌ی واقعی چرم' ),
					array( 'slide' => '1', 'pos' => 'bl', 'icon' => 'stitch', 'title' => 'دوخت دقیق', 'text' => 'لبه‌های تمیز و محکم' ),
					array( 'slide' => '2', 'pos' => 'tl', 'icon' => 'palette', 'title' => 'رنگ‌های گرم', 'text' => 'کنیاکی، شنی، عاجی' ),
					array( 'slide' => '2', 'pos' => 'bl', 'icon' => 'lock', 'title' => 'یراق برنجی', 'text' => 'ظریف و ماندگار' ),
					array( 'slide' => '3', 'pos' => 'tl', 'icon' => 'diamond', 'title' => 'کلکسیون جدید', 'text' => 'ست کامل چرم' ),
					array( 'slide' => '3', 'pos' => 'bl', 'icon' => 'gift', 'title' => 'هدیه‌ی ماندگار', 'text' => 'مناسب هر مناسبت' ),
				),
			)
		);

		$this->end_controls_section();

		/* ---------- تنظیمات ---------- */
		$this->start_controls_section(
			'section_settings',
			array(
				'label' => esc_html__( 'تنظیمات', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'چرخش خودکار', 'bagllet-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'interval',
			array(
				'label'   => esc_html__( 'مدت هر اسلاید (ثانیه)', 'bagllet-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 3,
				'max'     => 20,
			)
		);
		$this->add_control(
			'mobile_boxes',
			array(
				'label'        => esc_html__( 'نمایش باکس‌ها در موبایل (ارتفاع بنر بیشتر می‌شود)', 'bagllet-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'height',
			array(
				'label'   => esc_html__( 'ارتفاع در دسکتاپ (px)', 'bagllet-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
				'min'     => 360,
				'max'     => 900,
			)
		);

		$this->end_controls_section();
	}

	private function image_html( $sl, $index ) {
		$img    = $sl['image'];
		$alt    = ! empty( $sl['image_alt'] ) ? $sl['image_alt'] : trim( ( $sl['title_1'] ?? '' ) . ' ' . ( $sl['title_2'] ?? '' ) );
		$attrs  = array(
			'alt'      => $alt,
			'decoding' => 'async',
			'sizes'    => '(max-width: 767px) 100vw, 1385px',
		);
		if ( 0 === $index ) {
			$attrs['loading']       = 'eager';
			$attrs['fetchpriority'] = 'high';
		} else {
			$attrs['loading'] = 'lazy';
		}

		$main = '';
		if ( ! empty( $img['id'] ) ) {
			$main = wp_get_attachment_image( $img['id'], 'full', false, $attrs );
		} elseif ( ! empty( $img['url'] ) ) {
			$main = '<img src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $alt ) . '" decoding="async" loading="' . esc_attr( $attrs['loading'] ) . '">';
		}
		if ( ! $main ) {
			return '';
		}

		$mobile = '';
		if ( ! empty( $sl['image_mobile']['id'] ) ) {
			$mobile = wp_get_attachment_image_url( $sl['image_mobile']['id'], 'large' );
		} elseif ( ! empty( $sl['image_mobile']['url'] ) ) {
			$mobile = $sl['image_mobile']['url'];
		}

		if ( $mobile ) {
			return '<picture><source media="(max-width: 767px)" srcset="' . esc_url( $mobile ) . '">' . $main . '</picture>';
		}

		return $main;
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$slides = ! empty( $s['slides'] ) ? array_values( $s['slides'] ) : array();
		$total  = count( $slides );
		if ( ! $total ) {
			return;
		}

		$boxes = array();
		if ( ! empty( $s['boxes'] ) ) {
			foreach ( $s['boxes'] as $bx ) {
				$boxes[ (int) $bx['slide'] ][] = $bx;
			}
		}

		$height   = ! empty( $s['height'] ) ? (int) $s['height'] : 600;
		$interval = ! empty( $s['interval'] ) ? (int) $s['interval'] : 6;
		$autoplay = ( 'yes' === $s['autoplay'] && $total > 1 ) ? 'yes' : 'no';

		$mbx = ( 'yes' === $s['mobile_boxes'] ) ? ' bgl-hero--mbx' : '';

		echo '<section class="bgl-hero' . esc_attr( $mbx ) . '" role="region" aria-roledescription="carousel" aria-label="' . esc_attr__( 'اسلایدر معرفی', 'bagllet-widgets' ) . '" data-autoplay="' . esc_attr( $autoplay ) . '" data-interval="' . esc_attr( $interval ) . '" style="--bgl-h:' . (int) $height . 'px">'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="bgl-hero__track">';

		foreach ( $slides as $i => $sl ) {
			$n    = $i + 1;
			$side = ( 'end' === $sl['text_side'] ) ? 'is-end' : 'is-start';
			$pd   = isset( $sl['pos_d']['size'] ) ? (float) $sl['pos_d']['size'] : 50;
			$pm   = isset( $sl['pos_m']['size'] ) ? (float) $sl['pos_m']['size'] : 40;
			$py   = isset( $sl['pos_y']['size'] ) && '' !== $sl['pos_y']['size'] ? (float) $sl['pos_y']['size'] : 8;

			// عدد 0 = چپ تصویر، 100 = راست تصویر؛ پیش‌فرض CSS هم همین است.
			echo '<div class="bgl-hero__slide ' . esc_attr( $side ) . ( 0 === $i ? ' is-active' : '' ) . '" role="group" aria-roledescription="slide" aria-label="' . esc_attr( $n . ' / ' . $total ) . '" style="--px-d:' . esc_attr( $pd ) . '%;--px-m:' . esc_attr( $pm ) . '%;--py:' . esc_attr( $py ) . '%"' . ( 0 === $i ? '' : ' aria-hidden="true" inert' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput

			echo '<div class="bgl-hero__media">' . $this->image_html( $sl, $i ) . '<span class="bgl-hero__shade" aria-hidden="true"></span>' . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput

			echo '<div class="bgl-hero__content">';
			echo '<div class="bgl-hero__meta"><span class="bgl-hero__no" aria-hidden="true">' . esc_html( bagllet_widgets_fa( '0' . $n ) ) . '<i>/ ' . esc_html( bagllet_widgets_fa( '0' . $total ) ) . '</i></span>';
			if ( ! empty( $sl['tag'] ) ) {
				echo '<span class="bgl-hero__tag">' . esc_html( $sl['tag'] ) . '</span>';
			}
			echo '</div>';
			if ( ! empty( $sl['title_1'] ) || ! empty( $sl['title_2'] ) ) {
				echo '<h2 class="bgl-hero__title">' . esc_html( $sl['title_1'] );
				if ( ! empty( $sl['title_2'] ) ) {
					echo '<span>' . esc_html( $sl['title_2'] ) . '</span>';
				}
				echo '</h2>';
			}
			if ( ! empty( $sl['description'] ) ) {
				echo '<p class="bgl-hero__desc">' . esc_html( $sl['description'] ) . '</p>';
			}
			$facts = isset( $sl['facts'] ) ? array_filter( array_map( 'trim', preg_split( '/[,،]/u', (string) $sl['facts'] ) ) ) : array();
			if ( $facts ) {
				echo '<ul class="bgl-hero__facts">';
				foreach ( array_slice( $facts, 0, 4 ) as $f ) {
					echo '<li>' . bagllet_widgets_icon( 'check' ) . '<span>' . esc_html( $f ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				echo '</ul>';
			}

			if ( ! empty( $sl['cta_text'] ) || ! empty( $sl['link2_text'] ) ) {
				echo '<div class="bgl-hero__actions">';
				if ( ! empty( $sl['cta_text'] ) ) {
					$k = 'cta_' . $i;
					$this->add_render_attribute( $k, 'class', 'bgl-hero__cta' );
					if ( ! empty( $sl['cta_link']['url'] ) ) {
						$this->add_link_attributes( $k, $sl['cta_link'] );
					}
					echo '<a ';
					$this->print_render_attribute_string( $k );
					echo '><span>' . esc_html( $sl['cta_text'] ) . '</span>' . bagllet_widgets_icon( 'arrow' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				if ( ! empty( $sl['link2_text'] ) ) {
					$k = 'l2_' . $i;
					$this->add_render_attribute( $k, 'class', 'bgl-hero__link' );
					if ( ! empty( $sl['link2_link']['url'] ) ) {
						$this->add_link_attributes( $k, $sl['link2_link'] );
					}
					echo '<a ';
					$this->print_render_attribute_string( $k );
					echo '>' . esc_html( $sl['link2_text'] ) . '</a>';
				}
				echo '</div>';
			}
			echo '</div>';

			if ( ! empty( $boxes[ $n ] ) ) {
				echo '<div class="bgl-hero__boxes">';
				foreach ( $boxes[ $n ] as $j => $bx ) {
					$pos = in_array( $bx['pos'], array( 'tl', 'tr', 'ml', 'mr', 'bl', 'br' ), true ) ? $bx['pos'] : 'tl';
					$k   = 'bx_' . $i . '_' . $j;
					$has = ! empty( $bx['link']['url'] );
					echo '<div class="bgl-hero__box" data-pos="' . esc_attr( $pos ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput
					if ( $has ) {
						$this->add_render_attribute( $k, 'class', 'bgl-hero__card' );
						$this->add_link_attributes( $k, $bx['link'] );
						echo '<a ';
						$this->print_render_attribute_string( $k );
						echo '>';
					} else {
						echo '<div class="bgl-hero__card">';
					}
					echo '<span class="bgl-hero__card-ic">' . bagllet_widgets_icon( $bx['icon'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
					echo '<span class="bgl-hero__card-tx"><b>' . esc_html( $bx['title'] ) . '</b>';
					if ( ! empty( $bx['text'] ) ) {
						echo '<small>' . esc_html( $bx['text'] ) . '</small>';
					}
					echo '</span>';
					echo $has ? '</a>' : '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
					echo '</div>';
				}
				echo '</div>';
			}

			echo '</div>';
		}

		echo '</div>';

		if ( $total > 1 ) {
			echo '<button class="bgl-hero__nav bgl-hero__nav--prev" type="button" aria-label="' . esc_attr__( 'اسلاید قبلی', 'bagllet-widgets' ) . '">' . bagllet_widgets_icon( 'chev-r' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<button class="bgl-hero__nav bgl-hero__nav--next" type="button" aria-label="' . esc_attr__( 'اسلاید بعدی', 'bagllet-widgets' ) . '">' . bagllet_widgets_icon( 'chev-l' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<div class="bgl-hero__bars">';
			for ( $b = 0; $b < $total; $b++ ) {
				echo '<button class="bgl-hero__bar' . ( 0 === $b ? ' is-active' : '' ) . '" type="button" aria-label="' . esc_attr( sprintf( /* translators: %d: slide number */ __( 'اسلاید %d', 'bagllet-widgets' ), $b + 1 ) ) . '"' . ( 0 === $b ? ' aria-current="true"' : '' ) . '><span class="bgl-hero__bar-in"></span></button>';
			}
			echo '</div>';
		}

		echo '</section>';
	}
}
