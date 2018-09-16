<?php
/**
 * Extensions class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\License;
use BracketSpace\Notification\Utils\View;
use BracketSpace\Notification\Utils\EDDUpdater;
use BracketSpace\Notification\Utils\Cache\Transient as TransientCache;

/**
 * Extensions class
 */
class Extensions {

	/**
	 * Extensions API URL
	 *
	 * @var string
	 */
	private $api_url = 'https://bracketspace.com/extras/notification/extensions.php';

	/**
	 * Extensions list
	 *
	 * @var array
	 */
	private $extensions = array();

	/**
	 * Premium Extensions list
	 *
	 * @var array
	 */
	private $premium_extensions = array();

	/**
	 * View object
	 *
	 * @var object
	 */
	private $view;

	/**
	 * Extensions constructor
	 *
	 * @since 5.0.0
	 * @param View $view View class.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Register Extensions page under plugin's menu
	 *
	 * @action admin_menu
	 *
	 * @return void
	 */
	public function register_page() {

		if ( ! apply_filters( 'notification/whitelabel/extensions', true ) ) {
			return;
		}

		// change settings position if white labelled.
		if ( true !== apply_filters( 'notification/whitelabel/cpt/parent', true ) ) {
			$page_menu_label = __( 'Notification extensions', 'notification' );
		} else {
			$page_menu_label = __( 'Extensions', 'notification' );
		}

		$this->page_hook = add_submenu_page(
			apply_filters( 'notification/whitelabel/cpt/parent', 'edit.php?post_type=notification' ),
			__( 'Extensions', 'notification' ),
			$page_menu_label,
			'manage_options',
			'extensions',
			array( $this, 'extensions_page' )
		);

		add_action( 'load-' . $this->page_hook, array( $this, 'load_extensions' ) );

	}

	/**
	 * Loads all extensions
	 * If you want to get your extension listed please send a message via
	 * https://bracketspace.com/contact/ contact form
	 *
	 * @return void
	 */
	public function load_extensions() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		$extensions = $this->get_raw_extensions();

