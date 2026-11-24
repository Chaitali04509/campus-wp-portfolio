<?php
/**
 * Divi-specific adjustments.
 *
 * @package Marine_Dept
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hide Divi's built-in "Projects" post type.
 *
 * The department has no portfolio content, and the extra admin menu and public
 * /project/ URLs confused editors during the content audit. Hidden, not unregistered,
 * so any existing Divi layouts that reference it do not break.
 *
 * @param array $args Post type args.
 * @return array
 */
function marine_dept_hide_divi_projects( $args ) {
	return array_merge(
		$args,
		array(
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => false,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false,
		)
	);
}
add_filter( 'et_project_posttype_args', 'marine_dept_hide_divi_projects' );

/**
 * Let the Campus Directory shortcode render inside Divi's Visual Builder preview.
 * Divi renders shortcodes over AJAX in the builder, and the directory stylesheet
 * is normally only enqueued on the front end.
 */
function marine_dept_builder_assets() {
	if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() && wp_style_is( 'campus-directory', 'registered' ) ) {
		wp_enqueue_style( 'campus-directory' );
	}
}
add_action( 'wp_enqueue_scripts', 'marine_dept_builder_assets', 30 );
