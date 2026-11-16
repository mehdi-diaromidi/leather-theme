<?php
/**
 * صفحه‌ی «تماس با ما» (مستقل از المنتور).
 * - قالب صفحه: «تماس با ما (بگلت)»
 * - ویرایش محتوا: متاباکس داخل صفحه‌ی ویرایش
 * - فرم پیام: ارسال امن با ایمیل (honeypot، زمان‌سنج امضاشده، محدودیت تعداد)
 * - ساخت خودکار یک پیش‌نویس (فقط یک بار)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BAGLLET_CONTACT_TPL', 'bagllet-contact.php' );
define( 'BAGLLET_CONTACT_META', '_bagllet_contact' );

function bagllet_contact_defaults() {
	return array(
		'intro'    => array(
			'eyebrow' => 'تماس با ما',
			'title'   => 'تماس با بگلت',
			'lead'    => 'کارشناسان بگلت آماده‌ی پاسخ‌گویی به سؤالات شما درباره‌ی محصولات، سفارش و ارسال هستند. از یکی از راه‌های ارتباطی زیر با ما در تماس باشید.',
		),
		'wa'       => array(
			'status'   => 'پاسخ‌گویی در ساعات کاری',
			'title'    => 'ارتباط از طریق واتساپ',
			'text'     => 'سریع‌ترین راه ارتباط با کارشناسان ما، ارسال پیام در واتساپ است.',
			'btn_text' => 'ارسال پیام در واتساپ',
			'url'      => '',
		),
		'channels' => array(
			'items' => array(
				array( 'icon' => 'telegram', 'title' => 'تلگرام', 'text' => '@bagllet', 'url' => '' ),
				array( 'icon' => 'instagram', 'title' => 'اینستاگرام', 'text' => '@bagllet', 'url' => '' ),
				array( 'icon' => 'phone', 'title' => 'تماس تلفنی', 'text' => '۰۲۱ ۰۰۰ ۰۰۰۰', 'url' => '' ),
				array( 'icon' => 'mail', 'title' => 'ایمیل', 'text' => 'info@bagllet.ir', 'url' => '' ),
			),
		),
		'form'     => array(
			'enabled'  => '1',
			'to_email' => '',
			'eyebrow'  => 'فرم ارتباط با ما',
			'title'    => 'ارسال پیام',
			'lead'     => 'لطفاً فرم زیر را تکمیل کنید. پیام شما در اسرع وقت بررسی و پاسخ داده می‌شود.',
			'hint_1'   => 'پاسخ از طریق شماره‌ی تماس ثبت‌شده اطلاع‌رسانی می‌شود.',
			'hint_2'   => 'اطلاعات شما نزد ما محفوظ است و فقط برای پاسخ‌گویی استفاده می‌شود.',
			'topics'   => "سفارش و خرید\nارسال و پیگیری سفارش\nتعویض و بازگشت کالا\nاصالت و ضمانت کالا\nسایر موارد",
			'consent'  => 'با ارسال این فرم، با تماس کارشناسان ما برای پاسخ‌گویی موافقت می‌کنید.',
			'success'  => 'پیام شما با موفقیت ارسال شد. در اسرع وقت با شما تماس گرفته می‌شود.',
		),
		'faq'      => array(
			'eyebrow' => 'پرسش‌های متداول',
			'title'   => 'پاسخ پرسش‌های متداول مشتریان',
			'note'    => '',
			'items'   => array(
				array( 'q' => 'زمان ارسال سفارش چقدر است؟', 'a' => 'زمان تقریبی ارسال سفارش به شهرهای مختلف در این بخش توضیح داده می‌شود.' ),
				array( 'q' => 'آیا چرم محصولات طبیعی است؟', 'a' => 'درباره‌ی نوع چرم و روش تشخیص چرم طبیعی در این بخش توضیح داده می‌شود.' ),
				array( 'q' => 'شرایط تعویض و بازگشت کالا چگونه است؟', 'a' => 'شرایط تعویض یا بازگشت کالا در این بخش توضیح داده می‌شود.' ),
				array( 'q' => 'چگونه سفارش خود را پیگیری کنم؟', 'a' => 'روش پیگیری سفارش در این بخش توضیح داده می‌شود.' ),
				array( 'q' => '', 'a' => '' ),
				array( 'q' => '', 'a' => '' ),
			),
		),
	);
}

function bagllet_contact_get( $post_id ) {
	$saved = get_post_meta( $post_id, BAGLLET_CONTACT_META, true );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	$out = array();
	foreach ( bagllet_contact_defaults() as $sec => $fields ) {
		$out[ $sec ] = isset( $saved[ $sec ] ) && is_array( $saved[ $sec ] ) ? array_merge( $fields, $saved[ $sec ] ) : $fields;
	}
	return $out;
}

/* ---------------------------------------------------------------------
 * قالب
 * ------------------------------------------------------------------ */
