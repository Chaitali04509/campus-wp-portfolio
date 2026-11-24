<?php
/**
 * Generic card for posts, pages and any other post type.
 *
 * @package Coastal_Lab
 */

?>
<article <?php post_class( 'card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="card__media">
			<?php the_post_thumbnail( 'coastal-lab-card', array( 'alt' => '' ) ); ?>
		</div>
	<?php endif; ?>
	<div class="card__body">
		<?php if ( 'post' === get_post_type() ) : ?>
			<p class="meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		<?php endif; ?>
		<h3 class="card__title"><a class="card__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="card__summary"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
	</div>
</article>
