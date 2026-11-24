<?php
/**
 * Person fields.
 *
 * Two layers:
 *  1. register_post_meta(): the source of truth. Typed, sanitized, exposed to REST,
 *     and works with or without ACF.
 *  2. ACF local field group: a friendly editing UI over the same meta keys
 *     (ACF stores simple fields as ordinary post meta under the field name).
 *
 * @package Campus_Directory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions and registration.
 */
class Campus_Directory_Fields {

	/**
	 * Hook registration.
	 */
	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_meta' ) );
		add_action( 'acf/init', array( __CLASS__, 'register_acf_group' ) );
		add_action( 'admin_notices', array( __CLASS__, 'acf_notice' ) );
	}

	/**
	 * Field schema: meta key => [label, type, sanitize callback, ACF field type].
	 *
	 * @return array<string, array{label: string, sanitize: callable, acf: string, width: string}>
	 */
	public static function schema() {
		return array(
			'cd_job_title'   => array(
				'label'    => __( 'Job title', 'campus-directory' ),
				'sanitize' => 'sanitize_text_field',
				'acf'      => 'text',
				'width'    => '50',
			),
			'cd_email'       => array(
				'label'    => __( 'Email', 'campus-directory' ),
				'sanitize' => 'sanitize_email',
				'acf'      => 'email',
				'width'    => '50',
			),
			'cd_phone'       => array(
				'label'    => __( 'Phone', 'campus-directory' ),
				'sanitize' => array( __CLASS__, 'sanitize_phone' ),
				'acf'      => 'text',
				'width'    => '33',
			),
			'cd_office'      => array(
				'label'    => __( 'Office / building', 'campus-directory' ),
				'sanitize' => 'sanitize_text_field',
				'acf'      => 'text',
				'width'    => '33',
			),
			'cd_profile_url' => array(
				'label'    => __( 'External profile URL (optional)', 'campus-directory' ),
				'sanitize' => 'esc_url_raw',
				'acf'      => 'url',
				'width'    => '33',
			),
			'cd_expertise'   => array(
				'label'    => __( 'Areas of expertise', 'campus-directory' ),
				'sanitize' => 'sanitize_textarea_field',
				'acf'      => 'textarea',
				'width'    => '100',
			),
		);
	}

	/**
	 * Register meta keys so they are typed, sanitized and REST-visible.
	 */
	public static function register_meta() {
		foreach ( self::schema() as $key => $field ) {
			register_post_meta(
				Campus_Directory_Post_Type::POST_TYPE,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => $field['sanitize'],
					'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
						return current_user_can( 'edit_post', $post_id );
					},
				)
			);
		}
	}

	/**
	 * ACF UI over the same meta keys.
	 */
	public static function register_acf_group() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$fields = array();
		foreach ( self::schema() as $key => $field ) {
			$fields[] = array(
				'key'     => 'field_' . $key,
				'name'    => $key,
				'label'   => $field['label'],
				'type'    => $field['acf'],
				'rows'    => 'textarea' === $field['acf'] ? 3 : null,
				'wrapper' => array( 'width' => $field['width'] ),
			);
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_campus_directory_person',
				'title'    => __( 'Directory Details', 'campus-directory' ),
				'fields'   => $fields,
				'position' => 'acf_after_title',
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => Campus_Directory_Post_Type::POST_TYPE,
						),
					),
				),
			)
		);
	}

	/**
	 * Tell editors why fields are missing, only on directory screens.
	 */
	public static function acf_notice() {
		if ( class_exists( 'ACF' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || Campus_Directory_Post_Type::POST_TYPE !== $screen->post_type ) {
			return;
		}
		printf(
			'<div class="notice notice-info"><p>%s</p></div>',
			esc_html__( 'Campus Directory: activate Advanced Custom Fields to edit title, email, phone and office on this screen. Values can still be set through the REST API.', 'campus-directory' )
		);
	}

	/**
	 * Keep digits, spaces, and common phone punctuation.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	public static function sanitize_phone( $value ) {
		return trim( preg_replace( '/[^0-9+().\-\sx]/i', '', (string) $value ) );
	}
}
