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

		// Carriers translation.
		if ( isset( $data['carriers'] ) ) {
			$carriers = [];

			foreach ( $data['carriers'] as $carrier_slug => $carrier_data ) {
				$carrier = clone notification_get_carrier( $carrier_slug );

				if ( ! empty( $carrier ) ) {
					$carrier->set_data( $carrier_data );
					$carrier->enabled          = true;
					$carriers[ $carrier_slug ] = $carrier;
				}
			}

			$data['carriers'] = $carriers;
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
