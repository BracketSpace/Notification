<?php
/**
 * Webhook notification
 */

namespace underDEV\Notification\Defaults\Notification;
use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Webhook extends Abstracts\Notification {

	public function __construct() {
		parent::__construct( 'webhook', __( 'Webhook' ) );
	}

	public function form_fields() {

		$this->add_form_field( new Field\RecipientsField( array(
			'notification'     => $this->get_slug(),
			'label'            => __( 'URLs' ),
			'name'             => 'urls',
			'add_button_label' => __( 'Add URL', 'notification' ),
		) ) );

		$this->add_form_field( new Field\RepeaterField( array(
			'label'            => __( 'Arguments' ),
			'name'             => 'args',
			'add_button_label' => __( 'Add argument', 'notification' ),
			'fields'           => array(
				new Field\InputField( array(
					'label'      => __( 'Key' ),
					'name'       => 'key',
					'resolvable' => true,
					'description' => __( 'You can use merge tags' ),
				) ),
				new Field\InputField( array(
					'label'      => __( 'Value' ),
					'name'       => 'value',
					'resolvable' => true,
					'description' => __( 'You can use merge tags' ),
				) ),
			),
		) ) );



	}

	public function send( \underDEV\Notification\Abstracts\Trigger $trigger ) {
    	// file_put_contents( dirname( __FILE__ ) . '/email.log', print_r( $this, true ) . "\r\n\r\n", FILE_APPEND );
    }

}
