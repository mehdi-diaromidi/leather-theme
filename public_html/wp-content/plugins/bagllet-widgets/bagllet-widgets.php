<?php
/**
 * Plugin Name: Bagllet Widgets
 * Description: پنج ویجت المنتور برای bagllet.ir: «داستان برند»، «سکشن اینستاگرام»، «کارت دسته‌بندی»، «تیتر بخش» و «هیرو اسلایدر».
 * Version: 1.14.45
 * Author: Bagllet
 * Text Domain: bagllet-widgets
 * Requires Plugins: elementor
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BAGLLET_WIDGETS_VERSION', '1.14.45' );
define( 'BAGLLET_WIDGETS_PATH', plugin_dir_path( __FILE__ ) );
define( 'BAGLLET_WIDGETS_URL', plugin_dir_url( __FILE__ ) );

/**
 * هشدار در پیشخوان اگر المنتور فعال نباشد.
 */
function bagllet_widgets_missing_elementor_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'افزونه Bagllet Widgets برای کار کردن به المنتور نیاز دارد.', 'bagllet-widgets' ) . '</p></div>';
}

function bagllet_widgets_init() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'bagllet_widgets_missing_elementor_notice' );
		return;
	}

	add_action( 'elementor/elements/categories_registered', 'bagllet_widgets_register_category' );
	add_action( 'elementor/widgets/register', 'bagllet_widgets_register_widgets' );
	add_action( 'wp_enqueue_scripts', 'bagllet_widgets_register_assets' );
	add_action( 'elementor/frontend/after_register_scripts', 'bagllet_widgets_register_assets' );
	add_action( 'elementor/editor/after_enqueue_styles', 'bagllet_widgets_register_assets' );
	add_action( 'elementor/preview/enqueue_styles', 'bagllet_widgets_enqueue_preview' );
}
add_action( 'plugins_loaded', 'bagllet_widgets_init' );

/* نام فارسی برند «بگلت» است؛ اگر در تنظیمات ذخیره‌شده‌ی ویجت‌ها «باگلت» مانده باشد، هنگام نمایش اصلاح می‌شود. */
add_filter(
	'elementor/widget/render_content',
	function ( $content ) {
		return is_string( $content ) ? str_replace( 'باگلت', 'بگلت', $content ) : $content;
	},
	20
);

/* استایل سراسری سایت (دراپ‌داون منو و...) روی همه‌ی صفحه‌های بیرونی، بعد از قالب */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'bagllet-site', BAGLLET_WIDGETS_URL . 'assets/css/site-theme.css', array(), BAGLLET_WIDGETS_VERSION );
	},
	99
);

function bagllet_widgets_register_category( $elements_manager ) {
	$elements_manager->add_category(
		'bagllet',
		array(
			'title' => esc_html__( 'بگلت', 'bagllet-widgets' ),
			'icon'  => 'eicon-star',
		)
	);
}

function bagllet_widgets_register_assets() {
	wp_register_style(
		'bagllet-widgets',
		BAGLLET_WIDGETS_URL . 'assets/css/bagllet-widgets.css',
		array(),
		BAGLLET_WIDGETS_VERSION
	);

	wp_register_script(
		'bagllet-hero',
		BAGLLET_WIDGETS_URL . 'assets/js/bagllet-hero.js',
		array(),
		BAGLLET_WIDGETS_VERSION,
		true
	);
}

function bagllet_widgets_enqueue_preview() {
	bagllet_widgets_register_assets();
	wp_enqueue_style( 'bagllet-widgets' );
}

/**
 * بارگذاری امن: اگر یک فایل ویجت خطا داشته باشد، فقط همان ویجت ثبت نمی‌شود و سایت از کار نمی‌افتد.
 */
function bagllet_widgets_safe_require( $file ) {
	try {
		require_once BAGLLET_WIDGETS_PATH . $file;
		return true;
	} catch ( \Throwable $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Bagllet Widgets: ' . $file . ' - ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
		return false;
	}
}

