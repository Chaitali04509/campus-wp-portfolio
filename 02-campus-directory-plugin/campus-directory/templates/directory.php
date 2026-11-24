<?php
/**
 * Directory output. Themes can override by copying to
 * {theme}/campus-directory/directory.php.
 *
 * Available variables (from Campus_Directory_Shortcode::render):
 *
 * @var array[]              $people        Filtered people.
 * @var array<string,string> $departments   slug => name for the dropdown.
 * @var string               $query         Current search text.
 * @var string               $department    Current department slug.
 * @var string               $layout        'grid' | 'list'.
 * @var bool                 $show_search   Show search form.
 * @var bool                 $show_dept_dd  Show department dropdown.
 * @var bool                 $show_site_tag Show which site each person belongs to.
 *
 * @package Campus_Directory
 */

defined( 'ABSPATH' ) || exit;

$campus_directory_uid = wp_unique_id( 'cd-' );
?>
<div class="cd-directory cd-directory--<?php echo esc_attr( $layout ); ?>">
	<?php if ( $show_search ) : ?>
		<form class="cd-search" method="get" role="search" aria-label="<?php esc_attr_e( 'Search the directory', 'campus-directory' ); ?>">
			<div class="cd-search__field">
				<label for="<?php echo esc_attr( $campus_directory_uid ); ?>-q"><?php esc_html_e( 'Name or keyword', 'campus-directory' ); ?></label>
				<input type="search" id="<?php echo esc_attr( $campus_directory_uid ); ?>-q" name="cd_q" value="<?php echo esc_attr( $query ); ?>">
			</div>

			<?php if ( $show_dept_dd && $departments ) : ?>
				<div class="cd-search__field">
					<label for="<?php echo esc_attr( $campus_directory_uid ); ?>-dept"><?php esc_html_e( 'Department', 'campus-directory' ); ?></label>
					<select id="<?php echo esc_attr( $campus_directory_uid ); ?>-dept" name="cd_dept">
						<option value=""><?php esc_html_e( 'All departments', 'campus-directory' ); ?></option>
						<?php foreach ( $departments as $campus_directory_slug => $campus_directory_name ) : ?>
							<option value="<?php echo esc_attr( $campus_directory_slug ); ?>" <?php selected( $department, $campus_directory_slug ); ?>><?php echo esc_html( $campus_directory_name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<button type="submit" class="cd-search__submit"><?php esc_html_e( 'Search', 'campus-directory' ); ?></button>
			<?php if ( $query || ( $show_dept_dd && $department ) ) : ?>
				<a class="cd-search__reset" href="<?php echo esc_url( remove_query_arg( array( 'cd_q', 'cd_dept' ) ) ); ?>"><?php esc_html_e( 'Clear', 'campus-directory' ); ?></a>
			<?php endif; ?>
		</form>
	<?php endif; ?>

	<p class="cd-count" role="status">
		<?php
		echo esc_html(
			sprintf(
				/* translators: %d: number of people. */
				_n( '%d person', '%d people', count( $people ), 'campus-directory' ),
				count( $people )
			)
		);
		?>
	</p>

	<?php if ( ! $people ) : ?>
		<p class="cd-empty"><?php esc_html_e( 'No one matches that search. Try a shorter keyword or a different department.', 'campus-directory' ); ?></p>
	<?php else : ?>
		<ul class="cd-list">
			<?php foreach ( $people as $campus_directory_person ) : ?>
				<li class="cd-person">
					<?php if ( $campus_directory_person['photo'] ) : ?>
						<img class="cd-person__photo" src="<?php echo esc_url( $campus_directory_person['photo'] ); ?>" alt="" loading="lazy" width="96" height="96">
					<?php else : ?>
						<span class="cd-person__photo cd-person__photo--empty" aria-hidden="true"><?php echo esc_html( $campus_directory_person['initials'] ); ?></span>
					<?php endif; ?>

					<div class="cd-person__body">
						<h3 class="cd-person__name">
							<a href="<?php echo esc_url( $campus_directory_person['url'] ); ?>"><?php echo esc_html( $campus_directory_person['name'] ); ?></a>
						</h3>
						<?php if ( $campus_directory_person['job_title'] ) : ?>
							<p class="cd-person__title"><?php echo esc_html( $campus_directory_person['job_title'] ); ?></p>
						<?php endif; ?>

						<?php
						$campus_directory_meta = array_values( $campus_directory_person['departments'] );
						if ( $show_site_tag ) {
							$campus_directory_meta[] = $campus_directory_person['site_name'];
						}
						$campus_directory_meta = array_unique( array_filter( $campus_directory_meta ) );
						?>
						<?php if ( $campus_directory_meta ) : ?>
							<p class="cd-person__meta"><?php echo esc_html( implode( ' · ', $campus_directory_meta ) ); ?></p>
						<?php endif; ?>

						<ul class="cd-person__contact">
							<?php if ( is_email( $campus_directory_person['email'] ) ) : ?>
								<li><a href="<?php echo esc_url( 'mailto:' . antispambot( $campus_directory_person['email'] ) ); ?>"><?php echo esc_html( antispambot( $campus_directory_person['email'] ) ); ?></a></li>
							<?php endif; ?>
							<?php if ( $campus_directory_person['phone'] ) : ?>
								<li><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $campus_directory_person['phone'] ) ); ?>"><?php echo esc_html( $campus_directory_person['phone'] ); ?></a></li>
							<?php endif; ?>
							<?php if ( $campus_directory_person['office'] ) : ?>
								<li><?php echo esc_html( $campus_directory_person['office'] ); ?></li>
							<?php endif; ?>
						</ul>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
