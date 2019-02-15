<?php
/**
 * JSON Adapter class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Adapter;

use BracketSpace\Notification\Abstracts;

/**
 * JSON Adapter class
 */
class JSON extends Abstracts\Adapter {

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Exception If wrong input param provided.
	 * @param string $input JSON string.
	 * @return $this
	 */
	public function read( $input = null ) {

		$data = json_decode( $input, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new \Exception( 'Read method of JSON adapter expects valid JSON string' );
		}

		// Trigger translation.
		if ( isset( $data['trigger'] ) ) {
			$data['trigger'] = notification_get_single_trigger( $data['trigger'] );
		}

		// Notifications translation.
		if ( isset( $data['notifications'] ) ) {
			$notification_objects = [];

			foreach ( $data['notifications'] as $notification_slug => $notification_data ) {
				$notification = notification_get_single_notification( $notification_slug );
				if ( ! empty( $notification ) ) {
					$notification->set_data( $notification_data );
					$notification->enabled                      = true;
					$notification_objects[ $notification_slug ] = $notification;
				}
			}

			$data['notifications'] = $notification_objects;
		}

		$this->setup_notification( $data );

		return $this;

	}

	/**
	 * {@inheritdoc}
	 *
	 * @param int $json_options JSON options.
	 * @return mixed
	 */
	public function save( $json_options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) {
		$data = $this->get_notification()->to_array();
		return wp_json_encode( $data, $json_options );
	}

}
