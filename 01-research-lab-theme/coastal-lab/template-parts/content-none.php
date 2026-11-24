<?php
/**
 * Empty state.
 *
 * @package Coastal_Lab
 */

?>
<section class="empty-state">
	<h2><?php esc_html_e( 'Nothing here yet', 'coastal-lab' ); ?></h2>
	<?php if ( is_search() ) : ?>
		<p><?php esc_html_e( 'No results matched your search. Try different keywords.', 'coastal-lab' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Check back soon.', 'coastal-lab' ); ?></p>
	<?php endif; ?>
</section>
