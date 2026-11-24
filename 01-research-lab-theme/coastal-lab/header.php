<?php
/**
 * Site header.
 *
 * @package Coastal_Lab
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'coastal-lab' ); ?></a>

<?php if ( has_nav_menu( 'utility' ) ) : ?>
	<nav class="utility-bar" aria-label="<?php esc_attr_e( 'Utility', 'coastal-lab' ); ?>">
		<div class="container">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'utility',
					'container'      => false,
					'menu_class'     => 'utility-menu',
					'depth'          => 1,
				)
			);
			?>
		</div>
	</nav>
<?php endif; ?>

<header class="site-header">
	<div class="container site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
			<?php if ( get_bloginfo( 'description' ) ) : ?>
				<p class="site-description"><?php bloginfo( 'description' ); ?></p>
			<?php endif; ?>
		</div>

		<nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'coastal-lab' ); ?>">
			<button class="menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false" hidden>
				<span class="menu-toggle__icon" aria-hidden="true"></span>
				<span class="menu-toggle__label"><?php esc_html_e( 'Menu', 'coastal-lab' ); ?></span>
			</button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_id'        => 'primary-menu',
					'menu_class'     => 'primary-menu',
					'depth'          => 2,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	</div>
</header>
