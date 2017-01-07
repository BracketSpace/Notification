<?php
/**
 * Merge tag recipient
 */

namespace Notification\Recipients\Core;

use \Notification\Notification\Recipient;
use \Notification\Notification\Triggers;

class MergeTag extends Recipient {

	/**
	 * Class constructor
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'wp_ajax_notification_get_email_merge_tags', array( $this, 'ajax_get_email_merge_tags' ) );

	}

	/**
	 * Set name
	 */
	public function set_name() {
		$this->name = 'merge_tag';
	}

	/**
	 * Set description
	 */
	public function set_description() {
		$this->description = __( 'Merge tag', 'notification' );
	}

	/**
	 * Set default value
	 */
	public function set_default_value() {
		$this->default_value = '';
	}

	/**
	 * Parse value
	 * @param string  $value       saved value
	 * @param array   $tags_values parsed merge tags
	 * @return string              parsed value
	 */
	public function parse_value( $value = '', $tags_values = array(), $human_readable = false ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		if ( isset( $tags_values[ $value ] ) ) {
			return $tags_values[ $value ];
		}

		return $value;

	}

	/**
	 * Return input
	 * @return string input html
	 */
	public function input( $value = '', $id = 0 ) {

		$html = '<select name="notification_recipient[' . $id . '][value]" class="widefat" data-update="email_merge_tags" data-value="' . $value . '"></select>';

		return $html;

	}

	/**
	 * AJAX callback - get email merge tags for trigger
	 * @return json encoded json response
	 */
	public function ajax_get_email_merge_tags() {

		try {

			$trigger_tags = Triggers::get()->get_trigger_tags_types( $_POST['trigger'] );
			$tags         = array();

			foreach ( $trigger_tags as $tag => $type ) {
				if ( $type == 'email' ) {
					$tags[ $tag ] = '{' . $tag . '}';
				}
			}

			wp_send_json_success( $tags );

		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

	}

}
