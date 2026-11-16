<?php
/**
 * صفحه‌ی «درباره ما» (مستقل از المنتور).
 * - قالب صفحه: «درباره ما (بگلت)»
 * - ویرایش محتوا: متاباکس داخل خود صفحه‌ی ویرایش وردپرس
 * - ساخت خودکار یک پیش‌نویس با محتوای پیش‌فرض (فقط یک بار)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BAGLLET_ABOUT_TPL', 'bagllet-about.php' );
define( 'BAGLLET_ABOUT_META', '_bagllet_about' );

/**
 * محتوای پیش‌فرض (قابل ویرایش در صفحه).
 */
function bagllet_about_defaults() {
	return array(
		'story'    => array(
			'eyebrow'    => 'درباره ما',
			'title'      => "بگلت، فروشگاه اینترنتی
کیف و اکسسوری چرم",
			'question'   => 'کیف، کیف پول و کمربند چرم طبیعی، مردانه و زنانه.',
			'p1'         => 'بگلت یک فروشگاه اینترنتی تخصصی محصولات چرمی است. اطلاعات هر محصول، از جنس و اندازه تا رنگ‌های موجود، در صفحه‌ی همان محصول آمده تا با دید روشن خرید کنید.',
			'p2'         => 'سفارش‌ها پیش از ارسال بررسی و بسته‌بندی می‌شوند و برای راهنمایی و پیگیری سفارش، پشتیبانی از طریق واتساپ، تلگرام و تلفن در کنار شماست.',
			'sign'       => '',
			'img_main'   => 0,
			'alt_main'   => 'کیف دستی چرمی و کیف پول',
			'img_second' => 0,
			'alt_second' => 'قفل برنجی و دوخت روی کیف چرمی',
			'caption'    => 'چرم طبیعی · دوخت دقیق',
			'cta_text'   => '',
			'cta_url'    => '',
		),
		'pledges'  => array(
			'eyebrow' => 'مزایای خرید',
			'title'   => 'چرا از بگلت',
			'title_2' => 'خرید کنید؟',
			'lead'    => 'مواردی که هنگام خرید از فروشگاه برای شما فراهم است.',
			'items'   => array(
				array( 'icon' => 'shield', 'title' => 'اصالت کالا', 'text' => 'همه‌ی محصولات اصل‌اند و همراه فاکتور ارسال می‌شوند.', 'url' => '' ),
				array( 'icon' => 'leaf', 'title' => 'چرم طبیعی', 'text' => 'جنس محصولات چرم طبیعی است، نه چرم مصنوعی.', 'url' => '' ),
				array( 'icon' => 'truck', 'title' => 'ارسال به سراسر ایران', 'text' => 'بسته‌بندی مناسب و ارسال به همه‌ی شهرها.', 'url' => '' ),
				array( 'icon' => 'chat', 'title' => 'پشتیبانی', 'text' => 'پاسخگویی در واتساپ، تلگرام و تلفن.', 'url' => '' ),
			),
		),
		'details'  => array(
			'eyebrow' => 'گالری',
			'title'   => 'نگاهی به جزئیات محصولات',
			'lead'    => 'چرم، دوخت و یراق‌آلات را از نزدیک ببینید.',
			'items'   => array(
				array( 'img' => 0, 'alt' => 'سگک برنجی کمربند چرمی', 'title' => 'کمربند و سگک', 'text' => 'سگک و یراق برنجی', 'url' => '' ),
				array( 'img' => 0, 'alt' => 'دوخت و لبه‌ی کیف پول چرمی', 'title' => 'دوخت و لبه', 'text' => 'دوخت تمیز و یکدست', 'url' => '' ),
				array( 'img' => 0, 'alt' => 'قفل برنجی روی کیف چرمی مشکی', 'title' => 'قفل و چرم', 'text' => 'دانه‌ی طبیعی چرم', 'url' => '' ),
			),
		),
		'channels' => array(
			'eyebrow' => 'تماس',
			'title'   => 'راه‌های ارتباط با ما',
			'lead'    => 'برای راهنمایی خرید و پیگیری سفارش با ما در تماس باشید.',
			'note'    => '',
			'items'   => array(
				array( 'icon' => 'whatsapp', 'title' => 'واتساپ', 'text' => 'پیام در واتساپ', 'url' => '' ),
				array( 'icon' => 'telegram', 'title' => 'تلگرام', 'text' => '@bagllet', 'url' => '' ),
				array( 'icon' => 'instagram', 'title' => 'اینستاگرام', 'text' => '@bagllet', 'url' => '' ),
				array( 'icon' => 'phone', 'title' => 'تماس تلفنی', 'text' => '۰۲۱ ۰۰۰ ۰۰۰۰', 'url' => '' ),
				array( 'icon' => 'mail', 'title' => '', 'text' => '', 'url' => '' ),
			),
		),
		'doors'    => array(
			'title'    => 'خرید از',
			'title_2'  => 'مجموعه‌های بگلت',
			'lead'     => 'مجموعه‌ی مورد نظر خود را انتخاب کنید.',
			'cta_text' => 'مشاهده‌ی همه‌ی محصولات',
			'cta_url'  => '',
			'items'    => array(
				array( 'img' => 0, 'alt' => 'مجموعه مردانه', 'tag' => 'مجموعه', 'title' => 'مردانه', 'text' => 'کیف، کیف پول و کمربند', 'url' => '' ),
				array( 'img' => 0, 'alt' => 'مجموعه زنانه', 'tag' => 'مجموعه', 'title' => 'زنانه', 'text' => 'کیف دستی، کیف پول و کمربند', 'url' => '' ),
				array( 'img' => 0, 'alt' => '', 'tag' => 'مجموعه', 'title' => '', 'text' => '', 'url' => '' ),
			),
		),
	);
}

