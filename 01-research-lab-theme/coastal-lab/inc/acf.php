<?php
/**
 * Advanced Custom Fields integration.
 *
 * Approach:
 *  - Core field groups are registered in PHP (acf_add_local_field_group) so they
 *    are version-controlled, code-reviewed, and identical on every site.
 *  - ACF JSON save/load points to /acf-json, so any group an editor builds in the
 *    UI is also written to disk and can be committed. (Use ACF > Field Groups > Sync.)
 *  - Everything works on the free version of ACF. The options page only registers
 *    when ACF PRO is present.
 *  - If ACF is deactivated, templates fall back to post meta (see template-tags.php)
 *    and admins get a notice instead of a fatal error.
 *
 * @package Coastal_Lab
 */

defined( 'ABSPATH' ) || exit;

/**
 * Save ACF JSON into the theme.
 *
 * @return string
 */
function coastal_lab_acf_json_save_point() {
	return COASTAL_LAB_DIR . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'coastal_lab_acf_json_save_point' );

/**
 * Load ACF JSON from the theme.
 *
 * @param string[] $paths Existing load paths.
 * @return string[]
 */
function coastal_lab_acf_json_load_point( $paths ) {
	$paths[] = COASTAL_LAB_DIR . '/acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'coastal_lab_acf_json_load_point' );

/**
 * Admin notice when ACF is missing.
 */
function coastal_lab_acf_missing_notice() {
	if ( class_exists( 'ACF' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		esc_html__( 'Coastal Lab: Advanced Custom Fields is not active. Project and team details will not be editable until it is activated.', 'coastal-lab' )
	);
}
add_action( 'admin_notices', 'coastal_lab_acf_missing_notice' );

/**
 * Register field groups.
 */
function coastal_lab_register_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Research project details.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_coastal_lab_project',
			'title'                 => __( 'Project Details', 'coastal-lab' ),
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'show_in_rest'          => 1,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'research_project',
					),
				),
			),
			'fields'                => array(
				array(
					'key'           => 'field_coastal_lab_project_status',
					'label'         => __( 'Status', 'coastal-lab' ),
					'name'          => 'project_status',
					'type'          => 'select',
					'choices'       => array(
						'active'    => __( 'Active', 'coastal-lab' ),
						'planned'   => __( 'Planned', 'coastal-lab' ),
						'completed' => __( 'Completed', 'coastal-lab' ),
					),
					'default_value' => 'active',
					'wrapper'       => array( 'width' => '33' ),
				),
				array(
					'key'            => 'field_coastal_lab_project_start',
					'label'          => __( 'Start date', 'coastal-lab' ),
					'name'           => 'start_date',
					'type'           => 'date_picker',
					'display_format' => 'F Y',
					'return_format'  => 'F Y',
					'wrapper'        => array( 'width' => '33' ),
				),
				array(
					'key'          => 'field_coastal_lab_project_funding',
					'label'        => __( 'Funding source', 'coastal-lab' ),
					'name'         => 'funding_source',
					'type'         => 'text',
					'instructions' => __( 'e.g. "National Science Foundation, Award #0000000"', 'coastal-lab' ),
					'wrapper'      => array( 'width' => '33' ),
				),
				array(
					'key'           => 'field_coastal_lab_project_pi',
					'label'         => __( 'Lead investigator', 'coastal-lab' ),
					'name'          => 'lead_investigator',
					'type'          => 'post_object',
					'post_type'     => array( 'team_member' ),
					'return_format' => 'id',
					'allow_null'    => 1,
					'ui'            => 1,
				),
				array(
					'key'           => 'field_coastal_lab_project_people',
					'label'         => __( 'Team on this project', 'coastal-lab' ),
					'name'          => 'project_team',
					'type'          => 'relationship',
					'post_type'     => array( 'team_member' ),
					'filters'       => array( 'search' ),
					'return_format' => 'id',
				),
				array(
					'key'          => 'field_coastal_lab_project_summary',
					'label'        => __( 'Plain-language summary', 'coastal-lab' ),
					'name'         => 'plain_summary',
					'type'         => 'textarea',
					'rows'         => 3,
					'maxlength'    => 280,
					'instructions' => __( 'One or two sentences a non-specialist can understand. Shown on cards and in search results.', 'coastal-lab' ),
				),
				array(
					'key'   => 'field_coastal_lab_project_link',
					'label' => __( 'External project site / dataset', 'coastal-lab' ),
					'name'  => 'external_link',
					'type'  => 'url',
				),
			),
		)
	);

	// Team member profile.
	acf_add_local_field_group(
		array(
			'key'             => 'group_coastal_lab_person',
			'title'           => __( 'Profile', 'coastal-lab' ),
			'position'        => 'acf_after_title',
			'label_placement' => 'top',
			'show_in_rest'    => 1,
			'location'        => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'team_member',
					),
				),
			),
			'fields'          => array(
				array(
					'key'           => 'field_coastal_lab_person_role',
					'label'         => __( 'Group', 'coastal-lab' ),
					'name'          => 'role_group',
					'type'          => 'select',
					'instructions'  => __( 'Controls which section of the People page this person appears in.', 'coastal-lab' ),
					'choices'       => coastal_lab_role_groups(),
					'default_value' => 'phd',
					'wrapper'       => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_coastal_lab_person_title',
					'label'   => __( 'Position title', 'coastal-lab' ),
					'name'    => 'position_title',
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_coastal_lab_person_pronouns',
					'label'   => __( 'Pronouns (optional)', 'coastal-lab' ),
					'name'    => 'pronouns',
					'type'    => 'text',
					'wrapper' => array( 'width' => '33' ),
				),
				array(
					'key'     => 'field_coastal_lab_person_email',
					'label'   => __( 'Email', 'coastal-lab' ),
					'name'    => 'email',
					'type'    => 'email',
					'wrapper' => array( 'width' => '33' ),
				),
				array(
					'key'     => 'field_coastal_lab_person_office',
					'label'   => __( 'Office', 'coastal-lab' ),
					'name'    => 'office',
					'type'    => 'text',
					'wrapper' => array( 'width' => '33' ),
				),
				array(
					'key'   => 'field_coastal_lab_person_interests',
					'label' => __( 'Research interests', 'coastal-lab' ),
					'name'  => 'research_interests',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'     => 'field_coastal_lab_person_website',
					'label'   => __( 'Personal website', 'coastal-lab' ),
					'name'    => 'website',
					'type'    => 'url',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_coastal_lab_person_orcid',
					'label'   => __( 'ORCID URL', 'coastal-lab' ),
					'name'    => 'orcid',
					'type'    => 'url',
					'wrapper' => array( 'width' => '50' ),
				),
			),
		)
	);
}
add_action( 'acf/init', 'coastal_lab_register_field_groups' );

/**
 * Site-wide lab settings (ACF PRO only).
 */
function coastal_lab_register_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Lab Settings', 'coastal-lab' ),
			'menu_slug'  => 'coastal-lab-settings',
			'capability' => 'edit_theme_options',
			'icon_url'   => 'dashicons-admin-site-alt3',
		)
	);
}
add_action( 'acf/init', 'coastal_lab_register_options_page' );

/**
 * Role groups, in the order they appear on the People page.
 *
 * @return array<string,string>
 */
function coastal_lab_role_groups() {
	return array(
		'pi'        => __( 'Principal Investigator', 'coastal-lab' ),
		'postdoc'   => __( 'Postdoctoral Researchers', 'coastal-lab' ),
		'phd'       => __( 'PhD Students', 'coastal-lab' ),
		'ms'        => __( 'Master\'s Students', 'coastal-lab' ),
		'undergrad' => __( 'Undergraduate Researchers', 'coastal-lab' ),
		'staff'     => __( 'Staff', 'coastal-lab' ),
		'alumni'    => __( 'Alumni', 'coastal-lab' ),
	);
}
