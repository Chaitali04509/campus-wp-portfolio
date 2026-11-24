<?php
/**
 * Fallback template (blog posts, news, search fallback).
 *
 * @package Coastal_Lab
 */

get_header();
?>
<main id="main" class="site-main container" tabindex="-1">
	<header class="page-header">
		<h1 class="page-title">
			<?php
			if ( is_home() && ! is_front_page() ) {
				single_post_title();
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				esc_html_e( 'News', 'coastal-lab' );
			}
			?>
		</h1>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="card-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/card', get_post_type() );
			endwhile;
			?>
		</div>
		<?php coastal_lab_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</main>
<?php
get_footer();
