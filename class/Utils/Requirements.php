<?php
/**
 * Requirements checks for WordPress plugin
 *
 * @autor   Kuba Mikita (jakub@underdev.it)
 * @version 1.2.1
 * @usage   see https://github.com/Kubitomakita/Requirements
 * @package notification
 */

namespace BracketSpace\Notification\Utils;

/**
 * Requirements class
 */
class Requirements {

	/**
	 * Plugin display name
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * Array of checks
	 *
	 * @var array
	 */
	protected $checks;

	/**
	 * Array of check methods
	 *
	 * @var array
	 */
	private $check_methods;

	/**
	 * Array of errors
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Class constructor
	 *
	 * @param string $plugin_name plugin display name.
	 * @param array  $to_check    checks to perform.
	 */
	public function __construct( $plugin_name = '', $to_check = [] ) {

		$this->checks      = $to_check;
		$this->plugin_name = $plugin_name;

		// Add default checks.
		$this->add_check( 'php', [ $this, 'check_php' ] );
		$this->add_check( 'php_extensions', [ $this, 'check_php_extensions' ] );
		$this->add_check( 'wp', [ $this, 'check_wp' ] );
		$this->add_check( 'plugins', [ $this, 'check_plugins' ] );
		$this->add_check( 'theme', [ $this, 'check_theme' ] );
		$this->add_check( 'function_collision', [ $this, 'check_function_collision' ] );
		$this->add_check( 'class_collision', [ $this, 'check_class_collision' ] );

	}

	/**
	 * Adds the new check
	 *
	 * @param  string $check_name name of the check.
	 * @param  mixed  $callback   callable string or array.
	 * @return $this
	 */
	public function add_check( $check_name, $callback ) {

		$this->check_methods[ $check_name ] = $callback;

		return $this;

	}

	/**
	 * Runs checks
	 *
	 * @return $this
	 */
	public function check() {

		foreach ( $this->checks as $thing_to_check => $comparsion ) {

			if ( isset( $this->check_methods[ $thing_to_check ] ) && is_callable( $this->check_methods[ $thing_to_check ] ) ) {
				call_user_func( $this->check_methods[ $thing_to_check ], $comparsion, $this );
			}
		}

		return $this;

	}


	/**
	 * Adds the error
	 *
	 * @param string $error_message error message.
	 * @param bool   $allow_html    whether to allow HTML.
	 * @return $this
	 */
	public function add_error( $error_message, $allow_html = false ) {

		$this->errors[] = $allow_html ? $error_message : esc_html( $error_message );

		return $this;

	}

	/**
	 * Check if requirements has been satisfied
	 *
	 * @return boolean
	 */
	public function satisfied() {
		$this->check();
		return empty( $this->errors );
	}

	/**
	 * Displays notice for user about the plugin requirements
	 *
	 * @return void
	 */
	public function notice() {

		echo '<div class="error">';

			echo '<p><strong>The ' . esc_html( $this->plugin_name ) . ' plugin cannot be loaded</strong> because it needs:</p>';

			echo '<ul style="list-style: disc; padding-left: 20px;">';

		foreach ( $this->errors as $error ) {
			echo '<li>' . $error . '</li>'; // phpcs:ignore
		}

			echo '</ul>';

		echo '</div>';

	}

	/**
	 * Default check methods
	 */

	/**
	 * Check PHP version
	 *
	 * @param  string $version      version needed.
	 * @param  object $requirements requirements class.
	 * @return void
	 */
	public function check_php( $version, $requirements ) {

		if ( version_compare( phpversion(), $version, '<' ) ) {
			$requirements->add_error( sprintf( 'PHP at least in version %s. Your version is %s', $version, phpversion() ) );
		}

	}

