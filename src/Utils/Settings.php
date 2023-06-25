<?php

/**
 * Settings class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils;

use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
use BracketSpace\Notification\Utils\Settings\Section;

/**
 * Settings class
 */
class Settings
{
	use Casegnostic;

	/**
	 * Setting sections (ones in the Settings menu)
	 *
	 * @var array<mixed>
	 */
	private $sections = [];

	/**
	 * Settings handle, used as a prefix for options
	 *
	 * @var string
	 */
	public $handle;

	/**
	 * Textdomain for all strings, if not provided the handle is used
	 *
	 * @var string
	 */
	public $textdomain;

	/**
	 * Library root path
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Library root URI
	 *
	 * @var string
	 */
	public $uri = '';

	/**
	 * Settings page hook
	 * Should be set outside the class
	 * Typically result of add_menu_page function
	 *
	 * @var string
	 */
	public $pageHook = '';

	/**
	 * Settings constructor
	 *
	 * @param string $handle settings handle.
	 * @param string|bool $textdomain textdomain.
	 * @throws \Exception Exception.
	 * @since 5.0.0
	 */
	public function __construct($handle, $textdomain = false)
	{
		if (empty($handle)) {
			throw new \Exception('Handle cannot be empty');
		}

		$this->handle = $handle;

		if (empty($textdomain)) {
			$this->textdomain = $this->handle;
		}

		$this->setVariables();

		// settings autoload on admin side.
		add_action(
			'admin_init',
			[$this, 'setupFieldValues'],
			10
		);
		add_action(
			'admin_post_save_' . $this->handle . '_settings',
			[$this, 'saveSettings']
		);
	}

	/**
	 * Settings page output
	 *
	 * @return void
	 */
	public function settingsPage()
	{
		// We're using the GET variable only to get the section name.
		// phpcs:disable WordPress.Security.NonceVerification
		$sections = $this->getSections();

		$currentSection = ! empty($_GET['section'])
			? sanitize_text_field(wp_unslash($_GET['section']))
			: key($this->getSections());

		include $this->path . '/resources/templates/settings/page.php';
		// phpcs:enable
	}

	/**
	 * Add new section
	 *
	 * @param string $name Section name.
	 * @param string $slug Section slug.
	 * @return \BracketSpace\Notification\Utils\Settings\Section
	 */
	public function addSection($name, $slug)
	{

		if (!isset($this->sections[$slug])) {
			$this->sections[$slug] = new Section(
				$this->handle,
				$name,
				$slug
			);
		}

		return $this->sections[$slug];
	}

	/**
	 * Get all registered sections
	 *
	 * @return array<mixed>
	 */
	public function getSections()
	{

		return apply_filters(
			$this->handle . '/settings/sections',
			$this->sections,
			$this
		);
	}

	/**
	 * Get section by section slug
	 *
	 * @param string $slug section slug.
	 * @return mixed        section object or false if no section defined
	 */
	public function getSection($slug = '')
	{

		$sections = $this->getSections();

		if (isset($sections[$slug])) {
			return apply_filters(
				$this->handle . '/settings/section',
				$sections[$slug],
				$this
			);
		}

		return false;
	}

	/**
	 * Save Settings
	 *
	 * @return void
	 */
	public function saveSettings()
	{
		if (
			wp_verify_nonce(
				sanitize_text_field(wp_unslash($_POST['nonce'] ?? '')),
				'save_' . $this->handle . '_settings'
			) === false
		) {
			wp_die('Can\'t touch this');
		}

		$data = $_POST;

		$settings = $data[$this->handle . '_settings'];

		$toSave = [];

		foreach ($settings as $sectionSlug => $groupsValues) {
			foreach ($this->getSection($sectionSlug)->getGroups() as $group) {
				foreach ($group->getFields() as $field) {
					$value = isset($groupsValues[$field->group()][$field->slug()])
						? $field->sanitize($groupsValues[$field->group()][$field->slug()])
						: '';

					$toSave[$field->section()][$field->group()][$field->slug()] = $value;
				}
			}
		}

		foreach ($toSave as $section => $value) {
			update_option(
				$this->handle . '_' . $section,
				$value
			);
		}

		do_action(
			$this->handle . '/settings/saved',
			$toSave,
			$this
		);

		wp_safe_redirect(
			add_query_arg(
				'updated',
				'true',
				$data['_wp_http_referer']
			)
		);
	}

