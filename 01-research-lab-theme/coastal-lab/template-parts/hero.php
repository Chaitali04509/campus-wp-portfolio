<?php
/**
 * Front page hero. Heading and intro come from the static front page itself,
 * so editors change them in the block editor without touching code.
 *
 * @package Coastal_Lab
 */

$coastal_lab_front_id = (int) get_option( 'page_on_front' );
?>
<section class="hero">
	<div class="container hero__inner">
		<h1 class="hero__title">
			<?php echo esc_html( $coastal_lab_front_id ? get_the_title( $coastal_lab_front_id ) : get_bloginfo( 'name' ) ); ?>
		</h1>
		<?php if ( $coastal_lab_front_id && have_posts() ) : ?>
			<div class="hero__intro entry-content">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		<?php else : ?>
			<p class="hero__intro"><?php bloginfo( 'description' ); ?></p>
		<?php endif; ?>
	</div>
</section>
