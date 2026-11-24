<?php
/**
 * Research project card. The whole card is clickable, but only the title is a
 * link, so screen readers do not announce one giant link.
 *
 * @package Coastal_Lab
 */

?>
<article <?php post_class( 'card' ); ?> data-areas="<?php echo esc_attr( coastal_lab_area_slugs() ); ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="card__media">
			<?php the_post_thumbnail( 'coastal-lab-card', array( 'alt' => '' ) ); ?>
		</div>
	<?php endif; ?>
	<div class="card__body">
		<?php coastal_lab_project_status_badge(); ?>
		<h3 class="card__title"><a class="card__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="card__summary"><?php echo esc_html( wp_trim_words( coastal_lab_card_summary(), 30 ) ); ?></p>
		<?php coastal_lab_research_areas(); ?>
	</div>
</article>
