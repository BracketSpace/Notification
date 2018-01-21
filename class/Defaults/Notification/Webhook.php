<?php
/**
 * Webhook notification
 */

namespace underDEV\Notification\Defaults\Notification;
use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Webhook extends Abstracts\Notification {

	public function __construct() {
		parent::__construct( 'webhook', 'Webhook' );
	}

	public function form_fields() {

		$this->add_form_field( new Field\InputField( array(
			'label' => 'URL',
			'name'  => 'url',
			'type'  => 'url',
		) ) );

	}

	public function send( \underDEV\Notification\Abstracts\Trigger $trigger ) {
    	// file_put_contents( dirname( __FILE__ ) . '/email.log', print_r( $this, true ) . "\r\n\r\n", FILE_APPEND );
    }

}
