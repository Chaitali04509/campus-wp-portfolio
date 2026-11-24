<?php
/**
 * 404: give people a way forward instead of a dead end.
 *
 * @package Coastal_Lab
 */

get_header();
?>
<main id="main" class="site-main container container--narrow" tabindex="-1">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'We can\'t find that page', 'coastal-lab' ); ?></h1>
	</header>
	<p><?php esc_html_e( 'It may have moved during our recent site update. Try searching, or start from one of these:', 'coastal-lab' ); ?></p>
	<?php get_search_form(); ?>
	<ul>
		<li><a href="<?php echo esc_url( get_post_type_archive_link( 'research_project' ) ); ?>"><?php esc_html_e( 'Research projects', 'coastal-lab' ); ?></a></li>
		<li><a href="<?php echo esc_url( get_post_type_archive_link( 'team_member' ) ); ?>"><?php esc_html_e( 'People', 'coastal-lab' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'coastal-lab' ); ?></a></li>
	</ul>
</main>
<?php
get_footer();
