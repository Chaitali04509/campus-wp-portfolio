<?php
/**
 * Person post type and department taxonomy.
 *
 * Registered on every site in the network so each department/lab site manages
 * its own people with the same content model.
 *
 * @package Campus_Directory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Content types.
 */
class Campus_Directory_Post_Type {

	const POST_TYPE = 'cd_person';
	const TAXONOMY  = 'cd_department';

	/**
	 * Hook registration.
	 */
	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_types' ) );
		add_filter( 'enter_title_here', array( __CLASS__, 'title_placeholder' ), 10, 2 );
	}

	/**
	 * Register CPT + taxonomy.
	 */
	public static function register_types() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'       => array(
					'name'          => __( 'Directory', 'campus-directory' ),
					'singular_name' => __( 'Person', 'campus-directory' ),
					'add_new_item'  => __( 'Add Person', 'campus-directory' ),
					'edit_item'     => __( 'Edit Person', 'campus-directory' ),
					'all_items'     => __( 'All People', 'campus-directory' ),
					'search_items'  => __( 'Search People', 'campus-directory' ),
				),
				'public'       => true,
				'has_archive'  => false, // The shortcode page is the directory.
				'rewrite'      => array(
					'slug'       => 'directory',
					'with_front' => false,
				),
				'menu_icon'    => 'dashicons-id-alt',
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			)
		);

		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Departments / Units', 'campus-directory' ),
					'singular_name' => __( 'Department', 'campus-directory' ),
				),
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'directory-department' ),
			)
		);
	}

	/**
	 * Clearer title placeholder for editors.
	 *
	 * @param string  $text Default placeholder.
	 * @param WP_Post $post Current post.
	 * @return string
	 */
	public static function title_placeholder( $text, $post ) {
		if ( self::POST_TYPE === $post->post_type ) {
			return __( 'Full name (e.g. Dr. Jordan Reyes)', 'campus-directory' );
		}
		return $text;
	}
}
