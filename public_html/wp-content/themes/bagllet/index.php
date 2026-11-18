<?php
/**
 * Main fallback template.
 *
 * @package Bagllet
 */

get_header();
?>
<main id="bagllet-content" class="bagllet-site-main">
	<div class="bagllet-container">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<article <?php post_class( 'bagllet-entry' ); ?>>
					<h1 class="h1"><?php the_title(); ?></h1>
					<div class="bagllet-entry__content">
						<?php the_content(); ?>
					</div>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p class="bagllet-muted"><?php esc_html_e( 'مطلبی یافت نشد.', 'bagllet' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
