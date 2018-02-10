<?php
/**
 * Handles Recipients in admin
 */

namespace underDEV\Notification\Admin;

use underDEV\Notification\Utils;
use underDEV\Notification\Recipients as RecipientsCollection;

class Recipients {

	public function __construct( Utils\View $view, Utils\Ajax $ajax, RecipientsCollection $recipients ) {
		$this->view       = $view;
		$this->ajax       = $ajax;
		$this->recipients = $recipients;
	}

	/**
	 * Renders metabox for AJAX
     *
	 * @return void
	 */
	public function ajax_get_recipient_input() {

		ob_start();

		$recipient = $this->recipients->get_single( $_POST['notification'], $_POST['type'] );
		$input     = $recipient->input();

		// A little trick to get rid of the last part of input name
		// which will be added by the field itself
		$input->section = str_replace( '[' . $input->get_raw_name() . ']', '', $_POST['input_name'] );

		echo $input->field();

		$description = $input->get_description();
		if ( ! empty( $description ) ) {
			echo '<small class="description">' . $description . '</small>';
		}

		$this->ajax->success( ob_get_clean() );

	}

}
