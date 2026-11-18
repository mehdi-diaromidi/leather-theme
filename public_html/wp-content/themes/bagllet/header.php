<?php
/**
 * Header template.
 *
 * @package Bagllet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="bagllet-skip-link" href="#bagllet-content"><?php esc_html_e( 'پرش به محتوا', 'bagllet' ); ?></a>
<header class="bagllet-site-header bagllet-surface-dark" role="banner">
	<div class="bagllet-container bagllet-site-header__inner">
		<div class="bagllet-brand">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				?>
				<a class="bagllet-brand__name" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php bloginfo( 'name' ); ?>
				</a>
				<?php
			}
			?>
		</div>
		<nav class="bagllet-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'bagllet' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'bagllet-nav__list',
					'fallback_cb'    => false,
					'depth'          => 2,
				)
			);
			?>
		</nav>
	</div>
</header>
