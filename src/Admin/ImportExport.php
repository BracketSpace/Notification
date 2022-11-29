<?php

/**
 * Import/Export class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\Settings\Fields as SettingFields;
use BracketSpace\Notification\Queries\NotificationQueries;

/**
 * Import/Export class
 */
class ImportExport
{

	/**
	 * Registers Import/Export settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function settings( $settings )
	{
		$importexport = $settings->addSection(__('Import / Export', 'notification'), 'import_export');

		$importexport->addGroup(__('Import', 'notification'), 'import')
			->addField(
				[
					'name' => __('Notifications', 'notification'),
					'slug' => 'notifications',
					'render' => [ new SettingFields\Import(), 'input' ],
					'sanitize' => '__return_null',
				]
			);

		$importexport->addGroup(__('Export', 'notification'), 'export')
			->addField(
				[
					'name' => __('Notifications', 'notification'),
					'slug' => 'notifications',
					'render' => [ new SettingFields\Export(), 'input' ],
					'sanitize' => '__return_null',
				]
			);
	}

	/**
	 * Handles export request
	 *
	 * @action admin_post_notification_export
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function exportRequest()
	{
		check_admin_referer('notification-export', 'nonce');

		if (! isset($_GET['type'])) {
			wp_die('Wrong export type. Please go back and try again.');
		}

		$type = sanitize_text_field(wp_unslash($_GET['type']));

		try {
			$data = call_user_func(
				[ $this, 'prepare_' . $type . '_export_data' ],
				explode(',', sanitize_text_field(wp_unslash($_GET['items'] ?? '')))
			);
		} catch (\Throwable $e) {
			wp_die(esc_html($e->getMessage()), '', [ 'back_link' => true ]);
		}

		header('Content-Description: File Transfer');
		header('Content-Type: application/json; charset=utf-8');
		header(
			sprintf(
				'Content-Disposition: attachment; filename=notification-export-%s-%s.json',
				$type,
				current_time('Y-m-d-H-i-s')
			)
		);

		echo wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		die;
	}

	/**
	 * Prepares notifications data for export
	 *
	 * @throws \Exception When no items selected for export.
	 * @since  6.0.0
	 * @since  8.0.2 Accepts the items argument, instead reading it from GET.
	 * @param  array<int,string> $items Items to export.
	 * @return array<int,string>
	 */
	public function prepareNotificationsExportData( array $items = [] )
	{
		if (empty($items)) {
			throw new \Exception(__('No items selected for export'));
		}

		$data = [];
		$posts = get_posts(
			[
			'post_type' => 'notification',
			'post_status' => [ 'publish', 'draft' ],
			'posts_per_page' => -1,
			'post__in' => $items,
			]
		);

		foreach ($posts as $wppost) {
			$wpAdapter = notification_adapt_from('WordPress', $wppost);

			/**
			 * JSON Adapter
			 *
			 * @var \BracketSpace\Notification\Defaults\Adapter\JSON
			 */
			$jsonAdapter = notification_swap_adapter('JSON', $wpAdapter);
			$json = $jsonAdapter->save(null, false);

			// Decode because it's encoded in the last step of export.
			$data[] = json_decode($json);
		}

		return $data;
	}

	/**
	 * Handles import request
	 *
	 * @action wp_ajax_notification_import_json
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function importRequest()
	{
		if (check_ajax_referer('import-notifications', 'nonce', false) === false) {
			wp_send_json_error(__('Security check failed. Please refresh the page and try again'));
		}

		if (! current_user_can('manage_options')) {
			wp_send_json_error(__('You are not allowed to import notifications'));
		}

		if (! isset($_POST['type'])) {
			wp_send_json_error(__('Wrong import type'));
		}

		if (empty($_FILES)) {
			wp_send_json_error(__('Please select file for import'));
		}

		// phpcs:disable
		$file = fopen( $_FILES[0]['tmp_name'], 'rb' );
		$json = fread( $file, filesize( $_FILES[0]['tmp_name'] ) );
		fclose( $file );
		unlink( $_FILES[0]['tmp_name'] );
		// phpcs:enable

		$data = json_decode($json, true);
		$type = sanitize_text_field(wp_unslash($_POST['type']));

		// Wrap the singular notification into a collection.
		if (isset($data['hash'])) {
			$data = [ $data ];
		}

		try {
			$result = call_user_func([ $this, 'process_' . $type . '_import_request' ], $data);
		} catch (\Throwable $e) {
			wp_send_json_error($e->getMessage());
		}

		wp_send_json_success($result);
	}

	/**
	 * Imports notifications
	 *
	 * @since  6.0.0
	 * @param  array $data Notifications data.
	 * @return string
	 */
	public function processNotificationsImportRequest( $data )
	{
		$added = 0;
		$skipped = 0;
		$updated = 0;

		foreach ($data as $notificationData) {
			$jsonAdapter = notification_adapt_from('JSON', wp_json_encode($notificationData));

			/**
			 * WordPress Adapter
			 *
			 * @var \BracketSpace\Notification\Defaults\Adapter\WordPress
			 */
			$wpAdapter = notification_swap_adapter('WordPress', $jsonAdapter);

			/**
			 * @var \BracketSpace\Notification\Defaults\Adapter\WordPress|null
			 */
			$existingNotification = NotificationQueries::withHash($wpAdapter->getHash());

			if ($existingNotification === null) {
				$wpAdapter->save();
				$added++;
			} else {
				if ($existingNotification->getVersion() >= $wpAdapter->getVersion()) {
					$skipped++;
				} else {
					$wpAdapter->setPost($existingNotification->getPost())->save();
					$updated++;
				}
			}
		}

		// translators: number and number and number of notifications.
		return sprintf(__('%1$d notifications imported successfully. %2$d updated. %3$d skipped.'), ( $added + $updated ), $updated, $skipped);
	}
}
