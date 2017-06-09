<?php
/**
 * Notification Recipients class
 */

namespace underDEV\Notification\Notification;

use underDEV\Utils\Singleton;
use underDEV\Notification\Notification\Recipient;

class Recipients extends Singleton {

	/**
	 * Recipients array
	 * @var array
	 */
	protected $recipients = array();

	/**
	 * Counter for metabox table
	 * @var integer
	 */
	private $table_row_count = 0;

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'wp_ajax_notification_get_recipient_input', array( $this, 'ajax_get_input' ) );
		add_action( 'wp_ajax_notification_add_recipient', array( $this, 'ajax_add_recipient' ) );

	}

	/**
	 * Register new recipient
	 * @param  object $recipient object of class which implemented Recipient abstract class
	 * @return mixeed            throws an Exception on error or returns $this on success
	 */
	public function register( $recipient ) {

		if ( get_parent_class( $recipient ) != 'underDEV\Notification\Notification\Recipient' ) {
			throw new \Exception( 'You can register only instance which extends class underDEV\\Notification\\Notification\\Recipient' );

		}

		if ( isset( $this->recipients[ $recipient->get_name() ] ) ) {
			throw new \Exception( 'Recipient `' . $recipient->get_name() . '` already exists' );
		}

		$this->recipients[ $recipient->get_name() ] = $recipient;

		return $this;

	}

	/**
	 * Get all registered recipients
	 * @return array recipients
	 */
	public function get_recipients() {
		return $this->recipients;
	}

	/**
	 * Get recipient
	 * @return object recipient
	 */
	public function get_recipient( $name ) {

		if ( ! isset( $this->recipients[ $name ] ) ) {
			throw new \Exception( sprintf( __( 'No "%s" recipient defined', 'notification' ), $name ) );
		}

		return $this->recipients[ $name ];

	}

	/**
	 * AJAX callback - get input for selected recipient
	 * @return json encoded json response
	 */
	public function ajax_get_input() {

		$name = $_POST['recipient_name'];

		try {
			$r = $this->get_recipient( $name );
			wp_send_json_success( $r->input( $r->get_default_value() ) );
		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

	}

	/**
	 * AJAX callback - get first recipient from recipients array and render it
	 * @return json encoded json response
	 */
	public function ajax_add_recipient() {

		try {

			if ( isset( $_POST['type'] ) && ! empty( $_POST['type'] ) ) {

				$r = $this->get_recipient( $_POST['type'] );

				if ( isset( $_POST['value'] ) && ! empty( $_POST['value'] ) ) {
					$value = $_POST['value'];
				} else {
					$value = $r->get_default_value();
				}

			} else {

				$r     = array_shift( $this->get_recipients() );
				$value = $r->get_default_value();

			}

			wp_send_json_success( $this->render_row( $r, $value, '' ) );

		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

	}

	/**
	 * Render recipient row
	 * @param  object $current_recipient recipient instance
	 * @param  atring $value             input value
	 * @param  string $disabled          `disabled` if remove button should be disabled
	 * @return string                    row html
	 */
	public function render_row( $current_recipient, $value, $disabled = '' ) {

		$i = $this->table_row_count;

		$html = '<div class="recipient">';

			$html .= '<div class="field actions">';
				$html .= '<span class="dashicons dashicons-trash ' . $disabled . '" title="' . __( 'Remove recipient', 'notification' ) . '"></span>';
			$html .= '</div>';

			$html .= '<div class="field group">';
				$html .= '<select name="notification_recipient[' . $i . '][group]">';

				foreach ( $this->get_recipients() as $recipient ) {
					$html .= '<option value="' . $recipient->get_name() . '" ' . selected( $current_recipient->get_name(), $recipient->get_name(), false ) . '>' . $recipient->get_description() . '</option>';
				}

				$html .= '</select>';
			$html .= '</div>';

			$html .= '<div class="field value">';
				$html .= $current_recipient->input( $value, $this->table_row_count );
			$html .= '</div>';

		$html .= '</div>';

		$this->table_row_count++;

		return $html;

	}

}