add_filter(
	'theme_page_templates',
	function ( $templates ) {
		$templates[ BAGLLET_CONTACT_TPL ] = 'تماس با ما (بگلت)';
		return $templates;
	}
);

function bagllet_contact_is_template_page() {
	return is_page() && BAGLLET_CONTACT_TPL === get_post_meta( get_queried_object_id(), '_wp_page_template', true );
}

add_filter(
	'template_include',
	function ( $template ) {
		if ( bagllet_contact_is_template_page() ) {
			$file = BAGLLET_WIDGETS_PATH . 'templates/contact.php';
			if ( file_exists( $file ) ) {
				return $file;
			}
		}
		return $template;
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! bagllet_contact_is_template_page() ) {
			return;
		}
		bagllet_widgets_register_assets();
		wp_enqueue_style( 'bagllet-widgets' );
		wp_enqueue_style( 'bagllet-about', BAGLLET_WIDGETS_URL . 'assets/css/about-page.css', array( 'bagllet-widgets' ), BAGLLET_WIDGETS_VERSION );
		wp_enqueue_style( 'bagllet-contact', BAGLLET_WIDGETS_URL . 'assets/css/contact-page.css', array( 'bagllet-about' ), BAGLLET_WIDGETS_VERSION );
		wp_enqueue_script( 'bagllet-contact-faq', BAGLLET_WIDGETS_URL . 'assets/js/contact-faq.js', array(), BAGLLET_WIDGETS_VERSION, true );
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( bagllet_contact_is_template_page() ) {
			$classes[] = 'bgl-about-page';
			$classes[] = 'bgl-contact-page';
		}
		return $classes;
	}
);

/* ---------------------------------------------------------------------
 * متاباکس
 * ------------------------------------------------------------------ */
add_action(
	'add_meta_boxes_page',
	function ( $post ) {
		if ( BAGLLET_CONTACT_TPL !== get_post_meta( $post->ID, '_wp_page_template', true ) ) {
			return;
		}
		add_meta_box( 'bagllet_contact_box', 'محتوای صفحه‌ی «تماس با ما» (بگلت)', 'bagllet_contact_box_render', 'page', 'normal', 'high' );
	}
);

