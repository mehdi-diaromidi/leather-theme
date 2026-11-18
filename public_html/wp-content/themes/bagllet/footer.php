<?php
/**
 * Footer template.
 *
 * @package Bagllet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="bagllet-site-footer bagllet-surface-dark" role="contentinfo">
	<div class="bagllet-container bagllet-site-footer__inner">
		<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) ) : ?>
			<div class="bagllet-grid">
				<?php dynamic_sidebar( 'footer-1' ); ?>
				<?php dynamic_sidebar( 'footer-2' ); ?>
			</div>
		<?php endif; ?>
		<p class="bagllet-site-footer__copy">
			&copy; <?php echo esc_html( bagllet_persian_digits( gmdate( 'Y' ) ) ); ?>
			<?php bloginfo( 'name' ); ?>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