function bagllet_widgets_register_widgets( $widgets_manager ) {
	$map = array(
		'widgets/class-bagllet-story.php'         => 'Story',
		'widgets/class-bagllet-instagram.php'     => 'Instagram',
		'widgets/class-bagllet-category-card.php' => 'Category_Card',
		'widgets/class-bagllet-section-title.php' => 'Section_Title',
		'widgets/class-bagllet-hero.php'          => 'Hero',
		'widgets/class-bagllet-category-grid.php' => 'Category_Grid',
		'widgets/class-bagllet-installment.php'   => 'Installment',
	);

	foreach ( $map as $file => $class ) {
		if ( ! bagllet_widgets_safe_require( $file ) ) {
			continue;
		}
		$fqcn = '\\Bagllet_Widgets\\' . $class;
		if ( class_exists( $fqcn ) ) {
			try {
				$widgets_manager->register( new $fqcn() );
			} catch ( \Throwable $e ) {
				continue;
			}
		}
	}
}

/* قالب صفحه‌ی «درباره ما» (مستقل از المنتور؛ محتوا در خود صفحه‌ی ویرایش وارد می‌شود). */
add_action(
	'plugins_loaded',
	function () {
		bagllet_widgets_safe_require( 'includes/about-page.php' );
		bagllet_widgets_safe_require( 'includes/contact-page.php' );
		bagllet_widgets_safe_require( 'includes/footer.php' );
		bagllet_widgets_safe_require( 'includes/product-ui.php' );
		bagllet_widgets_safe_require( 'includes/pricing.php' );
	},
	20
);

/**
 * عدد را فارسی می‌کند.
 */
