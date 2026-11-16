<?php
namespace Bagllet_Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Instagram extends Widget_Base {

	public function get_name() {
		return 'bagllet-instagram';
	}

	public function get_title() {
		return esc_html__( 'بگلت: سکشن اینستاگرام', 'bagllet-widgets' );
	}

	public function get_icon() {
		return 'eicon-instagram-post';
	}

	public function get_categories() {
		return array( 'bagllet' );
	}

	public function get_keywords() {
		return array( 'bagllet', 'instagram', 'اینستاگرام', 'بگلت', 'باگلت' );
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
				'default'     => 'صفحه اینستاگرام',
				'label_block' => true,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => "هر هفته، چیز تازه‌ای\nاز کارگاه بگلت",
				'description' => esc_html__( 'با زدن Enter می‌توانید عنوان را در دو خط بنویسید.', 'bagllet-widgets' ),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'تگ عنوان', 'bagllet-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
				),
			)
		);

		$this->add_control(
			'handle',
			array(
				'label'       => esc_html__( 'آیدی پیج (برچسب مشکی)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '@bagllet.ir',
				'description' => esc_html__( 'خالی بگذارید تا برچسب حذف شود.', 'bagllet-widgets' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'متن معرفی', 'bagllet-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => 'محصولات جدید، پشت صحنه دوخت و پیشنهادهای ویژه را زودتر از همه در صفحه ببینید.',
			)
		);

		$this->end_controls_section();

		/* آمار */
		$this->start_controls_section(
			'section_stats',
			array(
				'label' => esc_html__( 'آمار صفحه', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'number',
			array(
				'label'   => esc_html__( 'عدد', 'bagllet-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '۱۰۰+',
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'   => esc_html__( 'برچسب', 'bagllet-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'پست',
			)
		);

		$this->add_control(
			'stats',
			array(
				'label'       => esc_html__( 'موارد', 'bagllet-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ number }}} {{{ label }}}',
				'description' => esc_html__( 'عددها دستی‌اند؛ گرد و با «+» بنویسید تا دیرتر کهنه شوند. برای حذف کامل آمار، همه موارد را پاک کنید.', 'bagllet-widgets' ),
				'default'     => array(
					array(
						'number' => '۱۰۰+',
						'label'  => 'پست',
					),
					array(
						'number' => '۲٬۵۰۰+',
						'label'  => 'دنبال‌کننده',
					),
				),
			)
		);

		$this->end_controls_section();

		/* دکمه */
		$this->start_controls_section(
			'section_button',
			array(
				'label' => esc_html__( 'دکمه', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'بگلت را در اینستاگرام دنبال کنید',
				'label_block' => true,
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => esc_html__( 'لینک صفحه اینستاگرام', 'bagllet-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://www.instagram.com/your-page',
				'default'     => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);

		$this->end_controls_section();

		/* گوشی */
		$this->start_controls_section(
			'section_media',
			array(
				'label' => esc_html__( 'تصویر گوشی', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'screenshot',
			array(
				'label'       => esc_html__( 'اسکرین‌شات صفحه اینستاگرام', 'bagllet-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => Utils::get_placeholder_image_src() ),
				'description' => esc_html__( 'اسکرین‌شات عمودی از گوشی (ترجیحاً حالت تیره). نوار وضعیت بالای گوشی را قبل از بارگذاری برش بزنید. اگر خالی بماند، کل بخش گوشی نمایش داده نمی‌شود.', 'bagllet-widgets' ),
			)
		);

		$this->add_control(
			'screenshot_alt',
			array(
				'label'       => esc_html__( 'متن جایگزین تصویر', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'صفحه اینستاگرام بگلت',
				'label_block' => true,
			)
		);

		$this->add_control(
			'phone_frame',
			array(
				'label'        => esc_html__( 'قاب گوشی دور تصویر', 'bagllet-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'اگر تصویرتان خودش قاب گوشی دارد، خاموش کنید.', 'bagllet-widgets' ),
			)
		);

		$this->add_control(
			'plate',
			array(
				'label'        => esc_html__( 'صفحه رنگی پشت گوشی', 'bagllet-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		/* کارت‌های شناور */
		$this->start_controls_section(
			'section_cards',
			array(
				'label' => esc_html__( 'کارت‌های شناور کنار گوشی', 'bagllet-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$card = new Repeater();

		$card->add_control(
			'image',
			array(
				'label'   => esc_html__( 'تصویر کوچک (۱:۱)', 'bagllet-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$card->add_control(
			'tag',
			array(
				'label'   => esc_html__( 'اتیکت', 'bagllet-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'جدید',
			)
		);

		$card->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'کیف دوشی تازه رسید',
				'label_block' => true,
			)
		);

		$card->add_control(
			'text',
			array(
				'label'       => esc_html__( 'توضیح کوتاه', 'bagllet-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'همین حالا در پیج',
				'label_block' => true,
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'کارت‌ها (حداکثر ۲ کارت نمایش داده می‌شود)', 'bagllet-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $card->get_controls(),
				'title_field' => '{{{ title }}}',
				'description' => esc_html__( 'کارت اول بالا و کارت دوم پایین کنار گوشی می‌نشیند. فقط چیزی بنویسید که واقعاً در پیج هست. در موبایل کارت‌ها نمایش داده نمی‌شوند. برای حذف، همه را پاک کنید.', 'bagllet-widgets' ),
				'default'     => array(
					array(
						'tag'   => 'جدید',
						'title' => 'کیف دوشی تازه رسید',
						'text'  => 'همین حالا در پیج',
					),
					array(
						'tag'   => 'پشت صحنه',
						'title' => 'از برش تا دوخت',
						'text'  => 'ویدیوی کارگاه',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$title_tag = in_array( $s['title_tag'], array( 'h2', 'h3' ), true ) ? $s['title_tag'] : 'h2';
		$stats     = ! empty( $s['stats'] ) && is_array( $s['stats'] ) ? $s['stats'] : array();
		$cards     = ! empty( $s['cards'] ) && is_array( $s['cards'] ) ? array_slice( $s['cards'], 0, 2 ) : array();
		$has_image = ! empty( $s['screenshot']['url'] );
		$has_link  = ! empty( $s['button_link']['url'] );
		$lines     = '' !== $s['title'] ? preg_split( '/\r\n|\r|\n/', $s['title'] ) : array();

		$classes = array( 'bgl-ig' );
		if ( ! $has_image ) {
			$classes[] = 'bgl-ig--no-media';
		}
		$this->add_render_attribute( 'wrapper', 'class', $classes );

		$this->add_render_attribute( 'button', 'class', array( 'bgl-btn', 'bgl-btn--dark' ) );
		if ( $has_link ) {
			$this->add_link_attributes( 'button', $s['button_link'] );
		}

		$phone_classes = array( 'bgl-ig__phone' );
		if ( 'yes' === $s['phone_frame'] ) {
			$phone_classes[] = 'bgl-ig__phone--frame';
		}
		$this->add_render_attribute( 'phone', 'class', $phone_classes );

		$img_src = '';
		if ( $has_image ) {
			$img_src = $s['screenshot']['url'];
			if ( ! empty( $s['screenshot']['id'] ) ) {
				$sized = wp_get_attachment_image_url( $s['screenshot']['id'], 'large' );
				if ( $sized ) {
					$img_src = $sized;
				}
			}
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="bgl-ig__text">
				<?php if ( '' !== $s['eyebrow'] ) : ?>
					<p class="bgl-ig__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( $lines ) : ?>
					<<?php echo esc_html( $title_tag ); ?> class="bgl-ig__title">
						<?php
						foreach ( $lines as $i => $line ) {
							if ( $i > 0 ) {
								echo '<br>';
							}
							echo esc_html( $line );
						}
						?>
					</<?php echo esc_html( $title_tag ); ?>>
				<?php endif; ?>

				<?php if ( '' !== $s['handle'] ) : ?>
					<span class="bgl-ig__handle">
						<?php echo bagllet_widgets_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<bdi><?php echo esc_html( $s['handle'] ); ?></bdi>
					</span>
				<?php endif; ?>

				<?php if ( $stats ) : ?>
					<ul class="bgl-ig__stats">
						<?php foreach ( $stats as $item ) : ?>
							<li>
								<strong><?php echo esc_html( isset( $item['number'] ) ? $item['number'] : '' ); ?></strong>
								<small><?php echo esc_html( isset( $item['label'] ) ? $item['label'] : '' ); ?></small>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( '' !== $s['description'] ) : ?>
					<div class="bgl-ig__desc"><?php echo wp_kses_post( wpautop( $s['description'] ) ); ?></div>
				<?php endif; ?>

				<?php if ( '' !== $s['button_text'] ) : ?>
					<a <?php $this->print_render_attribute_string( 'button' ); ?>>
						<?php echo bagllet_widgets_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( $s['button_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( $has_image ) : ?>
				<div class="bgl-ig__media">
					<?php if ( 'yes' === $s['plate'] ) : ?>
						<div class="bgl-ig__plate" aria-hidden="true"></div>
					<?php endif; ?>

					<div <?php $this->print_render_attribute_string( 'phone' ); ?>>
						<?php if ( $has_link ) : ?>
							<a href="<?php echo esc_url( $s['button_link']['url'] ); ?>" target="_blank" rel="noopener nofollow" tabindex="-1" aria-hidden="true">
						<?php endif; ?>
						<img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $s['screenshot_alt'] ); ?>" loading="lazy">
						<?php if ( $has_link ) : ?>
							</a>
						<?php endif; ?>
					</div>

					<?php foreach ( $cards as $i => $card ) : ?>
						<?php
						$c_title = isset( $card['title'] ) ? $card['title'] : '';
						$c_text  = isset( $card['text'] ) ? $card['text'] : '';
						$c_tag   = isset( $card['tag'] ) ? $card['tag'] : '';
						$c_img   = isset( $card['image'] ) ? $card['image'] : array();
						if ( '' === $c_title && '' === $c_text ) {
							continue;
						}
						?>
						<div class="bgl-ig__card bgl-ig__card--<?php echo (int) $i + 1; ?>">
							<?php if ( '' !== $c_tag ) : ?>
								<span class="bgl-ig__card-tag"><?php echo esc_html( $c_tag ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $c_img['url'] ) ) : ?>
								<span class="bgl-ig__card-img"><?php echo bagllet_widgets_image( $c_img, 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<?php endif; ?>
							<span class="bgl-ig__card-body">
								<?php if ( '' !== $c_title ) : ?>
									<strong><?php echo esc_html( $c_title ); ?></strong>
								<?php endif; ?>
								<?php if ( '' !== $c_text ) : ?>
									<small><?php echo esc_html( $c_text ); ?></small>
								<?php endif; ?>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>
		<?php
	}
}
