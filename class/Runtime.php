<?php
/**
 * Runtime
 */

namespace underDEV\Notification;
use underDEV\Notification\Utils;
use underDEV\Notification\Admin;

class Runtime {

	public function __construct( $plugin_file ) {

		$this->plugin_file = $plugin_file;

		$this->singletons();
		$this->boot();
		$this->actions();

	}

	public function boot() {

		require_once( $this->files->file_path( 'inc/global.php' ) );
		require_once( $this->files->file_path( 'inc/default-triggers.php' ) );
		require_once( $this->files->file_path( 'inc/default-notifications.php' ) );
		require_once( $this->files->file_path( 'inc/default-recipients.php' ) );

	}

	public function singletons() {

		$this->files = new Utils\Files( $this->plugin_file );

		$this->internationaliation = new Internationalization( $this->files, 'notification' );

		$this->notifications = new Notifications();

		$this->triggers = new Triggers();

		$this->post_data = new Admin\PostData( $this->notifications, $this->triggers );

		$this->admin_trigger = new Admin\Trigger( $this->view(), $this->triggers, $this->post_data );

		$this->admin_notifications = new Admin\Notifications( $this->notifications, $this->boxrenderer(), $this->formrenderer(), $this->post_data );

		$this->admin_post_type = new Admin\PostType( $this->admin_trigger, $this->admin_notifications );

		$this->admin_post_table = new Admin\PostTable;

		$this->admin_merge_tags = new Admin\MergeTags( $this->view(), $this->ajax(), $this->triggers );

		$this->admin_scripts = new Admin\Scripts( $this->files );

	}

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

	}

	public function view() {
		return new Utils\View( $this->files );
	}

	public function ajax() {
		return new Utils\Ajax;
	}

	public function boxrenderer() {
		return new Admin\BoxRenderer( $this->view() );
	}

	public function formrenderer() {
		return new Admin\FormRenderer( $this->view() );
	}

}