function bagllet_widgets_fa( $n ) {
	return strtr( (string) $n, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
}

/**
 * آیکون‌های خطی ثابت (۲۴×۲۴، ضخامت ۱٫۵) که رنگشان را از متن می‌گیرند.
 */
function bagllet_widgets_icon( $name ) {
	$paths = array(
		'phone'     => '<path fill="currentColor" fill-opacity=".18" d="M5.5 3.5h3.2l1.6 4-2 1.4a10 10 0 0 0 5.8 5.8l1.4-2 4 1.6v3.2a2 2 0 0 1-2 2A15.5 15.5 0 0 1 3.5 5.5a2 2 0 0 1 2-2z" /><path d="M14.5 4.5a5 5 0 0 1 5 5M14.5 8a1.8 1.8 0 0 1 1.8 1.8" opacity=".7" />',
		'arrow'     => '<path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" />',
		'instagram' => '<rect fill="currentColor" fill-opacity=".18" x="3" y="3" width="18" height="18" rx="5.5" /><circle cx="12" cy="12" r="4" /><circle cx="12" cy="12" r="1.4" fill="currentColor" stroke="none" /><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none" />',
		'shield'    => '<path fill="currentColor" fill-opacity=".18" d="M12 3l7.5 3v5.2c0 4.6-3 8.3-7.5 9.8-4.5-1.5-7.5-5.2-7.5-9.8V6z" /><path d="M8.6 11.8l2.3 2.3 4.5-4.6" /><path d="M12 6v1.5" opacity=".6" />',
		'truck'     => '<path fill="currentColor" fill-opacity=".18" d="M2.5 6.5h11v9.5h-11z" /><path d="M13.5 10h4.2l3.3 3.2V16h-7.5z" /><circle cx="7" cy="17.5" r="2" fill="currentColor" fill-opacity=".9" /><circle cx="17" cy="17.5" r="2" fill="currentColor" fill-opacity=".9" /><path d="M5 9h5M5 12h3" opacity=".6" />',
		'leaf'      => '<path fill="currentColor" fill-opacity=".18" d="M12 3c4.2 3.2 6.2 6.2 6.2 9.2a6.2 6.2 0 0 1-12.4 0C5.8 9.2 7.8 6.2 12 3z" /><path d="M12 21v-9M12 15l3-2.5M12 17.5l-3-2.5" />',
		'sparkle'   => '<path fill="currentColor" fill-opacity=".18" d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z" /><path d="M18.5 15.5l.7 1.8 1.8.7-1.8.7-.7 1.8-.7-1.8-1.8-.7 1.8-.7z" fill="currentColor" stroke="none" /><path d="M5.5 3.5l.5 1.2 1.2.5-1.2.5-.5 1.2-.5-1.2-1.2-.5 1.2-.5z" fill="currentColor" stroke="none" />',
		'gift'      => '<rect fill="currentColor" fill-opacity=".18" x="4" y="9" width="16" height="4" rx="1" /><path d="M5 13v7h14v-7" /><path d="M12 9v11" /><path d="M12 9c-2-3-5-3-5-1s3 1 5 1c2 0 5 1 5-1s-3-2-5 1" />',
		'star'      => '<path fill="currentColor" fill-opacity=".18" d="M12 17.75l-6.2 3.25 1.2-6.9-5-4.9 6.9-1 3.1-6.2 3.1 6.2 6.9 1-5 4.9 1.2 6.9z" /><circle cx="12" cy="11.5" r="1.2" fill="currentColor" stroke="none" />',
		'whatsapp'  => '<path fill="currentColor" fill-opacity=".18" d="M3.5 20.5 5 16.3a8.5 8.5 0 1 1 3 3z" /><path d="M9 8.8c-.5 1.6.5 3.6 2.2 5.2s3.7 2.4 5.2 1.8l.4-1.6-2-1-1 .8c-.9-.4-1.8-1.2-2.3-2.2l.8-1-.9-2z" fill="currentColor" stroke="none" />',
		'telegram'  => '<path fill="currentColor" fill-opacity=".18" d="M20.5 4 3.5 10.6l5.2 2 1.7 5.6 3-3.6 4.6 3.4z" /><path d="M8.7 12.6 20.5 4M10.4 18.2l.4-5.4" />',
		'mail'      => '<rect fill="currentColor" fill-opacity=".18" x="3" y="5.5" width="18" height="13" rx="3" /><path d="M3.8 7.5 12 13l8.2-5.5" /><path d="M3.8 17.5l5.6-5M20.2 17.5l-5.6-5" opacity=".55" />',
		'chat'      => '<path fill="currentColor" fill-opacity=".18" d="M4 5.5h16v11h-8.5L6.5 20.5v-4H4z" /><path d="M8 10h8M8 13h5" opacity=".75" />',
		'clock'     => '<circle fill="currentColor" fill-opacity=".18" cx="12" cy="12" r="9" /><path d="M12 7v5.2l3.4 2" /><circle cx="12" cy="12" r="1" fill="currentColor" stroke="none" /><path d="M12 3.4v1M12 19.6v1M3.4 12h1M19.6 12h1" opacity=".6" />',
		'lock'      => '<rect fill="currentColor" fill-opacity=".18" x="5" y="11" width="14" height="10" rx="2.5" /><path d="M8 11V7.5a4 4 0 1 1 8 0V11" /><circle cx="12" cy="15.2" r="1.4" fill="currentColor" stroke="none" /><path d="M12 16.4v2" />',
		'wallet'    => '<path fill="currentColor" fill-opacity=".18" d="M4 7.5a2.5 2.5 0 0 1 2.5-2.5H17v3H6.5A2.5 2.5 0 0 1 4 5.5v13a2.5 2.5 0 0 0 2.5 2.5H18a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2H6.5" /><path d="M20 12.5v4h-4a2 2 0 0 1 0-4z" /><circle cx="16.2" cy="14.5" r=".9" fill="currentColor" stroke="none" />',
		'buckle'    => '<rect fill="currentColor" fill-opacity=".18" x="2" y="8.5" width="20" height="7" rx="1.5" /><rect x="7" y="5.5" width="9" height="13" rx="2" /><path d="M11.5 12H18" /><path d="M4 12h1.5M19.5 12H21" opacity=".6" />',
		'stitch'    => '<path fill="currentColor" fill-opacity=".18" d="M3 7.5c4 0 4-3 9-3s5 3 9 3v9c-4 0-4 3-9 3s-5-3-9-3z" /><path d="M5.5 11h2M10 11h4M16.5 11h2M5.5 14.5h2M10 14.5h4M16.5 14.5h2" />',
		'layers'    => '<path fill="currentColor" fill-opacity=".18" d="M12 4l-8.5 4.2L12 12.4l8.5-4.2z" /><path d="M3.5 12.2 12 16.4l8.5-4.2" /><path d="M3.5 16.2 12 20.4l8.5-4.2" opacity=".6" />',
		'scissors'  => '<circle fill="currentColor" fill-opacity=".18" cx="6" cy="7" r="3" /><circle fill="currentColor" fill-opacity=".18" cx="6" cy="17" r="3" /><path d="M8.4 8.8 20 18M8.4 15.2 20 6" /><circle cx="13" cy="12" r="1" fill="currentColor" stroke="none" />',
		'diamond'   => '<path fill="currentColor" fill-opacity=".18" d="M6 5h12l3 5-9 10-9-10z" /><path d="M3 10h18M9 5l-2 5 5 10 5-10-2-5" /><path d="M10 10l2-5 2 5" opacity=".6" />',
		'palette'   => '<path fill="currentColor" fill-opacity=".18" d="M12 21a9 9 0 0 1 0-18c5 0 9 3.6 9 8 0 1.1-.5 2.1-1.3 2.8-.8.8-2 1.2-3.2 1.2h-2.5a2 2 0 0 0-1 3.75A1.3 1.3 0 0 1 12 21z" /><circle cx="8.2" cy="10.5" r="1.2" fill="currentColor" stroke="none" /><circle cx="12.2" cy="7.5" r="1.2" fill="currentColor" stroke="none" /><circle cx="16.5" cy="10.5" r="1.2" fill="currentColor" stroke="none" />',
		'zoom'      => '<circle fill="currentColor" fill-opacity=".18" cx="10.5" cy="10.5" r="7" /><path d="M21 21l-5.5-5.5" /><path d="M7.5 10.5h6M10.5 7.5v6" opacity=".7" />',
		'check'     => '<path d="M5 12l5 5l10 -10" />',
		'chev-l'    => '<path d="M15 6l-6 6l6 6" />',
		'chev-r'    => '<path d="M9 6l6 6l-6 6" />',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return '<svg class="bgl-icon bgl-icon--' . esc_attr( $name ) . '" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * فهرست آیکون‌ها برای لیست انتخاب در المنتور.
 */
function bagllet_widgets_icon_options() {
	return array(
		'lock'     => 'قفل',
		'wallet'   => 'کیف پول',
		'buckle'   => 'کمربند و سگک',
		'stitch'   => 'دوخت',
		'layers'   => 'لایه‌ی چرم',
		'leaf'     => 'برگ (طبیعی)',
		'shield'   => 'سپر (ضمانت)',
		'diamond'  => 'الماس (ممتاز)',
		'palette'  => 'رنگ‌ها',
		'scissors' => 'برش',
		'zoom'     => 'ذره‌بین (جزئیات)',
		'truck'    => 'ارسال',
		'gift'     => 'هدیه',
		'star'     => 'ستاره',
		'sparkle'  => 'درخشش',
	);
}

/**
 * خروجی تصویر از کنترل MEDIA المنتور.
 */
function bagllet_widgets_image( $media, $size = 'large' ) {
	if ( ! empty( $media['id'] ) ) {
		return wp_get_attachment_image( $media['id'], $size, false, array( 'loading' => 'lazy' ) );
	}

	if ( ! empty( $media['url'] ) ) {
		return '<img src="' . esc_url( $media['url'] ) . '" alt="" loading="lazy">';
	}

	return '';
}
