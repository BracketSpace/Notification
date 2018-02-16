<?php
/**
 * Enqueues admin scripts
 *
 * @package notification
 */

namespace underDEV\Notification\Admin;

use underDEV\Notification\Utils\Files;

/**
 * Scripts class
 */
class Scripts {

	/**
	 * Files class
     *
	 * @var object
	 */
	private $files;

	/**
	 * Scripts constructor
	 *
	 * @since [Next]
	 * @param Files $files Files class.
	 */
	public function __construct( Files $files ) {
		$this->files = $files;
	}

	/**
	 * Enqueue scripts and styles for admin
     *
	 * @param  string $page_hook current page hook.
	 * @return void
	 */
	public function enqueue_scripts( $page_hook ) {

		$allowed_hooks = array(
			'notification_page_extensions',
			'notification_page_settings',
			'plugins.php',
			'post-new.php',
			'post.php'
		);

		if ( get_post_type() != 'notification' && ! in_array( $page_hook, $allowed_hooks )  ) {
			return;
		}

		wp_enqueue_script( 'notification', $this->files->asset_url( 'js', 'scripts.min.js' ), array( 'jquery' ), null, false );

		wp_enqueue_style( 'notification', $this->files->asset_url( 'css', 'style.css' ) );

		wp_localize_script( 'notification', 'notification', array(
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'i18n'     => array(
				'copied'              => __( 'Copied', 'notification' ),
				'remove_confirmation' => __( 'Do you really want to delete this?', 'notification' ),
			),
		) );

	}


}
