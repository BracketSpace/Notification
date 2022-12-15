<?php

/**
 * Extensions class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\License;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Core\Whitelabel;
use BracketSpace\Notification\ErrorHandler;
use BracketSpace\Notification\Utils\EDDUpdater;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;

/**
 * Extensions class
 */
class Extensions
{

	/**
	 * Extensions API URL
	 *
	 * @var string
	 */
	private $apiUrl = 'https://bracketspace.com/extras/notification/extensions.php';

	/**
	 * Extensions list
	 *
	 * @var array<mixed>
	 */
	private $extensions = [];

	/**
	 * Premium Extensions list
	 *
	 * @var array<mixed>
	 */
	public $premiumExtensions = [];

	/**
	 * Extensions admin page hook
	 *
	 * @var string|false
	 */
	public $pageHook = 'none';

	/**
	 * Register Extensions page under plugin's menu
	 *
	 * @action admin_menu
	 *
	 * @return void
	 */
	public function registerPage()
	{
		if (
			!apply_filters(
				'notification/whitelabel/extensions',
				true
			)
		) {
			return;
		}

		// change settings position if white labelled.
		$pageMenuLabel = apply_filters(
			'notification/whitelabel/cpt/parent',
			true
		) !== true
			? __(
				'Notification extensions',
				'notification'
			)
			: __(
				'Extensions',
				'notification'
			);

		$this->pageHook = add_submenu_page(
			apply_filters(
				'notification/whitelabel/cpt/parent',
				'edit.php?post_type=notification'
			),
			__(
				'Extensions',
				'notification'
			),
			$pageMenuLabel,
			'manage_options',
			'extensions',
			[$this, 'extensionsPage']
		);

		add_action(
			'load-' . $this->pageHook,
			[$this, 'loadExtensions']
		);
	}

	/**
	 * Loads all extensions
	 * If you want to get your extension listed please send a message via
	 * https://bracketspace.com/contact/ contact form
	 *
	 * @return void
	 */
	public function loadExtensions()
	{
		if (!function_exists('is_plugin_active')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if (!function_exists('plugins_api')) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		$extensions = $this->getRawExtensions();

		if (empty($extensions)) {
			return;
		}

		/**
		 * Fix for changed Custom Fields slug:
		 * notification-customfields/notification-customfields.php
		 *  ->
		 * notification-custom-fields/notification-customfields.php
		 */
		if (is_plugin_active('notification-custom-fields/notification-customfields.php')) {
			$extensions['notification-customfields/notification-customfields.php']['slug'] =
				'notification-custom-fields/notification-customfields.php';
		}

		foreach ($extensions as $extension) {
			if (isset($extension['wporg'])) {
				$extension['wporg'] = plugins_api(
					'plugin_information',
					$extension['wporg']
				);
				$extension['url'] = self_admin_url($extension['url']);
			}

			// Fix for the PRO extension having a version number in the directory name.
			$globSlug = wp_normalize_path(trailingslashit(WP_PLUGIN_DIR)) . str_replace(
				'/',
				'-*/',
				$extension['slug']
			);
			$proInstalled = is_plugin_active($extension['slug']) || !empty(glob($globSlug));

			if (isset($extension['edd']) && $proInstalled) {
				$extension['license'] = new License($extension);
				$this->premiumExtensions[] = $extension;
			} else {
				$this->extensions[] = $extension;
			}
		}
	}

	/**
	 * Gets raw extensions data from API
	 *
	 * @return array<mixed>
	 */
	public function getRawExtensions()
	{
		$driver = new CacheDriver\Transient(
			ErrorHandler::debugEnabled()
				? 60
				: DAY_IN_SECONDS
		);
		$cache = new Cache(
			$driver,
			'notification_extensions'
		);

		return $cache->collect(
			function () {
				$response = wp_remote_get($this->apiUrl);
				$extensions = [];

				if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
					$extensions = json_decode(
						wp_remote_retrieve_body($response),
						true
					);
				}

				return $extensions;
			}
		);
	}

	/**
	 * Gets single raw extension data
	 *
	 * @param string $slug extension slug.
	 * @return array<mixed>|false
	 */
	public function getRawExtension($slug)
	{
		$extensions = $this->getRawExtensions();
		return $extensions[$slug] ?? false;
	}

	/**
	 * Outputs extensions page
	 *
	 * @return void
	 */
	public function extensionsPage()
	{
		Templates::render(
			'extension/page',
			[
				'premium_extensions' => $this->premiumExtensions,
				'extensions' => $this->extensions,
				'bundles' => static::getBundles(),
			]
		);
	}

