<?php
/**
 * Single research project.
 *
 * @package Coastal_Lab
 */

get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		$coastal_lab_lead     = coastal_lab_field( 'lead_investigator' );
		$coastal_lab_team     = (array) coastal_lab_field( 'project_team' );
		$coastal_lab_start    = coastal_lab_field( 'start_date' );
		$coastal_lab_funding  = coastal_lab_field( 'funding_source' );
		$coastal_lab_external = coastal_lab_field( 'external_link' );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="project-header container">
				<?php coastal_lab_project_status_badge(); ?>
				<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
				<?php coastal_lab_research_areas(); ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="project-hero container">
					<?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="container project-layout">
				<div class="entry-content">
					<?php the_content(); ?>
				</div>

				<aside class="project-facts" aria-label="<?php esc_attr_e( 'Project facts', 'coastal-lab' ); ?>">
					<dl>
						<?php if ( $coastal_lab_lead ) : ?>
							<dt><?php esc_html_e( 'Lead investigator', 'coastal-lab' ); ?></dt>
							<dd><a href="<?php echo esc_url( get_permalink( $coastal_lab_lead ) ); ?>"><?php echo esc_html( get_the_title( $coastal_lab_lead ) ); ?></a></dd>
						<?php endif; ?>

						<?php if ( $coastal_lab_start ) : ?>
							<dt><?php esc_html_e( 'Started', 'coastal-lab' ); ?></dt>
							<dd><?php echo esc_html( $coastal_lab_start ); ?></dd>
						<?php endif; ?>

						<?php if ( $coastal_lab_funding ) : ?>
							<dt><?php esc_html_e( 'Funding', 'coastal-lab' ); ?></dt>
							<dd><?php echo esc_html( $coastal_lab_funding ); ?></dd>
						<?php endif; ?>
					</dl>

					<?php if ( $coastal_lab_external ) : ?>
						<a class="button button--outline" href="<?php echo esc_url( $coastal_lab_external ); ?>">
							<?php esc_html_e( 'Project website / data', 'coastal-lab' ); ?>
						</a>
					<?php endif; ?>
				</aside>
			</div>

			<?php $coastal_lab_team = array_filter( array_map( 'absint', $coastal_lab_team ) ); ?>
			<?php if ( $coastal_lab_team ) : ?>
				<section class="section container" aria-labelledby="project-team">
					<h2 id="project-team"><?php esc_html_e( 'People on this project', 'coastal-lab' ); ?></h2>
					<ul class="person-grid">
						<?php foreach ( $coastal_lab_team as $coastal_lab_person_id ) : ?>
							<li><?php get_template_part( 'template-parts/card', 'person', array( 'post_id' => $coastal_lab_person_id ) ); ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
