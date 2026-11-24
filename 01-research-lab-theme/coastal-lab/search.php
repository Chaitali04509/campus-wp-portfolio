<?php
/**
 * Search results.
 *
 * @package Coastal_Lab
 */

get_header();
?>
<main id="main" class="site-main container" tabindex="-1">
	<header class="page-header">
		<h1 class="page-title">
			<?php
			/* translators: %s: search query. */
			printf( esc_html__( 'Search results for "%s"', 'coastal-lab' ), esc_html( get_search_query() ) );
			?>
		</h1>
		<?php get_search_form(); ?>
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
