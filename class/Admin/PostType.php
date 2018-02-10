<?php
/**
 * Handles Post Type
 */

namespace underDEV\Notification\Admin;

class PostType {

	/**
	 * Class constructor
	 */
	public function __construct( Trigger $trigger, Notifications $notifications ) {
		$this->trigger       = $trigger;
		$this->notifications = $notifications;
		add_filter( 'notification/admin/allow_metabox/submitdiv', '__return_true' );
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
			'labels'              => $labels,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
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
			'capability_type'     => apply_filters( 'notification/cpt/capability_type', 'post' ),
			'supports'            => array( 'title' )
		) );

	}

	/**
	 * Moves the metaboxes under title in WordPress
     *
	 * @param  object $post WP_Post
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
	 * @param  object $post WP_Post
	 * @return void
	 */
	public function render_notification_metaboxes( $post ) {

		if ( get_post_type( $post ) != 'notification' ) {
			return;
		}

		echo '<h3 class="notifications-section-title">' . __( 'Notifications', 'notification' ) . '</h3>';
		echo '<div id="notification-boxes">';
	    	$this->notifications->render_notifications();
    	echo '</div>';

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
