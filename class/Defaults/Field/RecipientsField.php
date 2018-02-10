<?php
/**
 * Recipients field class
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Field;

use underDEV\Notification\Recipients;

class RecipientsField extends RepeaterField {

	public function __construct( $params = array() ) {

		if ( ! isset( $params['notification'] ) ) {
    		trigger_error( 'RecipientsField requires notification param', E_USER_ERROR );
    	}

		$params = wp_parse_args( $params, array(
			'notification'     => '',
			'label'            => 'Recipients',
			'name'             => 'recipients',
			'add_button_label' => __( 'Add recipient', 'notification' ),
			'css_class'        => '',
		) );

		$this->notification = $params['notification'];

		// add our CSS class required by JS.
		$params['css_class'] .= 'recipients-repeater';

		// add data attr for JS identification
		$params['data_attr'] = array(
			'notification' => $this->notification,
		);

		$this->recipients_collection = new Recipients();
		$recipients                  = $this->recipients_collection->get_for_notification( $this->notification );

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
						'label'     => 'Type',
						'name'      => 'type',
						'css_class' => 'recipient-type',
						'options'   => $recipient_types
					) ),
				);

			}

			$params['fields'][] = $first_recipient->input();

		}

		parent::__construct( $params );

	}

	public function row( $values = array(), $model = false ) {

		$html = '';

		if ( $model ) {
			$html .= '<tr class="row model">';
		} else {
			$html .= '<tr class="row">';
		}

			$html .= '<td class="handle"></td>';

			foreach ( $this->fields as $sub_field ) {

				if ( isset( $values[ $sub_field->get_raw_name() ] ) ) {
					$sub_field->set_value( $values[ $sub_field->get_raw_name() ] );
				}

				$sub_field->section = $this->get_name() . '[' . $this->current_row . ']';

				// extract the type of recipient for the second field.
				if ( ! $model && $sub_field->get_raw_name() === 'type' ) {
					$recipient_type = $sub_field->get_value();
				}

				// don't print useless informations for hidden field.
				if ( isset( $sub_field->type ) && $sub_field->type === 'hidden' ) {
					$html .= $sub_field->field();
				} else {

					// swap the field to correct type
					if ( isset( $recipient_type ) &&
						$recipient_type &&
						$sub_field->get_raw_name() === 'recipient' ) {

						$recipient = $this->recipients_collection->get_single( $this->notification, $recipient_type );

						if ( empty( $recipient ) ) {
							return '';
						}

						$sub_field = $recipient->input();

						// rewrite value and section.
						if ( isset( $values[ $sub_field->get_raw_name() ] ) ) {
							$sub_field->set_value( $values[ $sub_field->get_raw_name() ] );
						}
						$sub_field->section = $this->get_name() . '[' . $this->current_row . ']';

						// reset value for another type
						$recipient_type = false;

					}

					$html .= '<td class="subfield ' . esc_attr( $sub_field->get_raw_name() ) . '">';
						$html .= $sub_field->field();
						$description = $sub_field->get_description();
						if ( ! empty( $description ) ) {
							$html .= '<small class="description">' . $description . '</small>';
						}
					$html .= '</td>';
				}

			}

		$html .= '</tr>';

		return $html;

	}

}
