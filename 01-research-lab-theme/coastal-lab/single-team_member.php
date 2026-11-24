<?php
/**
 * Single team member profile.
 *
 * @package Coastal_Lab
 */

get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		$coastal_lab_title     = coastal_lab_field( 'position_title' );
		$coastal_lab_pronouns  = coastal_lab_field( 'pronouns' );
		$coastal_lab_email     = coastal_lab_field( 'email' );
		$coastal_lab_office    = coastal_lab_field( 'office' );
		$coastal_lab_interests = coastal_lab_field( 'research_interests' );
		$coastal_lab_website   = coastal_lab_field( 'website' );
		$coastal_lab_orcid     = coastal_lab_field( 'orcid' );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'container profile' ); ?>>
			<div class="profile__photo">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'coastal-lab-portrait', array( 'alt' => get_the_title() ) );
				} else {
					echo '<div class="avatar-placeholder" aria-hidden="true"></div>';
				}
				?>
			</div>

			<div class="profile__body">
				<header>
					<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
					<?php if ( $coastal_lab_title || $coastal_lab_pronouns ) : ?>
						<p class="profile__title">
							<?php echo esc_html( $coastal_lab_title ); ?>
							<?php if ( $coastal_lab_pronouns ) : ?>
								<span class="profile__pronouns">(<?php echo esc_html( $coastal_lab_pronouns ); ?>)</span>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</header>

				<ul class="profile__contact">
					<?php if ( $coastal_lab_email && is_email( $coastal_lab_email ) ) : ?>
						<li><a href="<?php echo esc_url( 'mailto:' . antispambot( $coastal_lab_email ) ); ?>"><?php echo esc_html( antispambot( $coastal_lab_email ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $coastal_lab_office ) : ?>
						<li><?php echo esc_html( $coastal_lab_office ); ?></li>
					<?php endif; ?>
					<?php if ( $coastal_lab_website ) : ?>
						<li><a href="<?php echo esc_url( $coastal_lab_website ); ?>"><?php esc_html_e( 'Personal website', 'coastal-lab' ); ?></a></li>
					<?php endif; ?>
					<?php if ( $coastal_lab_orcid ) : ?>
						<li><a href="<?php echo esc_url( $coastal_lab_orcid ); ?>">ORCID</a></li>
					<?php endif; ?>
				</ul>

				<?php if ( $coastal_lab_interests ) : ?>
					<h2><?php esc_html_e( 'Research interests', 'coastal-lab' ); ?></h2>
					<p><?php echo esc_html( $coastal_lab_interests ); ?></p>
				<?php endif; ?>

				<div class="entry-content"><?php the_content(); ?></div>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
