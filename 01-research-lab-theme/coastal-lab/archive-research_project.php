<?php
/**
 * Research projects archive with a progressive-enhancement filter.
 *
 * Without JavaScript, the filter links go to the research_area term archives.
 * With JavaScript, they filter the grid in place and announce the result count.
 *
 * @package Coastal_Lab
 */

get_header();

$coastal_lab_areas = get_terms(
	array(
		'taxonomy'   => 'research_area',
		'hide_empty' => true,
	)
);
?>
<main id="main" class="site-main container" tabindex="-1">
	<header class="page-header">
		<h1 class="page-title"><?php post_type_archive_title(); ?></h1>
		<p class="page-intro"><?php esc_html_e( 'Browse current and past projects by research area.', 'coastal-lab' ); ?></p>
	</header>

	<?php if ( ! is_wp_error( $coastal_lab_areas ) && count( $coastal_lab_areas ) > 1 ) : ?>
		<nav class="filter" aria-label="<?php esc_attr_e( 'Filter projects by research area', 'coastal-lab' ); ?>" data-filter-target="#project-grid">
			<ul class="filter__list">
				<li><a class="filter__btn" href="<?php echo esc_url( get_post_type_archive_link( 'research_project' ) ); ?>" data-filter="all" aria-current="true"><?php esc_html_e( 'All', 'coastal-lab' ); ?></a></li>
				<?php foreach ( $coastal_lab_areas as $coastal_lab_area ) : ?>
					<li><a class="filter__btn" href="<?php echo esc_url( get_term_link( $coastal_lab_area ) ); ?>" data-filter="<?php echo esc_attr( $coastal_lab_area->slug ); ?>"><?php echo esc_html( $coastal_lab_area->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<p class="filter__status screen-reader-text" role="status" aria-live="polite"></p>
		</nav>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="card-grid" id="project-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/card', 'research_project' );
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
