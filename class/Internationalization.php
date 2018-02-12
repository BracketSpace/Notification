<?php
/**
 * Internationalization (i18n) class
 * Loads plugin textdomain
 *
 * Since WordPress 4.6 translations are loaded via repository
 *
 * @package notification
 */

namespace underDEV\Notification;

use underDEV\Notification\Utils\Files;

/**
 * Internationalization class
 */
class Internationalization {

	/**
	 * Files class
     *
	 * @var object
	 */
	protected $files;

	/**
	 * Textomain
     *
	 * @var string
	 */
	protected $textdomain;

	/**
	 * Class constructor
     *
	 * @param object $files      instance of Files object.
	 * @param string $textdomain textdomain string.
	 */
	public function __construct( Files $files, $textdomain ) {
		$this->files      = $files;
		$this->textdomain = $textdomain;
	}

	/**
	 * Loads plugin textdomain
     *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( $this->textdomain, false, $this->files->dir_path( 'languages' ) );
	}

	/**
	 * Fixes admin strings returning
     *
	 * @return void
	 */
	public function load_native_admin_textdomain() {
		if ( ! is_admin() ) {
			load_textdomain( 'default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo' );
		}
	}

}
