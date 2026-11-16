<?php
/**
 * قالب صفحه‌ی «تماس با ما (بگلت)».
 * محتوا از متاباکس صفحه خوانده می‌شود (_bagllet_contact).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$pid = get_queried_object_id();
$c   = bagllet_contact_get( $pid );

$bgl_open = function ( $url, $class ) {
	if ( ! empty( $url ) ) {
		return array( 'a', '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' );
	}
	return array( 'div', '<div class="' . esc_attr( $class ) . '">' );
};

echo '<main id="bgl-about" class="bgl-about bgl-contact">';

/* ---------- ۱) ابتدا + واتساپ ---------- */
$i  = $c['intro'];
$wa = $c['wa'];
$has_wa = ! empty( $wa['title'] );
echo '<section class="bgl-w bgl-ci-w"><div class="bgl-ci' . ( $has_wa ? '' : ' bgl-ci--solo' ) . '"><div class="bgl-ci__tx">';
if ( ! empty( $i['eyebrow'] ) ) {
	echo '<div class="bgl-eb">' . esc_html( $i['eyebrow'] ) . '</div>';
}
if ( ! empty( $i['title'] ) ) {
	echo '<h1 class="bgl-ci__h">' . esc_html( $i['title'] ) . '</h1>';
}
if ( ! empty( $i['lead'] ) ) {
	echo '<p>' . esc_html( $i['lead'] ) . '</p>';
}
echo '</div>';
if ( $has_wa ) {
	echo '<div class="bgl-wa"><div class="bgl-wa__top"><span class="bgl-wa__ic">' . bagllet_widgets_icon( 'whatsapp' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
	if ( ! empty( $wa['status'] ) ) {
		echo '<span class="bgl-wa__st">' . esc_html( $wa['status'] ) . '</span>';
	}
	echo '</div><div><h2>' . esc_html( $wa['title'] ) . '</h2>';
	if ( ! empty( $wa['text'] ) ) {
		echo '<p>' . esc_html( $wa['text'] ) . '</p>';
	}
	if ( ! empty( $wa['btn_text'] ) && ! empty( $wa['url'] ) ) {
		echo '<a class="bgl-btn bgl-btn--clay" href="' . esc_url( $wa['url'] ) . '"><span>' . esc_html( $wa['btn_text'] ) . '</span>' . bagllet_widgets_icon( 'arrow' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div></div>';
}
echo '</div></section>';

/* ---------- ۲) راه‌های ارتباطی ---------- */
$items = array_values(
	array_filter(
		$c['channels']['items'],
		function ( $it ) {
			return ! empty( $it['title'] );
		}
	)
);
if ( $items ) {
	echo '<section class="bgl-w bgl-ch-w bgl-ch-w--tight" aria-label="راه‌های ارتباطی"><div class="bgl-hd"><h2 class="bgl-hd__t">راه‌های ارتباطی</h2></div><ul class="bgl-ch">';
	foreach ( $items as $it ) {
		$o = $bgl_open( $it['url'], 'bgl-ch__it' );
		echo '<li>' . $o[1]; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<span class="bgl-ch__ic">' . bagllet_widgets_icon( $it['icon'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<span class="bgl-ch__tx"><b>' . esc_html( $it['title'] ) . '</b>';
		if ( ! empty( $it['text'] ) ) {
			echo '<small>' . esc_html( $it['text'] ) . '</small>';
		}
		echo '</span>' . bagllet_widgets_icon( 'arrow' ) . '</' . $o[0] . '></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</ul></section>';
}

/* ---------- ۳) فرم ---------- */
$f = $c['form'];
if ( '0' !== $f['enabled'] ) {
	$status = isset( $_GET['bgl'] ) ? sanitize_key( wp_unslash( $_GET['bgl'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$msgs   = array(
		'ok'      => array( 'ok', ! empty( $f['success'] ) ? $f['success'] : 'پیام شما ارسال شد.' ),
		'name'    => array( 'err', 'لطفاً نام و نام خانوادگی خود را وارد کنید.' ),
		'phone'   => array( 'err', 'شماره‌ی تماس معتبر نیست. شماره‌ی موبایل یا تلفن ثابت را با کد شهر وارد کنید.' ),
		'msg'     => array( 'err', 'متن پیام باید بین ۵ تا ۳۰۰۰ نویسه باشد.' ),
		'expired' => array( 'err', 'مهلت ارسال فرم تمام شد یا فرم خیلی سریع ارسال شد. لطفاً دوباره تلاش کنید.' ),
		'limit'   => array( 'err', 'تعداد پیام‌های ارسالی شما زیاد است. لطفاً کمی بعد دوباره تلاش کنید.' ),
		'fail'    => array( 'err', 'ارسال پیام انجام نشد. لطفاً از یکی از راه‌های ارتباطی بالا استفاده کنید.' ),
	);
	$topics = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $f['topics'] ) ) ) );

	echo '<section class="bgl-w bgl-fm-w" id="bgl-form">';
	if ( ! empty( $f['eyebrow'] ) || ! empty( $f['title'] ) || ! empty( $f['lead'] ) ) {
		echo '<div class="bgl-hd">';
		if ( ! empty( $f['eyebrow'] ) ) {
			echo '<div class="bgl-eb">' . esc_html( $f['eyebrow'] ) . '</div>';
		}
		if ( ! empty( $f['title'] ) ) {
			echo '<h2 class="bgl-hd__t">' . esc_html( $f['title'] ) . '</h2>';
		}
		if ( ! empty( $f['lead'] ) ) {
			echo '<p class="bgl-hd__l">' . esc_html( $f['lead'] ) . '</p>';
		}
		echo '</div>';
	}
	echo '<div class="bgl-fm"><div class="bgl-fm__side">';
	if ( ! empty( $f['hint_1'] ) || ! empty( $f['hint_2'] ) ) {
		echo '<ul class="bgl-fm__hint">';
		if ( ! empty( $f['hint_1'] ) ) {
			echo '<li>' . bagllet_widgets_icon( 'clock' ) . '<span>' . esc_html( $f['hint_1'] ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( ! empty( $f['hint_2'] ) ) {
			echo '<li>' . bagllet_widgets_icon( 'shield' ) . '<span>' . esc_html( $f['hint_2'] ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</ul>';
	}
	echo '</div>';

	echo '<form class="bgl-fm__form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" novalidate>';
	if ( isset( $msgs[ $status ] ) ) {
		echo '<div class="bgl-fm__msg bgl-fm__msg--' . esc_attr( $msgs[ $status ][0] ) . '" role="' . ( 'ok' === $msgs[ $status ][0] ? 'status' : 'alert' ) . '">' . esc_html( $msgs[ $status ][1] ) . '</div>';
	}
	echo '<input type="hidden" name="action" value="bagllet_contact_send">';
	echo '<input type="hidden" name="pid" value="' . (int) $pid . '">';
	echo '<input type="hidden" name="bgl_t" value="' . esc_attr( bagllet_contact_token() ) . '">';
	echo '<div class="bgl-fm__hp" aria-hidden="true"><label>وب‌سایت <input type="text" name="bgl_website" tabindex="-1" autocomplete="off"></label></div>';

	echo '<div class="bgl-fm__f"><label for="bgl-name">نام و نام خانوادگی</label><input id="bgl-name" name="bgl_name" type="text" maxlength="80" autocomplete="name" required placeholder="نام و نام خانوادگی خود را وارد کنید"></div>';
	echo '<div class="bgl-fm__f"><label for="bgl-phone">شماره‌ی تماس</label><input id="bgl-phone" name="bgl_phone" type="tel" inputmode="tel" dir="ltr" autocomplete="tel" required placeholder="09123456789"></div>';

	if ( $topics ) {
		echo '<div class="bgl-fm__f bgl-fm__f--w"><label for="bgl-topic">موضوع</label><select id="bgl-topic" name="bgl_topic"><option value="">موضوع پیام را انتخاب کنید</option>';
		foreach ( $topics as $t ) {
			echo '<option value="' . esc_attr( $t ) . '">' . esc_html( $t ) . '</option>';
		}
		echo '</select></div>';
	}

	echo '<div class="bgl-fm__f bgl-fm__f--w"><label for="bgl-msg">متن پیام</label><textarea id="bgl-msg" name="bgl_msg" rows="5" maxlength="3000" required placeholder="متن پیام خود را بنویسید…"></textarea></div>';
	echo '<div class="bgl-fm__sub">';
	if ( ! empty( $f['consent'] ) ) {
		echo '<small>' . esc_html( $f['consent'] ) . '</small>';
	}
	echo '<button class="bgl-btn bgl-btn--dark" type="submit"><span>ارسال پیام</span>' . bagllet_widgets_icon( 'arrow' ) . '</button></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</form></div></section>';
}

/* ---------- ۴) پرسش‌های متداول ---------- */
$q   = $c['faq'];
$qs  = array_values(
	array_filter(
		$q['items'],
		function ( $it ) {
			return ! empty( $it['q'] ) && ! empty( $it['a'] );
		}
	)
);
if ( $qs ) {
	echo '<section class="bgl-w bgl-fq-w"><div class="bgl-hd">';
	if ( ! empty( $q['eyebrow'] ) ) {
		echo '<div class="bgl-eb">' . esc_html( $q['eyebrow'] ) . '</div>';
	}
	if ( ! empty( $q['title'] ) ) {
		echo '<h2 class="bgl-hd__t">' . esc_html( $q['title'] ) . '</h2>';
	}
	echo '</div><div class="bgl-fq">';
	foreach ( $qs as $n => $it ) {
		echo '<details class="bgl-fq__it"' . ( 0 === $n ? ' open' : '' ) . '><summary><span>' . esc_html( $it['q'] ) . '</span><span class="bgl-fq__pm" aria-hidden="true"></span></summary><div class="bgl-fq__a">' . nl2br( esc_html( $it['a'] ) ) . '</div></details>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
	if ( ! empty( $q['note'] ) ) {
		echo '<p class="bgl-fq__note">' . esc_html( $q['note'] ) . '</p>';
	}
	echo '</section>';
}

echo '</main>';

get_footer();
