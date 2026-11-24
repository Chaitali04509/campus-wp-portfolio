<?php
/**
 * Custom post types and taxonomies.
 *
 * Content model (see docs/site-configuration.md):
 *   research_project  -> what the lab works on
 *   team_member       -> people (PI, postdocs, students, alumni)
 *   research_area     -> shared taxonomy that links projects and people
 *
 * Note: in production these could live in a small "functionality" plugin so the
 * content survives a theme switch. They are kept in the theme here because this
 * lab site is a single-theme build. That choice is recorded in the docs.
 *
 * @package Coastal_Lab
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register CPTs and taxonomy.
 */
function coastal_lab_register_content_types() {
	register_post_type(
		'research_project',
		array(
			'labels'        => array(
				'name'          => __( 'Research Projects', 'coastal-lab' ),
				'singular_name' => __( 'Research Project', 'coastal-lab' ),
				'add_new_item'  => __( 'Add New Research Project', 'coastal-lab' ),
				'edit_item'     => __( 'Edit Research Project', 'coastal-lab' ),
				'all_items'     => __( 'All Projects', 'coastal-lab' ),
			),
			'public'        => true,
			'has_archive'   => 'research',
			'rewrite'       => array(
				'slug'       => 'research',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-chart-area',
			'menu_position' => 20,
			'show_in_rest'  => true, // Block editor + REST API.
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		)
	);

	register_post_type(
		'team_member',
		array(
			'labels'        => array(
				'name'          => __( 'Team', 'coastal-lab' ),
				'singular_name' => __( 'Team Member', 'coastal-lab' ),
				'add_new_item'  => __( 'Add New Team Member', 'coastal-lab' ),
				'edit_item'     => __( 'Edit Team Member', 'coastal-lab' ),
				'all_items'     => __( 'All Team Members', 'coastal-lab' ),
			),
			'public'        => true,
			'has_archive'   => 'people',
			'rewrite'       => array(
				'slug'       => 'people',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-groups',
			'menu_position' => 21,
			'show_in_rest'  => true,
			// page-attributes gives editors a manual "Order" field for the team page.
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions' ),
		)
	);

	register_taxonomy(
		'research_area',
		array( 'research_project', 'team_member' ),
		array(
			'labels'            => array(
				'name'          => __( 'Research Areas', 'coastal-lab' ),
				'singular_name' => __( 'Research Area', 'coastal-lab' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'research-area' ),
		)
	);
}
add_action( 'init', 'coastal_lab_register_content_types' );

/**
 * Flush rewrite rules once when the theme is activated so /research and /people work.
 */
function coastal_lab_flush_rewrites() {
	coastal_lab_register_content_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'coastal_lab_flush_rewrites' );
