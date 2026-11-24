<?php
/**
 * Aggregate people across the network.
 *
 * Calling switch_to_blog() is expensive (it swaps globals and the object cache group),
 * so the full, flattened list is cached as a network-wide transient. Filtering
 * by department/search happens in PHP on the cached list. That is fine for the
 * expected scale (hundreds of people), and it keeps the number of cache keys small.
 *
 * Cache invalidation uses a version number stored in a site option. Any change to
 * a person on any site bumps the version, so every cached key becomes stale at once.
 *
 * @package Campus_Directory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Network-wide directory query.
 */
class Campus_Directory_Network_Query {

	const CACHE_VERSION_OPTION = 'campus_directory_cache_version';
	const SITES_OPTION         = 'campus_directory_sites';
	const CACHE_TTL            = HOUR_IN_SECONDS;
	const MAX_PER_SITE         = 500;

	/**
	 * Hook registration: invalidate cache whenever directory content changes.
	 */
	public static function register() {
		add_action( 'save_post_' . Campus_Directory_Post_Type::POST_TYPE, array( __CLASS__, 'bump_cache_version' ) );
		add_action( 'deleted_post', array( __CLASS__, 'maybe_bump_on_delete' ), 10, 2 );
		add_action( 'set_object_terms', array( __CLASS__, 'maybe_bump_on_terms' ), 10, 4 );
		add_action( 'updated_post_meta', array( __CLASS__, 'maybe_bump_on_meta' ), 10, 3 );
		add_action( 'edited_' . Campus_Directory_Post_Type::TAXONOMY, array( __CLASS__, 'bump_cache_version' ) );
	}

