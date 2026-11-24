<?php
/**
 * [campus_directory] shortcode.
 *
 * Shortcodes are used on purpose instead of a custom block: they work in the
 * block editor (Shortcode block), the classic editor, and page builders like Divi,
 * which all appear across CampusPress networks.
 *
 * Usage:
 *   [campus_directory]                               All included sites, with search
 *   [campus_directory scope="site"]                  Only this site's people
 *   [campus_directory department="marine-sciences"]  Pre-filtered, no dropdown
 *   [campus_directory search="no" layout="list"]
 *
 * @package Campus_Directory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode controller.
 */
class Campus_Directory_Shortcode {

	const TAG = 'campus_directory';

	/**
	 * Hook registration.
	 */
	public static function register() {
		add_shortcode( self::TAG, array( __CLASS__, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
	}

	/**
	 * Register (not enqueue) the stylesheet; it is enqueued only when the shortcode renders.
	 */
	public static function register_assets() {
		wp_register_style(
			'campus-directory',
			CAMPUS_DIRECTORY_URL . 'assets/directory.css',
			array(),
			CAMPUS_DIRECTORY_VERSION
		);
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string
	 */
	public static function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'scope'      => 'network',
				'department' => '',
				'search'     => 'yes',
				'layout'     => 'grid',
			),
			$atts,
			self::TAG
		);

		$scope         = in_array( $atts['scope'], array( 'network', 'site' ), true ) ? $atts['scope'] : 'network';
		$layout        = in_array( $atts['layout'], array( 'grid', 'list' ), true ) ? $atts['layout'] : 'grid';
		$show_search   = 'no' !== $atts['search'];
		$fixed_dept    = sanitize_title( $atts['department'] );
		$show_dept_dd  = $show_search && '' === $fixed_dept;
		$show_site_tag = 'network' === $scope && is_multisite();

		// Visitor input (GET form). Read-only query, so no nonce is needed.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$query      = isset( $_GET['cd_q'] ) ? sanitize_text_field( wp_unslash( $_GET['cd_q'] ) ) : '';
		$department = $fixed_dept ? $fixed_dept : ( isset( $_GET['cd_dept'] ) ? sanitize_title( wp_unslash( $_GET['cd_dept'] ) ) : '' );
		// phpcs:enable

		$all_people  = Campus_Directory_Network_Query::get_people( array( 'scope' => $scope ) );
		$departments = Campus_Directory_Network_Query::departments_in( $all_people );
		$people      = Campus_Directory_Network_Query::get_people(
			array(
				'scope'      => $scope,
				'department' => $department,
				'search'     => $show_search ? $query : '',
			)
		);

		wp_enqueue_style( 'campus-directory' );

		ob_start();
		$template = locate_template( 'campus-directory/directory.php' ); // Theme override.
		include $template ? $template : CAMPUS_DIRECTORY_DIR . 'templates/directory.php';
		return (string) ob_get_clean();
	}
}
