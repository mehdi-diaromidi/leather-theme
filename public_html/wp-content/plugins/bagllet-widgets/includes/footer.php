<?php
/**
 * Custom site footer replacing the Woodmart footer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Footer content. Filter `bglw_ft_config` to change links or contact data.
 *
 * @return array
 */
function bglw_ft_config() {
	$config = array(
		'tagline'  => 'کیف و اکسسوری چرم، با تمرکز بر جزئیات و کیفیت ماندگار.',
		'links'    => array(
			array( 'فروشگاه', bglw_ft_page_url( 'shop' ) ),
			array( 'درباره ما', bglw_ft_page_url( 'درباره-ما' ) ),
			array( 'تماس با ما', bglw_ft_page_url( 'تماس-با-ما' ) ),
			array( 'قوانین و مقررات', bglw_ft_page_url( 'قوانین-و-مقررات' ) ),
			array( 'همکاری در فروش', bglw_ft_page_url( 'همکاری-در-فروش' ) ),
		),
		'women'    => array(
			array( 'کیف زنانه', 'womens-leather-bag' ),
			array( 'کیف پول زنانه', 'womens-leather-wallet' ),
			array( 'کمربند زنانه', 'womens-leather-belt' ),
			array( 'ساک زنانه', 'ساک-زنانه' ),
			array( 'اکسسوری زنانه', 'womens-leather-accessories' ),
			array( 'ست زنانه', 'ست-زنانه' ),
		),
		'men'      => array(
			array( 'کیف مردانه', 'mens-leather-bag' ),
			array( 'کیف پول مردانه', 'mens-leather-wallet' ),
			array( 'کمربند مردانه', 'leather-belt' ),
			array( 'ساک مردانه', 'ساک-مردانه' ),
			array( 'کفش راحتی', 'کفش-راحتی' ),
			array( 'ست مردانه', 'ست-مردانه' ),
		),
		'address'  => 'تهران، زعفرانیه',
		'phone'    => '02198765431',
		'hours'    => '۱۰ تا ۱۴ و ۱۶ تا ۲۲',
		'email'    => 'info@bagllet.ir',
		'social'   => array(
			'instagram' => array( 'اینستاگرام', '#' ),
			'telegram'  => array( 'تلگرام', '#' ),
			'whatsapp'  => array( 'واتساپ', '#' ),
			'youtube'   => array( 'یوتیوب', '#' ),
		),
		'badges'   => array(
			array( 'نماد اعتماد الکترونیکی', '/wp-content/uploads/2023/04/enamad-1-2.png' ),
			array( 'اتحادیه کسب‌وکارهای مجازی', '/wp-content/uploads/2023/04/etehadiekeshvaripng.parspng-1.png' ),
			array( 'ساماندهی', '/wp-content/uploads/2023/04/samandehipng.parspng-2.png' ),
		),
	);

	return apply_filters( 'bagllet_footer_config', $config );
}

/**
 * URL of a page by slug, falling back to `#` while the page does not exist.
 *
 * @param string $slug Page slug.
 * @return string
 */
function bglw_ft_page_url( $slug ) {
	if ( 'shop' === $slug && function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( 'shop' );
	}

	$page = get_page_by_path( $slug );

	return $page ? get_permalink( $page ) : '#';
}

/**
 * Category URL by slug, `#` if the term is missing.
 *
 * @param string $slug Product category slug.
 * @return string
 */
function bglw_ft_cat_url( $slug ) {
	$link = get_term_link( $slug, 'product_cat' );

	return is_wp_error( $link ) ? '#' : $link;
}

/**
 * Inline SVG icon from assets/icons with its stroke switched to currentColor.
 *
 * @param string $name Icon file name without extension.
 * @param int    $size Rendered size in px.
 * @return string
 */