	/**
	 * Gets bundles
	 *
	 * @return array<int, array{name: string, description: string, price: int}>
	 * @since  8.0.0
	 */
	public static function getBundles()
	{
		return [
			[
				'name' => 'All-In',
				'description' => __(
					'Every available Notification extension and all the
					<strong>future extensions</strong>
					at a static price!
					You get the whole package and the price will never
					change even if a new add-on will be released.',
					'notification'
				),
				'price' => 249,
			],
			[
				'name' => 'Standard',
				'description' => esc_html__(
					'All extensions from Essential bundle plus much needed Carriers:
					Discord, Mailgun, Slack and Twilio.
					Use multiple notification channels!',
					'notification'
				),
				'price' => 199,
			],
			[
				'name' => 'Essential',
				'description' => esc_html__(
					'Crucial extensions including Conditionals, Custom Fields and File Log',
					'notification'
				),
				'price' => 99,
			],
		];
	}

	/**
	 * Initializes the Updater for all the premium plugins
	 *
	 * @action admin_init
	 *
	 * @return void
	 */
	public function updater()
	{
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$extensions = $this->getRawExtensions();
		$premium = [];
		$wpPlugins = get_plugins();
		$pluginSlugs = array_keys($wpPlugins);

		if (empty($extensions)) {
			return;
		}

		foreach ($extensions as $extension) {
			if (
				!isset($extension['edd']) || !in_array(
					$extension['slug'],
					$pluginSlugs,
					true
				)
			) {
				continue;
			}

			$license = new License($extension);

			$wpPlugin = $wpPlugins[$extension['slug']];

			new EDDUpdater(
				$extension['edd']['store_url'],
				$extension['slug'],
				[
					'version' => $wpPlugin['Version'],
					'license' => $license->getKey(),
					'item_name' => $extension['edd']['item_name'],
					'author' => $extension['author'],
					'beta' => false,
				]
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
	public function activate()
	{
		if (!isset($_POST['_wpnonce'])) {
			return;
		}

		if (
			!wp_verify_nonce(
				wp_unslash(sanitize_key($_POST['_wpnonce'])),
				'activate_extension_' . wp_unslash(sanitize_key($_POST['extension'] ?? ''))
			)
		) {
			wp_safe_redirect(
				add_query_arg(
					'activation-status',
					'wrong-nonce',
					esc_url_raw(wp_unslash($_POST['_wp_http_referer'] ?? ''))
				)
			);
			exit;
		}

		$data = $_POST;

		$extension = $this->getRawExtension($data['extension']);

		if ($extension === false) {
			wp_safe_redirect(
				add_query_arg(
					'activation-status',
					'wrong-extension',
					$data['_wp_http_referer']
				)
			);
			exit;
		}

		$license = new License($extension);
		$activation = $license->activate($data['license-key']);

		if (is_wp_error($activation)) {
			$licenseData = $activation->get_error_data();
			$params = [
				'activation-status' => $activation->get_error_message(),
				'extension' => rawurlencode($licenseData->itemName),
			];

			if ($activation->get_error_message() === 'expired') {
				$params['expiration'] = $licenseData->expires;
			}

			wp_safe_redirect(
				add_query_arg(
					$params,
					$data['_wp_http_referer']
				)
			);
			exit;
		}

		wp_safe_redirect(
			add_query_arg(
				'activation-status',
				'success',
				$data['_wp_http_referer']
			)
		);
		exit;
	}

	/**
	 * Deactivates the premium extension.
	 *
	 * @action admin_post_notification_deactivate_extension
	 *
	 * @return void
	 */
	public function deactivate()
	{
		if (!isset($_POST['_wpnonce'])) {
			return;
		}

		if (
			!wp_verify_nonce(
				wp_unslash(sanitize_key($_POST['_wpnonce'])),
				'activate_extension_' . sanitize_key($_POST['extension'] ?? '')
			)
		) {
			wp_safe_redirect(
				add_query_arg(
					'activation-status',
					'wrong-nonce',
					esc_url_raw(wp_unslash($_POST['_wp_http_referer'] ?? ''))
				)
			);
			exit;
		}

		$data = $_POST;

		$extension = $this->getRawExtension($data['extension']);

		if ($extension === false) {
			wp_safe_redirect(
				add_query_arg(
					'activation-status',
					'wrong-extension',
					$data['_wp_http_referer']
				)
			);
			exit;
		}

		$license = new License($extension);
		$activation = $license->deactivate();

		if (is_wp_error($activation)) {
			$licenseData = $activation->get_error_data();
			$params = [
				'activation-status' => $activation->get_error_message(),
				'extension' => rawurlencode($licenseData->itemName),
			];

			wp_safe_redirect(
				add_query_arg(
					$params,
					$data['_wp_http_referer']
				)
			);
			exit;
		}

		wp_safe_redirect(
			add_query_arg(
				'activation-status',
				'deactivated',
				$data['_wp_http_referer']
			)
		);
		exit;
	}

	/**
	 * Displays activation notices
	 *
	 * @action admin_notices
	 *
	 * @return void
	 */
	public function activationNotices()
	{

		// We're just checking for the status slug.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (!isset($_GET['activation-status'])) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$status = sanitize_key($_GET['activation-status']);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$extensionSlug = sanitize_title_with_dashes(wp_unslash($_GET['extension'] ?? ''));

		switch ($status) {
			case 'success':
				$view = 'success';
				$message = __(
					'Your license has been activated.',
					'notification'
				);
				break;

			case 'deactivated':
				$view = 'success';
				$message = __(
					'Your license has been deactivated.',
					'notification'
				);
				break;

			case 'wrong-nonce':
				$view = 'error';
				$message = __(
					'Couldn\'t activate the license, please try again.',
					'notification'
				);
				break;

			case 'expired':
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$expiration = strtotime(sanitize_text_field(wp_unslash($_GET['expiration'] ?? '')));

				$view = 'error';
				$message = sprintf(
				// translators: 1. Date.
					__(
						'Your license key expired on %s.',
						'notification'
					),
					date_i18n(
						get_option('date_format'),
						$expiration
					)
				);
				break;

			case 'revoked':
			case 'inactive':
				$view = 'error';
				$message = __(
					'Your license key has been disabled.',
					'notification'
				);
				break;

			case 'missing':
				$view = 'error';
				$message = sprintf(
				// Translators: Extension slug.
					__(
						'Invalid license key for %s.',
						'notification'
					),
					$extensionSlug
				);
				break;

			case 'invalid':
			case 'site_inactive':
				$view = 'error';
				$message = __(
					'Your license is not active for this URL.',
					'notification'
				);
				break;

			case 'item_name_mismatch':
				$view = 'error';
				$message = sprintf(
				// translators: 1. Extension name.
					__(
						'This appears to be an invalid license key for %s.',
						'notification'
					),
					$extensionSlug
				);
				break;

			case 'no_activations_left':
				$view = 'error';
				$message = __(
					'Your license key has reached its activation limit.',
					'notification'
				);
				break;

			default:
				$view = 'error';
				$message = __(
					'An error occurred, please try again.',
					'notification'
				);
				break;
		}

		Templates::render(
			sprintf(
				'extension/activation-%s',
				$view
			),
			['message' => $message]
		);
	}

	/**
	 * Displays activation notice nag
	 *
	 * @action admin_notices
	 *
	 * @return void
	 */
	public function activationNag()
	{
		if (Whitelabel::isWhitelabeled()) {
			return;
		}

		if (!current_user_can('manage_options')) {
			return;
		}

		if (get_current_screen()->id === $this->pageHook) {
			return;
		}

		$extensions = $this->getRawExtensions();

		if (empty($extensions)) {
			return;
		}

		$invalidExtensions = [];

		foreach ($extensions as $extension) {
			if (!isset($extension['edd']) || !is_plugin_active($extension['slug'])) {
				continue;
			}

			$license = new License($extension);

			if ($license->isValid()) {
				continue;
			}

			$invalidExtensions[] = $extension['edd']['item_name'];
		}

		if ($invalidExtensions === []) {
			return;
		}

		$message = _n(
			'Please activate the following plugin to get the updates.',
			'Please activate the following plugins to get the updates.',
			count($invalidExtensions),
			'notification'
		);

		$extensionsLink = sprintf(
			'<a href="%s">%s</a>',
			admin_url('edit.php?post_type=notification&page=extensions'),
			__(
				'Go to Extensions',
				'notification'
			)
		);

		$message .= ' ' . $extensionsLink;

		Templates::render(
			'extension/activation-error',
			[
				'message' => $message,
				'extensions' => $invalidExtensions,
			]
		);
	}
}