/**
 * متن‌های پیش‌فرض نسخه‌ی قبلی (فقط برای مهاجرت یک‌باره).
 */
function bagllet_about_old_defaults_v1() {
	return array(
		'story'    => array(
			'eyebrow'  => 'درباره بگلت',
			'title'    => "یک سؤال ساده،
یک برند",
			'question' => 'چرا خرید یک کیف چرمی خوب باید این‌قدر سخت باشد؟',
			'p1'       => 'بگلت از همین سؤال شروع شد. ما چرم را از نزدیک انتخاب می‌کنیم، با دوخت دقیق می‌دوزیم و هر محصول را پیش از ارسال بررسی می‌کنیم.',
			'p2'       => 'هدف ساده است: کیف، کیف پول و کمربندی که با گذر زمان بهتر می‌شود.',
			'sign'     => 'تیم بگلت',
		),
		'pledges'  => array(
			'eyebrow' => 'چرا بگلت',
			'title'   => 'قولی که پای',
			'title_2' => 'هر سفارش می‌ایستیم',
			'lead'    => 'چهار تعهد ساده، که هر خریدار باید بتواند روی آن حساب کند.',
			'items'   => array(
				array( 'title' => 'ضمانت اصالت', 'text' => 'کالای اصل، همراه با گارانتی و فاکتور.' ),
				array( 'title' => 'چرم طبیعی', 'text' => 'بدون ترکیب مصنوعی، با دانه‌ی واقعی چرم.' ),
				array( 'title' => 'ارسال سراسری', 'text' => 'بسته‌بندی امن و ارسال به همه‌ی ایران.' ),
				array( 'title' => 'پشتیبانی واقعی', 'text' => 'پاسخ سریع در واتساپ و تلگرام.' ),
			),
		),
		'details'  => array(
			'eyebrow' => 'از نزدیک',
			'title'   => 'جزئیاتی که فرق را می‌سازند',
			'lead'    => 'چرم، دوخت و یراق؛ همان چیزهایی که در دست حس می‌شود.',
			'items'   => array(
				array( 'text' => 'یراق برنجی ماندگار' ),
				array( 'text' => 'تمیز، یکدست، محکم' ),
				array( 'text' => 'دانه‌ی طبیعی چرم' ),
			),
		),
		'channels' => array(
			'eyebrow' => 'ارتباط با ما',
			'title'   => 'فروشگاه ما آنلاین است؛ از هر کجا بپرس',
			'lead'    => 'برای انتخاب چرم، رنگ و سفارش، همین حالا پیام بده.',
			'items'   => array(
				array( 'text' => 'سریع‌ترین راه' ),
			),
		),
		'doors'    => array(
			'title'    => 'کیف مناسب',
			'title_2'  => 'خودت را پیدا کن',
			'lead'     => 'از کدام مجموعه شروع کنیم؟',
			'cta_text' => 'مشاهده همه‌ی کلکسیون',
		),
	);
}

