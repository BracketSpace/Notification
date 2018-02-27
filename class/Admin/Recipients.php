<?php
/**
 * Handles Recipients in admin
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;
use BracketSpace\Notification\Utils\Ajax;

/**
 * Recipients class
 */
class Recipients {

	/**
	 * Recipients constructor
	 *
	 * @since [Next]
	 * @param View $view View class.
	 * @param Ajax $ajax Ajax class.
	 */
	public function __construct( View $view, Ajax $ajax ) {
		$this->view = $view;
		$this->ajax = $ajax;
	}

	/**
	 * Renders metabox for AJAX
     *
	 * @return void
	 */
	public function ajax_get_recipient_input() {

		ob_start();

		$recipient = notification_get_single_recipient( $_POST['notification'], $_POST['type'] );
		$input     = $recipient->input();

		// A little trick to get rid of the last part of input name
		// which will be added by the field itself.
		$input->section = str_replace( '[' . $input->get_raw_name() . ']', '', $_POST['input_name'] );

		echo $input->field();

		$description = $input->get_description();
		if ( ! empty( $description ) ) {
			echo '<small class="description">' . $description . '</small>';
		}

		$this->ajax->success( ob_get_clean() );

	}

}
