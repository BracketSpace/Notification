<?php

/**
 * EDD Plugin updater class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils;

use stdClass;

/**
 * Allows plugins to use their own update API.
 *
 * @author Easy Digital Downloads
 * @version Release: 1.9.1
 */
class EDDUpdater
{
	/** @var string */
	private $apiUrl = '';

	/** @var array<mixed>|null */
	private $apiData = [];

	/** @var string */
	private $pluginFile = '';

	/** @var string */
	private $name = '';

	/** @var string */
	private $slug = '';

	/** @var mixed */
	private $version = '';

	/** @var bool */
	private $wpOverride = false;

	/** @var bool */
	private $beta = false;

	/** @var string */
	private $failedRequestCacheKey;

	/**
	 * Class constructor.
	 *
	 * @param string $apiUrl The URL pointing to the custom API endpoint.
	 * @param string $pluginFile Path to the plugin file.
	 * @param array<mixed> $apiData Optional data to send with API calls.
	 * @uses hook()
	 *
	 * @uses plugin_basename()
	 */
	public function __construct($apiUrl, $pluginFile, $apiData = null)
	{

		global $eddPluginData;

		$this->apiUrl = trailingslashit($apiUrl);
		$this->apiData = $apiData;
		$this->pluginFile = $pluginFile;
		$this->name = plugin_basename($pluginFile);
		$this->slug = basename(
			$pluginFile,
			'.php'
		);
		$this->version = $apiData['version'];
		$this->wpOverride = isset($apiData['wp_override']) && $apiData['wp_override'];
		$this->beta = !empty($this->apiData['beta']);
		$this->failedRequestCacheKey = 'edd_sl_failed_http_' . md5($this->apiUrl);

		$eddPluginData[$this->slug] = $this->apiData;

		/**
		 * Fires after the $eddPluginData is setup.
		 *
		 * @param array<mixed> $eddPluginData Array of EDD SL plugin data.
		 * @since x.x.x
		 *
		 */
		do_action(
			'post_edd_sl_plugin_updater_setup',
			$eddPluginData
		);

		// Set up hooks.
		$this->init();
	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @return void
	 * @uses add_filter()
	 *
	 */
	public function init()
	{

		add_filter(
			'pre_set_site_transient_update_plugins',
			[$this, 'checkUpdate']
		);
		add_filter(
			'plugins_api',
			[$this, 'pluginsApiFilter'],
			10,
			3
		);
		add_action(
			'after_plugin_row',
			[$this, 'showUpdateNotification'],
			10,
			2
		);
		add_action(
			'admin_init',
			[$this, 'showChangelog']
		);
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @param array<mixed> $transientData Update array build by WordPress.
	 * @return array<mixed> Modified update array with custom plugin data.
	 * @uses api_request()
	 *
	 */
	public function checkUpdate($transientData)
	{

		global $pagenow;

		if (!is_object($transientData)) {
			$transientData = new stdClass();
		}

		if (
			!empty($transientData->response) &&
			!empty($transientData->response[$this->name]) &&
			$this->wpOverride === false
		) {
			return $transientData;
		}

		$current = $this->getRepoApiData();
		if ($current !== false && is_object($current) && isset($current->newVersion)) {
			if (
				version_compare(
					$this->version,
					$current->newVersion,
					'<'
				)
			) {
				$transientData->response[$this->name] = $current;
			} else {
				// Populating the no_update information is required to support auto-updates in WordPress 5.5.
				$transientData->noUpdate[$this->name] = $current;
			}
		}
		$transientData->lastChecked = time();
		$transientData->checked[$this->name] = $this->version;

		return $transientData;
	}

	/**
	 * Get repo API data from store.
	 * Save to cache.
	 *
	 * @return \stdClass|bool
	 */
	public function getRepoApiData()
	{
		$versionInfo = $this->getCachedVersionInfo();

		if ($versionInfo === false) {
			$versionInfo = $this->apiRequest(
				'plugin_latest_version',
				[
					'slug' => $this->slug,
					'beta' => $this->beta,
				]
			);
			if (!$versionInfo) {
				return false;
			}

			// This is required for your plugin to support auto-updates in WordPress 5.5.
			$versionInfo->plugin = $this->name;
			$versionInfo->id = $this->name;

			$this->setVersionInfoCache($versionInfo);
		}

		return $versionInfo;
	}

	/**
	 * Show the update notification on multisite subsites.
	 *
	 * @param string $file
	 * @param array<mixed> $plugin
	 */
	public function showUpdateNotification($file, $plugin)
	{

		// Return early if in the network admin, or if this is not a multisite install.
		if (is_network_admin() || !is_multisite()) {
			return;
		}

		// Allow single site admins to see that an update is available.
		if (!current_user_can('activate_plugins')) {
			return;
		}

		if ($this->name !== $file) {
			return;
		}

		// Do not print any message if update does not exist.
		$updateCache = get_site_transient('update_plugins');

		if (!isset($updateCache->response[$this->name])) {
			if (!is_object($updateCache)) {
				$updateCache = new stdClass();
			}
			$updateCache->response[$this->name] = $this->getRepoApiData();
		}

		// Return early if this plugin isn't in the transient->response or
		//if the site is running the current or newer version of the plugin.
		if (
			empty($updateCache->response[$this->name]) || version_compare(
				$this->version,
				$updateCache->response[$this->name]->newVersion,
				'>='
			)
		) {
			return;
		}

		printf(
			'<tr class="plugin-update-tr %3$s" id="%1$s-update" data-slug="%1$s" data-plugin="%2$s">',
			$this->slug,
			$file,
			in_array(
				$this->name,
				$this->getActivePlugins(),
				true
			)
				? 'active'
				: 'inactive'
		);

		echo '<td colspan="3" class="plugin-update colspanchange">';
		echo '<div class="update-message notice inline notice-warning notice-alt"><p>';

		$changelogLink = '';
		if (!empty($updateCache->response[$this->name]->sections->changelog)) {
			$changelogLink = add_query_arg(
				[
					'edd_sl_action' => 'view_plugin_changelog',
					'plugin' => rawurlencode($this->name),
					'slug' => rawurlencode($this->slug),
					'TB_iframe' => 'true',
					'width' => 77,
					'height' => 911,
				],
				self_admin_url('index.php')
			);
		}
		$updateLink = add_query_arg(
			[
				'action' => 'upgrade-plugin',
				'plugin' => rawurlencode($this->name),
			],
			self_admin_url('update.php')
		);

		printf(
		/* translators: the plugin name. */
			esc_html__('There is a new version of %1$s available.', 'easy-digital-downloads'),
			esc_html($plugin['Name'])
		);

		if (!current_user_can('update_plugins')) {
			echo ' ';
			esc_html_e(
				'Contact your network administrator to install the update.',
				'easy-digital-downloads'
			);
		} elseif (empty($updateCache->response[$this->name]->package) && !empty($changelogLink)) {
			echo ' ';
			printf(
			/* translators:
				1. opening anchor tag, do not translate
				2. the new plugin version
				3. closing anchor tag, do not translate.
			*/
				__('%1$sView version %2$s details%3$s.', 'easy-digital-downloads'),
				'<a target="_blank" class="thickbox open-plugin-details-modal" href="' . esc_url($changelogLink) . '">',
				esc_html($updateCache->response[$this->name]->newVersion),
				'</a>'
			);
		} elseif (!empty($changelogLink)) {
			echo ' ';
			printf(
				/* Translators: @todo */
				__('%1$sView version %2$s details%3$s or %4$supdate now%5$s.', 'easy-digital-downloads'),
				'<a target="_blank" class="thickbox open-plugin-details-modal" href="' . esc_url($changelogLink) . '">',
				esc_html($updateCache->response[$this->name]->newVersion),
				'</a>',
				'<a target="_blank" class="update-link" href="' . esc_url(
					wp_nonce_url(
						$updateLink,
						'upgrade-plugin_' . $file
					)
				) . '">',
				'</a>'
			);
		} else {
			printf(
				' %1$s%2$s%3$s',
				'<a target="_blank" class="update-link" href="' . esc_url(
					wp_nonce_url(
						$updateLink,
						'upgrade-plugin_' . $file
					)
				) . '">',
				esc_html__('Update now.', 'easy-digital-downloads'),
				'</a>'
			);
		}

		do_action(
			"in_plugin_update_message-{$file}",
			$plugin,
			$plugin
		);

		echo '</p></div></td></tr>';
	}

	/**
	 * Gets the plugins active in a multisite network.
	 *
	 * @return array
	 */
	private function getActivePlugins()
	{
		$activePlugins = (array)get_option('active_plugins');
		$activeNetworkPlugins = (array)get_site_option('active_sitewide_plugins');

		return array_merge(
			$activePlugins,
			array_keys($activeNetworkPlugins)
		);
	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @param mixed $_data
	 * @param string $_action
	 * @param object $_args
	 * @return object $_data
	 * @uses api_request()
	 *
	 */
	public function pluginsApiFilter($_data, $_action = '', $_args = null)
	{

		if ($_action !== 'plugin_information') {
			return $_data;
		}

		if (!isset($_args->slug) || ($_args->slug !== $this->slug)) {
			return $_data;
		}

		$toSend = [
			'slug' => $this->slug,
			'is_ssl' => is_ssl(),
			'fields' => [
				'banners' => [],
				'reviews' => false,
				'icons' => [],
			],
		];

		// Get the transient where we store the api request for this plugin for 24 hours
		$eddApiRequestTransient = $this->getCachedVersionInfo();

		//If we have no transient-saved value, run the API,
		//set a fresh transient with the API value,
		//and return that value too right now.
		if (empty($eddApiRequestTransient)) {
			$apiResponse = $this->apiRequest(
				'plugin_information',
				$toSend
			);

			// Expires in 3 hours
			$this->setVersionInfoCache($apiResponse);

			if ($apiResponse !== false) {
				$_data = $apiResponse;
			}
		} else {
			$_data = $eddApiRequestTransient;
		}

		// Convert sections into an associative array, since we're getting an object, but Core expects an array.
		if (isset($_data->sections) && !is_array($_data->sections)) {
			$_data->sections = $this->convertObjectToArray($_data->sections);
		}

		// Convert banners into an associative array, since we're getting an object, but Core expects an array.
		if (isset($_data->banners) && !is_array($_data->banners)) {
			$_data->banners = $this->convertObjectToArray($_data->banners);
		}

		// Convert icons into an associative array, since we're getting an object, but Core expects an array.
		if (isset($_data->icons) && !is_array($_data->icons)) {
			$_data->icons = $this->convertObjectToArray($_data->icons);
		}

		// Convert contributors into an associative array, since we're getting an object, but Core expects an array.
		if (isset($_data->contributors) && !is_array($_data->contributors)) {
			$_data->contributors = $this->convertObjectToArray($_data->contributors);
		}

		if (!isset($_data->plugin)) {
			$_data->plugin = $this->name;
		}

		return $_data;
	}

	/**
	 * Convert some objects to arrays when injecting data into the update API
	 *
	 * Some data like sections, banners, and icons are expected to be an associative array, however due to the JSON
	 * decoding, they are objects. This method allows us to pass in the object and return an associative array.
	 *
	 * @param \BracketSpace\Notification\Utils\stdClass $data
	 *
	 * @return array
	 * @since 3.6.5
	 *
	 */
	private function convertObjectToArray($data)
	{
		if (!is_array($data) && !is_object($data)) {
			return [];
		}
		$newData = [];
		foreach ($data as $key => $value) {
			$newData[$key] = is_object($value)
				? $this->convertObjectToArray($value)
				: $value;
		}

		return $newData;
	}

	/**
	 * Disable SSL verification in order to prevent download update failures
	 *
	 * @param array<mixed> $args
	 * @param string $url
	 * @return object $array
	 */
	public function httpRequestArgs($args, $url)
	{

		if (
			strpos(
				$url,
				'https://'
			) !== false && strpos(
				$url,
				'edd_action=package_download'
			)
		) {
			$args['sslverify'] = $this->verifySsl();
		}
		return $args;
	}

	/**
	 * Calls the API and, if successful, returns the object delivered by the API.
	 *
	 * @param string $_action The requested action.
	 * @param array<mixed> $_data Parameters for the API action.
	 * @return false|object|void
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 *
	 */
	private function apiRequest($_action, $_data)
	{
		$data = array_merge(
			$this->apiData,
			$_data
		);

		if ($data['slug'] !== $this->slug) {
			return;
		}

		// Don't allow a plugin to ping itself
		if (trailingslashit(home_url()) === $this->apiUrl) {
			return false;
		}

		if ($this->requestRecentlyFailed()) {
			return false;
		}

		return $this->getVersionFromRemote();
	}

	/**
	 * Determines if a request has recently failed.
	 *
	 * @return bool
	 * @since 1.9.1
	 *
	 */
	private function requestRecentlyFailed()
	{
		$failedRequestDetails = get_option($this->failedRequestCacheKey);

		// Request has never failed.
		if (empty($failedRequestDetails) || !is_numeric($failedRequestDetails)) {
			return false;
		}

		/*
		 * Request previously failed, but the timeout has expired.
		 * This means we're allowed to try again.
		 */
		if (time() > $failedRequestDetails) {
			delete_option($this->failedRequestCacheKey);

			return false;
		}

		return true;
	}

	/**
	 * Logs a failed HTTP request for this API URL.
	 * We set a timestamp for 1 hour from now. This prevents future API requests from being
	 * made to this domain for 1 hour. Once the timestamp is in the past, API requests
	 * will be allowed again. This way if the site is down for some reason we don't bombard
	 * it with failed API requests.
	 *
	 * @see EDD_SL_Plugin_Updater::requestRecentlyFailed
	 *
	 * @since 1.9.1
	 */
	private function logFailedRequest()
	{
		update_option(
			$this->failedRequestCacheKey,
			strtotime('+1 hour')
		);
	}

	/**
	 * If available, show the changelog for sites in a multisite install.
	 */
	public function showChangelog()
	{

		if (empty($_REQUEST['edd_sl_action']) || $_REQUEST['edd_sl_action'] !== 'view_plugin_changelog') {
			return;
		}

		if (empty($_REQUEST['plugin'])) {
			return;
		}

		if (empty($_REQUEST['slug']) || $this->slug !== $_REQUEST['slug']) {
			return;
		}

		if (!current_user_can('update_plugins')) {
			wp_die(
				esc_html__('You do not have permission to install plugin updates', 'easy-digital-downloads'),
				esc_html__('Error', 'easy-digital-downloads'),
				['response' => 403]
			);
		}

		$versionInfo = $this->getRepoApiData();
		if (isset($versionInfo->sections)) {
			$sections = $this->convertObjectToArray($versionInfo->sections);
			if (!empty($sections['changelog'])) {
				echo '<div style="background:#fff;padding:10px;">' . wp_kses_post($sections['changelog']) . '</div>';
			}
		}

		exit;
	}

	/**
	 * Gets the current version information from the remote site.
	 *
	 * @return array<mixed>|false
	 */
	private function getVersionFromRemote()
	{
		$apiParams = [
			'edd_action' => 'get_version',
			'license' => !empty($this->apiData['license'])
				? $this->apiData['license']
				: '',
			'item_name' => $this->apiData['item_name'] ?? false,
			'item_id' => $this->apiData['item_id'] ?? false,
			'version' => $this->apiData['version'] ?? false,
			'slug' => $this->slug,
			'author' => $this->apiData['author'],
			'url' => home_url(),
			'beta' => $this->beta,
			'php_version' => phpversion(),
			'wp_version' => get_bloginfo('version'),
		];

		/**
		 * Filters the parameters sent in the API request.
		 *
		 * @param array<mixed> $apiParams The array of data sent in the request.
		 * @param array<mixed> $this- >apiData    The array of data set up in the class constructor.
		 * @param string $this- >pluginFile The full path and filename of the file.
		 */
		$apiParams = apply_filters(
			'edd_sl_plugin_updater_api_params',
			$apiParams,
			$this->apiData,
			$this->pluginFile
		);

		$request = wp_remote_post(
			$this->apiUrl,
			[
				'timeout' => 15,
				'sslverify' => $this->verifySsl(),
				'body' => $apiParams,
			]
		);

		if (is_wp_error($request) || (wp_remote_retrieve_response_code($request) !== 200)) {
			$this->logFailedRequest();

			return false;
		}

		$request = json_decode(wp_remote_retrieve_body($request));

		if ($request && isset($request->sections)) {
			$request->sections = maybe_unserialize($request->sections);
		} else {
			$request = false;
		}

		if ($request && isset($request->banners)) {
			$request->banners = maybe_unserialize($request->banners);
		}

		if ($request && isset($request->icons)) {
			$request->icons = maybe_unserialize($request->icons);
		}

		if (!empty($request->sections)) {
			foreach ($request->sections as $key => $section) {
				$request->$key = (array)$section;
			}
		}

		return $request;
	}

	/**
	 * Get the version info from the cache, if it exists.
	 *
	 * @param string $cacheKey
	 * @return object
	 */
	public function getCachedVersionInfo($cacheKey = '')
	{

		if (empty($cacheKey)) {
			$cacheKey = $this->getCacheKey();
		}

		$cache = get_option($cacheKey);

		// Cache is expired
		if (empty($cache['timeout']) || time() > $cache['timeout']) {
			return false;
		}

		// We need to turn the icons into an array, thanks to WP Core forcing these into an object at some point.
		$cache['value'] = json_decode($cache['value']);
		if (!empty($cache['value']->icons)) {
			$cache['value']->icons = (array)$cache['value']->icons;
		}

		return $cache['value'];
	}

	/**
	 * Adds the plugin version information to the database.
	 *
	 * @param string $value
	 * @param string $cacheKey
	 */
	public function setVersionInfoCache($value = '', $cacheKey = '')
	{

		if (empty($cacheKey)) {
			$cacheKey = $this->getCacheKey();
		}

		$data = [
			'timeout' => strtotime(
				'+3 hours',
				time()
			),
			'value' => wp_json_encode($value),
		];

		update_option(
			$cacheKey,
			$data,
			'no'
		);

		// Delete the duplicate option
		//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		delete_option('edd_api_request_' . md5(serialize($this->slug . $this->apiData['license'] . $this->beta)));
	}

	/**
	 * Returns if the SSL of the store should be verified.
	 *
	 * @return bool
	 * @since  1.6.13
	 */
	private function verifySsl()
	{
		return (bool)apply_filters(
			'edd_sl_api_request_verify_ssl',
			true,
			$this
		);
	}

	/**
	 * Gets the unique key (option name) for a plugin.
	 *
	 * @return string
	 * @since 1.9.0
	 */
	private function getCacheKey()
	{
		$string = $this->slug . $this->apiData['license'] . $this->beta;

		//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		return 'edd_sl_' . md5(serialize($string));
	}
}
