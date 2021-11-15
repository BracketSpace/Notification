<?php
/**
 * SyncTable field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\Fields;

use BracketSpace\Notification\Admin\PostType;
use BracketSpace\Notification\Core\Sync as CoreSync;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Utils\Settings\Field;
use BracketSpace\Notification\Queries\NotificationQueries;

/**
 * SyncTable class
 */
class SyncTable {

	/**
	 * Field markup.
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		// Get all Notifications.
		$wp_json_notifiactions = PostType::get_all_notifications();
		$json_notifiactions    = CoreSync::get_all_json();
		$collection            = [];

		// Load the WP Notifications first.
		foreach ( $wp_json_notifiactions as $json ) {
			try {
				$adapter      = notification_adapt_from( 'JSON', $json );
				$notification = $adapter->get_notification();
			} catch ( \Exception $e ) {
				// Do nothing.
				continue;
			}

			/**
			 * @var \BracketSpace\Notification\Defaults\Adapter\WordPress|null
			 */
			$notification_adapter = NotificationQueries::with_hash( $notification->get_hash() );

			if ( null === $notification_adapter ) {
				continue;
			}

			$collection[ $notification->get_hash() ] = [
				'source'       => 'WordPress',
				'has_json'     => false,
				'up_to_date'   => false,
				'post_id'      => $notification_adapter->get_id(),
				'notification' => $notification,
			];
		}

		// Compare against JSON.
		foreach ( $json_notifiactions as $json ) {
			try {
				$adapter      = notification_adapt_from( 'JSON', $json );
				$notification = $adapter->get_notification();
			} catch ( \Exception $e ) {
				// Do nothing.
				continue;
			}

			if ( isset( $collection[ $notification->get_hash() ] ) ) {
				$collection[ $notification->get_hash() ]['has_json'] = true;

				$wp_notification = $collection[ $notification->get_hash() ]['notification'];

				if ( version_compare( (string) $wp_notification->get_version(), (string) $notification->get_version(), '>=' ) ) {
					$collection[ $notification->get_hash() ]['up_to_date'] = true;
				}
			} else {
				$collection[ $notification->get_hash() ] = [
					'source'       => 'JSON',
					'has_post'     => false,
					'up_to_date'   => false,
					'notification' => $notification,
				];
			}
		}

		// Filter synchronized.
		foreach ( $collection as $key => $data ) {
			if ( $data['up_to_date'] ) {
				unset( $collection[ $key ] );
			}
		}

		if ( empty( $collection ) ) {
			Templates::render( 'sync/notifications-empty' );
			return;
		}

		Templates::render( 'sync/notifications', [
			'collection' => array_reverse( $collection ),
		] );
	}

}
