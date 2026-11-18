<?php
/**
 * Page template.
 *
 * @package Bagllet
 */

get_header();
?>
<main id="bagllet-content" class="bagllet-site-main">
	<div class="bagllet-container">
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article <?php post_class( 'bagllet-page' ); ?>>
				<h1 class="h1"><?php the_title(); ?></h1>
				<div class="bagllet-page__content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
