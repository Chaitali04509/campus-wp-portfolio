<?php
/**
 * Front page.
 *
 * UX decision: visitors to a lab site are mostly (1) prospective students,
 * (2) collaborators/funders, (3) press. The home page answers "what do you study,
 * who are you, how do I get involved" in that order, with editable intro copy
 * from the page content and live project/people data below it.
 *
 * @package Coastal_Lab
 */

get_header();
?>
<main id="main" class="site-main site-main--flush" tabindex="-1">
	<?php get_template_part( 'template-parts/hero' ); ?>

	<?php
	$coastal_lab_projects = new WP_Query(
		array(
			'post_type'      => 'research_project',
			'posts_per_page' => 3,
			'no_found_rows'  => true, // No pagination here, so skip the COUNT query.
		)
	);
	?>
	<?php if ( $coastal_lab_projects->have_posts() ) : ?>
		<section class="section container" aria-labelledby="home-research">
			<div class="section__header">
				<h2 id="home-research"><?php esc_html_e( 'Current research', 'coastal-lab' ); ?></h2>
				<a class="section__more" href="<?php echo esc_url( get_post_type_archive_link( 'research_project' ) ); ?>">
					<?php esc_html_e( 'All projects', 'coastal-lab' ); ?><span class="screen-reader-text"> <?php esc_html_e( 'in research', 'coastal-lab' ); ?></span>
				</a>
			</div>
			<div class="card-grid">
				<?php
				while ( $coastal_lab_projects->have_posts() ) :
					$coastal_lab_projects->the_post();
					get_template_part( 'template-parts/card', 'research_project' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	<section class="section section--tinted" aria-labelledby="home-join">
		<div class="container container--narrow cta">
			<h2 id="home-join"><?php esc_html_e( 'Join the lab', 'coastal-lab' ); ?></h2>
			<p><?php esc_html_e( 'We welcome undergraduate researchers, graduate applicants and community science partners.', 'coastal-lab' ); ?></p>
			<a class="button" href="<?php echo esc_url( get_post_type_archive_link( 'team_member' ) ); ?>"><?php esc_html_e( 'Meet the team', 'coastal-lab' ); ?></a>
		</div>
	</section>
</main>
<?php
get_footer();
