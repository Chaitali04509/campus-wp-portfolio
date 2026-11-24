<?php
/**
 * Uninstall cleanup.
 *
 * Removes settings and cached data. Directory people (posts) are intentionally
 * KEPT: deleting staff records network-wide on uninstall would be destructive
 * and surprising. Remove them manually from each site if required.
 *
 * @package Campus_Directory
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_site_option( 'campus_directory_sites' );
delete_site_option( 'campus_directory_cache_version' );
delete_site_option( 'campus_directory_needs_flush' );

if ( is_multisite() ) {
	$campus_directory_site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);
	foreach ( $campus_directory_site_ids as $campus_directory_site_id ) {
		switch_to_blog( $campus_directory_site_id );
		delete_option( 'campus_directory_flushed' );
		restore_current_blog();
	}
} else {
	delete_option( 'campus_directory_flushed' );
}

// Cached listings expire on their own (1 hour TTL) and are keyed by a version
// number that no longer exists, so they can never be served again.
