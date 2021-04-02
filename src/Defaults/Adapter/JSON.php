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

		$this->setup_notification( notification_convert_data( $data ) );
		$this->set_source( 'JSON' );

		return $this;

	}

	/**
	 * {@inheritdoc}
	 *
	 * @param int|null $json_options          JSON options, pass null to use default as well.
	 * @param bool     $only_enabled_carriers If only enabled Carriers should be saved.
	 * @return mixed
	 */
	public function save( $json_options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE, $only_enabled_carriers = false ) {

		if ( null === $json_options ) {
			$json_options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
		}

		$data = $this->get_notification()->to_array( $only_enabled_carriers );
		return wp_json_encode( $data, $json_options );

	}

}
