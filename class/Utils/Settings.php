<?php
/**
 * Settings class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils;

use BracketSpace\Notification\Utils\Settings\Section;

/**
 * Settings class``
 */
class Settings {

	/**
	 * Setting sections (ones in the Settings menu)
	 *
	 * @var array
	 */
	private $sections = [];

	/**
	 * All saved Settings
	 *
	 * @var array
	 */
	private $settings = [];

	/**
	 * Settings handle, used as a prefix for options
	 *
	 * @var string
	 */
	protected $handle;

	/**
	 * Textdomain for all strings, if not provided the handle is used
	 *
	 * @var string
	 */
	protected $textdomain;

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
	public $page_hook = '';

	/**
	 * Settings constructor
	 *
	 * @throws \Exception Exception.
	 * @since 5.0.0
	 * @param string $handle     settings handle.
	 * @param string $textdomain textdomain.
	 */
	public function __construct( $handle, $textdomain = false ) {

		if ( empty( $handle ) ) {
			throw new \Exception( 'Handle cannot be empty' );
		}

		$this->handle = $handle;

		if ( empty( $textdomain ) ) {
			$this->textdomain = $this->handle;
		}

		$this->set_variables();

		// settings autoload on admin side.
		add_action( 'admin_init', [ $this, 'setup_field_values' ], 10 );

		// save the settings config for later easly-action uses.
		add_action( 'admin_footer', [ $this, 'catch_config' ], 10 );

		add_action( 'admin_post_save_' . $this->handle . '_settings', [ $this, 'save_settings' ] );

	}

	/**
	 * Settings page output
	 *
	 * @return void
	 */
	public function settings_page() {

		$sections = $this->get_sections();

		if ( isset( $_GET['section'] ) && ! empty( $_GET['section'] ) ) { // phpcs:ignore
			$current_section = sanitize_text_field( wp_unslash( $_GET['section'] ) ); // phpcs:ignore
		} else {
			$current_section = key( $this->get_sections() );
		}

		include $this->path . '/views/settings/page.php';

	}

	/**
	 * Add new section
	 *
	 * @param string $name Section name.
	 * @param string $slug Section slug.
	 * @return Section
	 */
	public function add_section( $name, $slug ) {

		if ( ! isset( $this->sections[ $slug ] ) ) {
			$this->sections[ $slug ] = new Section( $this->handle, $name, $slug );
		}

		return $this->sections[ $slug ];

	}

	/**
	 * Get all registered sections
	 *
	 * @return array
	 */
	public function get_sections() {

		return apply_filters( $this->handle . '/settings/sections', $this->sections, $this );

	}

	/**
	 * Get section by section slug
	 *
	 * @param  string $slug section slug.
	 * @return mixed        section object or false if no section defined
	 */
	public function get_section( $slug = '' ) {

		$sections = $this->get_sections();

		if ( isset( $sections[ $slug ] ) ) {
			return apply_filters( $this->handle . '/settings/section', $sections[ $slug ], $this );
		}

		return false;

	}

	/**
	 * Save Settings
	 *
	 * @return void
	 */
	public function save_settings() {

		$data = $_POST; // phpcs:ignore

		if ( wp_verify_nonce( $data['nonce'], 'save_' . $this->handle . '_settings' ) === false ) {
			wp_die( 'Can\'t touch this' );
		}

		$settings = $data[ $this->handle . '_settings' ];

		$to_save = [];

		foreach ( $settings as $section_slug => $groups_values ) {

			foreach ( $this->get_section( $section_slug )->get_groups() as $group ) {

				foreach ( $group->get_fields() as $field ) {

					if ( isset( $groups_values[ $field->group() ][ $field->slug() ] ) ) {
						$value = $field->sanitize( $groups_values[ $field->group() ][ $field->slug() ] );
					} else {
						$value = '';
					}

					$to_save[ $field->section() ][ $field->group() ][ $field->slug() ] = $value;

				}
			}
		}

		foreach ( $to_save as $section => $value ) {
			update_option( $this->handle . '_' . $section, $value );
		}

		wp_safe_redirect( add_query_arg( 'updated', 'true', $data['_wp_http_referer'] ) );

	}

