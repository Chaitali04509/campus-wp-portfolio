<?php
/**
 * Plugin Name:       Campus Directory
 * Description:       People directory for a WordPress Multisite / CampusPress network. Each site manages its own people; the [campus_directory] shortcode and REST endpoint can show one site or the whole network.
 * Version:           0.6.0
 * Requires at least: 6.3
 * Requires PHP:      8.0
 * Network:           true
 * Author:            Portfolio Project
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       campus-directory
 *
 * @package Campus_Directory
 */

defined( 'ABSPATH' ) || exit;

define( 'CAMPUS_DIRECTORY_VERSION', '0.6.0' );
define( 'CAMPUS_DIRECTORY_FILE', __FILE__ );
define( 'CAMPUS_DIRECTORY_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAMPUS_DIRECTORY_URL', plugin_dir_url( __FILE__ ) );

require_once CAMPUS_DIRECTORY_DIR . 'includes/class-post-type.php';
require_once CAMPUS_DIRECTORY_DIR . 'includes/class-fields.php';
require_once CAMPUS_DIRECTORY_DIR . 'includes/class-network-query.php';
require_once CAMPUS_DIRECTORY_DIR . 'includes/class-network-settings.php';
require_once CAMPUS_DIRECTORY_DIR . 'includes/class-shortcode.php';
require_once CAMPUS_DIRECTORY_DIR . 'includes/class-rest.php';

/**
 * Wire up hooks. Each class exposes a static register() so dependencies stay explicit.
 */
function campus_directory_init() {
	Campus_Directory_Post_Type::register();
	Campus_Directory_Fields::register();
	Campus_Directory_Network_Query::register();
	Campus_Directory_Shortcode::register();
	Campus_Directory_Rest::register();

	if ( is_multisite() ) {
		Campus_Directory_Network_Settings::register();
	}
}
add_action( 'plugins_loaded', 'campus_directory_init' );

/**
 * Flush rewrites on activation (for every site when network-activated).
 *
 * @param bool $network_wide Whether the plugin was network-activated.
 */
function campus_directory_activate( $network_wide ) {
	if ( is_multisite() && $network_wide ) {
		// Avoid flushing hundreds of sites synchronously: mark them and flush lazily.
		update_site_option( 'campus_directory_needs_flush', time() );
		return;
	}

	Campus_Directory_Post_Type::register_types();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'campus_directory_activate' );

/**
 * Lazy per-site rewrite flush after network activation.
 */
function campus_directory_maybe_flush() {
	$network_flag = (int) get_site_option( 'campus_directory_needs_flush', 0 );
	if ( ! $network_flag || (int) get_option( 'campus_directory_flushed', 0 ) >= $network_flag ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'campus_directory_flushed', $network_flag, false );
}
add_action( 'init', 'campus_directory_maybe_flush', 99 );
