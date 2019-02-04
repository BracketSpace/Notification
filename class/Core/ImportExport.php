<?php
/**
 * Import/Export class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Import/Export class
 */
class ImportExport {

	/**
	 * Registers Import/Export settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function settings( $settings ) {

		$importexport = $settings->add_section( __( 'Import / Export', 'notification' ), 'import_export' );

		$importexport->add_group( __( 'Import', 'notification' ), 'import' )
			->add_field(
				array(
					'name'     => __( 'Notifications', 'notification' ),
					'slug'     => 'notifications',
					'addons'   => array(
						'message' => $this->notification_import_form(),
					),
					'render'   => array( new CoreFields\Message(), 'input' ),
					'sanitize' => array( new CoreFields\Message(), 'sanitize' ),
				)
			);

		$importexport->add_group( __( 'Export', 'notification' ), 'export' )
			->add_field(
				array(
					'name'     => __( 'Notifications', 'notification' ),
					'slug'     => 'notifications',
					'addons'   => array(
						'message' => $this->notification_export_form(),
					),
					'render'   => array( new CoreFields\Message(), 'input' ),
					'sanitize' => array( new CoreFields\Message(), 'sanitize' ),
				)
			);

	}

	/**
	 * Returns notifications import form
	 *
	 * @since  [Next]
	 * @return string
	 */
	public function notification_import_form() {
		$view = notification_create_view();
		return $view->get_view_output( 'import/notifications' );
	}

	/**
	 * Returns notifications export form
	 *
	 * @since  [Next]
	 * @return string
	 */
	public function notification_export_form() {

		$view = notification_create_view();

		$view->set_var( 'notifications', notification_get_posts() );
		$view->set_var( 'download_link', admin_url( 'admin-post.php?action=notification_export&nonce=' . wp_create_nonce( 'notification-export' ) . '&type=notifications&items=' ) );

		return $view->get_view_output( 'export/notifications' );

	}

	/**
	 * Handles export request
	 *
	 * @action admin_post_notification_export
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function export_request() {

		check_admin_referer( 'notification-export', 'nonce' );

		if ( ! isset( $_GET['type'] ) ) {
			wp_die( 'Wrong export type. Please go back and try again.' );
		}

		$type = sanitize_text_field( wp_unslash( $_GET['type'] ) );

		try {
			$data = call_user_func( array( $this, 'prepare_' . $type . '_export_data' ) );
		} catch ( \Exception $e ) {
			wp_die( esc_html( $e->getMessage() ), '', array( 'back_link' => true ) );
		}

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=notification-export-' . $type . '-' . current_time( 'Y-m-d-H-i-s' ) . '.json' );
		header( 'Content-Type: application/json; charset=utf-8' );

		echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		die;

	}

	/**
	 * Prepares notifications data for export
	 *
	 * @throws \Exception When no items selected for export.
	 * @since  [Next]
	 * @return array
	 */
	public function prepare_notifications_export_data() {

		if ( ! isset( $_GET['items'] ) || empty( $_GET['items'] ) ) { // phpcs:ignore
			throw new \Exception( __( 'No items selected for export' ) );
		}

		$data  = array();
		$items = explode( ',', sanitize_text_field( wp_unslash( $_GET['items'] ) ) ); // phpcs:ignore
		$posts = get_posts( array(
			'post_type'      => 'notification',
			'posts_per_page' => -1,
			'post__in'       => $items,
		) );

		foreach ( $posts as $wppost ) {

			$notification = notification_get_post( $wppost );
			$data[]       = $this->get_notification_data( $notification );

		}

		return $data;

	}

	/**
	 * Gets single notification export data
	 *
	 * @since  [Next]
	 * @param  Notification $notification Notification post object.
	 * @return array
	 */
	public function get_notification_data( $notification ) {

		$notifications = array();

		foreach ( $notification->get_notifications( 'objects', true ) as $notification_type ) {
			$fields = array();
			foreach ( $notification_type->get_form_fields() as $field ) {
				if ( $field->get_raw_name() === '_nonce' ) {
					continue;
				}
				$fields[ $field->get_raw_name() ] = $field->get_value();
			}
			$notifications[ $notification_type->get_slug() ] = $fields;
		}

		// Hook into this filter to add extra export data. Should add a unique key and export values.
		$extras = apply_filters( 'notification/post/export/extras', array(), $notification );

		return array(
			'hash'          => $notification->get_hash(),
			'title'         => $notification->get_title(),
			'trigger'       => $notification->get_trigger(),
			'notifications' => $notifications,
			'enabled'       => $notification->is_enabled(),
			'extras'        => $extras,
			'version'       => $notification->get_version(),
		);

	}

	/**
	 * Handles import request
	 *
	 * @action wp_ajax_notification_import_json
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function import_request() {

		if ( false === check_ajax_referer( 'import-notifications', 'nonce', false ) ) {
			wp_send_json_error( __( 'Security check failed. Please refresh the page and try again' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to import notifications' ) );
		}

		if ( ! isset( $_POST['type'] ) ) {
			wp_send_json_error( __( 'Wrong import type' ) );
		}

		if ( ! isset( $_FILES[0] ) ) {
			wp_send_json_error( __( 'Please select file for import' ) );
		}

		// phpcs:disable
		$file = fopen( $_FILES[0]['tmp_name'], 'rb' );
		$json = fread( $file, filesize( $_FILES[0]['tmp_name'] ) );
		fclose( $file );
		unlink( $_FILES[0]['tmp_name'] );
		// phpcs:enable

		$data = json_decode( $json, true );
		$type = sanitize_text_field( wp_unslash( $_POST['type'] ) );

		try {
			$result = call_user_func( array( $this, 'process_' . $type . '_import_request' ), $data );
		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		wp_send_json_success( $result );

	}

	/**
	 * Imports notifications
	 *
	 * @since  [Next]
	 * @param  array $data Notifications data.
	 * @return string
	 */
	public function process_notifications_import_request( $data ) {

		$added   = 0;
		$skipped = 0;
		$updated = 0;

		foreach ( $data as $notification_data ) {
			$existing_notification = notification_get_post_by_hash( $notification_data['hash'] );
			if ( empty( $existing_notification ) ) {
				notification_create( $notification_data );
				$added++;
			} else {
				if ( $existing_notification->get_version() >= $notification_data['version'] ) {
					$skipped++;
				} else {
					notification_update( $notification_data );
					$updated++;
				}
			}
		}

		// translators: number and number and number of notifications.
		return sprintf( __( '%1$d notifications imported successfully. %2$d updated. %3$d skipped.' ), ( $added + $updated ), $updated, $skipped );

	}

}