	/**
	 * Get all settings
	 * Uses stored config
	 *
	 * @return array settings
	 */
	public function get_settings() {

		if ( empty( $this->settings ) ) {

			$config = get_option( '_' . $this->handle . '_settings_config' );

			foreach ( (array) $config as $section_slug => $groups ) {

				$setting = get_option( $this->handle . '_' . $section_slug );

				$this->settings[ $section_slug ] = [];

				if ( empty( $groups ) ) {
					continue;
				}

				foreach ( $groups as $group_slug => $fields ) {

					$this->settings[ $section_slug ][ $group_slug ] = [];

					foreach ( $fields as $field_slug => $default_value ) {

						if ( isset( $setting[ $group_slug ][ $field_slug ] ) ) {
							$value = $setting[ $group_slug ][ $field_slug ];
						} else {
							$value = $default_value;
						}

						$this->settings[ $section_slug ][ $group_slug ][ $field_slug ] = $value;

					}
				}
			}
		}

		return apply_filters( $this->handle . '/settings/saved_settings', $this->settings, $this );

	}

	/**
	 * Sets up the field values for Settings form
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function setup_field_values() {

		foreach ( $this->get_sections() as $section_slug => $section ) {

			foreach ( $section->get_groups() as $group_slug => $group ) {

				foreach ( $group->get_fields() as $field_slug => $field ) {

					$setting_name = implode( '/', [ $section_slug, $group_slug, $field_slug ] );
					$field->value( $this->get_setting( $setting_name ) );

				}
			}
		}

	}


	/**
	 * Get single setting value
	 *
	 * @throws \Exception Exception.
	 * @param  string $setting setting section/group/field separated with /.
	 * @return mixed           field value or null if name not found
	 */
	public function get_setting( $setting ) {

		$parts = explode( '/', $setting );

		if ( count( $parts ) !== 3 ) {
			throw new \Exception( 'You must provide exactly 3 parts as the setting name' );
		}

		$settings = $this->get_settings();

		if ( ! isset( $settings[ $parts[0] ], $settings[ $parts[0] ][ $parts[1] ], $settings[ $parts[0] ][ $parts[1] ][ $parts[2] ] ) ) {
			return null;
		}

		$value = $settings[ $parts[0] ][ $parts[1] ][ $parts[2] ];

		return apply_filters( $this->handle . '/settings/setting/' . $setting, $value, $this );

	}

	/**
	 * Set Library variables like path and URI
	 *
	 * @return void
	 */
	public function set_variables() {

		// path.
		$this->path = dirname( dirname( dirname( __FILE__ ) ) );

		// URI.
		$theme_url = wp_parse_url( get_stylesheet_directory_uri() );
		$theme_pos = strpos( $this->path, $theme_url['path'] );

		if ( false !== $theme_pos ) { // loaded from theme.

			$plugin_relative_dir = str_replace( $theme_url['path'], '', substr( $this->path, $theme_pos ) );
			$this->uri           = $theme_url['scheme'] . '://' . $theme_url['host'] . $theme_url['path'] . $plugin_relative_dir;

		} else { // loaded from plugin.

			$this->uri = trailingslashit( plugins_url( '', dirname( __FILE__ ) ) );

		}

	}

	/**
	 * Catches settings config and saves it for later use
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function catch_config() {

		$config = [];

		foreach ( $this->get_sections() as $section_slug => $section ) {

			$config[ $section_slug ] = [];

			foreach ( $section->get_groups() as $group_slug => $group ) {

				$config[ $section_slug ][ $group_slug ] = [];

				foreach ( $group->get_fields() as $field_slug => $field ) {

					$config[ $section_slug ][ $group_slug ][ $field_slug ] = $field->default_value();

				}
			}
		}

		$config_hash = md5( wp_json_encode( $config ) );
		$prev_hash   = get_option( '_' . $this->handle . '_settings_hash' );

		if ( $config_hash !== $prev_hash ) {
			update_option( '_' . $this->handle . '_settings_config', $config );
			update_option( '_' . $this->handle . '_settings_hash', $config_hash );
		}

	}

}
