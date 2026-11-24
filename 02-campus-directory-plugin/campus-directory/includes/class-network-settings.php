<?php
/**
 * Network Admin → Settings → Campus Directory.
 *
 * Lets a network admin choose which sites appear in network-wide listings and
 * clear the directory cache. Network settings pages do not get the Settings API's
 * automatic saving, so the form posts to edit.php?action=... and is handled on
 * the network_admin_edit_{action} hook.
 *
 * @package Campus_Directory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Network settings screen.
 */
class Campus_Directory_Network_Settings {

	const SLUG   = 'campus-directory';
	const ACTION = 'campus_directory_save';
	const CAP    = 'manage_network_options';

	/**
	 * Hook registration.
	 */
	public static function register() {
		add_action( 'network_admin_menu', array( __CLASS__, 'add_page' ) );
		add_action( 'network_admin_edit_' . self::ACTION, array( __CLASS__, 'save' ) );
	}

	/**
	 * Add the submenu under Network Settings.
	 */
	public static function add_page() {
		add_submenu_page(
			'settings.php',
			__( 'Campus Directory', 'campus-directory' ),
			__( 'Campus Directory', 'campus-directory' ),
			self::CAP,
			self::SLUG,
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * Render the settings form.
	 */
	public static function render() {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'You do not have permission to manage network settings.', 'campus-directory' ) );
		}

		$selected = array_map( 'absint', (array) get_site_option( Campus_Directory_Network_Query::SITES_OPTION, array() ) );
		$sites    = get_sites(
			array(
				'number'   => 1000,
				'archived' => 0,
				'deleted'  => 0,
				'spam'     => 0,
			)
		);
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display flag set by our own redirect.
		$notice = isset( $_GET['cd_notice'] ) ? sanitize_key( wp_unslash( $_GET['cd_notice'] ) ) : '';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Campus Directory', 'campus-directory' ); ?></h1>

			<?php if ( 'saved' === $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved. Directory cache cleared.', 'campus-directory' ); ?></p></div>
			<?php elseif ( 'cleared' === $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Directory cache cleared.', 'campus-directory' ); ?></p></div>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( network_admin_url( 'edit.php?action=' . self::ACTION ) ); ?>">
				<?php wp_nonce_field( self::ACTION ); ?>

				<h2><?php esc_html_e( 'Sites included in the network directory', 'campus-directory' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Leave everything unchecked to include all public sites automatically (new sites are picked up without changing this page).', 'campus-directory' ); ?>
				</p>

				<fieldset>
					<legend class="screen-reader-text"><?php esc_html_e( 'Sites', 'campus-directory' ); ?></legend>
					<?php foreach ( $sites as $site ) : ?>
						<?php $details = get_site( $site->blog_id ); ?>
						<label style="display:block;margin:.35em 0">
							<input type="checkbox" name="cd_sites[]" value="<?php echo esc_attr( $site->blog_id ); ?>" <?php checked( in_array( (int) $site->blog_id, $selected, true ) ); ?>>
							<?php echo esc_html( $details->blogname ); ?>
							<code><?php echo esc_html( $details->domain . $details->path ); ?></code>
							<?php if ( ! $site->public ) : ?>
								<em>(<?php esc_html_e( 'not public', 'campus-directory' ); ?>)</em>
							<?php endif; ?>
						</label>
					<?php endforeach; ?>
				</fieldset>

				<p class="submit">
					<?php submit_button( __( 'Save settings', 'campus-directory' ), 'primary', 'cd_save', false ); ?>
					<?php submit_button( __( 'Clear cache only', 'campus-directory' ), 'secondary', 'cd_clear', false ); ?>
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle the form post.
	 */
	public static function save() {
		check_admin_referer( self::ACTION );

		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'You do not have permission to manage network settings.', 'campus-directory' ), 403 );
		}

		$notice = 'cleared';

		if ( isset( $_POST['cd_save'] ) ) {
			$sites = isset( $_POST['cd_sites'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['cd_sites'] ) ) : array();
			// Only keep IDs that are real sites on this network.
			$sites = array_values( array_filter( $sites, static fn( $id ) => $id && get_site( $id ) ) );
			update_site_option( Campus_Directory_Network_Query::SITES_OPTION, $sites );
			$notice = 'saved';
		}

		Campus_Directory_Network_Query::bump_cache_version();

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'      => self::SLUG,
					'cd_notice' => $notice,
				),
				network_admin_url( 'settings.php' )
			)
		);
		exit;
	}
}