	/**
	 * Get people, optionally filtered.
	 *
	 * Supported args: 'scope' ('network' = all included sites, 'site' = current
	 * site only), 'department' (slug) and 'search' (matches name, title, expertise).
	 *
	 * @param array $args Query args (see above).
	 * @return array[] List of person arrays (see build_person()).
	 */
	public static function get_people( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'scope'      => 'network',
				'department' => '',
				'search'     => '',
			)
		);

		$site_ids = ( 'site' === $args['scope'] || ! is_multisite() )
			? array( get_current_blog_id() )
			: self::included_site_ids();

		$people = self::get_cached_people( $site_ids );

		if ( '' !== $args['department'] ) {
			$dept   = sanitize_title( $args['department'] );
			$people = array_filter(
				$people,
				static fn( $p ) => isset( $p['departments'][ $dept ] )
			);
		}

		if ( '' !== $args['search'] ) {
			$needle = mb_strtolower( $args['search'] );
			$people = array_filter(
				$people,
				static function ( $p ) use ( $needle ) {
					$haystack = mb_strtolower( implode( ' ', array( $p['name'], $p['job_title'], $p['expertise'], implode( ' ', $p['departments'] ) ) ) );
					return str_contains( $haystack, $needle );
				}
			);
		}

		return array_values( $people );
	}

	/**
	 * All departments present in a result set, for building the filter dropdown.
	 *
	 * @param array[] $people People from get_people().
	 * @return array<string,string> slug => name, sorted by name.
	 */
	public static function departments_in( array $people ) {
		$departments = array();
		foreach ( $people as $person ) {
			$departments += $person['departments'];
		}
		asort( $departments, SORT_NATURAL | SORT_FLAG_CASE );
		return $departments;
	}

	/**
	 * Site IDs included in network listings (network setting; empty = all public sites).
	 *
	 * @return int[]
	 */
	public static function included_site_ids() {
		$configured = array_filter( array_map( 'absint', (array) get_site_option( self::SITES_OPTION, array() ) ) );
		if ( $configured ) {
			return $configured;
		}

		return array_map(
			'intval',
			get_sites(
				array(
					'fields'   => 'ids',
					'public'   => 1,
					'archived' => 0,
					'spam'     => 0,
					'deleted'  => 0,
					'number'   => 1000,
				)
			)
		);
	}

	/**
	 * Read-through cache around the expensive cross-site query.
	 *
	 * @param int[] $site_ids Sites to include.
	 * @return array[]
	 */
	private static function get_cached_people( array $site_ids ) {
		sort( $site_ids );
		$version = (int) get_site_option( self::CACHE_VERSION_OPTION, 1 );
		$key     = 'cd_people_' . $version . '_' . md5( implode( ',', $site_ids ) );

		$cached = is_multisite() ? get_site_transient( $key ) : get_transient( $key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$people = array();
		foreach ( $site_ids as $site_id ) {
			$people = array_merge( $people, self::query_site( $site_id ) );
		}

		usort(
			$people,
			static fn( $a, $b ) => strnatcasecmp( $a['sort_name'], $b['sort_name'] )
		);

		if ( is_multisite() ) {
			set_site_transient( $key, $people, self::CACHE_TTL );
		} else {
			set_transient( $key, $people, self::CACHE_TTL );
		}

		return $people;
	}

	/**
	 * Query one site's people.
	 *
	 * @param int $site_id Blog ID.
	 * @return array[]
	 */
	private static function query_site( $site_id ) {
		$switched = is_multisite() && get_current_blog_id() !== $site_id;
		if ( $switched ) {
			switch_to_blog( $site_id );
		}

		try {
			$site_name = get_bloginfo( 'name' );
			$posts     = get_posts(
				array(
					'post_type'        => Campus_Directory_Post_Type::POST_TYPE,
					'post_status'      => 'publish',
					'numberposts'      => self::MAX_PER_SITE,
					'orderby'          => 'title',
					'order'            => 'ASC',
					'suppress_filters' => false,
				)
			);

			$people = array();
			foreach ( $posts as $post ) {
				$people[] = self::build_person( $post, $site_id, $site_name );
			}
			return $people;
		} finally {
			// Always restore, even if a query or filter throws.
			if ( $switched ) {
				restore_current_blog();
			}
		}
	}

	/**
	 * Flatten a person into plain data while still switched to their site, so
	 * permalinks and image URLs resolve against the correct blog.
	 *
	 * @param WP_Post $post      Person post.
	 * @param int     $site_id   Blog ID.
	 * @param string  $site_name Blog name.
	 * @return array
	 */
	private static function build_person( WP_Post $post, $site_id, $site_name ) {
		$terms       = get_the_terms( $post, Campus_Directory_Post_Type::TAXONOMY );
		$departments = ( $terms && ! is_wp_error( $terms ) ) ? wp_list_pluck( $terms, 'name', 'slug' ) : array();
		$profile_url = get_post_meta( $post->ID, 'cd_profile_url', true );
		$name        = get_the_title( $post );

		return array(
			'id'          => $post->ID,
			'site_id'     => $site_id,
			'site_name'   => $site_name,
			'name'        => $name,
			'sort_name'   => self::sort_name( $name ),
			'initials'    => self::initials( $name ),
			'job_title'   => (string) get_post_meta( $post->ID, 'cd_job_title', true ),
			'email'       => (string) get_post_meta( $post->ID, 'cd_email', true ),
			'phone'       => (string) get_post_meta( $post->ID, 'cd_phone', true ),
			'office'      => (string) get_post_meta( $post->ID, 'cd_office', true ),
			'expertise'   => (string) get_post_meta( $post->ID, 'cd_expertise', true ),
			'url'         => $profile_url ? $profile_url : get_permalink( $post ),
			'photo'       => (string) get_the_post_thumbnail_url( $post, 'medium' ),
			'departments' => $departments,
		);
	}

	/**
	 * Strip honorifics: "Dr. Jordan Reyes" -> "Jordan Reyes".
	 *
	 * @param string $name Display name.
	 * @return string
	 */
	private static function strip_honorific( $name ) {
		return (string) preg_replace( '/^(dr|prof|professor|mr|ms|mrs|mx)\.?\s+/i', '', trim( $name ) );
	}

	/**
	 * "Dr. Jordan Reyes" -> "JR" for the photo placeholder.
	 *
	 * @param string $name Display name.
	 * @return string
	 */
	private static function initials( $name ) {
		$parts = preg_split( '/\s+/', self::strip_honorific( $name ), -1, PREG_SPLIT_NO_EMPTY );
		if ( ! $parts ) {
			return '';
		}
		$first = mb_substr( $parts[0], 0, 1 );
		$last  = count( $parts ) > 1 ? mb_substr( end( $parts ), 0, 1 ) : '';
		return mb_strtoupper( $first . $last );
	}

	/**
	 * "Dr. Jordan Reyes" -> "Reyes Jordan" so lists sort by last name.
	 *
	 * @param string $name Display name.
	 * @return string
	 */
	private static function sort_name( $name ) {
		$parts = preg_split( '/\s+/', self::strip_honorific( $name ) );
		$last  = array_pop( $parts );
		return trim( $last . ' ' . implode( ' ', $parts ) );
	}

	/**
	 * Invalidate every cached listing across the network.
	 */
	public static function bump_cache_version() {
		$version = (int) get_site_option( self::CACHE_VERSION_OPTION, 1 );
		update_site_option( self::CACHE_VERSION_OPTION, $version + 1 );
	}

	/**
	 * Bump when a person is deleted.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public static function maybe_bump_on_delete( $post_id, $post ) {
		if ( $post instanceof WP_Post && Campus_Directory_Post_Type::POST_TYPE === $post->post_type ) {
			self::bump_cache_version();
		}
	}

	/**
	 * Bump when a person's departments change.
	 *
	 * @param int    $object_id Object ID.
	 * @param array  $terms     Terms.
	 * @param array  $tt_ids    Term taxonomy IDs.
	 * @param string $taxonomy  Taxonomy.
	 */
	public static function maybe_bump_on_terms( $object_id, $terms, $tt_ids, $taxonomy ) {
		if ( Campus_Directory_Post_Type::TAXONOMY === $taxonomy ) {
			self::bump_cache_version();
		}
	}

	/**
	 * Bump when directory meta changes outside a full post save (e.g. REST or ACF).
	 *
	 * @param int    $meta_id   Meta ID.
	 * @param int    $object_id Post ID.
	 * @param string $meta_key  Meta key.
	 */
	public static function maybe_bump_on_meta( $meta_id, $object_id, $meta_key ) {
		if ( str_starts_with( $meta_key, 'cd_' ) && Campus_Directory_Post_Type::POST_TYPE === get_post_type( $object_id ) ) {
			self::bump_cache_version();
		}
	}
}
