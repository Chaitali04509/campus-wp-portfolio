<?php
/**
 * Playground demo, step 2 of 2: sample content.
 *
 * Mirrors bin/setup.sh so the online demo matches the local Docker environment.
 *
 * @package Campus_WP_Portfolio
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- standalone demo script.

require_once '/wordpress/wp-load.php';

$network = get_network();
$lab_id  = get_blog_id_from_url( $network->domain, trailingslashit( $network->path . 'coastal-lab' ) );
$dept_id = get_blog_id_from_url( $network->domain, trailingslashit( $network->path . 'marine-sciences' ) );

/**
 * Pretty permalinks; rules regenerate on the next request.
 */
function demo_permalinks() {
	update_option( 'permalink_structure', '/%postname%/' );
	delete_option( 'rewrite_rules' );
}

/**
 * Create a published post and return its ID.
 *
 * @param string $type  Post type.
 * @param string $title Title.
 * @param array  $meta  Post meta.
 * @param string $body  Content.
 * @return int
 */
function demo_post( $type, $title, $meta = array(), $body = '' ) {
	return (int) wp_insert_post(
		array(
			'post_type'    => $type,
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_content' => $body,
			'meta_input'   => $meta,
		)
	);
}

demo_permalinks();

// ---------- Lab site ----------
switch_to_blog( $lab_id );
demo_permalinks();

if ( ! get_posts( array( 'post_type' => 'research_project', 'numberposts' => 1 ) ) ) {
	wp_insert_term( 'Salt Marsh Restoration', 'research_area', array( 'slug' => 'salt-marsh-restoration' ) );
	wp_insert_term( 'Coastal Sensing', 'research_area', array( 'slug' => 'coastal-sensing' ) );

	$pi  = demo_post(
		'team_member',
		'Dr. Avery Lin',
		array(
			'role_group'         => 'pi',
			'position_title'     => 'Associate Professor & Lab Director',
			'email'              => 'a.lin@example.edu',
			'office'             => 'Marine Science Center 210',
			'research_interests' => 'Salt marsh ecology, sea-level rise, restoration monitoring',
		),
		'Avery leads the lab\'s work on marsh resilience.'
	);
	$stu = demo_post(
		'team_member',
		'Sam Okafor',
		array(
			'role_group'     => 'phd',
			'position_title' => 'PhD Candidate',
			'pronouns'       => 'they/them',
		)
	);
	demo_post(
		'team_member',
		'Priya Natarajan',
		array(
			'role_group'     => 'undergrad',
			'position_title' => 'Undergraduate Researcher',
		)
	);
	wp_set_object_terms( $pi, 'salt-marsh-restoration', 'research_area' );

	$p1 = demo_post(
		'research_project',
		'Living Shorelines Monitoring',
		array(
			'project_status'    => 'active',
			'funding_source'    => 'Sample Foundation Grant',
			'plain_summary'     => 'We measure how restored marsh edges hold up against storm surge.',
			'lead_investigator' => $pi,
			'project_team'      => array( (string) $pi, (string) $stu ),
		),
		'Five-year monitoring of restored marsh edges along the outer harbor.'
	);
	$p2 = demo_post(
		'research_project',
		'Low-Cost Tide Sensors',
		array(
			'project_status' => 'completed',
			'plain_summary'  => 'Open-hardware water level sensors that community groups can build for under $60.',
		)
	);
	wp_set_object_terms( $p1, 'salt-marsh-restoration', 'research_area' );
	wp_set_object_terms( $p2, 'coastal-sensing', 'research_area' );

	// Directory people (Campus Directory plugin).
	wp_insert_term( 'Marine Sciences', 'cd_department', array( 'slug' => 'marine-sciences' ) );
	$d1 = demo_post(
		'cd_person',
		'Dr. Jordan Reyes',
		array(
			'cd_job_title' => 'Professor',
			'cd_email'     => 'j.reyes@example.edu',
			'cd_phone'     => '(555) 010-0101',
			'cd_office'    => 'Marine Science Center 118',
		)
	);
	$d2 = demo_post(
		'cd_person',
		'Dana Whitfield',
		array(
			'cd_job_title' => 'Graduate Program Coordinator',
			'cd_email'     => 'd.whitfield@example.edu',
		)
	);
	wp_set_object_terms( $d1, 'marine-sciences', 'cd_department' );
	wp_set_object_terms( $d2, 'marine-sciences', 'cd_department' );

	// Pages.
	$home = demo_post( 'page', 'Coastal Ecology Lab', array(), '<!-- wp:paragraph --><p>We study how salt marshes and shorelines respond to sea-level rise, and we build tools communities can use to monitor them.</p><!-- /wp:paragraph -->' );
	$dir  = demo_post( 'page', 'Directory', array(), '<!-- wp:shortcode -->[campus_directory]<!-- /wp:shortcode -->' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );

	// Primary menu.
	$menu_id = wp_create_nav_menu( 'Main' );
	if ( ! is_wp_error( $menu_id ) ) {
		$items = array(
			'Research' => home_url( '/research/' ),
			'People'   => home_url( '/people/' ),
		);
		foreach ( $items as $label => $url ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => $label,
					'menu-item-url'    => $url,
					'menu-item-status' => 'publish',
					'menu-item-type'   => 'custom',
				)
			);
		}
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-object-id' => $dir,
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
		set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu_id ) );
	}

	// Footer: lab contact details instead of WordPress's default widgets.
	update_option(
		'widget_text',
		array(
			2              => array(
				'title' => 'Contact',
				'text'  => 'Coastal Ecology Lab · Marine Science Center, Room 200 · coastal-lab@example.edu',
			),
			'_multiwidget' => 1,
		)
	);
	update_option(
		'sidebars_widgets',
		array(
			'wp_inactive_widgets' => array(),
			'footer-1'            => array( 'text-2' ),
			'array_version'       => 3,
		)
	);
}
restore_current_blog();

// ---------- Department site: one person, to show network-wide aggregation ----------
switch_to_blog( $dept_id );
demo_permalinks();
if ( ! get_posts( array( 'post_type' => 'cd_person', 'numberposts' => 1 ) ) ) {
	wp_insert_term( 'Administration', 'cd_department', array( 'slug' => 'administration' ) );
	$d3 = demo_post(
		'cd_person',
		'Morgan Ellis',
		array(
			'cd_job_title' => 'Department Administrator',
			'cd_email'     => 'm.ellis@example.edu',
		)
	);
	wp_set_object_terms( $d3, 'administration', 'cd_department' );
}
restore_current_blog();
