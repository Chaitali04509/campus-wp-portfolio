<?php
/**
 * Template helpers. All output is escaped at the point of output.
 *
 * @package Coastal_Lab
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get a custom field, with or without ACF active.
 *
 * @param string   $name    Field name.
 * @param int|null $post_id Post ID (defaults to current post).
 * @return mixed
 */
function coastal_lab_field( $name, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( function_exists( 'get_field' ) ) {
		return get_field( $name, $post_id );
	}

	return get_post_meta( $post_id, $name, true );
}

/**
 * Status badge for a research project.
 *
 * @param int|null $post_id Post ID.
 */
function coastal_lab_project_status_badge( $post_id = null ) {
	$status = coastal_lab_field( 'project_status', $post_id );
	$labels = array(
		'active'    => __( 'Active', 'coastal-lab' ),
		'planned'   => __( 'Planned', 'coastal-lab' ),
		'completed' => __( 'Completed', 'coastal-lab' ),
	);

	if ( ! $status || ! isset( $labels[ $status ] ) ) {
		return;
	}

	printf(
		'<span class="badge badge--%1$s">%2$s</span>',
		esc_attr( $status ),
		esc_html( $labels[ $status ] )
	);
}

/**
 * Card summary: prefer the plain-language ACF summary, fall back to the excerpt.
 *
 * @param int|null $post_id Post ID.
 * @return string Unescaped text; caller escapes.
 */
function coastal_lab_card_summary( $post_id = null ) {
	$summary = coastal_lab_field( 'plain_summary', $post_id );
	return $summary ? $summary : get_the_excerpt( $post_id );
}

/**
 * Comma-separated research area links.
 *
 * @param int|null $post_id Post ID.
 */
function coastal_lab_research_areas( $post_id = null ) {
	$list = get_the_term_list( $post_id ? $post_id : get_the_ID(), 'research_area', '', ', ' );

	if ( $list && ! is_wp_error( $list ) ) {
		echo '<p class="meta meta--areas"><span class="screen-reader-text">' . esc_html__( 'Research areas:', 'coastal-lab' ) . ' </span>' . wp_kses_post( $list ) . '</p>';
	}
}

/**
 * Space-separated term slugs, used as data attributes for the client-side filter.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function coastal_lab_area_slugs( $post_id = null ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), 'research_area' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}

	return implode( ' ', wp_list_pluck( $terms, 'slug' ) );
}

/**
 * Accessible pagination wrapper.
 */
function coastal_lab_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => __( 'Previous', 'coastal-lab' ),
			'next_text'          => __( 'Next', 'coastal-lab' ),
			'screen_reader_text' => __( 'Posts navigation', 'coastal-lab' ),
		)
	);
}
