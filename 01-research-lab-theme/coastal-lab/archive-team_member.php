<?php
/**
 * People page, grouped by role (PI, postdocs, students, alumni).
 *
 * UX decision: visitors scan a lab roster by role ("who is the PI?", "how many
 * PhD students?"), so people are grouped under headings rather than shown as
 * one alphabetical grid. Group order is defined in coastal_lab_role_groups().
 *
 * @package Coastal_Lab
 */

get_header();

// Bucket the main query by role group. One query instead of seven.
$coastal_lab_groups = array_fill_keys( array_keys( coastal_lab_role_groups() ), array() );
while ( have_posts() ) {
	the_post();
	$coastal_lab_role = coastal_lab_field( 'role_group' );
	$coastal_lab_role = isset( $coastal_lab_groups[ $coastal_lab_role ] ) ? $coastal_lab_role : 'staff';

	$coastal_lab_groups[ $coastal_lab_role ][] = get_the_ID();
}
rewind_posts();
?>
<main id="main" class="site-main container" tabindex="-1">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'People', 'coastal-lab' ); ?></h1>
	</header>

	<?php if ( ! have_posts() ) : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

	<?php foreach ( coastal_lab_role_groups() as $coastal_lab_key => $coastal_lab_label ) : ?>
		<?php
		if ( empty( $coastal_lab_groups[ $coastal_lab_key ] ) ) {
			continue;
		}
		?>
		<section class="section" aria-labelledby="group-<?php echo esc_attr( $coastal_lab_key ); ?>">
			<h2 id="group-<?php echo esc_attr( $coastal_lab_key ); ?>"><?php echo esc_html( $coastal_lab_label ); ?></h2>
			<ul class="person-grid">
				<?php foreach ( $coastal_lab_groups[ $coastal_lab_key ] as $coastal_lab_person_id ) : ?>
					<li><?php get_template_part( 'template-parts/card', 'person', array( 'post_id' => $coastal_lab_person_id ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endforeach; ?>
</main>
<?php
get_footer();
