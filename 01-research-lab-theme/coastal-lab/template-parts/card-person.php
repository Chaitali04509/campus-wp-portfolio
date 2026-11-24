<?php
/**
 * Person card. Accepts $args['post_id'] so it can be used outside the loop.
 *
 * @package Coastal_Lab
 */

$coastal_lab_pid   = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();
$coastal_lab_title = coastal_lab_field( 'position_title', $coastal_lab_pid );

if ( ! $coastal_lab_pid || 'publish' !== get_post_status( $coastal_lab_pid ) ) {
	return;
}
?>
<article class="person-card">
	<?php if ( has_post_thumbnail( $coastal_lab_pid ) ) : ?>
		<?php echo get_the_post_thumbnail( $coastal_lab_pid, 'coastal-lab-portrait', array( 'alt' => '' ) ); ?>
	<?php else : ?>
		<div class="avatar-placeholder" aria-hidden="true"></div>
	<?php endif; ?>
	<h3 class="person-card__name">
		<a class="card__link" href="<?php echo esc_url( get_permalink( $coastal_lab_pid ) ); ?>"><?php echo esc_html( get_the_title( $coastal_lab_pid ) ); ?></a>
	</h3>
	<?php if ( $coastal_lab_title ) : ?>
		<p class="person-card__title"><?php echo esc_html( $coastal_lab_title ); ?></p>
	<?php endif; ?>
</article>
