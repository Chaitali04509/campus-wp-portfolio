<?php
/**
 * Asset loading.
 *
 * @wordpress/scripts writes a *.asset.php file next to each compiled bundle that
 * lists script dependencies and a content hash. Using it means cache-busting
 * and dependency lists never have to be maintained by hand.
 *
 * @package Coastal_Lab
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read the webpack asset manifest for a bundle.
 *
 * @param string $name Bundle name (entry key in webpack.config.js).
 * @return array{dependencies: string[], version: string}
 */
function coastal_lab_asset( $name ) {
	$path = COASTAL_LAB_DIR . "/build/{$name}.asset.php";

	if ( file_exists( $path ) ) {
		return require $path;
	}

	// Build has not been run: fall back so the site still renders, and nudge developers.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		trigger_error( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			esc_html( "Coastal Lab: build/{$name}.asset.php missing. Run `npm run build`." ),
			E_USER_NOTICE
		);
	}

	return array(
		'dependencies' => array(),
		'version'      => COASTAL_LAB_VERSION,
	);
}

/**
 * Front-end styles and scripts.
 */
function coastal_lab_enqueue_assets() {
	$main = coastal_lab_asset( 'main' );

	wp_enqueue_style(
		'coastal-lab-main',
		COASTAL_LAB_URI . '/build/main.css',
		array(),
		$main['version']
	);

	wp_enqueue_script(
		'coastal-lab-main',
		COASTAL_LAB_URI . '/build/main.js',
		$main['dependencies'],
		$main['version'],
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	// Strings used by the front-end JS, translatable from PHP.
	wp_localize_script(
		'coastal-lab-main',
		'coastalLab',
		array(
			'i18n' => array(
				/* translators: %d: number of visible projects. */
				'showing' => __( 'Showing %d projects', 'coastal-lab' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'coastal_lab_enqueue_assets' );
