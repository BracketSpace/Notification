<?php
/**
 * Minimum_Requirements for Theme and plugin
 *
 * @package     Minimum_Requirements
 * @author      Enea Overclokk
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Minimum_Requirements for Theme and plugin
 * Plugin URI:  https://example.com/plugin-name
 * Description: ONLY FOR DEVELOPERS - Use it if you want to check the minimum requirements for you theme and plugin, include it in your project.
 * Version:     1.0.0
 * Author:      Enea Overclokk
 * Author URI:  https://www.italystrap.com
 * Text Domain: minimum_requirements
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! class_exists( 'Minimum_Requirements' ) ) {

	/**
	 * Class for the minimum plugin requirements
	 */
	class Minimum_Requirements {

		/**
		 * Minimum requirement PHP
		 *
		 * @var string
		 */
		private $php_ver;

		/**
		 * Minimum requirement WordPress
		 *
		 * @var string
		 */
		private $wp_ver;

		/**
		 * The nema of the them/plugin ti verify requirements
		 *
		 * @var string
		 */
		private $name;

		/**
		 * The name of the required plugin
		 *
		 * @var string
		 */
		private $plugins;

		/**
		 * The list of not found plugin
		 *
		 * @var array
		 */
		private $not_found = array();

		/**
		 * Init the constructor
		 *
		 * @param string $php_ver The minimum PHP version.
		 * @param string $wp_ver  The minimum WP version.
		 * @param string $name    The name of the theme/plugin to check.
		 * @param array  $plugins Required plugins format plugin_path/plugin_name.php.
		 * @throws InvalidArgumentException Get error if is not a string.
		 */
		public function __construct( $php_ver, $wp_ver, $name = '', array $plugins = array() ) {
			if ( ! is_string( $php_ver ) ) {
				throw new InvalidArgumentException( 'PHP version must be a string' );
			}

			if ( ! is_string( $wp_ver ) ) {
				throw new InvalidArgumentException( 'WordPress version must be a string' );
			}

			if ( ! is_string( $name ) ) {
				throw new InvalidArgumentException( 'Plugin name must be a string' );
			}

			$this->php_ver = $php_ver;

			$this->wp_ver = $wp_ver;

			$this->name = $name;

			$this->plugins = $plugins;

		}

		/**
		 * Checking the compatibility of the plugin with the version of PHP
		 * In case of incompatibility still fully loaded plugin (return)
		 *
		 * @return boolean Check PHP compatibility
		 */
		public function is_compatible_php() {

			if ( version_compare( phpversion(), $this->php_ver, '<' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Checking the compatibility of the plugin with the version of Wordpress
		 * In case of incompatibility still fully loaded plugin (return)
		 *
		 * @return boolean Check WordPress compatibility
		 */
		public function is_compatible_wordpress() {

			if ( version_compare( $GLOBALS['wp_version'], $this->wp_ver, '<' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Check if the required plugin is active
		 *
		 * @return bool Return true if plugin is active
		 */
		public function are_required_plugins_active() {

			if ( empty( $this->plugins ) ) {
				return true;
			}

			$result = true;

			$_active_plugins = wp_get_active_and_valid_plugins();
			$active_plugins  = array_filter( array_map( array( $this, 'get_plugin_name' ), $_active_plugins ) );

			foreach ( $this->plugins as $plugin ) {

				if ( ! in_array( $plugin, $active_plugins, true ) ) {
					$result            = false;
					$this->not_found[] = $plugin;
				}
			}

			return $result;

		}

		/**
		 * Checking compatibility with installed versions of the plugin
		 * In case of incompatibility still fully loaded plugin (return)
		 *
		 * @return boolean Check if plugin is compatible
		 */
		public function is_compatible_version() {

			if ( $this->is_compatible_php() && $this->is_compatible_wordpress() && $this->are_required_plugins_active() ) {
				return true;
			}

			return false;
		}

		/**
		 * Get the admin notice PHP
		 *
		 * @param  bolean $wrap Is wrappable or not.
		 *
		 * @return string       Return the admin notice for PHP
		 */
		public function get_admin_notices_php( $wrap ) {

			return $this->get_admin_notices_text( $wrap, 'PHP', phpversion(), $this->php_ver );
		}

		/**
		 * Get the admin notice WordPress
		 *
		 * @param  bolean $wrap Is wrappable or not.
		 *
		 * @return string       Return the admin notice for WordPress
		 */
		public function get_admin_notices_wordpress( $wrap ) {

			return $this->get_admin_notices_text( $wrap, 'WordPress', $GLOBALS['wp_version'], $this->wp_ver );
		}

		/**
		 * Get the admin notice for required plugin if is not activated
		 *
		 * @param  bolean $wrap Is wrappable or not.
		 *
		 * @return string Return the admin notice
		 */
		public function get_admin_notices_required_plugins( $wrap ) {

			$html = __( 'requires %s plugins to work', 'minimum_requirements' );

			if ( false === $wrap ) {
				$html = '<div>' . $html . '</div>';
			} else {
				$html = '<div class="error"><p><b>' . $this->name . '</b> - ' . $html . '</p></div>';
			}

			$plugins = implode( ', ', $this->not_found );

			return sprintf( $html, $plugins );
		}

		/**
		 * A function that creates a generic error to be displayed during
		 * the activation function or on the bulletin board of directors.
		 *
		 * @param  bolean $wrap Is wrappable or not.
		 * @param  string $s1   PHP or WordPress.
		 * @param  string $s2   Current version.
		 * @param  string $s3   Required version.
		 *
		 * @return string       Display errors
		 */
		public function get_admin_notices_text( $wrap, $s1, $s2, $s3 ) {

			$html = __( 'Your server is running %s version %s but this plugin requires at least %s', 'minimum_requirements' );

			if ( false === $wrap ) {
				$html = '<div>' . $html . '</div>';
			} else {
				$html = '<div class="error"><p><b>' . $this->name . '</b> - ' . $html . '</p></div>';
			}

			return sprintf( $html, $s1, $s2, $s3 );
		}

		/**
		 * Check if plugin is compatible, if it is not then it wont activate
		 * Show error message in case plugin is not compatible
		 */
		public function check_compatibility_on_install() {

			if ( ! $this->is_compatible_version() ) {

				$message = __( 'Activation of %s in not possible', 'minimum_requirements' );

				$html = '<div>' . __( 'Activation of ' . $this->name . ' in not possible', 'minimum_requirements' ) . ':</div><ul>';

				if ( ! $this->is_compatible_php() ) {
					$html .= '<li>' . $this->get_admin_notices_php( false ) . '</li>';
				}

				if ( ! $this->is_compatible_wordpress() ) {
					$html .= '<li>' . $this->get_admin_notices_wordpress( false ) . '</li>';
				}

				if ( ! $this->are_required_plugins_active() ) {
					$html .= '<li>' . $this->get_admin_notices_required_plugins( false ) . '</li>';
				}

				$html .= '</ul>';

				wp_die( $html, __( 'Activation of ' . $this->name . ' in not possible', 'minimum_requirements' ), array( 'back_link' => true ) ); // XSS ok.
			};
		}

		/**
		 * If the plugin is active, but the minimum requirements are not met
		 * the function is called to add the details on the notice board error
		 * Print error message
		 */
		public function load_plugin_admin_notices() {

			if ( ! $this->is_compatible_php() ) {
				echo $this->get_admin_notices_php( true ); // XSS ok.
			}

			if ( ! $this->is_compatible_wordpress() ) {
				echo $this->get_admin_notices_wordpress( true ); // XSS ok.
			}

			if ( ! $this->are_required_plugins_active() ) {
				echo $this->get_admin_notices_required_plugins( true ); // XSS ok.
			}
		}

		/**
		 * Get the plugin name
		 *
		 * @param  string $plugin The plguin_path/plugin_name.php.
		 *
		 * @return string         The plugin name
		 */
		private function get_plugin_name( $plugin ) {
			$plugin_data = get_plugin_data( $plugin );
			if ( ! ( is_array( $plugin_data ) && isset( $plugin_data['Name'] ) ) ) {
				return false;
			}

			return sanitize_title( $plugin_data['Name'] );
		}
	}

}
