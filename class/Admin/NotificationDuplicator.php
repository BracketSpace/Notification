<?php
/**
 * Notification duplicator class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

/**
 * Notification duplicator class
 */
class NotificationDuplicator {

	/**
	 * Adds duplicate link to row actions
	 *
	 * @filter post_row_actions
	 *
	 * @param  array  $row_actions array with action links.
	 * @param  object $post        WP_Post object.
	 * @return array               filtered actions
	 */
	public function add_duplicate_row_action( $row_actions, $post ) {

		if ( 'notification' !== $post->post_type ) {
			return $row_actions;
		}

		$row_actions['duplicate'] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin-post.php?action=notification_duplicate&duplicate=' . $post->ID ), __( 'Duplicate', 'notification' ) );

		return $row_actions;

	}

	/**
	 * Duplicates the notification
	 *
	 * @action admin_post_notification_duplicate
	 *
	 * @since  5.2.3
	 * @return void
	 */
	public function notification_duplicate() {

		if ( ! isset( $_GET['duplicate'] ) ) {
			exit;
		}

		// Get the source notification post.
		$source = get_post( sanitize_text_field( wp_unslash( $_GET['duplicate'] ) ) );

		if ( get_post_type( $source ) !== 'notification' ) {
			wp_die( 'You cannot duplicate post that\'s not Notification post' );
		}

		$new_id = wp_insert_post(
			array(
				'post_title'  => sprintf( '(%s) %s', __( 'Duplicate', 'notification' ), $source->post_title ),
				'post_status' => 'draft',
				'post_type'   => 'notification',
			)
		);

		// Copy all the meta data.
		$meta_data = get_post_custom( $source->ID );

		foreach ( $meta_data as $key => $values ) {
			foreach ( $values as $value ) {
				add_post_meta( $new_id, $key, maybe_unserialize( $value ) );
			}
		}

		wp_safe_redirect( html_entity_decode( get_edit_post_link( $new_id ) ) );
		exit;

	}

}
