<?php

namespace underDEV\Notification\Defaults\Field;
use underDEV\Notification\Recipients;

class RecipientsField extends RepeaterField {

	public function __construct( $params = array() ) {

		$params = wp_parse_args( $params, array(
			'notification'     => '',
			'label'            => 'Recipients',
			'name'             => 'recipients',
			'add_button_label' => __( 'Add recipient', 'notification' ),
		) );

		$recipients_collection = new Recipients();
		$recipients            = $recipients_collection->get_for_notification( $params['notification'] );

		if ( ! empty( $recipients ) ) {

			$first_recipient = array_values( $recipients )[0];

			if ( count( $recipients ) === 1 ) {

				$params['fields'] = array(
					new InputField( array(
						'label'   => 'Type',
						'name'    => 'type',
						'type'    => 'hidden',
						'value'   => $first_recipient->get_slug(),
					) ),
				);

			} else {

				$recipient_types = array();

				foreach ( $recipients as $recipient ) {
					$recipient_types[ $recipient->get_slug() ] = $recipient->get_name();
				}

				$params['fields'] = array(
					new SelectField( array(
						'label'   => 'Type',
						'name'    => 'type',
						'options' => $recipient_types
					) ),
				);

			}

			$params['fields'][] = $first_recipient->input();

		}

		parent::__construct( $params );

	}

}
