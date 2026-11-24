<?php
/**
 * REST endpoint: GET /wp-json/campus-directory/v1/people
 *
 * Lets other sites, a static department page, or a future headless front end
 * read the network directory. Only fields already public on the directory page
 * are returned.
 *
 * @package Campus_Directory
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST controller.
 */
class Campus_Directory_Rest {

	/**
	 * Hook registration.
	 */
	public static function register() {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	/**
	 * Register routes.
	 */
	public static function routes() {
		register_rest_route(
			'campus-directory/v1',
			'/people',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_people' ),
				'permission_callback' => '__return_true', // Public directory data.
				'args'                => array(
					'scope'      => array(
						'type'    => 'string',
						'enum'    => array( 'network', 'site' ),
						'default' => 'network',
					),
					'department' => array(
						'type'              => 'string',
						// Wrapped: REST passes ($value, $request, $param), and sanitize_title()
						// would treat $request as its fallback title for empty values.
						'sanitize_callback' => static fn( $value ) => sanitize_title( (string) $value ),
						'default'           => '',
					),
					'search'     => array(
						'type'              => 'string',
						'sanitize_callback' => static fn( $value ) => sanitize_text_field( (string) $value ),
						'default'           => '',
					),
				),
			)
		);
	}

	/**
	 * Callback.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function get_people( WP_REST_Request $request ) {
		$people = Campus_Directory_Network_Query::get_people(
			array(
				'scope'      => $request['scope'],
				'department' => $request['department'],
				'search'     => $request['search'],
			)
		);

		// Drop internal keys before returning.
		$people = array_map(
			static function ( $p ) {
				unset( $p['sort_name'], $p['initials'] );
				return $p;
			},
			$people
		);

		$response = rest_ensure_response( $people );
		$response->header( 'X-WP-Total', (string) count( $people ) );
		return $response;
	}
}
