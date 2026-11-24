<?php
/**
 * Coastal Lab theme bootstrap.
 *
 * Keeps functions.php small: every concern lives in its own file under /inc
 * so changes show up as focused diffs in version control.
 *
 * @package Coastal_Lab
 */

defined( 'ABSPATH' ) || exit;

define( 'COASTAL_LAB_VERSION', '1.0.0-rc.2' );
define( 'COASTAL_LAB_DIR', get_template_directory() );
define( 'COASTAL_LAB_URI', get_template_directory_uri() );

$coastal_lab_includes = array(
	'inc/setup.php',         // Theme supports, menus, image sizes.
	'inc/enqueue.php',       // Compiled asset loading (webpack manifest aware).
	'inc/post-types.php',    // Research projects, team members, research areas.
	'inc/acf.php',           // ACF field groups, JSON sync, graceful fallback.
	'inc/template-tags.php', // Escaped output helpers used by templates.
);

foreach ( $coastal_lab_includes as $coastal_lab_file ) {
	require_once COASTAL_LAB_DIR . '/' . $coastal_lab_file;
}
