<?php
/**
 * قالب صفحه‌ی «درباره ما (بگلت)».
 * محتوا از متاباکس صفحه خوانده می‌شود (_bagllet_about).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$c = bagllet_about_get( get_queried_object_id() );

/** عنصر لینک‌دار یا ساده. */
$bgl_open = function ( $url, $class ) {
	if ( ! empty( $url ) ) {
		return array( 'a', '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' );
	}
	return array( 'div', '<div class="' . esc_attr( $class ) . '">' );
};

/** سرتیتر مشترک. */
$bgl_head = function ( $s, $center = false ) {
	if ( empty( $s['eyebrow'] ) && empty( $s['title'] ) && empty( $s['lead'] ) ) {
		return;
	}
	echo '<div class="bgl-hd' . ( $center ? ' bgl-hd--c' : '' ) . '">';
	if ( ! empty( $s['eyebrow'] ) ) {
		echo '<div class="bgl-eb">' . esc_html( $s['eyebrow'] ) . '</div>';
	}
	if ( ! empty( $s['title'] ) || ! empty( $s['title_2'] ) ) {
		echo '<h2 class="bgl-hd__t">' . esc_html( $s['title'] );
		if ( ! empty( $s['title_2'] ) ) {
			echo ' <em>' . esc_html( $s['title_2'] ) . '</em>';
		}
		echo '</h2>';
	}
	if ( ! empty( $s['lead'] ) ) {
		echo '<p class="bgl-hd__l">' . esc_html( $s['lead'] ) . '</p>';
	}
	echo '</div>';
};

echo '<main id="bgl-about" class="bgl-about">';

