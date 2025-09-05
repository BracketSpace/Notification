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
	 * @var array<array<mixed>>
	 */
	private $extensions = [];

	/**
	 * Premium Extensions list
	 *
	 * @var array<array<mixed>>
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
		if (!apply_filters('notification/whitelabel/extensions', true)) {
			return;
		}

		// change settings position if white labelled.
		$pageMenuLabel = apply_filters('notification/whitelabel/cpt/parent', true) !== true
			? __('Notification extensions', 'notification')
			: __('Extensions', 'notification');

		$this->pageHook = add_submenu_page(
			apply_filters(
				'notification/whitelabel/cpt/parent',
				'edit.php?post_type=notification'
			),
			__('Extensions', 'notification'),
			$pageMenuLabel,
			'manage_options',
			'extensions',
			[$this, 'extensionsPage']
		);

		add_action('load-' . $this->pageHook, [$this, 'loadExtensions']);
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
			// Validate extension data structure
			if (!is_array($extension) || !isset($extension['slug']) || !is_string($extension['slug'])) {
				continue;
			}

			if (isset($extension['wporg'])) {
				$extension['wporg'] = plugins_api(
					'plugin_information',
					$extension['wporg']
				);
				if (isset($extension['url']) && is_string($extension['url'])) {
					$extension['url'] = self_admin_url($extension['url']);
				}
			}

			// Fix for the PRO extension having a version number in the directory name.
			$globSlug = wp_normalize_path(trailingslashit(WP_PLUGIN_DIR)) .
				str_replace('/', '-*/', $extension['slug']);
			$proInstalled = is_plugin_active($extension['slug']) || !empty(glob($globSlug));

			if (isset($extension['edd']) && is_array($extension['edd']) && $proInstalled) {
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
	 * @return array<string, array<mixed>>
	 */
	public function getRawExtensions()
	{
		$driver = new CacheDriver\Transient(ErrorHandler::debugEnabled() ? 60 : DAY_IN_SECONDS);
		$cache = new Cache($driver, 'notification_extensions');

		$result = $cache->collect(
			function () {
				$response = wp_remote_get($this->apiUrl);
				$extensions = [];

				if (! is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
					$decoded = json_decode(wp_remote_retrieve_body($response), true);
					if (is_array($decoded)) {
						$extensions = $decoded;
					}
				}

				return $extensions;
			}
		);

		return is_array($result) ? $result : [];
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
			]
		);
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
			// Validate extension data structure
			if (
				!is_array($extension) || !isset($extension['edd'], $extension['slug']) ||
				!is_array($extension['edd']) || !is_string($extension['slug']) ||
				!in_array($extension['slug'], $pluginSlugs, true)
			) {
				continue;
			}

			$license = new License($extension);

			$wpPlugin = $wpPlugins[$extension['slug']];

			if (
				!isset($extension['edd']['store_url'], $extension['edd']['item_name']) ||
				!is_string($extension['edd']['store_url']) || !is_string($extension['edd']['item_name'])
			) {
				continue;
			}

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

		if (
			$extension === false || !is_array($extension) ||
			!isset($extension['edd']['item_name']) || !is_string($extension['edd']['item_name'])
		) {
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
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				'extension' => rawurlencode(
					// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
					isset($licenseData->item_name) && is_string($licenseData->item_name)
						// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
						? $licenseData->item_name : ''
				),
			];

			if ($activation->get_error_message() === 'expired') {
				$params['expiration'] = $licenseData->expires;
			}

			wp_safe_redirect(add_query_arg($params, $data['_wp_http_referer']));
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
	 * Refreshes license status for all premium extensions.
	 *
	 * @action admin_post_notification_refresh_all_licenses
	 *
	 * @return void
	 */
	public function refreshAllLicenses()
	{
		if (!isset($_GET['_wpnonce'])) {
			return;
		}

		if (!wp_verify_nonce(wp_unslash(sanitize_key($_GET['_wpnonce'])), 'refresh_all_licenses')) {
			wp_safe_redirect(
				add_query_arg(
					'refresh-status',
					'wrong-nonce',
					esc_url_raw(
						wp_unslash(
							$_GET['_wp_http_referer'] ??
							admin_url('edit.php?post_type=notification&page=extensions')
						)
					)
				)
			);
			exit;
		}

		$extensions = $this->getRawExtensions();
		$refreshedCount = 0;
		$stillInvalidCount = 0;

		if (!empty($extensions)) {
			foreach ($extensions as $extension) {
				// Validate extension data structure
				if (
					!is_array($extension) || !isset($extension['edd'], $extension['slug']) ||
					!is_array($extension['edd']) || !is_string($extension['slug']) ||
					!is_plugin_active($extension['slug'])
				) {
					continue;
				}

				// Clear all cache and force fresh check
				$this->clearStaleLicenseCache($extension);

				$license = new License($extension);
				$isValid = $license->isValid(); // This will trigger a fresh API check

				$refreshedCount++;
				if ($isValid) {
					continue;
				}

				$stillInvalidCount++;
			}
		}

		wp_safe_redirect(
			add_query_arg(
				[
					'refresh-status' => 'bulk-complete',
					'refreshed' => $refreshedCount,
					'invalid' => $stillInvalidCount,
				],
				esc_url_raw(
					wp_unslash(
						$_GET['_wp_http_referer'] ??
						admin_url('edit.php?post_type=notification&page=extensions')
					)
				)
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

		if (
			$extension === false || !is_array($extension) ||
			!isset($extension['edd']['item_name']) || !is_string($extension['edd']['item_name'])
		) {
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
			$errorMessage = $activation->get_error_message();
			$licenseData = $activation->get_error_data();

			// If API deactivation failed but we have license data, try to remove locally anyway
			if ($errorMessage === 'deactivation-error' && !empty($licenseData)) {
				// Force local removal for stubborn licenses
				$license->remove();
				$params = [
					'activation-status' => 'force-deactivated',
					'extension' => rawurlencode($extension['edd']['item_name']),
				];
			} else {
				$params = [
					'activation-status' => $errorMessage,
					'extension' => rawurlencode($extension['edd']['item_name']),
				];
			}

			wp_safe_redirect(add_query_arg($params, $data['_wp_http_referer']));
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
	 * Displays refresh notices
	 *
	 * @return void
	 */
	public function refreshNotices()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$status = sanitize_key($_GET['refresh-status']);

		switch ($status) {
			case 'bulk-complete':
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$refreshedCount = absint($_GET['refreshed'] ?? 0);
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$invalidCount = absint($_GET['invalid'] ?? 0);

				if ($invalidCount === 0) {
					$view = 'success';
					$message = sprintf(
						// translators: %d is the number of licenses refreshed
						_n(
							'%d license status has been refreshed successfully.',
							'%d license statuses have been refreshed successfully.',
							$refreshedCount,
							'notification'
						),
						$refreshedCount
					);
				} else {
					$view = 'error';
					$message = sprintf(
						// translators: %1$d is total refreshed, %2$d is still invalid
						_n(
							'%1$d license refreshed, but %2$d is still inactive.',
							'%1$d licenses refreshed, but %2$d are still inactive.',
							$refreshedCount,
							'notification'
						),
						$refreshedCount,
						$invalidCount
					);
				}
				break;

			case 'wrong-nonce':
				$view = 'error';
				$message = __("Couldn't refresh license statuses, please try again.", 'notification');
				break;

			default:
				$view = 'error';
				$message = __('An error occurred while refreshing license statuses.', 'notification');
				break;
		}

		Templates::render(
			sprintf('extension/activation-%s', $view),
			['message' => $message]
		);
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
		// Handle refresh status notices first
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (isset($_GET['refresh-status'])) {
			$this->refreshNotices();
			return;
		}

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
				$message = __('Your license has been activated.', 'notification');
				break;

			case 'deactivated':
				$view = 'success';
				$message = __('Your license has been deactivated.', 'notification');
				break;

			case 'force-deactivated':
				$view = 'success';
				$message = __(
					'Your license has been deactivated locally ' .
					'(API deactivation failed, but license removed from this site).',
					'notification'
				);
				break;

			case 'wrong-nonce':
				$view = 'error';
				$message = __("Couldn't activate the license, please try again.", 'notification');
				break;

			case 'expired':
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$expiration = strtotime(sanitize_text_field(wp_unslash($_GET['expiration'] ?? '')));

				$view = 'error';
				$dateFormat = get_option('date_format');
				$message = sprintf(
					// translators: 1. Date.
					__('Your license key expired on %s.', 'notification'),
					date_i18n(is_string($dateFormat) ? $dateFormat : 'Y-m-d', $expiration)
				);
				break;

			case 'revoked':
			case 'inactive':
				$view = 'error';
				$message = __('Your license key has been disabled.', 'notification');
				break;

			case 'missing':
				$view = 'error';
				$message = sprintf(
					// Translators: Extension slug.
					__('Invalid license key for %s.', 'notification'),
					$extensionSlug
				);
				break;

			case 'invalid':
			case 'site_inactive':
				$view = 'error';
				$message = __('Your license is not active for this URL.', 'notification');
				break;

			case 'item_name_mismatch':
				$view = 'error';
				$message = sprintf(
					// translators: 1. Extension name.
					__('This appears to be an invalid license key for %s.', 'notification'),
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
				$message = __('An error occurred, please try again.', 'notification');
				break;
		}

		Templates::render(
			sprintf('extension/activation-%s', $view),
			['message' => $message]
		);
	}

	/**
	 * Clears stale license cache for extension
	 *
	 * @param array{slug: string, edd?: array<mixed>} $extension Extension data.
	 * @return void
	 */
	private function clearStaleLicenseCache(array $extension)
	{
		// Clear ObjectCache for license data
		$driver = new CacheDriver\ObjectCache('notification_license/v2');
		$cache = new Cache($driver, $extension['slug']);
		$cache->delete();

		// Clear Transient cache for license check
		$driver = new CacheDriver\Transient(ErrorHandler::debugEnabled() ? 60 : DAY_IN_SECONDS);
		$cache = new Cache($driver, sprintf('notification_license_check_%s', $extension['slug']));
		$cache->delete();
	}

	/**
	 * Checks if license data appears stale and needs refresh
	 *
	 * @param object|null $licenseData License data object.
	 * @return bool
	 */
	private function isLicenseDataStale($licenseData)
	{
		if (empty($licenseData) || !isset($licenseData->license, $licenseData->expires)) {
			return false;
		}

		// If license shows inactive/expired status but expiration is in the future, it might be stale
		if (in_array($licenseData->license, ['inactive', 'site_inactive', 'expired'], true)) {
			if ($licenseData->expires !== 'lifetime') {
				$expirationTime = strtotime((string)$licenseData->expires);
				// If expiration is more than 1 day in the future, data might be stale
				if ($expirationTime > (time() + DAY_IN_SECONDS)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Gets extensions with invalid license
	 *
	 * @return array<string>
	 */
	public function getInvalidLicenseExtensions()
	{
		$extensions = $this->getRawExtensions();

		if (empty($extensions)) {
			return [];
		}

		$invalidExtensions = [];

		foreach ($extensions as $extension) {
			// Validate extension data structure
			if (
				!is_array($extension) || !isset($extension['edd'], $extension['slug']) ||
				!is_array($extension['edd']) || !is_string($extension['slug']) ||
				!is_plugin_active($extension['slug'])
			) {
				continue;
			}

			$license = new License($extension);
			$licenseData = $license->get();

			// Check if license data appears stale and clear cache if so
			if (is_object($licenseData) && $this->isLicenseDataStale($licenseData)) {
				$this->clearStaleLicenseCache($extension);
			}

			if ($license->isValid()) {
				continue;
			}

			// Validate edd item_name exists and is string
			if (!isset($extension['edd']['item_name']) || !is_string($extension['edd']['item_name'])) {
				continue;
			}

			$invalidExtensions[] = $extension['edd']['item_name'];
		}

		return $invalidExtensions;
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

		if (! current_user_can('manage_options')) {
			return;
		}

		if (get_current_screen()->id === $this->pageHook) {
			return;
		}

		$invalidExtensions = $this->getInvalidLicenseExtensions();

		if (empty($invalidExtensions)) {
			return;
		}

		$message = _n(
			'The Notification extension license is inactive,
			which means your WordPress <strong>might break</strong>.',
			'The Notification extension licenses are inactive,
			which means your WordPress <strong>might break</strong>.',
			count($invalidExtensions),
			'notification'
		);

		$extensionsLink = sprintf(
			'<a href="%s" class="button button-small button-secondary">%s</a>',
			admin_url('edit.php?post_type=notification&page=extensions'),
			__('Inspect the issue', 'notification')
		);

		$additionalLink = sprintf(
			'or <a href="%s" target="_blank">%s</a>',
			'https://bracketspace.com/expired-license/',
			__('read more about inactive license', 'notification')
		);

		$message .= sprintf(' %s %s', $extensionsLink, $additionalLink);

		Templates::render(
			'extension/activation-error',
			[
				'message' => $message,
				'extensions' => $invalidExtensions,
			]
		);
	}

	/**
	 * Displays inactive license warning
	 *
	 * @action notification/admin/extensions/premium/pre
	 *
	 * @return void
	 */
	public function inactiveLicenseWarning()
	{
		if (Whitelabel::isWhitelabeled()) {
			return;
		}

		if (! current_user_can('manage_options')) {
			return;
		}

		if (get_current_screen()->id !== $this->pageHook) {
			return;
		}

		$invalidExtensions = $this->getInvalidLicenseExtensions();

		if (empty($invalidExtensions)) {
			return;
		}

		Templates::render('extension/inactive-license', ['extensions' => $invalidExtensions]);
	}
}
