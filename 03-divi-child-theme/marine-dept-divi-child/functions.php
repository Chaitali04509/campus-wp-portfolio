<?php
/**
 * Marine Sciences: Divi child theme.
 *
 * Rule for this site: Divi itself is never edited. Everything custom lives here
 * or in the Divi Theme Builder / Divi Library (which is exported to /docs).
 *
 * @package Marine_Dept
 */

defined( 'ABSPATH' ) || exit;

define( 'MARINE_DEPT_VERSION', '0.1.0' );

require_once get_stylesheet_directory() . '/inc/divi-tweaks.php';
require_once get_stylesheet_directory() . '/inc/accessibility.php';

/**
 * Enqueue child styles after Divi.
 *
 * Divi 4.10+ enqueues the parent stylesheet itself (and splits most CSS into
 * dynamic per-page files), so the parent style.css is NOT re-enqueued here. Doing
 * so would load it twice. Priority 20 makes sure these rules print after Divi's.
 * Verify in DevTools → Network after any Divi major update.
 */
function marine_dept_enqueue_styles() {
	wp_enqueue_style(
		'marine-dept',
		get_stylesheet_directory_uri() . '/assets/css/marine.css',
		array(),
		MARINE_DEPT_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'marine_dept_enqueue_styles', 20 );