/**
 * مهاجرت یک‌باره: فیلدهایی که هنوز برابر متن قدیمی‌اند با متن تازه جایگزین می‌شوند.
 * ویرایش‌های دستی (متن متفاوت) و تصویرها دست‌نخورده می‌مانند.
 */
function bagllet_about_migrate_copy( $saved, $old, $new ) {
	if ( ! is_array( $saved ) ) {
		return $saved;
	}
	foreach ( $old as $k => $ov ) {
		if ( ! array_key_exists( $k, $saved ) || ! array_key_exists( $k, $new ) ) {
			continue;
		}
		if ( is_array( $ov ) ) {
			$saved[ $k ] = bagllet_about_migrate_copy( $saved[ $k ], $ov, $new[ $k ] );
		} elseif ( is_string( $saved[ $k ] ) && trim( str_replace( "
", "
", $saved[ $k ] ) ) === $ov ) {
			$saved[ $k ] = $new[ $k ];
		}
	}
	return $saved;
}

add_action(
	'init',
	function () {
		if ( get_option( 'bagllet_about_copy_v2' ) ) {
			return;
		}
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => 20,
				'fields'         => 'ids',
				'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => BAGLLET_ABOUT_TPL, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		foreach ( $pages as $pid ) {
			$saved = get_post_meta( $pid, BAGLLET_ABOUT_META, true );
			if ( is_array( $saved ) && $saved ) {
				update_post_meta( $pid, BAGLLET_ABOUT_META, bagllet_about_migrate_copy( $saved, bagllet_about_old_defaults_v1(), bagllet_about_defaults() ) );
			}
		}
		update_option( 'bagllet_about_copy_v2', 1, false );
	}
);

/**
 * محتوای ذخیره‌شده با پیش‌فرض‌ها ادغام می‌شود.
 */
function bagllet_about_get( $post_id ) {
	$saved = get_post_meta( $post_id, BAGLLET_ABOUT_META, true );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	$out = array();
	foreach ( bagllet_about_defaults() as $sec => $fields ) {
		$out[ $sec ] = isset( $saved[ $sec ] ) && is_array( $saved[ $sec ] ) ? array_merge( $fields, $saved[ $sec ] ) : $fields;
	}
	return $out;
}

/**
 * تصویر انتخاب‌شده، یا تصویر پیش‌فرض افزونه.
 */
function bagllet_about_img( $id, $fallback_file, $alt, $eager = false ) {
	$attrs = array(
		'alt'      => $alt,
		'decoding' => 'async',
		'loading'  => $eager ? 'eager' : 'lazy',
	);
	if ( $eager ) {
		$attrs['fetchpriority'] = 'high';
	}
	if ( $id ) {
		$html = wp_get_attachment_image( (int) $id, 'large', false, $attrs );
		if ( $html ) {
			return $html;
		}
	}
	return '<img src="' . esc_url( BAGLLET_WIDGETS_URL . 'assets/images/' . $fallback_file ) . '" alt="' . esc_attr( $alt ) . '" width="1200" height="1000" decoding="async" loading="' . esc_attr( $attrs['loading'] ) . '">';
}

/* ---------------------------------------------------------------------
 * قالب صفحه
 * ------------------------------------------------------------------ */
add_filter(
	'theme_page_templates',
	function ( $templates ) {
		$templates[ BAGLLET_ABOUT_TPL ] = 'درباره ما (بگلت)';
		return $templates;
	}
);

function bagllet_about_is_template_page() {
	return is_page() && BAGLLET_ABOUT_TPL === get_post_meta( get_queried_object_id(), '_wp_page_template', true );
}

add_filter(
	'template_include',
	function ( $template ) {
		if ( bagllet_about_is_template_page() ) {
			$file = BAGLLET_WIDGETS_PATH . 'templates/about.php';
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
		if ( ! bagllet_about_is_template_page() ) {
			return;
		}
		bagllet_widgets_register_assets();
		wp_enqueue_style( 'bagllet-widgets' );
		wp_enqueue_style( 'bagllet-about', BAGLLET_WIDGETS_URL . 'assets/css/about-page.css', array( 'bagllet-widgets' ), BAGLLET_WIDGETS_VERSION );
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( bagllet_about_is_template_page() ) {
			$classes[] = 'bgl-about-page';
		}
		return $classes;
	}
);

/* ---------------------------------------------------------------------
 * متاباکس ویرایش محتوا (داخل صفحه‌ی ویرایش وردپرس)
 * ------------------------------------------------------------------ */
add_action(
	'add_meta_boxes_page',
	function ( $post ) {
		if ( BAGLLET_ABOUT_TPL !== get_post_meta( $post->ID, '_wp_page_template', true ) ) {
			return;
		}
		add_meta_box( 'bagllet_about_box', 'محتوای صفحه‌ی «درباره ما» (بگلت)', 'bagllet_about_box_render', 'page', 'normal', 'high' );
	}
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$post = get_post();
		if ( ! $post || 'page' !== $post->post_type || BAGLLET_ABOUT_TPL !== get_post_meta( $post->ID, '_wp_page_template', true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'bagllet-about-admin', BAGLLET_WIDGETS_URL . 'assets/js/about-admin.js', array( 'jquery' ), BAGLLET_WIDGETS_VERSION, true );
	}
);

function bagllet_about_in( $name, $value, $label, $type = 'text', $hint = '' ) {
	echo '<p class="bga-f"><label><span>' . esc_html( $label ) . '</span>';
	if ( 'textarea' === $type ) {
		echo '<textarea name="' . esc_attr( $name ) . '" rows="3">' . esc_textarea( $value ) . '</textarea>';
	} elseif ( 'url' === $type ) {
		echo '<input type="text" dir="ltr" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" placeholder="https://">';
	} else {
		echo '<input type="text" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
	}
	echo '</label>';
	if ( $hint ) {
		echo '<em>' . esc_html( $hint ) . '</em>';
	}
	echo '</p>';
}

function bagllet_about_in_img( $name, $id, $label ) {
	$src = $id ? wp_get_attachment_image_url( (int) $id, 'medium' ) : '';
	echo '<div class="bga-f bga-img"><span class="bga-lb">' . esc_html( $label ) . '</span>';
	echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( (int) $id ) . '">';
	echo '<div class="bga-prev">' . ( $src ? '<img src="' . esc_url( $src ) . '" alt="">' : '<span>' . esc_html__( 'تصویر پیش‌فرض', 'bagllet-widgets' ) . '</span>' ) . '</div>';
	echo '<button type="button" class="button bga-pick">' . esc_html__( 'انتخاب تصویر', 'bagllet-widgets' ) . '</button> ';
	echo '<button type="button" class="button-link bga-clear">' . esc_html__( 'حذف (بازگشت به پیش‌فرض)', 'bagllet-widgets' ) . '</button>';
	echo '</div>';
}

function bagllet_about_icon_select( $name, $value, $options ) {
	echo '<p class="bga-f"><label><span>' . esc_html__( 'آیکون', 'bagllet-widgets' ) . '</span><select name="' . esc_attr( $name ) . '">';
	foreach ( $options as $k => $label ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $value, $k, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></label></p>';
}

function bagllet_about_box_render( $post ) {
	$c = bagllet_about_get( $post->ID );
	wp_nonce_field( 'bagllet_about_save', 'bagllet_about_nonce' );

	$pl_icons = array(
		'shield'  => 'سپر (ضمانت)',
		'leaf'    => 'برگ (طبیعی)',
		'truck'   => 'کامیون (ارسال)',
		'chat'    => 'پیام (پشتیبانی)',
		'sparkle' => 'درخشش',
		'gift'    => 'هدیه',
		'star'    => 'ستاره',
	);
	$ch_icons = array(
		'whatsapp'  => 'واتساپ',
		'telegram'  => 'تلگرام',
		'instagram' => 'اینستاگرام',
		'phone'     => 'تلفن',
		'mail'      => 'ایمیل',
	);

	echo '<style>
	#bagllet_about_box .inside{margin:0;padding:0}
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
	.bga .bga-prev{width:180px;height:120px;border:1px dashed #c3c4c7;border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center;margin-bottom:8px;color:#646970;font-size:12px;background:#f6f7f7}
	.bga .bga-prev img{width:100%;height:100%;object-fit:cover}
	.bga .bga-note{padding:10px 16px;background:#fff8e5;border-top:1px solid #dcdcde;font-size:12px}
	</style><div class="bga" dir="rtl">';
	echo '<div class="bga-note">' . esc_html__( 'هر فیلد را که خالی بگذاری، همان مورد در صفحه نمایش داده نمی‌شود. تصویری که انتخاب نکنی با تصویر نمونه‌ی بگلت جایگزین می‌شود. بعد از تغییر، «به‌روزرسانی» را بزن.', 'bagllet-widgets' ) . '</div>';

	/* ۱) داستان */
	$s = $c['story'];
	echo '<details open><summary>۱) داستان (بخش اول صفحه)</summary><div class="bga-body">';
	bagllet_about_in( 'bagllet_about[story][eyebrow]', $s['eyebrow'], 'برچسب کوچک بالای تیتر' );
	bagllet_about_in( 'bagllet_about[story][title]', $s['title'], 'تیتر اصلی (برای رفتن به خط بعد Enter بزن)', 'textarea' );
	bagllet_about_in( 'bagllet_about[story][question]', $s['question'], 'جمله‌ی برجسته', 'textarea' );
	bagllet_about_in( 'bagllet_about[story][sign]', $s['sign'], 'امضا' );
	echo '<div class="bga-wide">';
	bagllet_about_in( 'bagllet_about[story][p1]', $s['p1'], 'پاراگراف اول', 'textarea' );
	bagllet_about_in( 'bagllet_about[story][p2]', $s['p2'], 'پاراگراف دوم', 'textarea' );
	echo '</div>';
	bagllet_about_in_img( 'bagllet_about[story][img_main]', $s['img_main'], 'تصویر اصلی (پیشنهاد: عمودی ۴:۵)' );
	bagllet_about_in( 'bagllet_about[story][alt_main]', $s['alt_main'], 'توضیح تصویر اصلی (alt)' );
	bagllet_about_in_img( 'bagllet_about[story][img_second]', $s['img_second'], 'تصویر دوم (نمای نزدیک)' );
	bagllet_about_in( 'bagllet_about[story][alt_second]', $s['alt_second'], 'توضیح تصویر دوم (alt)' );
	bagllet_about_in( 'bagllet_about[story][caption]', $s['caption'], 'برچسب روی تصویر اصلی' );
	bagllet_about_in( 'bagllet_about[story][cta_text]', $s['cta_text'], 'متن دکمه (اختیاری)' );
	bagllet_about_in( 'bagllet_about[story][cta_url]', $s['cta_url'], 'لینک دکمه', 'url' );
	echo '</div></details>';

	/* ۲) قول‌ها */
	$s = $c['pledges'];
	echo '<details><summary>۲) قول‌ها (پنل مشکی)</summary><div class="bga-body">';
	bagllet_about_in( 'bagllet_about[pledges][eyebrow]', $s['eyebrow'], 'برچسب کوچک' );
	bagllet_about_in( 'bagllet_about[pledges][title]', $s['title'], 'تیتر (سفید)' );
	bagllet_about_in( 'bagllet_about[pledges][title_2]', $s['title_2'], 'ادامه‌ی تیتر (رنگ Clay)' );
	bagllet_about_in( 'bagllet_about[pledges][lead]', $s['lead'], 'توضیح کوتاه', 'textarea' );
	foreach ( $s['items'] as $i => $it ) {
		$p = 'bagllet_about[pledges][items][' . $i . ']';
		echo '<div class="bga-card"><h4>قول ' . esc_html( bagllet_widgets_fa( $i + 1 ) ) . '</h4>';
		bagllet_about_icon_select( $p . '[icon]', $it['icon'], $pl_icons );
		bagllet_about_in( $p . '[title]', $it['title'], 'عنوان' );
		bagllet_about_in( $p . '[text]', $it['text'], 'متن کوتاه', 'textarea' );
		bagllet_about_in( $p . '[url]', $it['url'], 'لینک (اختیاری)', 'url' );
		echo '</div>';
	}
	echo '</div></details>';

	/* ۳) جزئیات */
	$s = $c['details'];
	echo '<details><summary>۳) جزئیات (سه عکس نزدیک)</summary><div class="bga-body">';
	bagllet_about_in( 'bagllet_about[details][eyebrow]', $s['eyebrow'], 'برچسب کوچک' );
	bagllet_about_in( 'bagllet_about[details][title]', $s['title'], 'تیتر' );
	echo '<div class="bga-wide">';
	bagllet_about_in( 'bagllet_about[details][lead]', $s['lead'], 'توضیح کوتاه', 'textarea' );
	echo '</div>';
	foreach ( $s['items'] as $i => $it ) {
		$p = 'bagllet_about[details][items][' . $i . ']';
		echo '<div class="bga-card"><h4>عکس ' . esc_html( bagllet_widgets_fa( $i + 1 ) ) . '</h4>';
		bagllet_about_in_img( $p . '[img]', $it['img'], 'تصویر (پیشنهاد: ۴:۳)' );
		bagllet_about_in( $p . '[alt]', $it['alt'], 'توضیح تصویر (alt)' );
		bagllet_about_in( $p . '[title]', $it['title'], 'عنوان' );
		bagllet_about_in( $p . '[text]', $it['text'], 'توضیح کوتاه' );
		bagllet_about_in( $p . '[url]', $it['url'], 'لینک (اختیاری)', 'url' );
		echo '</div>';
	}
	echo '</div></details>';

	/* ۴) ارتباط */
	$s = $c['channels'];
	echo '<details><summary>۴) کانال‌های ارتباطی</summary><div class="bga-body">';
	bagllet_about_in( 'bagllet_about[channels][eyebrow]', $s['eyebrow'], 'برچسب کوچک' );
	bagllet_about_in( 'bagllet_about[channels][title]', $s['title'], 'تیتر' );
	bagllet_about_in( 'bagllet_about[channels][lead]', $s['lead'], 'توضیح کوتاه', 'textarea' );
	bagllet_about_in( 'bagllet_about[channels][note]', $s['note'], 'یادداشت پایین (مثلاً ساعت پاسخ‌گویی)' );
	foreach ( $s['items'] as $i => $it ) {
		$p = 'bagllet_about[channels][items][' . $i . ']';
		echo '<div class="bga-card"><h4>کانال ' . esc_html( bagllet_widgets_fa( $i + 1 ) ) . ' (کانالی که نداری را خالی بگذار)</h4>';
		bagllet_about_icon_select( $p . '[icon]', $it['icon'], $ch_icons );
		bagllet_about_in( $p . '[title]', $it['title'], 'عنوان' );
		bagllet_about_in( $p . '[text]', $it['text'], 'متن زیر عنوان (آی‌دی یا شماره)' );
		bagllet_about_in( $p . '[url]', $it['url'], 'لینک (https://wa.me/… یا tel:… یا https://t.me/…)', 'url' );
		echo '</div>';
	}
	echo '</div></details>';

	/* ۵) دو در */
	$s = $c['doors'];
	echo '<details><summary>۵) پایان صفحه: انتخاب مجموعه</summary><div class="bga-body">';
	bagllet_about_in( 'bagllet_about[doors][title]', $s['title'], 'تیتر (سفید)' );
	bagllet_about_in( 'bagllet_about[doors][title_2]', $s['title_2'], 'ادامه‌ی تیتر (Clay)' );
	bagllet_about_in( 'bagllet_about[doors][lead]', $s['lead'], 'توضیح کوتاه' );
	bagllet_about_in( 'bagllet_about[doors][cta_text]', $s['cta_text'], 'متن دکمه‌ی پایانی' );
	bagllet_about_in( 'bagllet_about[doors][cta_url]', $s['cta_url'], 'لینک دکمه‌ی پایانی', 'url' );
	foreach ( $s['items'] as $i => $it ) {
		$p = 'bagllet_about[doors][items][' . $i . ']';
		echo '<div class="bga-card"><h4>کارت ' . esc_html( bagllet_widgets_fa( $i + 1 ) ) . ( $i > 1 ? ' (اختیاری)' : '' ) . '</h4>';
		bagllet_about_in_img( $p . '[img]', $it['img'], 'تصویر' );
		bagllet_about_in( $p . '[alt]', $it['alt'], 'توضیح تصویر (alt)' );
		bagllet_about_in( $p . '[tag]', $it['tag'], 'تگ کوچک' );
		bagllet_about_in( $p . '[title]', $it['title'], 'عنوان' );
		bagllet_about_in( $p . '[text]', $it['text'], 'توضیح کوتاه' );
		bagllet_about_in( $p . '[url]', $it['url'], 'لینک', 'url' );
		echo '</div>';
	}
	echo '</div></details></div>';
}

/* ---------------------------------------------------------------------
 * ذخیره
 * ------------------------------------------------------------------ */
function bagllet_about_clean( $key, $val ) {
	if ( ! is_string( $val ) && ! is_numeric( $val ) ) {
		return '';
	}
	$val = (string) $val;

	if ( 'img' === $key || 0 === strpos( $key, 'img_' ) ) {
		return absint( $val );
	}
	if ( 'url' === $key || 'cta_url' === $key ) {
		return esc_url_raw( trim( $val ) );
	}
	if ( 'icon' === $key ) {
		return sanitize_key( $val );
	}
	if ( in_array( $key, array( 'title', 'question', 'p1', 'p2', 'lead' ), true ) ) {
		return sanitize_textarea_field( $val );
	}
	return sanitize_text_field( $val );
}

function bagllet_about_sanitize( $raw ) {
	$raw = is_array( $raw ) ? $raw : array();
	$out = array();
	foreach ( bagllet_about_defaults() as $sec => $fields ) {
		foreach ( $fields as $k => $dv ) {
			if ( 'items' === $k ) {
				$items = array();
				$ritems = isset( $raw[ $sec ]['items'] ) && is_array( $raw[ $sec ]['items'] ) ? $raw[ $sec ]['items'] : array();
				foreach ( $dv as $i => $idefault ) {
					$ri   = isset( $ritems[ $i ] ) && is_array( $ritems[ $i ] ) ? $ritems[ $i ] : array();
					$item = array();
					foreach ( $idefault as $ik => $idv ) {
						$item[ $ik ] = bagllet_about_clean( $ik, isset( $ri[ $ik ] ) ? $ri[ $ik ] : '' );
					}
					$items[ $i ] = $item;
				}
				$out[ $sec ]['items'] = $items;
			} else {
				$out[ $sec ][ $k ] = bagllet_about_clean( $k, isset( $raw[ $sec ][ $k ] ) ? $raw[ $sec ][ $k ] : '' );
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
		if ( ! isset( $_POST['bagllet_about_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bagllet_about_nonce'] ) ), 'bagllet_about_save' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$raw = isset( $_POST['bagllet_about'] ) ? wp_unslash( $_POST['bagllet_about'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		update_post_meta( $post_id, BAGLLET_ABOUT_META, bagllet_about_sanitize( $raw ) );
	}
);

/* ---------------------------------------------------------------------
 * ساخت خودکار پیش‌نویس (فقط یک بار، فقط برای مدیری که وارد پیشخوان می‌شود)
 * ------------------------------------------------------------------ */
add_action(
	'admin_init',
	function () {
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ! current_user_can( 'publish_pages' ) || get_option( 'bagllet_about_page_id' ) ) {
			return;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'  => 'درباره ما',
				'post_name'   => 'about',
				'post_status' => 'draft',
				'post_type'   => 'page',
			),
			true
		);
		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return;
		}

		update_post_meta( $post_id, '_wp_page_template', BAGLLET_ABOUT_TPL );
		update_post_meta( $post_id, BAGLLET_ABOUT_META, bagllet_about_defaults() );
		update_option( 'bagllet_about_page_id', (int) $post_id, false );
		update_option( 'bagllet_about_notice', 1, false );
	}
);

add_action(
	'admin_notices',
	function () {
		$id = (int) get_option( 'bagllet_about_page_id' );
		if ( ! $id || ! get_option( 'bagllet_about_notice' ) || ! current_user_can( 'edit_post', $id ) ) {
			return;
		}
		delete_option( 'bagllet_about_notice' );
		echo '<div class="notice notice-success is-dismissible"><p><strong>بگلت:</strong> صفحه‌ی «درباره ما» به‌صورت پیش‌نویس ساخته شد. <a class="button button-primary" href="' . esc_url( get_edit_post_link( $id ) ) . '">ویرایش محتوای صفحه</a></p></div>';
	}
);
