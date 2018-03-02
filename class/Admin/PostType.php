<?php
/**
 * Handles Post Type
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;

/**
 * PostType class
 */
class PostType {

	/**
	 * PostType constructor
	 *
	 * @since 5.0.0
	 * @param Trigger       $trigger       Trigger class.
	 * @param Notifications $notifications Notifications class.
	 * @param View          $view          View class.
	 */
	public function __construct( Trigger $trigger, Notifications $notifications, View $view ) {
		$this->trigger       = $trigger;
		$this->notifications = $notifications;
		$this->view          = $view;
	}

	/**
	 * Registers Notification post type
     *
	 * @return void
	 */
	public function register() {

		$labels = array(
			'name'                => __( 'Notifications', 'notification' ),
			'singular_name'       => __( 'Notification', 'notification' ),
			'add_new'             => _x( 'Add New Notification', 'notification', 'notification' ),
			'add_new_item'        => __( 'Add New Notification', 'notification' ),
			'edit_item'           => __( 'Edit Notification', 'notification' ),
			'new_item'            => __( 'New Notification', 'notification' ),
			'view_item'           => __( 'View Notification', 'notification' ),
			'search_items'        => __( 'Search Notifications', 'notification' ),
			'not_found'           => __( 'No Notifications found', 'notification' ),
			'not_found_in_trash'  => __( 'No Notifications found in Trash', 'notification' ),
			'parent_item_colon'   => __( 'Parent Notification:', 'notification' ),
			'menu_name'           => __( 'Notifications', 'notification' ),
		);

		register_post_type( 'notification', array(
			'labels'              => apply_filters( 'notification/whitelabel/cpt/labels', $labels ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => apply_filters( 'notification/whitelabel/cpt/parent', true ),
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-megaphone',
			'menu_position'       => 103,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
			'rewrite'             => false,
			'capabilities'        => apply_filters( 'notification/post_type/capabilities', array(
			    'edit_post'          => 'manage_options',
			    'read_post'          => 'manage_options',
			    'delete_post'        => 'manage_options',
			    'edit_posts'         => 'manage_options',
			    'edit_others_posts'  => 'manage_options',
			    'delete_posts'       => 'manage_options',
			    'publish_posts'      => 'manage_options',
			    'read_private_posts' => 'manage_options'
			) ),
			'supports'            => array( 'title' )
		) );

	}

	/**
	 * Moves the metaboxes under title in WordPress
     *
	 * @param  object $post WP_Post.
	 * @return void
	 */
	public function render_trigger_select( $post ) {

		if ( get_post_type( $post ) != 'notification' ) {
			return;
		}

		echo '<h3 class="trigger-section-title">' . __( 'Trigger', 'notification' ) . '</h3>';
		$this->trigger->render_select( $post );

	}

	/**
	 * Adds Notifications section title on post edit screen,
	 * just under the Trigger and prints Notifications metaboxes
     *
	 * @param  object $post WP_Post.
	 * @return void
	 */
	public function render_notification_metaboxes( $post ) {

		if ( get_post_type( $post ) != 'notification' ) {
			return;
		}

		echo '<h3 class="notifications-section-title">' . __( 'Notifications', 'notification' ) . '</h3>';

		do_action( 'notitication/admin/notifications/pre', $post );

		echo '<div id="notification-boxes">';
	    	$this->notifications->render_notifications();
    	echo '</div>';

    	do_action( 'notitication/admin/notifications', $post );

	}

	/**
	 * Adds metabox with Save button
     *
	 * @return void
	 */
	public function add_save_meta_box() {

		add_meta_box(
            'notification_save',
            __( 'Save', 'notification' ),
            array( $this, 'save_metabox' ),
            'notification',
            'side',
            'high'
        );

		// enable metabox.
        add_filter( 'notification/admin/allow_metabox/notification_save', '__return_true' );

	}

	/**
	 * Saves post status in relation to on/off switch
	 *
	 * @since  5.0.0
	 * @param  array $data    post data.
	 * @param  array $postarr saved data.
	 * @return array
	 */
	public function save_notification_status( $data, $postarr ) {

		// fix for brand new posts.
		if ( $data['post_status'] == 'auto-draft' ) {
			return $data;
		}

		if ( $data['post_type'] != 'notification' ||
			$postarr['post_status'] == 'trash' ||
			( isset( $_POST['action'] ) && $_POST['action'] == 'change_notification_status' ) ) {
			return $data;
		}

		if ( isset( $postarr['onoffswitch'] ) && $postarr['onoffswitch'] == '1' ) {
			$data['post_status'] = 'publish';
		} else {
			$data['post_status'] = 'draft';
		}

		return $data;

	}

	/**
	 * Prints Save metabox
     *
     * @param  object $post current WP_Post.
	 * @return void
	 */
	public function save_metabox( $post ) {

		if ( ! EMPTY_TRASH_DAYS ) {
			$delete_text = __( 'Delete Permanently', 'notification' );
		} else {
			$delete_text = __( 'Move to Trash', 'notification' );
		}

		$enabled = notification_is_new_notification( $post ) || get_post_status( $post->ID ) != 'draft';

		$this->view->set_var( 'enabled', $enabled );
		$this->view->set_var( 'post_id', $post->ID );
		$this->view->set_var( 'delete_link_label', $delete_text );

		$this->view->get_view( 'save-metabox' );

	}

	/**
	 * Cleans up all metaboxes to keep the screen nice and clean
     *
	 * @return void
	 */
	public function metabox_cleanup() {

		global $wp_meta_boxes;

		if ( ! isset( $wp_meta_boxes['notification'] ) ) {
			return;
		}

		foreach ( $wp_meta_boxes['notification'] as $context_name => $context ) {

			foreach ( $context as $priority => $boxes ) {

				foreach ( $boxes as $box_id => $box ) {

					$allow_box = apply_filters( 'notification/admin/allow_metabox/' . $box_id, false );

					if ( ! $allow_box ) {
						unset( $wp_meta_boxes['notification'][ $context_name ][ $priority ][ $box_id ] );
					}

				}

			}

		}

	}

}