function bglw_ft_icon( $name, $size = 24 ) {
	static $cache = array();

	if ( ! isset( $cache[ $name ] ) ) {
		$file           = BAGLLET_WIDGETS_PATH . 'assets/footer/icons/' . $name . '.svg';
		$cache[ $name ] = file_exists( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	$svg = preg_replace( '/stroke="#[0-9A-Fa-f]{3,6}"/', 'stroke="currentColor"', $cache[ $name ] );
	$svg = preg_replace( '/width="\d+" height="\d+"/', 'width="' . (int) $size . '" height="' . (int) $size . '" aria-hidden="true" focusable="false"', $svg, 1 );

	return $svg;
}

/**
 * Official horizontal logo (cropped SVG-60), inlined so it needs no extra request.
 *
 * @return string
 */
function bglw_ft_logo() {
	$file = BAGLLET_WIDGETS_PATH . 'assets/footer/logo/bagllet-logo-horizontal.svg';
	$svg  = file_exists( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	return str_replace( 'role="img" aria-label="Bagllet Leather"', 'aria-hidden="true" focusable="false"', $svg );
}

/**
 * Current Jalali year in Persian digits.
 *
 * @return string
 */
function bglw_ft_jalali_year() {
	if ( class_exists( 'IntlDateFormatter' ) ) {
		$formatter = new IntlDateFormatter( 'fa_IR@calendar=persian', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Asia/Tehran', IntlDateFormatter::TRADITIONAL, 'yyyy' );
		$year      = $formatter->format( time() );
		if ( $year ) {
			return $year;
		}
	}

	$now  = new DateTime( 'now', new DateTimeZone( 'Asia/Tehran' ) );
	$year = (int) $now->format( 'Y' ) - ( (int) $now->format( 'nd' ) >= 321 ? 621 : 622 );

	return bglw_ft_fa_digits( (string) $year );
}

/**
 * Convert Latin digits to Persian digits.
 *
 * @param string $text Text.
 * @return string
 */
function bglw_ft_fa_digits( $text ) {
	return strtr( $text, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
}

/**
 * Turn off the Woodmart footer, prefooter and floating scroll-top button on the front end.
 */
add_filter(
	'woodmart_option',
	function ( $value, $slug ) {
		if ( is_admin() ) {
			return $value;
		}

		switch ( $slug ) {
			case 'disable_footer':
			case 'disable_copyrights':
			case 'scroll_top_btn':
				return false;
			case 'prefooter_area':
			case 'prefooter_html_block':
				return '';
		}

		return $value;
	},
	10,
	2
);

add_action( 'wp_enqueue_scripts', 'bglw_ft_enqueue_assets', 50 );

/**
 * Footer assets on every front-end page.
 */
function bglw_ft_enqueue_assets() {
	wp_enqueue_style( 'bgl-footer', BAGLLET_WIDGETS_URL . 'assets/footer/footer.css', array(), BAGLLET_WIDGETS_VERSION );
	wp_enqueue_script( 'bgl-footer', BAGLLET_WIDGETS_URL . 'assets/footer/footer.js', array(), BAGLLET_WIDGETS_VERSION, true );
}

add_action( 'woodmart_before_wp_footer', 'bglw_ft_render' );

/**
 * Print the footer.
 */
function bglw_ft_render() {
	if ( function_exists( 'woodmart_needs_footer' ) && ! woodmart_needs_footer() ) {
		return;
	}

	$c         = bglw_ft_config();
	$phone_fa  = bglw_ft_fa_digits( $c['phone'] );
	$phone_tel = '+98' . ltrim( $c['phone'], '0' );
	$year      = bglw_ft_jalali_year();

	$list = function ( $items, $is_cat ) {
		echo '<ul class="bgf__list">';
		foreach ( $items as $item ) {
			$url = $is_cat ? bglw_ft_cat_url( $item[1] ) : $item[1];
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $item[0] ) . '</a></li>';
		}
		echo '</ul>';
	};

	$chevron = bglw_ft_icon( 'chevron-down', 20 );
	?>
	<footer class="bgf" role="contentinfo">
		<div class="bgf__container bgf__main">
			<div class="bgf__brand">
				<a class="bgf__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="بگلت؛ صفحه اصلی">
					<?php echo bglw_ft_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<p class="bgf__tagline"><?php echo esc_html( $c['tagline'] ); ?></p>
			</div>

			<section class="bgf__acc bgf__acc--links" data-bgf-acc>
				<h2 class="bgf__acc-head">
					<button type="button" class="bgf__acc-btn" aria-expanded="false" aria-controls="bgf-panel-links" id="bgf-head-links">
						<span>لینک‌های مهم</span><?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</h2>
				<div class="bgf__acc-panel" id="bgf-panel-links" role="region" aria-labelledby="bgf-head-links">
					<div class="bgf__acc-inner"><?php $list( $c['links'], false ); ?></div>
				</div>
			</section>

			<section class="bgf__acc bgf__acc--cats is-open" data-bgf-acc>
				<h2 class="bgf__acc-head">
					<button type="button" class="bgf__acc-btn" aria-expanded="true" aria-controls="bgf-panel-cats" id="bgf-head-cats">
						<span>دسته‌بندی‌ها</span><?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</h2>
				<div class="bgf__acc-panel" id="bgf-panel-cats" role="region" aria-labelledby="bgf-head-cats">
					<div class="bgf__acc-inner bgf__cats">
						<div class="bgf__col bgf__col--women">
							<h3 class="bgf__col-title">زنانه</h3>
							<?php $list( $c['women'], true ); ?>
						</div>
						<div class="bgf__col bgf__col--men">
							<h3 class="bgf__col-title">مردانه</h3>
							<?php $list( $c['men'], true ); ?>
						</div>
					</div>
				</div>
			</section>

			<section class="bgf__acc bgf__acc--contact" data-bgf-acc>
				<h2 class="bgf__acc-head">
					<button type="button" class="bgf__acc-btn" aria-expanded="false" aria-controls="bgf-panel-contact" id="bgf-head-contact">
						<span>تماس با ما</span><?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</h2>
				<div class="bgf__acc-panel" id="bgf-panel-contact" role="region" aria-labelledby="bgf-head-contact">
					<div class="bgf__acc-inner">
						<ul class="bgf__list">
							<li><a href="<?php echo esc_url( bglw_ft_page_url( 'تماس-با-ما' ) ); ?>">صفحه تماس با ما</a></li>
							<li><a href="tel:<?php echo esc_attr( $phone_tel ); ?>">تماس تلفنی</a></li>
							<li><a href="mailto:<?php echo esc_attr( $c['email'] ); ?>">ارسال ایمیل</a></li>
							<li><a href="<?php echo esc_url( $c['social']['whatsapp'][1] ); ?>">پشتیبانی در واتساپ</a></li>
						</ul>
					</div>
				</div>
			</section>

			<ul class="bgf__card">
				<li class="bgf__info">
					<?php echo bglw_ft_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="bgf__info-text"><span class="bgf__info-label">آدرس</span><span class="bgf__info-value"><?php echo esc_html( $c['address'] ); ?></span></span>
				</li>
				<li class="bgf__info">
					<?php echo bglw_ft_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="bgf__info-text"><span class="bgf__info-label">شماره تماس</span><a class="bgf__info-value" href="tel:<?php echo esc_attr( $phone_tel ); ?>"><?php echo esc_html( $phone_fa ); ?></a></span>
				</li>
				<li class="bgf__info">
					<?php echo bglw_ft_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="bgf__info-text"><span class="bgf__info-label">ساعت کاری</span><span class="bgf__info-value"><?php echo esc_html( $c['hours'] ); ?></span></span>
				</li>
				<li class="bgf__info">
					<?php echo bglw_ft_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="bgf__info-text"><span class="bgf__info-label">ایمیل</span><a class="bgf__info-value" href="mailto:<?php echo esc_attr( $c['email'] ); ?>" dir="ltr"><?php echo esc_html( $c['email'] ); ?></a></span>
				</li>
			</ul>

			<ul class="bgf__social" aria-label="شبکه‌های اجتماعی">
				<?php foreach ( $c['social'] as $key => $item ) : ?>
					<li><a href="<?php echo esc_url( $item[1] ); ?>" aria-label="<?php echo esc_attr( $item[0] ); ?>" target="_blank" rel="noopener"><?php echo bglw_ft_icon( 'brand-' . $key, 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
				<?php endforeach; ?>
			</ul>

			<ul class="bgf__badges" aria-label="نمادهای اعتماد">
				<?php foreach ( $c['badges'] as $badge ) : ?>
					<li><img src="<?php echo esc_url( home_url( $badge[1] ) ); ?>" alt="<?php echo esc_attr( $badge[0] ); ?>" width="48" height="48" loading="lazy" decoding="async"></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="bgf__bottom">
			<div class="bgf__container bgf__bottom-inner">
				<p class="bgf__copy"><span>© <?php echo esc_html( $year ); ?> bagllet.ir</span><span class="bgf__copy-sep">، </span><span class="bgf__copy-rest">تمامی حقوق محفوظ است.</span></p>
				<a class="bgf__top" href="#" aria-label="بازگشت به بالای صفحه"><?php echo bglw_ft_icon( 'arrow-up', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</div>
		</div>
	</footer>
	<?php
}
