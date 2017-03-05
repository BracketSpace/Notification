<?php
/**
 * Extensions class
 */

namespace Notification;

use Notification\Singleton;

class Extensions extends Singleton {

	/**
	 * Extensions list
	 * @var array
	 */
	private $extensions = array();

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'register_page' ) );

	}

	/**
	 * Register Extensions page under plugin's menu
	 * @return void
	 */
	public function register_page() {

		$this->page_hook = add_submenu_page(
			'edit.php?post_type=notification',
	        __( 'Extensions', 'notification' ),
	        __( 'Extensions', 'notification' ),
	        'manage_options',
	        'extensions',
	        array( $this, 'extensions_page' )
	    );

	    add_action( 'load-' . $this->page_hook, array( $this, 'load_extensions' ) );

	}

	/**
	 * Load extensions
	 * If you want to get your extension listed please send a message via
	 * https://notification.underdev.it/contact/ contact form
	 * @return void
	 */
	public function load_extensions() {

		include( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$this->extensions[] = array(
			'wporg'    => plugins_api( 'plugin_information', array( 'slug' => 'notification-bbpress' ) ),
			'url'      => self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=notification-bbpress&amp;TB_iframe=true&amp;width=600&amp;height=550' ),
			'official' => true,
			'slug'     => 'notification-bbpress',
			'name'     => 'bbPress',
			'desc'     => __( 'Triggers for bbPress: Forums, Topics and Replies.', 'notification' ),
			'author'   => 'underDEV',
			'icon'     => '//ps.w.org/notification-bbpress/assets/icon-256x256.png',
		);

	}

	/**
	 * Extensions page output
	 * @return void
	 */
	public function extensions_page() {

		add_thickbox();

		echo '<div class="wrap notification-extensions">';

			echo '<h1>' . __( 'Extensions', 'notification' ) . '</h1>';

			echo '<div id="the-list">';

				foreach ( $this->extensions as $extension ) {
					$this->render_extension_box( $extension );
				}

				$this->render_promo_box();

			echo '</div>';

		echo '</div>';

	}

	protected function render_extension_box( $ext ) {

		// fragment forked from wp-admin/includes/class-wp-plugin-install-list-table.php

		if ( isset( $ext['wporg'] ) && ! is_wp_error( $ext['wporg'] ) && ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) ) {
			$status = install_plugin_install_status( $ext['wporg'] );

			switch ( $status['status'] ) {
				case 'install':
					if ( $status['url'] ) {
						/* translators: 1: Plugin name and version. */
						$action_button = '<a class="install-now button" data-slug="' . esc_attr( $plugin['slug'] ) . '" href="' . esc_url( $status['url'] ) . '" aria-label="' . esc_attr( sprintf( __( 'Install %s now' ), $name ) ) . '" data-name="' . esc_attr( $name ) . '">' . __( 'Install Now' ) . '</a>';
					}
					break;

				case 'update_available':
					if ( $status['url'] ) {
						/* translators: 1: Plugin name and version */
						$action_button = '<a class="update-now button aria-button-if-js" data-plugin="' . esc_attr( $status['file'] ) . '" data-slug="' . esc_attr( $plugin['slug'] ) . '" href="' . esc_url( $status['url'] ) . '" aria-label="' . esc_attr( sprintf( __( 'Update %s now' ), $name ) ) . '" data-name="' . esc_attr( $name ) . '">' . __( 'Update Now' ) . '</a>';
					}
					break;

				case 'latest_installed':
				case 'newer_installed':
					if ( is_plugin_active( $status['file'] ) ) {
						$action_button = '<button type="button" class="button button-disabled" disabled="disabled">' . _x( 'Active', 'plugin' ) . '</button>';
					} elseif ( current_user_can( 'activate_plugins' ) ) {
						$button_text  = __( 'Activate' );
						/* translators: %s: Plugin name */
						$button_label = _x( 'Activate %s', 'plugin' );
						$activate_url = add_query_arg( array(
							'_wpnonce'    => wp_create_nonce( 'activate-plugin_' . $status['file'] ),
							'action'      => 'activate',
							'plugin'      => $status['file'],
						), network_admin_url( 'plugins.php' ) );

						if ( is_network_admin() ) {
							$button_text  = __( 'Network Activate' );
							/* translators: %s: Plugin name */
							$button_label = _x( 'Network Activate %s', 'plugin' );
							$activate_url = add_query_arg( array( 'networkwide' => 1 ), $activate_url );
						}

						$action_button = sprintf(
							'<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
							esc_url( $activate_url ),
							esc_attr( sprintf( $button_label, $ext['name'] ) ),
							$button_text
						);
					} else {
						$action_button = '<button type="button" class="button button-disabled" disabled="disabled">' . _x( 'Installed', 'plugin' ) . '</button>';
					}
					break;
			}
		} else {

			$action_button = '<a href="' . esc_url( $ext['url'] ) . '" class="button" target="_blank">' . __( 'More Details' ) . '</a>';

		}

	?>

		<div class="plugin-card plugin-card-<?php echo $ext['slug']; ?>">
			<div class="plugin-card-top">
				<div class="name column-name">
					<h3>
						<?php echo esc_html( $ext['name'] ); ?>
						<img src="<?php echo esc_attr( $ext['icon'] ) ?>" class="plugin-icon" alt="<?php echo esc_attr( $ext['name'] ); ?>">
					</h3>
				</div>
				<div class="action-links">
					<ul class="plugin-action-buttons">
						<li><?php echo $action_button; ?></li>
						<?php if ( $ext['official'] ): ?>
							<li><span class="official"><?php _e( 'Official', 'notification' ); ?></span></li>
						<?php endif ?>
					</ul>
				</div>
				<div class="desc column-description">
					<p><?php echo mb_strimwidth( $ext['desc'], 0, 117, '...' ); ?></p>
					<p class="authors"><?php _e( 'Author', 'notification' ); ?>: <?php echo esc_html( $ext['author'] ); ?></p>
				</div>
			</div>
		</div>

	<?php

	}

	protected function render_promo_box() {

	?>

		<div class="plugin-card promo">
			<div class="plugin-card-top">
				<div class="name column-name">
					<h3><?php _e( 'Your extension', 'notification' ); ?></h3>
				</div>
				<div class="action-links">
					<ul class="plugin-action-buttons">
						<li><a href="mailto:jakub@underdev.it?subject=Notification Extension" class="button"><?php _e( 'Send extension', 'notification' ); ?></a></li>
					</ul>
				</div>
				<div class="desc column-description">
					<p><?php _e( 'If you wrote a Notification extension or you have a plugin which complete Notification, let me know!', 'notification' ); ?></p>
				</div>
			</div>
		</div>

	<?php

	}

}