		foreach ( $extensions as $extension ) {

			if ( isset( $extension['wporg'] ) ) {
				$extension['wporg'] = plugins_api( 'plugin_information', $extension['wporg'] );
				$extension['url']   = self_admin_url( $extension['url'] );
			}

			if ( isset( $extension['edd'] ) && is_plugin_active( $extension['slug'] ) ) {
				$extension['license']       = new License( $extension );
				$this->premium_extensions[] = $extension;
			} else {
				$this->extensions[] = $extension;
			}
		}

	}

	/**
	 * Gets raw extensions data from API
	 *
	 * @return array
	 */
	public function get_raw_extensions() {

		$extensions_cache = new TransientCache( 'notification_extensions', DAY_IN_SECONDS );

		if ( defined( 'NOTIFICATION_DEBUG' ) && NOTIFICATION_DEBUG ) {
			$extensions = false;
		} else {
			$extensions = $extensions_cache->get();
		}

		if ( false === $extensions ) {

			$response   = wp_remote_get( $this->api_url );
			$extensions = array();

			if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
				$extensions = json_decode( wp_remote_retrieve_body( $response ), true );
				$extensions_cache->set( $extensions );
			}
		}

		return $extensions;

	}

	/**
	 * Gets single raw extension data
	 *
	 * @param string $slug extension slug.
	 * @return array
	 */
	public function get_raw_extension( $slug ) {
		$extensions = $this->get_raw_extensions();
		return isset( $extensions[ $slug ] ) ? $extensions[ $slug ] : false;
	}

	/**
	 * Outputs extensions page
	 *
	 * @return void
	 */
	public function extensions_page() {
		$this->view->set_var( 'premium_extensions', $this->premium_extensions );
		$this->view->set_var( 'extensions', $this->extensions );
		$this->view->get_view( 'extension/page' );
	}

	/**
	 * Initializes the Updater for all the premium plugins
	 *
	 * @action admin_init
	 *
	 * @return void
	 */
	public function updater() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$extensions   = $this->get_raw_extensions();
		$premium      = array();
		$wp_plugins   = get_plugins();
		$plugin_slugs = array_keys( $wp_plugins );

		foreach ( $extensions as $extension ) {

			if ( ! isset( $extension['edd'] ) || ! in_array( $extension['slug'], $plugin_slugs, true ) ) {
				continue;
			}

			$license = new License( $extension );

			if ( ! $license->is_valid() ) {
				continue;
			}

			$wp_plugin = $wp_plugins[ $extension['slug'] ];

			new EDDUpdater(
				$extension['edd']['store_url'],
				$extension['slug'],
				array(
					'version'   => $wp_plugin['Version'],
					'license'   => '',
					'item_name' => $extension['edd']['item_name'],
					'author'    => $extension['author'],
					'beta'      => false,
				)
			);

		}

	}

	/**
	 * Activates the premium extension.
	 *
	 * @action admin_post_notification_activate_extension
	 *
	 * @return void
	 */
	public function activate() {

		$data = $_POST;

		$extension = $this->get_raw_extension( $data['extension'] );

		if ( false === $extension ) {
			wp_safe_redirect( add_query_arg( 'activation-status', 'wrong-extension', $data['_wp_http_referer'] ) );
			exit();
		}

		if ( ! wp_verify_nonce( $data['_wpnonce'], 'activate_extension_' . $extension['slug'] ) ) {
			wp_safe_redirect( add_query_arg( 'activation-status', 'wrong-nonce', $data['_wp_http_referer'] ) );
			exit();
		}

		$license    = new License( $extension );
		$activation = $license->activate( $data['license-key'] );

		if ( is_wp_error( $activation ) ) {
			wp_safe_redirect( add_query_arg( 'activation-status', $activation->get_error_message(), $data['_wp_http_referer'] ) );
			exit();
		}

		wp_safe_redirect( add_query_arg( 'activation-status', 'success', $data['_wp_http_referer'] ) );
		exit();

	}

	/**
	 * Deactivates the premium extension.
	 *
	 * @action admin_post_notification_deactivate_extension
	 *
	 * @return void
	 */
	public function deactivate() {

		$data = $_POST;

		$extension = $this->get_raw_extension( $data['extension'] );

		if ( false === $extension ) {
			wp_safe_redirect( add_query_arg( 'activation-status', 'wrong-extension', $data['_wp_http_referer'] ) );
			exit();
		}

		if ( ! wp_verify_nonce( $data['_wpnonce'], 'activate_extension_' . $extension['slug'] ) ) {
			wp_safe_redirect( add_query_arg( 'activation-status', 'wrong-nonce', $data['_wp_http_referer'] ) );
			exit();
		}

		$license    = new License( $extension );
		$activation = $license->deactivate();

		if ( is_wp_error( $activation ) ) {
			wp_safe_redirect( add_query_arg( 'activation-status', $activation->get_error_message(), $data['_wp_http_referer'] ) );
			exit();
		}

		wp_safe_redirect( add_query_arg( 'activation-status', 'deactivated', $data['_wp_http_referer'] ) );
		exit();

	}

	/**
	 * Displays activation notices
	 *
	 * @action admin_notices
	 *
	 * @return void
	 */
	public function activation_notices() {

		if ( ! isset( $_GET['activation-status'] ) ) {
			return;
		}

		switch ( $_GET['activation-status'] ) {
			case 'success':
				$view    = 'success';
				$message = __( 'Your license has been activated.', 'notification' );
				break;

			case 'deactivated':
				$view    = 'success';
				$message = __( 'Your license has been deactivated.', 'notification' );
				break;

			case 'wrong-nonce':
				$view    = 'error';
				$message = __( 'Couldn\'t activate the license, please try again.', 'notification' );
				break;

			case 'expired':
				$view    = 'error';
				$message = sprintf(
					// translators: 1. Date.
					__( 'Your license key expired on %s.', 'notification' ),
					date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
				);
				break;

			case 'revoked':
			case 'inactive':
				$view    = 'error';
				$message = __( 'Your license key has been disabled.', 'notification' );
				break;

			case 'missing':
				$view    = 'error';
				$message = __( 'Invalid license key.', 'notification' );
				break;

			case 'invalid':
			case 'site_inactive':
				$view    = 'error';
				$message = __( 'Your license is not active for this URL.', 'notification' );
				break;

			case 'item_name_mismatch':
				$view = 'error';
				// translators: 1. Extension name.
				$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'notification' ), $this->extension['edd']['item_name'] );
				break;

			case 'no_activations_left':
				$view    = 'error';
				$message = __( 'Your license key has reached its activation limit.', 'notification' );
				break;

			default:
				$view    = 'error';
				$message = __( 'An error occurred, please try again.', 'notification' );
				break;
		}

		$this->view->set_var( 'message', $message );
		$this->view->get_view( 'extension/activation-' . $view );

	}

	/**
	 * Displays activation notice nag
	 *
	 * @action admin_notices
	 *
	 * @return void
	 */
	public function activation_nag() {

		if ( notification_is_whitelabeled() ) {
			return;
		}

		if ( get_current_screen()->id === $this->page_hook ) {
			return;
		}

		$extensions = $this->get_raw_extensions();

		foreach ( $extensions as $extension ) {

			if ( isset( $extension['edd'] ) && is_plugin_active( $extension['slug'] ) ) {

				$license = new License( $extension );

				if ( ! $license->is_valid() ) {

					$message = sprintf(
						// Translators: 1. Plugin name, 2. Link.
						__( 'Please activate the %1$s plugin to get the updates. %2$s', 'notification' ),
						$extension['edd']['item_name'],
						'<a href="' . admin_url( 'edit.php?post_type=notification&page=extensions' ) . '">' . __( 'Go to Extensions', 'notification' ) . '</a>'
					);

					$this->view->set_var( 'message', $message, true );
					$this->view->get_view( 'extension/activation-error' );

				}
			}
		}

	}

}
