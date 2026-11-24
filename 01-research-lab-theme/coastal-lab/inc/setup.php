<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package Coastal_Lab
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function coastal_lab_setup() {
	load_theme_textdomain( 'coastal-lab', COASTAL_LAB_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_editor_style( 'build/editor.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'coastal-lab' ),
			'utility' => __( 'Utility links (top bar)', 'coastal-lab' ),
			'footer'  => __( 'Footer navigation', 'coastal-lab' ),
		)
	);

	// Card images are cropped to a consistent ratio so grids stay aligned.
	add_image_size( 'coastal-lab-card', 640, 400, true );
	add_image_size( 'coastal-lab-portrait', 400, 400, true );
}
add_action( 'after_setup_theme', 'coastal_lab_setup' );

/**
 * Footer widget area for contact info / lab address.
 */
function coastal_lab_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer', 'coastal-lab' ),
			'id'            => 'footer-1',
			'description'   => __( 'Lab address, contact details, funding acknowledgements.', 'coastal-lab' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'coastal_lab_widgets_init' );

/**
 * Show more team members per page on the archive so the whole lab fits on one screen.
 *
 * @param WP_Query $query Main query.
 */
function coastal_lab_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'team_member' ) ) {
		$query->set( 'posts_per_page', 50 );
		$query->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			)
		);
	}
}
add_action( 'pre_get_posts', 'coastal_lab_pre_get_posts' );
