<?php
/**
 * View class
 * Loads views
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils;

/**
 * View class
 */
class View {

	/**
	 * Files class
	 *
	 * @var object
	 */
	private $files;

	/**
	 * Views dir name
	 *
	 * @var string
	 */
	private $views_dir;

	/**
	 * View vars
	 *
	 * @var array
	 */
	private $vars = [];

	/**
	 * Class constructor
	 *
	 * @param Files $files Utils\Files instance.
	 */
	public function __construct( Files $files ) {

		$this->files     = $files;
		$this->views_dir = 'views';

	}

	/**
	 * Alters the views directory
	 *
	 * @param string $dir directory name.
	 * @return this
	 */
	public function set_views_dir( $dir ) {

		$this->views_dir = $dir;

		return $this;

	}

	/**
	 * Sets var
	 *
	 * @param  string $var_name  var slug.
	 * @param  mixed  $var_value var value.
	 * @param  bool   $override  override var if it already exists.
	 * @return this
	 */
	public function set_var( $var_name = null, $var_value = null, $override = false ) {

		if ( null === $var_name ) {
			return $this;
		}

		if ( ! $override && $this->get_var( $var_name ) !== null ) {
			trigger_error( 'Variable ' . esc_html( $var_name ) . ' already exists, skipping', E_USER_NOTICE );
			return $this;
		}

		$this->vars[ $var_name ] = $var_value;

		return $this;

	}

	/**
	 * Sets many vars at once
	 *
	 * @param array $vars array of vars in format: var name => var value.
	 * @return $this
	 */
	public function set_vars( $vars ) {

		if ( ! is_array( $vars ) ) {
			trigger_error( 'Variables to set should be in an array', E_USER_NOTICE );
			return $this;
		}

		foreach ( $vars as $var_name => $var_value ) {
			$this->set_var( $var_name, $var_value );
		}

		return $this;

	}

	/**
	 * Gets the var
	 *
	 * @param  string $var_name var name.
	 * @return mixed            var value or null
	 */
	public function get_var( $var_name ) {

		return isset( $this->vars[ $var_name ] ) ? $this->vars[ $var_name ] : null;

	}

	/**
	 * Prints the var
	 *
	 * @param  string $var_name var name.
	 * @return void
	 */
	public function echo_var( $var_name ) {
		echo $this->get_var( $var_name ); // phpcs:ignore
	}

	/**
	 * Removes var
	 *
	 * @param  string $var_name var name.
	 * @return this
	 */
	public function remove_var( $var_name ) {

		if ( isset( $this->vars[ $var_name ] ) ) {
			unset( $this->vars[ $var_name ] );
		}

		return $this;

	}

	/**
	 * Removes all vars
	 *
	 * @return this
	 */
	public function clear_vars() {
		$this->vars = [];
		return $this;
	}

	/**
	 * Gets view file and includes it
	 *
	 * @param  string $part file.
	 * @return this
	 */
	public function get_view( $part ) {

		$file_path = $this->files->file_path(
			[
				$this->views_dir,
				$part . '.php',
			]
		);

		include $file_path;

		return $this;

	}

	/**
	 * Gets view output as a string
	 *
	 * @param  string $part file.
	 * @return string       view output
	 */
	public function get_view_output( $part ) {
		ob_start();
		$this->get_view( $part );
		return ob_get_clean();
	}

}
