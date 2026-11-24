<?php
/**
 * Playground demo, step 1 of 2: network setup.
 *
 * Runs in its own PHP request so that step 2 starts with the plugins and the
 * theme already loaded (their post types and taxonomies must be registered
 * before content can be created).
 *
 * @package Campus_WP_Portfolio
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- standalone demo script.

require_once '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

// Network-activate ACF and the directory plugin.
foreach ( array( 'advanced-custom-fields/acf.php', 'campus-directory/campus-directory.php' ) as $plugin ) {
	if ( ! is_plugin_active_for_network( $plugin ) ) {
		activate_plugin( $plugin, '', true );
	}
}

// Make the theme available to every site, and use it on the main site too.
$allowed                = (array) get_site_option( 'allowedthemes', array() );
$allowed['coastal-lab'] = true;
update_site_option( 'allowedthemes', $allowed );
switch_theme( 'coastal-lab' );

// Create the two sub-sites.
$network = get_network();
$sites   = array(
	'coastal-lab'     => 'Coastal Ecology Lab',
	'marine-sciences' => 'Department of Marine Sciences',
);

foreach ( $sites as $slug => $title ) {
	$path = trailingslashit( $network->path . $slug );
	$blog_id = get_blog_id_from_url( $network->domain, $path );
	if ( ! $blog_id ) {
		$blog_id = wpmu_create_blog( $network->domain, $path, $title, 1 );
	}
	// Playground creates sites as "discourage search engines" (public = 0), and the
	// directory only lists public sites by default, so mark the demo sites public.
	update_blog_details( $blog_id, array( 'public' => 1 ) );
}

$lab_id = get_blog_id_from_url( $network->domain, trailingslashit( $network->path . 'coastal-lab' ) );
switch_to_blog( $lab_id );
switch_theme( 'coastal-lab' );
restore_current_blog();