function bagllet_contact_box_render( $post ) {
	$c = bagllet_contact_get( $post->ID );
	wp_nonce_field( 'bagllet_contact_save', 'bagllet_contact_nonce' );

	$ch_icons = array(
		'whatsapp'  => 'واتساپ',
		'telegram'  => 'تلگرام',
		'instagram' => 'اینستاگرام',
		'phone'     => 'تلفن',
		'mail'      => 'ایمیل',
	);

	echo '<style>
	#bagllet_contact_box .inside{margin:0;padding:0}
	.bga details{border-top:1px solid #dcdcde;padding:0}
	.bga summary{cursor:pointer;padding:14px 16px;font-size:14px;font-weight:600;background:#f6f7f7}
	.bga .bga-body{padding:8px 16px 16px;display:grid;grid-template-columns:1fr 1fr;gap:0 16px}
	.bga .bga-f{margin:10px 0}
	.bga .bga-f label span,.bga .bga-lb{display:block;margin-bottom:4px;font-weight:600;font-size:12px}
	.bga .bga-f input[type=text],.bga .bga-f textarea,.bga .bga-f select{width:100%}
	.bga .bga-f em{display:block;margin-top:4px;color:#646970;font-size:12px}
	.bga .bga-wide{grid-column:1/-1}
	.bga .bga-card{grid-column:1/-1;border:1px solid #dcdcde;border-radius:6px;padding:4px 14px 8px;margin:8px 0;background:#fff;display:grid;grid-template-columns:1fr 1fr;gap:0 16px}
	.bga .bga-card h4{grid-column:1/-1;margin:10px 0 0;font-size:13px}
	.bga .bga-note{padding:10px 16px;background:#fff8e5;border-top:1px solid #dcdcde;font-size:12px}
	</style><div class="bga" dir="rtl">';
	echo '<div class="bga-note">' . esc_html__( 'هر فیلد را که خالی بگذاری، همان مورد در صفحه نمایش داده نمی‌شود. بعد از تغییر، «به‌روزرسانی» را بزن.', 'bagllet-widgets' ) . '</div>';

	/* ۱) ابتدای صفحه */
	$s = $c['intro'];
	echo '<details open><summary>۱) ابتدای صفحه</summary><div class="bga-body">';
	bagllet_about_in( 'bagllet_contact[intro][eyebrow]', $s['eyebrow'], 'برچسب کوچک' );
	bagllet_about_in( 'bagllet_contact[intro][title]', $s['title'], 'تیتر اصلی' );
	echo '<div class="bga-wide">';
	bagllet_about_in( 'bagllet_contact[intro][lead]', $s['lead'], 'توضیح', 'textarea' );
	echo '</div></div></details>';

	/* ۲) واتساپ */
	$s = $c['wa'];
	echo '<details><summary>۲) کارت واتساپ</summary><div class="bga-body">';
	bagllet_about_in( 'bagllet_contact[wa][status]', $s['status'], 'متن وضعیت (مثلاً ساعت پاسخ‌گویی)' );
	bagllet_about_in( 'bagllet_contact[wa][title]', $s['title'], 'عنوان' );
	bagllet_about_in( 'bagllet_contact[wa][text]', $s['text'], 'توضیح کوتاه' );
	bagllet_about_in( 'bagllet_contact[wa][btn_text]', $s['btn_text'], 'متن دکمه' );
	bagllet_about_in( 'bagllet_contact[wa][url]', $s['url'], 'لینک واتساپ (مثلاً https://wa.me/989121234567)', 'url', 'اگر خالی بگذاری دکمه‌ی واتساپ نمایش داده نمی‌شود (عنوان را خالی کنی، کل کارت مخفی می‌شود).' );
	echo '</div></details>';

	/* ۳) کانال‌ها */
	$s = $c['channels'];
	echo '<details><summary>۳) راه‌های ارتباطی</summary><div class="bga-body">';
	foreach ( $s['items'] as $i => $it ) {
		$p = 'bagllet_contact[channels][items][' . $i . ']';
		echo '<div class="bga-card"><h4>کانال ' . esc_html( bagllet_widgets_fa( $i + 1 ) ) . ' (کانالی که نداری را خالی بگذار)</h4>';
		bagllet_about_icon_select( $p . '[icon]', $it['icon'], $ch_icons );
		bagllet_about_in( $p . '[title]', $it['title'], 'عنوان' );
		bagllet_about_in( $p . '[text]', $it['text'], 'متن زیر عنوان (آی‌دی، شماره یا ایمیل)' );
		bagllet_about_in( $p . '[url]', $it['url'], 'لینک (https://t.me/… یا tel:+98… یا mailto:…)', 'url' );
		echo '</div>';
	}
	echo '</div></details>';

	/* ۴) فرم */
	$s = $c['form'];
	echo '<details><summary>۴) فرم ارسال پیام</summary><div class="bga-body">';
	echo '<p class="bga-f bga-wide"><label><span>نمایش فرم</span><select name="bagllet_contact[form][enabled]"><option value="1"' . selected( $s['enabled'], '1', false ) . '>نمایش داده شود</option><option value="0"' . selected( $s['enabled'], '0', false ) . '>مخفی باشد</option></select></label></p>';
	bagllet_about_in( 'bagllet_contact[form][to_email]', $s['to_email'], 'ایمیل دریافت پیام‌ها', 'text', 'اگر خالی باشد، پیام‌ها به ایمیل مدیر سایت (' . get_option( 'admin_email' ) . ') می‌رسد.' );
	bagllet_about_in( 'bagllet_contact[form][eyebrow]', $s['eyebrow'], 'برچسب کوچک' );
	bagllet_about_in( 'bagllet_contact[form][title]', $s['title'], 'تیتر' );
	bagllet_about_in( 'bagllet_contact[form][lead]', $s['lead'], 'توضیح', 'textarea' );
	bagllet_about_in( 'bagllet_contact[form][hint_1]', $s['hint_1'], 'یادآوری اول' );
	bagllet_about_in( 'bagllet_contact[form][hint_2]', $s['hint_2'], 'یادآوری دوم' );
	echo '<div class="bga-wide">';
	bagllet_about_in( 'bagllet_contact[form][topics]', $s['topics'], 'موضوع‌های قابل انتخاب (هر خط یک مورد)', 'textarea' );
	bagllet_about_in( 'bagllet_contact[form][consent]', $s['consent'], 'متن کنار دکمه‌ی ارسال' );
	bagllet_about_in( 'bagllet_contact[form][success]', $s['success'], 'پیام موفقیت پس از ارسال' );
	echo '</div></div></details>';

	/* ۵) پرسش‌ها */
	$s = $c['faq'];
	echo '<details><summary>۵) پرسش‌های متداول</summary><div class="bga-body">';
	bagllet_about_in( 'bagllet_contact[faq][eyebrow]', $s['eyebrow'], 'برچسب کوچک' );
	bagllet_about_in( 'bagllet_contact[faq][title]', $s['title'], 'تیتر' );
	echo '<div class="bga-wide">';
	bagllet_about_in( 'bagllet_contact[faq][note]', $s['note'], 'یادداشت زیر فهرست (اختیاری)' );
	echo '</div>';
	foreach ( $s['items'] as $i => $it ) {
		$p = 'bagllet_contact[faq][items][' . $i . ']';
		echo '<div class="bga-card"><h4>پرسش ' . esc_html( bagllet_widgets_fa( $i + 1 ) ) . '</h4>';
		echo '<div class="bga-wide" style="grid-column:1/-1">';
		bagllet_about_in( $p . '[q]', $it['q'], 'پرسش' );
		bagllet_about_in( $p . '[a]', $it['a'], 'پاسخ', 'textarea' );
		echo '</div></div>';
	}
	echo '</div></details></div>';
}

/* ---------------------------------------------------------------------
 * ذخیره
 * ------------------------------------------------------------------ */
function bagllet_contact_clean( $key, $val ) {
	if ( ! is_string( $val ) && ! is_numeric( $val ) ) {
		return '';
	}
	$val = (string) $val;

	if ( 'url' === $key ) {
		$val = trim( $val );
		// فقط https/http/tel/mailto
		return esc_url_raw( $val, array( 'http', 'https', 'tel', 'mailto' ) );
	}
	if ( 'to_email' === $key ) {
		$e = sanitize_email( trim( $val ) );
		return is_email( $e ) ? $e : '';
	}
	if ( 'icon' === $key ) {
		return sanitize_key( $val );
	}
	if ( 'enabled' === $key ) {
		return '0' === $val ? '0' : '1';
	}
	if ( in_array( $key, array( 'lead', 'a', 'topics' ), true ) ) {
		return sanitize_textarea_field( $val );
	}
	return sanitize_text_field( $val );
}

function bagllet_contact_sanitize( $raw ) {
	$raw = is_array( $raw ) ? $raw : array();
	$out = array();
	foreach ( bagllet_contact_defaults() as $sec => $fields ) {
		foreach ( $fields as $k => $dv ) {
			if ( 'items' === $k ) {
				$items  = array();
				$ritems = isset( $raw[ $sec ]['items'] ) && is_array( $raw[ $sec ]['items'] ) ? $raw[ $sec ]['items'] : array();
				foreach ( $dv as $i => $idefault ) {
					$ri   = isset( $ritems[ $i ] ) && is_array( $ritems[ $i ] ) ? $ritems[ $i ] : array();
					$item = array();
					foreach ( $idefault as $ik => $idv ) {
						$item[ $ik ] = bagllet_contact_clean( $ik, isset( $ri[ $ik ] ) ? $ri[ $ik ] : '' );
					}
					$items[ $i ] = $item;
				}
				$out[ $sec ]['items'] = $items;
			} else {
				$out[ $sec ][ $k ] = bagllet_contact_clean( $k, isset( $raw[ $sec ][ $k ] ) ? $raw[ $sec ][ $k ] : '' );
			}
		}
	}
	return $out;
}

add_action(
	'save_post_page',
	function ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['bagllet_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bagllet_contact_nonce'] ) ), 'bagllet_contact_save' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$raw = isset( $_POST['bagllet_contact'] ) ? wp_unslash( $_POST['bagllet_contact'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		update_post_meta( $post_id, BAGLLET_CONTACT_META, bagllet_contact_sanitize( $raw ) );
	}
);

/* ---------------------------------------------------------------------
 * فرم پیام: ارسال امن
 * ------------------------------------------------------------------ */
function bagllet_contact_ascii_digits( $s ) {
	return strtr(
		(string) $s,
		array(
			'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
			'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
		)
	);
}

/** طول متن بر حسب نویسه (با یا بدون mbstring). */
function bagllet_contact_len( $s ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( $s, 'UTF-8' ) : (int) preg_match_all( '/./su', $s );
}

/** توکن زمان‌دار امضاشده (بدون نیاز به nonce تا با کش صفحه هم کار کند). */
function bagllet_contact_token() {
	$t = time();
	return $t . '.' . substr( wp_hash( 'bagllet_contact|' . $t ), 0, 16 );
}

function bagllet_contact_token_age( $token ) {
	$parts = explode( '.', (string) $token );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( ! hash_equals( substr( wp_hash( 'bagllet_contact|' . $parts[0] ), 0, 16 ), $parts[1] ) ) {
		return false;
	}
	return time() - (int) $parts[0];
}

function bagllet_contact_back( $post_id, $status ) {
	$url = $post_id ? get_permalink( $post_id ) : home_url( '/' );
	$url = add_query_arg( 'bgl', $status, $url ) . '#bgl-form';
	wp_safe_redirect( $url );
	exit;
}

function bagllet_contact_send() {
	$post_id = isset( $_POST['pid'] ) ? absint( $_POST['pid'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
	$post    = $post_id ? get_post( $post_id ) : null;

	if ( ! $post || 'page' !== $post->post_type || 'publish' !== $post->post_status || BAGLLET_CONTACT_TPL !== get_post_meta( $post_id, '_wp_page_template', true ) ) {
		// صفحه‌ی پیش‌نویس هم برای مدیر قابل تست است.
		if ( ! $post || ! current_user_can( 'edit_post', $post_id ) || BAGLLET_CONTACT_TPL !== get_post_meta( $post_id, '_wp_page_template', true ) ) {
			wp_safe_redirect( home_url( '/' ) );
			exit;
		}
	}

	// ۱) تله‌ی ربات: فیلد پنهان باید خالی بماند.
	if ( ! empty( $_POST['bgl_website'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		bagllet_contact_back( $post_id, 'ok' ); // ربات را بی‌صدا رد می‌کنیم.
	}

	// ۲) توکن زمان‌دار: حداقل ۳ ثانیه و حداکثر یک روز.
	$age = bagllet_contact_token_age( isset( $_POST['bgl_t'] ) ? sanitize_text_field( wp_unslash( $_POST['bgl_t'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( false === $age || $age < 3 || $age > DAY_IN_SECONDS ) {
		bagllet_contact_back( $post_id, 'expired' );
	}

	// ۳) محدودیت تعداد: ۵ پیام در ساعت برای هر IP.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'bgl_c_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 5 ) {
		bagllet_contact_back( $post_id, 'limit' );
	}

	// ۴) اعتبارسنجی
	$name    = isset( $_POST['bgl_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bgl_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$phone   = isset( $_POST['bgl_phone'] ) ? preg_replace( '/\D+/', '', bagllet_contact_ascii_digits( sanitize_text_field( wp_unslash( $_POST['bgl_phone'] ) ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$topic   = isset( $_POST['bgl_topic'] ) ? sanitize_text_field( wp_unslash( $_POST['bgl_topic'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$message = isset( $_POST['bgl_msg'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bgl_msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	if ( bagllet_contact_len( $name ) < 2 || bagllet_contact_len( $name ) > 80 ) {
		bagllet_contact_back( $post_id, 'name' );
	}
	if ( ! preg_match( '/^(?:0098|98|0)?9\d{9}$/', $phone ) && ! preg_match( '/^0[1-8]\d{9}$/', $phone ) ) {
		bagllet_contact_back( $post_id, 'phone' );
	}
	if ( bagllet_contact_len( $message ) < 5 || bagllet_contact_len( $message ) > 3000 ) {
		bagllet_contact_back( $post_id, 'msg' );
	}

	$c      = bagllet_contact_get( $post_id );
	$topics = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $c['form']['topics'] ) ) );
	if ( $topics && ! in_array( $topic, $topics, true ) ) {
		$topic = '';
	}

	$to = ! empty( $c['form']['to_email'] ) ? $c['form']['to_email'] : get_option( 'admin_email' );
	if ( ! is_email( $to ) ) {
		bagllet_contact_back( $post_id, 'fail' );
	}

	$subject = 'پیام جدید از فرم تماس با ما' . ( $topic ? ' - ' . $topic : '' );
	$subject = str_replace( array( "\r", "\n" ), ' ', $subject );
	$body    = "نام: {$name}\nشماره‌ی تماس: {$phone}\nموضوع: " . ( $topic ? $topic : '-' ) . "\n\nپیام:\n{$message}\n\n---\nارسال‌شده از صفحه‌ی تماس با ما";

	set_transient( $key, $n + 1, HOUR_IN_SECONDS );

	$sent = wp_mail( $to, $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	bagllet_contact_back( $post_id, $sent ? 'ok' : 'fail' );
}
add_action( 'admin_post_nopriv_bagllet_contact_send', 'bagllet_contact_send' );
add_action( 'admin_post_bagllet_contact_send', 'bagllet_contact_send' );

/* ---------------------------------------------------------------------
 * ساخت خودکار پیش‌نویس (فقط یک بار)
 * ------------------------------------------------------------------ */
add_action(
	'admin_init',
	function () {
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ! current_user_can( 'publish_pages' ) || get_option( 'bagllet_contact_page_id' ) ) {
			return;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'  => 'تماس با ما',
				'post_name'   => 'contact-us',
				'post_status' => 'draft',
				'post_type'   => 'page',
			),
			true
		);
		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return;
		}

		update_post_meta( $post_id, '_wp_page_template', BAGLLET_CONTACT_TPL );
		update_post_meta( $post_id, BAGLLET_CONTACT_META, bagllet_contact_defaults() );
		update_option( 'bagllet_contact_page_id', (int) $post_id, false );
		update_option( 'bagllet_contact_notice', 1, false );
	}
);

add_action(
	'admin_notices',
	function () {
		$id = (int) get_option( 'bagllet_contact_page_id' );
		if ( ! $id || ! get_option( 'bagllet_contact_notice' ) || ! current_user_can( 'edit_post', $id ) ) {
			return;
		}
		delete_option( 'bagllet_contact_notice' );
		echo '<div class="notice notice-success is-dismissible"><p><strong>بگلت:</strong> صفحه‌ی «تماس با ما» به‌صورت پیش‌نویس ساخته شد. <a class="button button-primary" href="' . esc_url( get_edit_post_link( $id ) ) . '">ویرایش محتوای صفحه</a></p></div>';
	}
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$post = get_post();
		if ( ! $post || 'page' !== $post->post_type || BAGLLET_CONTACT_TPL !== get_post_meta( $post->ID, '_wp_page_template', true ) ) {
			return;
		}
		wp_enqueue_script( 'bagllet-about-admin', BAGLLET_WIDGETS_URL . 'assets/js/about-admin.js', array( 'jquery' ), BAGLLET_WIDGETS_VERSION, true );
	}
);
