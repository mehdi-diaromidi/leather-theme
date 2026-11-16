<?php
namespace Bagllet_Widgets;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Story extends Widget_Base {

	public function get_name() {
		return 'bagllet-story';
	}

	public function get_title() {
		return esc_html__( 'بگلت: داستان برند', 'bagllet-widgets' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_categories() {
		return array( 'bagllet' );
	}

	public function get_keywords() {
		return array( 'bagllet', 'about', 'story', 'درباره', 'داستان', 'بگلت', 'باگلت' );
	}

	public function get_style_depends() {
		return array( 'bagllet-widgets' );
	}

	protected function register_controls() {

		/* متن */
		$this->start_controls_section(
			'section_text',
			array(
				'label' => esc_html__( 'متن', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'سرتیتر کوچک', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'داستان ما',
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_line_1',
			array(
				'label'       => esc_html__( 'تیتر، خط اول (سفید)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'هر کیف، یک دوخت',
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_line_2',
			array(
				'label'       => esc_html__( 'تیتر، خط دوم (Warm Clay)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'که سال‌ها می‌ماند',
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'تگ تیتر', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
				),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'متن معرفی', 'bagllet-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 6,
				'default' => 'متن معرفی برند را اینجا بنویسید. سه تا چهار خط کافی است.',
			)
		);

		$this->end_controls_section();

		/* دکمه‌ها */
		$this->start_controls_section(
			'section_actions',
			array(
				'label' => esc_html__( 'دکمه‌ها', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'primary_text',
			array(
				'label'   => esc_html__( 'متن دکمه اصلی', 'bagllet-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'تماس با ما',
			)
		);

		$this->add_control(
			'primary_link',
			array(
				'label'       => esc_html__( 'لینک دکمه اصلی', 'bagllet-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://bagllet.ir/contact',
			)
		);

		$this->add_control(
			'primary_style',
			array(
				'label'       => esc_html__( 'ظاهر دکمه اصلی', 'bagllet-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'filled',
				'options'     => array(
					'filled'  => esc_html__( 'پر (Warm Clay)', 'bagllet-widgets' ),
					'outline' => esc_html__( 'توخالی', 'bagllet-widgets' ),
				),
				'description' => esc_html__( 'اگر در دید اول صفحه دکمه Warm Clay دیگری هست، توخالی را انتخاب کنید.', 'bagllet-widgets' ),
			)
		);

		$this->add_control(
			'primary_icon',
			array(
				'label'        => esc_html__( 'آیکون تلفن روی دکمه', 'bagllet-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'secondary_text',
			array(
				'label'     => esc_html__( 'متن لینک دوم', 'bagllet-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'داستان ما را بخوانید',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'secondary_link',
			array(
				'label'       => esc_html__( 'لینک دوم', 'bagllet-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://bagllet.ir/about',
			)
		);

		$this->end_controls_section();

		/* تصاویر */
		$this->start_controls_section(
			'section_media',
			array(
				'label' => esc_html__( 'تصاویر', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'main_image',
			array(
				'label'       => esc_html__( 'تصویر اصلی (۴:۵)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => Utils::get_placeholder_image_src() ),
				'description' => esc_html__( 'عمودی، حداقل ۶۰۰×۷۵۰ پیکسل.', 'bagllet-widgets' ),
			)
		);

		$this->add_control(
			'detail_image',
			array(
				'label'       => esc_html__( 'تصویر جزئیات (۱:۱)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => Utils::get_placeholder_image_src() ),
				'description' => esc_html__( 'مربع، حداقل ۳۲۰×۳۲۰ پیکسل. اگر خالی بماند نمایش داده نمی‌شود.', 'bagllet-widgets' ),
			)
		);

		$this->add_control(
			'tag_text',
			array(
				'label'       => esc_html__( 'متن اتیکت روی تصویر', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'دست‌دوز',
				'description' => esc_html__( 'خالی بگذارید تا اتیکت حذف شود.', 'bagllet-widgets' ),
			)
		);

		$this->add_control(
			'stitch',
			array(
				'label'        => esc_html__( 'قاب و حاشیه کوک‌دوزی', 'bagllet-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		/* آمار */
		$this->start_controls_section(
			'section_stats',
			array(
				'label' => esc_html__( 'کارت آمار', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'kind',
			array(
				'label'   => esc_html__( 'نوع', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'number',
				'options' => array(
					'number' => esc_html__( 'عدد', 'bagllet-widgets' ),
					'icon'   => esc_html__( 'آیکون', 'bagllet-widgets' ),
				),
			)
		);

		$repeater->add_control(
			'number',
			array(
				'label'     => esc_html__( 'عدد', 'bagllet-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '۱۵+',
				'condition' => array( 'kind' => 'number' ),
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'     => esc_html__( 'آیکون', 'bagllet-widgets' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-certificate',
					'library' => 'fa-solid',
				),
				'condition' => array( 'kind' => 'icon' ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'سال سابقه',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'       => esc_html__( 'توضیح کوتاه', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'stats',
			array(
				'label'       => esc_html__( 'موارد (حداکثر ۳ مورد پیشنهاد می‌شود)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'kind'   => 'number',
						'number' => '۱۵+',
						'title'  => 'سال سابقه',
						'text'   => 'در طراحی و دوخت',
					),
					array(
						'kind'   => 'number',
						'number' => '۹۸٪',
						'title'  => 'رضایت مشتریان',
						'text'   => 'از نظر خریداران',
					),
					array(
						'kind'  => 'icon',
						'icon'  => array(
							'value'   => 'fas fa-certificate',
							'library' => 'fa-solid',
						),
						'title' => 'ضمانت اصالت',
						'text'  => 'چرم طبیعی',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$title_tag = in_array( $s['title_tag'], array( 'h1', 'h2', 'h3' ), true ) ? $s['title_tag'] : 'h2';
		$stats     = ! empty( $s['stats'] ) && is_array( $s['stats'] ) ? $s['stats'] : array();
		$has_main  = ! empty( $s['main_image']['url'] );
		$has_det   = ! empty( $s['detail_image']['url'] );

		$classes = array( 'bgl-story' );
		if ( 'yes' === $s['stitch'] ) {
			$classes[] = 'bgl-story--stitch';
		}
		if ( $stats ) {
			$classes[] = 'bgl-story--has-stats';
		}
		if ( ! $has_main ) {
			$classes[] = 'bgl-story--no-media';
		}

		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$primary_classes = array( 'bgl-btn', 'outline' === $s['primary_style'] ? 'bgl-btn--outline' : 'bgl-btn--clay' );
		$this->add_render_attribute( 'primary', 'class', $primary_classes );
		if ( ! empty( $s['primary_link']['url'] ) ) {
			$this->add_link_attributes( 'primary', $s['primary_link'] );
		}

		$this->add_render_attribute( 'secondary', 'class', 'bgl-link' );
		if ( ! empty( $s['secondary_link']['url'] ) ) {
			$this->add_link_attributes( 'secondary', $s['secondary_link'] );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="bgl-story__box">
				<div class="bgl-story__text">
					<?php if ( '' !== $s['eyebrow'] ) : ?>
						<p class="bgl-story__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $s['title_line_1'] || '' !== $s['title_line_2'] ) : ?>
						<<?php echo esc_html( $title_tag ); ?> class="bgl-story__title">
							<?php if ( '' !== $s['title_line_1'] ) : ?>
								<span><?php echo esc_html( $s['title_line_1'] ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== $s['title_line_2'] ) : ?>
								<span class="bgl-story__title-accent"><?php echo esc_html( $s['title_line_2'] ); ?></span>
							<?php endif; ?>
						</<?php echo esc_html( $title_tag ); ?>>
					<?php endif; ?>

					<?php if ( '' !== $s['description'] ) : ?>
						<div class="bgl-story__desc"><?php echo wp_kses_post( wpautop( $s['description'] ) ); ?></div>
					<?php endif; ?>

					<?php if ( '' !== $s['primary_text'] || '' !== $s['secondary_text'] ) : ?>
						<div class="bgl-story__actions">
							<?php if ( '' !== $s['primary_text'] ) : ?>
								<a <?php $this->print_render_attribute_string( 'primary' ); ?>>
									<?php echo esc_html( $s['primary_text'] ); ?>
									<?php
									if ( 'yes' === $s['primary_icon'] ) {
										echo bagllet_widgets_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput
									}
									?>
								</a>
							<?php endif; ?>
							<?php if ( '' !== $s['secondary_text'] ) : ?>
								<a <?php $this->print_render_attribute_string( 'secondary' ); ?>>
									<?php echo esc_html( $s['secondary_text'] ); ?>
									<?php echo bagllet_widgets_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( $has_main ) : ?>
					<div class="bgl-story__media">
						<div class="bgl-story__main">
							<?php echo bagllet_widgets_image( $s['main_image'], 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
						<?php if ( $has_det ) : ?>
							<div class="bgl-story__detail">
								<?php echo bagllet_widgets_image( $s['detail_image'], 'medium_large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</div>
						<?php endif; ?>
						<?php if ( '' !== $s['tag_text'] ) : ?>
							<span class="bgl-story__tag"><?php echo esc_html( $s['tag_text'] ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $stats ) : ?>
				<div class="bgl-story__patch">
					<ul class="bgl-story__stats">
						<?php foreach ( $stats as $item ) : ?>
							<?php
							$kind   = isset( $item['kind'] ) ? $item['kind'] : 'number';
							$number = isset( $item['number'] ) ? $item['number'] : '';
							$title  = isset( $item['title'] ) ? $item['title'] : '';
							$text   = isset( $item['text'] ) ? $item['text'] : '';
							?>
							<li class="bgl-story__stat">
								<?php if ( 'icon' === $kind && ! empty( $item['icon']['value'] ) ) : ?>
									<span class="bgl-story__stat-icon"><?php Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
								<?php elseif ( 'number' === $kind && '' !== $number ) : ?>
									<span class="bgl-story__stat-num"><?php echo esc_html( $number ); ?></span>
								<?php endif; ?>
								<span class="bgl-story__stat-body">
									<?php if ( '' !== $title ) : ?>
										<strong><?php echo esc_html( $title ); ?></strong>
									<?php endif; ?>
									<?php if ( '' !== $text ) : ?>
										<small><?php echo esc_html( $text ); ?></small>
									<?php endif; ?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</section>
		<?php
	}
}
