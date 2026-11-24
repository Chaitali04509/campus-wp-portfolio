<?php
/**
 * Site footer.
 *
 * @package Coastal_Lab
 */

?>
<footer class="site-footer">
	<div class="container site-footer__inner">
		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="site-footer__widgets">
				<?php dynamic_sidebar( 'footer-1' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav aria-label="<?php esc_attr_e( 'Footer', 'coastal-lab' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<p class="site-footer__copy">
			&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