	/**
	 * Check PHP extensions
	 *
	 * @param  string $extensions   array of extension names.
	 * @param  object $requirements requirements class.
	 * @return void
	 */
	public function check_php_extensions( $extensions, $requirements ) {

		$missing_extensions = [];

		foreach ( $extensions as $extension ) {
			if ( ! extension_loaded( $extension ) ) {
				$missing_extensions[] = $extension;
			}
		}

		if ( ! empty( $missing_extensions ) ) {
			$requirements->add_error(
				sprintf(
					// Translators: %s number of extensions.
					_n( 'PHP extension: %s', 'PHP extensions: %s', count( $missing_extensions ), 'notification' ),
					implode( ', ', $missing_extensions )
				)
			);
		}

	}

	/**
	 * Check WordPress version
	 *
	 * @param  string $version      version needed.
	 * @param  object $requirements requirements class.
	 * @return void
	 */
	public function check_wp( $version, $requirements ) {

		if ( version_compare( get_bloginfo( 'version' ), $version, '<' ) ) {
			$requirements->add_error( sprintf( 'WordPress at least in version %s. Your version is %s', $version, get_bloginfo( 'version' ) ) );
		}

	}

	/**
	 * Check if plugins are active and are in needed versions
	 *
	 * @param  array  $plugins       array with plugins,
	 *                               where key is the plugin file and value is the version.
	 * @param  object $requirements requirements class.
	 * @return void
	 */
	public function check_plugins( $plugins, $requirements ) {

		$active_plugins_raw      = wp_get_active_and_valid_plugins();
		$active_plugins          = [];
		$active_plugins_versions = [];

		foreach ( $active_plugins_raw as $plugin_full_path ) {
			$plugin_file                             = str_replace( WP_PLUGIN_DIR . '/', '', $plugin_full_path );
			$active_plugins[]                        = $plugin_file;
			$plugin_api_data                         = @get_file_data( $plugin_full_path, [ 'Version' ] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$active_plugins_versions[ $plugin_file ] = $plugin_api_data[0];
		}

		foreach ( $plugins as $plugin_file => $plugin_data ) {

			if ( ! in_array( $plugin_file, $active_plugins, true ) ) {
				$requirements->add_error( sprintf( '%s plugin active', $plugin_data['name'] ) );
			} elseif ( version_compare( $active_plugins_versions[ $plugin_file ], $plugin_data['version'], '<' ) ) {
				$requirements->add_error( sprintf( '%s plugin at least in version %s', $plugin_data['name'], $plugin_data['version'] ) );
			}
		}

	}

	/**
	 * Check if theme is active
	 *
	 * @param  array  $needed_theme theme data.
	 * @param  object $requirements requirements class.
	 * @return void
	 */
	public function check_theme( $needed_theme, $requirements ) {

		$theme = wp_get_theme();

		if ( $theme->get_template() !== $needed_theme['slug'] ) {
			$requirements->add_error( sprintf( '%s theme active', $needed_theme['name'] ) );
		}

	}

	/**
	 * Check function collision
	 *
	 * @param  array  $functions    function names.
	 * @param  object $requirements requirements class.
	 * @return void
	 */
	public function check_function_collision( $functions, $requirements ) {

		$collisions = [];

		foreach ( $functions as $function ) {
			if ( function_exists( $function ) ) {
				$collisions[] = $function;
			}
		}

		if ( ! empty( $collisions ) ) {
			$requirements->add_error(
				sprintf(
					// Translators: 1: function name, 2: function names.
					_n( 'register %s function but it\'s already taken', 'register %s functions but these are already taken', count( $collisions ), 'notification' ),
					implode( ', ', $collisions )
				)
			);
		}

	}

	/**
	 * Check class collision
	 *
	 * @param  array  $classes      class names.
	 * @param  object $requirements requirements class.
	 * @return void
	 */
	public function check_class_collision( $classes, $requirements ) {

		$collisions = [];

		foreach ( $classes as $class ) {
			if ( class_exists( $class ) ) {
				$collisions[] = $class;
			}
		}

		if ( ! empty( $collisions ) ) {
			$requirements->add_error(
				sprintf(
					// Translators: 1: class name, 2: class names.
					_n( 'register %s class but it\'s already defined', 'register %s classes but these are already defined', count( $collisions ), 'notification' ),
					implode( ', ', $collisions )
				)
			);
		}

	}

}
