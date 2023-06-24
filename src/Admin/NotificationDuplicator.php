<?php
/**
 * Notification duplicator class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Notification;

/**
 * Notification duplicator class
 */
class NotificationDuplicator {

	/**
	 * Adds duplicate link to row actions
	 *
	 * @filter post_row_actions 50
	 *
	 * @param  array  $row_actions array with action links.
	 * @param  object $post        WP_Post object.
	 * @return array               filtered actions
	 */
	public function add_duplicate_row_action( $row_actions, $post ) {
		if ( 'notification' !== $post->post_type ) {
			return $row_actions;
		}

		$row_actions['duplicate'] = sprintf(
			'<a href="%s">%s</a>',
			admin_url( sprintf(
				'admin-post.php?action=notification_duplicate&duplicate=%s&nonce=%s',
				$post->ID,
				wp_create_nonce( 'duplicate_notification' )
			) ),
			__( 'Duplicate', 'notification' )
		);

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
		check_admin_referer( 'duplicate_notification', 'nonce' );

		if ( ! isset( $_GET['duplicate'] ) ) {
			exit;
		}

		// Get the source notification post.
		$source = get_post( intval( wp_unslash( $_GET['duplicate'] ) ) );
		$wp     = notification_adapt_from( 'WordPress', $source );

		/**
		 * JSON Adapter
		 *
		 * @var \BracketSpace\Notification\Defaults\Adapter\JSON
		 */
		$json = notification_swap_adapter( 'JSON', $wp );

		$json->refresh_hash();
		$json->set_enabled( false );

		if ( get_post_type( $source ) !== 'notification' ) {
			wp_die( 'You cannot duplicate post that\'s not Notification post' );
		}

		$new_id = wp_insert_post( [
			'post_title'   => sprintf( '(%s) %s', __( 'Duplicate', 'notification' ), $source->post_title ),
			'post_content' => wp_slash( $json->save( JSON_UNESCAPED_UNICODE ) ),
			'post_status'  => 'draft',
			'post_type'    => 'notification',
		] );

		wp_safe_redirect( html_entity_decode( get_edit_post_link( $new_id ) ) );
		exit;
	}

}
