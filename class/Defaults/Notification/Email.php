<?php
/**
 * Email notification
 */

namespace underDEV\Notification\Defaults\Notification;
use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Email extends Abstracts\Notification {

	public function __construct() {
		parent::__construct( 'email', 'Email' );
	}

	public function form_fields() {

		$this->add_form_field( new Field\InputField( array(
			'label' => 'Subject',
			'name'  => 'subject',
		) ) );

		$this->add_form_field( new Field\EditorField( array(
			'label'    => 'Body',
			'name'     => 'body',
			'settings' => array(
				'media_buttons' => false
			)
		) ) );

		$this->add_form_field( new Field\RecipientsField( array(
			'notification' => $this->get_slug(),
		) ) );

	}

	public function send( \underDEV\Notification\Abstracts\Trigger $trigger ) {
    	file_put_contents( dirname( __FILE__ ) . '/email.log', print_r( $this, true ) . "\r\n\r\n" );
    	file_put_contents( dirname( __FILE__ ) . '/trigger.log', print_r( $trigger, true ) . "\r\n\r\n" );
    }

}
