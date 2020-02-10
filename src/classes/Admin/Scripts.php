<?php
/**
 * Enqueues admin scripts
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Vendor\Micropackage\Filesystem\Filesystem;

/**
 * Scripts class
 */
class Scripts {

	/**
	 * Filesystem object
	 *
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * Runtime class
	 *
	 * @var object
	 */
	private $runtime;

	/**
	 * Scripts constructor
	 *
	 * @since 5.0.0
	 * @param object     $runtime Plugin Runtime class.
	 * @param Filesystem $fs      Assets filesystem object.
	 */
	public function __construct( $runtime, Filesystem $fs ) {
		$this->filesystem = $fs;
		$this->runtime    = $runtime;
	}

	/**
	 * Enqueue scripts and styles for admin
	 *
	 * @action admin_enqueue_scripts
	 *
	 * @param  string $page_hook current page hook.
	 * @return void
	 */
	public function enqueue_scripts( $page_hook ) {

		$allowed_hooks = apply_filters( 'notification/scripts/allowed_hooks', [
			$this->runtime->admin_extensions->page_hook,
			$this->runtime->core_settings->page_hook,
			$this->runtime->admin_wizard->page_hook,
			'plugins.php',
			'post-new.php',
			'post.php',
			'edit.php',
		] );

		$allowed_post_types = apply_filters( 'notification/scripts/allowed_post_types', [
			'notification',
		] );

		if ( ! in_array( $page_hook, $allowed_hooks, true ) ) {
			return;
		}

		// Check if we are on a correct post type if we edit the post.
		if ( in_array( $page_hook, [ 'post-new.php', 'post.php', 'edit.php' ], true ) && ! in_array( get_post_type(), $allowed_post_types, true ) ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script( 'notification', $this->filesystem->url( 'js/scripts.js' ), [ 'jquery', 'wp-color-picker', 'wp-i18n', 'wp-hooks', 'jquery-ui-sortable' ], $this->filesystem->mtime( 'js/scripts.js' ), true );

		wp_enqueue_style( 'notification', $this->filesystem->url( 'css/style.css' ), [], $this->filesystem->mtime( 'css/style.css' ) );

		wp_set_script_translations( 'notification', 'notification' );

		wp_localize_script( 'notification', 'notification', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		] );

		// Remove TinyMCE styles as they are not applied to any frontend content.
		remove_editor_styles();

		do_action( 'notification/scripts', $page_hook );

	}

}
