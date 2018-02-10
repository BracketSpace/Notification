<?php
/**
 * Runtime
 *
 * @package notification
 */

namespace underDEV\Notification;

use underDEV\Notification\Utils;
use underDEV\Notification\Admin;

/**
 * Runtime class
 */
class Runtime {

	/**
	 * Class constructor
	 *
	 * @since [Next]
	 * @param string $plugin_file plugin main file full path.
	 */
	public function __construct( $plugin_file ) {

		$this->plugin_file = $plugin_file;

		$this->singletons();
		$this->boot();
		$this->actions();

	}

	/**
	 * Loads needed files
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function boot() {

		require_once $this->files->file_path( 'inc/functions.php' );
		require_once $this->files->file_path( 'inc/defaults.php' );

	}

	/**
	 * Creates needed classes
	 * Singletons are used for a sake of performance
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function singletons() {

		$this->files               = new Utils\Files( $this->plugin_file );
		$this->internationaliation = new Internationalization( $this->files, 'notification' );
		$this->settings            = new Admin\Settings();
		$this->post_data           = new Admin\PostData();
		$this->admin_trigger       = new Admin\Trigger( $this->view(), $this->post_data );
		$this->admin_notifications = new Admin\Notifications( $this->boxrenderer(), $this->formrenderer(), $this->post_data );
		$this->admin_post_type     = new Admin\PostType( $this->admin_trigger, $this->admin_notifications );
		$this->admin_post_table    = new Admin\PostTable();
		$this->admin_merge_tags    = new Admin\MergeTags( $this->view(), $this->ajax() );
		$this->admin_scripts       = new Admin\Scripts( $this->files );
		$this->admin_recipients    = new Admin\Recipients( $this->view(), $this->ajax() );
		$this->admin_extensions    = new Admin\Extensions( $this->view() );

	}

	/**
	 * All WordPress actions this plugin utilizes
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function actions() {

		add_action( 'plugins_loaded', array( $this->internationaliation, 'load_textdomain' ) );

		add_action( 'init', array( $this->admin_post_type, 'register' ) );
		add_action( 'edit_form_after_title', array( $this->admin_post_type, 'render_trigger_select' ) );
		add_action( 'edit_form_after_title', array( $this->admin_post_type, 'render_notification_metaboxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this->admin_post_type, 'metabox_cleanup' ), 999999999 );

		add_filter( 'manage_notification_posts_columns', array( $this->admin_post_table, 'table_columns' ) );
		add_action( 'manage_notification_posts_custom_column', array( $this->admin_post_table, 'table_column_content' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this->admin_post_table, 'remove_quick_edit' ), 10, 2 );

		add_action( 'add_meta_boxes', array( $this->admin_merge_tags, 'add_meta_box' ) );

		add_action( 'save_post_notification', array( $this->admin_trigger, 'save' ) );
		add_action( 'save_post_notification', array( $this->admin_notifications, 'save' ) );

		add_action( 'admin_enqueue_scripts', array( $this->admin_scripts, 'enqueue_scripts' ) );

		add_action( 'wp_ajax_get_merge_tags_for_trigger', array( $this->admin_merge_tags, 'ajax_render' ) );
		add_action( 'wp_ajax_get_recipient_input', array( $this->admin_recipients, 'ajax_get_recipient_input' ) );

		add_action( 'admin_menu', array( $this->admin_extensions, 'register_page' ) );
		add_action( 'admin_menu', array( $this->settings, 'register_page' ), 20 );

		add_action( 'init', array( $this->settings, 'register_settings' ), 20 );

		notification_register_settings( array( $this->settings, 'general_settings' ) );
		notification_register_settings( array( $this->settings, 'triggers_settings' ), 20 );
		notification_register_settings( array( $this->settings, 'notifications_settings' ), 30 );

	}

	/**
	 * Returns new View object
	 *
	 * @since  [Next]
	 * @return View view object
	 */
	public function view() {
		return new Utils\View( $this->files );
	}

	/**
	 * Returns new Ajax object
	 *
	 * @since  [Next]
	 * @return Ajax ajax object
	 */
	public function ajax() {
		return new Utils\Ajax();
	}

	/**
	 * Returns new BoxRenderer object
	 *
	 * @since  [Next]
	 * @return BoxRenderer BoxRenderer object
	 */
	public function boxrenderer() {
		return new Admin\BoxRenderer( $this->view() );
	}

	/**
	 * Returns new FormRenderer object
	 *
	 * @since  [Next]
	 * @return FormRenderer FormRenderer object
	 */
	public function formrenderer() {
		return new Admin\FormRenderer( $this->view() );
	}

}