	/**
	 * Get all settings
	 *
	 * @return array<mixed> settings
	 */
	public function getSettings()
	{
		$settings = [];

		foreach ($this->getSections() as $sectionSlug => $section) {
			$setting = get_option($this->handle . '_' . $sectionSlug);

			$settings[$sectionSlug] = [];

			$groups = $section->getGroups();

			foreach ($groups as $groupSlug => $group) {
				$settings[$sectionSlug][$groupSlug] = [];

				$fields = $group->getFields();

				foreach ($fields as $fieldSlug => $field) {
					$value = $setting[$groupSlug][$fieldSlug] ?? $field->defaultValue();

					$settings[$sectionSlug][$groupSlug][$fieldSlug] = $value;
				}
			}
		}

		return apply_filters(
			$this->handle . '/settings/saved_settings',
			$settings,
			$this
		);
	}

	/**
	 * Sets up the field values for Settings form
	 *
	 * @return void
	 * @since  5.0.0
	 */
	public function setupFieldValues()
	{
		foreach ($this->getSections() as $sectionSlug => $section) {
			foreach ($section->getGroups() as $groupSlug => $group) {
				foreach ($group->getFields() as $fieldSlug => $field) {
					$settingName = implode(
						'/',
						[$sectionSlug, $groupSlug, $fieldSlug]
					);
					$field->value($this->getSetting($settingName));
				}
			}
		}
	}

	/**
	 * Get single setting value
	 *
	 * @param string $setting setting section/group/field separated with /.
	 * @return mixed           field value or null if name not found
	 * @throws \Exception Exception.
	 */
	public function getSetting($setting)
	{
		$parts = explode(
			'/',
			$setting
		);

		if (count($parts) !== 3) {
			throw new \Exception('You must provide exactly 3 parts as the setting name');
		}

		$settings = $this->getSettings();

		if (!isset($settings[$parts[0]], $settings[$parts[0]][$parts[1]], $settings[$parts[0]][$parts[1]][$parts[2]])) {
			return null;
		}

		$value = $settings[$parts[0]][$parts[1]][$parts[2]];

		return apply_filters(
			$this->handle . '/settings/setting/' . $setting,
			$value,
			$this
		);
	}

	/**
	 * Update single settings value.
	 *
	 * @param string $setting setting name in `a/b/c` format.
	 * @param mixed $value setting value.
	 * @return  mixed
	 * @throws \Exception Exception.
	 */
	public function updateSetting($setting, $value)
	{
		$parts = explode(
			'/',
			$setting
		);

		if (count($parts) !== 3) {
			throw new \Exception('You must provide exactly 3 parts as the setting name');
		}

		// phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
		list($sectionSlug, $groupSlug, $fieldSlug) = $parts;

		$section = $this->getSection($sectionSlug);

		if ($section === false) {
			throw new \Exception("Cannot find \"${sectionSlug}\" settings section.");
		}

		$sanitized = false;

		foreach ($section->getGroups() as $group) {
			if ($group->slug() !== $groupSlug) {
				continue;
			}

			foreach ($group->getFields() as $field) {
				if ($field->slug() !== $fieldSlug) {
					continue;
				}

				$value = $field->sanitize($value);
				$sanitized = true;
			}
		}

		if ($sanitized === false) {
			throw new \Exception("Cannot update \"${setting}\" setting.");
		}

		$settings = $this->getSettings();

		if (!is_array($settings)) {
			$settings = [];
		}

		if (!isset($settings[$sectionSlug])) {
			$settings[$sectionSlug] = [];
		}

		if (!isset($settings[$sectionSlug][$groupSlug])) {
			$settings[$sectionSlug][$groupSlug] = [];
		}

		$settings[$sectionSlug][$groupSlug][$fieldSlug] = $value;

		return update_option(
			$this->handle . '_' . $sectionSlug,
			$settings
		);
	}

	/**
	 * Set Library variables like path and URI
	 *
	 * @return void
	 */
	public function setVariables()
	{
		// path.
		$this->path = dirname(dirname(dirname(__FILE__)));

		// URI.
		$themeUrl = wp_parse_url(get_stylesheet_directory_uri());
		$themePos = strpos(
			$this->path,
			$themeUrl['path']
		);

		if ($themePos !== false) { // loaded from theme.
			$pluginRelativeDir = str_replace(
				$themeUrl['path'],
				'',
				substr(
					$this->path,
					$themePos
				)
			);
			$this->uri = $themeUrl['scheme'] . '://' . $themeUrl['host'] . $themeUrl['path'] . $pluginRelativeDir;
		} else { // loaded from plugin.
			$this->uri = trailingslashit(
				plugins_url(
					'',
					dirname(__FILE__)
				)
			);
		}
	}
}