/* ---------- ۱) داستان ---------- */
$s = $c['story'];
echo '<section class="bgl-w bgl-st-w"><div class="bgl-st"><div class="bgl-st__g"><div class="bgl-st__tx">';
if ( ! empty( $s['eyebrow'] ) ) {
	echo '<div class="bgl-eb">' . esc_html( $s['eyebrow'] ) . '</div>';
}
if ( ! empty( $s['title'] ) ) {
	echo '<h1 class="bgl-st__h">' . nl2br( esc_html( $s['title'] ) ) . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
if ( ! empty( $s['question'] ) ) {
	echo '<div class="bgl-st__q"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 17c0-4 1.5-7 5-9l1 1.5C9.2 10.5 8.5 12 8.5 13H11v4zM14 17c0-4 1.5-7 5-9l1 1.5c-1.8 1-2.5 2.5-2.5 3.5H20v4z"/></svg><b>' . esc_html( $s['question'] ) . '</b></div>';
}
if ( ! empty( $s['p1'] ) ) {
	echo '<p>' . esc_html( $s['p1'] ) . '</p>';
}
if ( ! empty( $s['p2'] ) ) {
	echo '<p>' . esc_html( $s['p2'] ) . '</p>';
}
if ( ! empty( $s['cta_text'] ) && ! empty( $s['cta_url'] ) ) {
	echo '<a class="bgl-btn bgl-btn--dark" href="' . esc_url( $s['cta_url'] ) . '"><span>' . esc_html( $s['cta_text'] ) . '</span>' . bagllet_widgets_icon( 'arrow' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
if ( ! empty( $s['sign'] ) ) {
	echo '<div class="bgl-st__sg"><span aria-hidden="true"></span><span>' . esc_html( $s['sign'] ) . '</span></div>';
}
echo '</div><span class="bgl-st__sp" aria-hidden="true"></span>';
echo '<div class="bgl-st__co"><span class="bgl-st__patch" aria-hidden="true"></span>';
echo '<div class="bgl-st__p1">' . bagllet_about_img( $s['img_main'], 'women-category.webp', $s['alt_main'], true ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
echo '<div class="bgl-st__p2">' . bagllet_about_img( $s['img_second'], 'men-category.webp', $s['alt_second'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
if ( ! empty( $s['caption'] ) ) {
	echo '<span class="bgl-st__cap">' . esc_html( $s['caption'] ) . '</span>';
}
echo '</div></div></div></section>';

/* ---------- ۲) قول‌ها ---------- */
$s     = $c['pledges'];
$items = array_values(
	array_filter(
		$s['items'],
		function ( $it ) {
			return ! empty( $it['title'] );
		}
	)
);
if ( $items ) {
	echo '<section class="bgl-w bgl-pl-w"><div class="bgl-pl on-dark"><div class="bgl-pl__top">';
	$bgl_head( $s );
	echo '</div><ul class="bgl-pl__row">';
	foreach ( $items as $i => $it ) {
		$o = $bgl_open( $it['url'], 'bgl-pl__card' );
		echo '<li>' . $o[1]; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<span class="bgl-pl__hd"><span class="bgl-pl__ic">' . bagllet_widgets_icon( $it['icon'] ) . '</span><span class="bgl-pl__n" aria-hidden="true">' . esc_html( bagllet_widgets_fa( '0' . ( $i + 1 ) ) ) . '</span></span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<h3>' . esc_html( $it['title'] ) . '</h3>';
		if ( ! empty( $it['text'] ) ) {
			echo '<p>' . esc_html( $it['text'] ) . '</p>';
		}
		echo '</' . $o[0] . '></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</ul></div></section>';
}

/* ---------- ۳) جزئیات ---------- */
$s        = $c['details'];
$fallback = array( 'men-category.webp', 'women-category.webp', 'men-category.webp' );
$zoom     = array( '--s:3.2;--ox:18%;--oy:88%', '--s:2.4;--ox:22%;--oy:64%', '--s:2.3;--ox:50%;--oy:54%' );
$has      = false;
foreach ( $s['items'] as $it ) {
	if ( ! empty( $it['title'] ) ) {
		$has = true;
	}
}
if ( $has ) {
	echo '<section class="bgl-w bgl-dt-w">';
	$bgl_head( $s );
	echo '<div class="bgl-dt">';
	foreach ( $s['items'] as $i => $it ) {
		if ( empty( $it['title'] ) ) {
			continue;
		}
		$o = $bgl_open( $it['url'], 'bgl-dt__fig' );
		echo $o[1]; // phpcs:ignore WordPress.Security.EscapeOutput
		// اگر خودت تصویر آپلود کنی، همان بدون بزرگ‌نمایی نمایش داده می‌شود.
		$style = empty( $it['img'] ) ? $zoom[ $i % 3 ] : '';
		echo '<span class="bgl-dt__ph"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . bagllet_about_img( $it['img'], $fallback[ $i % 3 ], $it['alt'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<span class="bgl-dt__cap"><b>' . esc_html( $it['title'] ) . '</b>';
		if ( ! empty( $it['text'] ) ) {
			echo '<span>' . esc_html( $it['text'] ) . '</span>';
		}
		echo '</span></' . $o[0] . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div></section>';
}

/* ---------- ۴) کانال‌های ارتباطی ---------- */
$s     = $c['channels'];
$items = array_values(
	array_filter(
		$s['items'],
		function ( $it ) {
			return ! empty( $it['title'] );
		}
	)
);
if ( $items ) {
	echo '<section class="bgl-w bgl-ch-w">';
	$bgl_head( $s );
	echo '<ul class="bgl-ch">';
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
	echo '</ul>';
	if ( ! empty( $s['note'] ) ) {
		echo '<p class="bgl-ch__note">' . esc_html( $s['note'] ) . '</p>';
	}
	echo '</section>';
}

/* ---------- ۵) پایان: انتخاب مجموعه ---------- */
$s        = $c['doors'];
$fallback = array( 'men-category.webp', 'women-category.webp', 'men-products.webp' );
$pos      = array( '--ox:55%;--oy:50%', '--ox:45%;--oy:55%', '--ox:50%;--oy:50%' );
$items    = array();
foreach ( $s['items'] as $i => $it ) {
	if ( ! empty( $it['title'] ) ) {
		$it['_i'] = $i;
		$items[]  = $it;
	}
}
if ( $items ) {
	echo '<section class="bgl-w bgl-dr-w"><div class="bgl-dr on-dark">';
	$bgl_head( $s, true );
	echo '<div class="bgl-dr__row bgl-dr__row--' . (int) count( $items ) . '">';
	foreach ( $items as $it ) {
		$o = $bgl_open( $it['url'], 'bgl-dr__door' );
		echo $o[1]; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<span class="bgl-dr__ph" style="' . esc_attr( $pos[ $it['_i'] % 3 ] ) . '">' . bagllet_about_img( $it['img'], $fallback[ $it['_i'] % 3 ], $it['alt'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<span class="bgl-dr__tx">';
		if ( ! empty( $it['tag'] ) ) {
			echo '<span class="bgl-dr__tag">' . esc_html( $it['tag'] ) . '</span>';
		}
		echo '<b>' . esc_html( $it['title'] ) . '</b>';
		if ( ! empty( $it['text'] ) ) {
			echo '<small>' . esc_html( $it['text'] ) . '</small>';
		}
		echo '</span><span class="bgl-dr__go">' . bagllet_widgets_icon( 'arrow' ) . '</span></' . $o[0] . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
	if ( ! empty( $s['cta_text'] ) && ! empty( $s['cta_url'] ) ) {
		echo '<div class="bgl-dr__all"><a class="bgl-btn bgl-btn--clay" href="' . esc_url( $s['cta_url'] ) . '"><span>' . esc_html( $s['cta_text'] ) . '</span>' . bagllet_widgets_icon( 'arrow' ) . '</a></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div></section>';
}

echo '</main>';

get_footer();
