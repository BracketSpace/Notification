<?php
/**
 * Recipients field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

/**
 * Recipients field class
 */
class RecipientsField extends RepeaterField {

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( ! isset( $params['carrier'] ) ) {
			trigger_error( 'RecipientsField requires carrier param', E_USER_ERROR );
		}

		$params = wp_parse_args( $params, [
			'carrier'          => '',
			'label'            => __( 'Recipients', 'notification' ),
			'name'             => 'recipients',
			'add_button_label' => __( 'Add recipient', 'notification' ),
			'css_class'        => '',
		] );

		$this->carrier = $params['carrier'];

		// add our CSS class required by JS.
		$params['css_class'] .= 'recipients-repeater';

		// add data attr for JS identification.
		$params['data_attr'] = [
			'carrier' => $this->carrier,
		];

		$recipients = notification_get_carrier_recipients( $this->carrier );

		if ( ! empty( $recipients ) ) {

			$first_recipient = array_values( $recipients )[0];

			if ( count( $recipients ) === 1 ) {

				$params['fields'] = [
					new InputField( [
						'label' => __( 'Type', 'notification' ),
						'name'  => 'type',
						'type'  => 'hidden',
						'value' => $first_recipient->get_slug(),
					] ),
				];

			} else {

				$recipient_types = [];

				foreach ( $recipients as $recipient ) {
					$recipient_types[ $recipient->get_slug() ] = $recipient->get_name();
				}

				$params['fields'] = [
					new SelectField( [
						'label'     => __( 'Type', 'notification' ),
						'name'      => 'type',
						'css_class' => 'recipient-type',
						'options'   => $recipient_types,
					] ),
				];

			}

			$params['fields'][] = $first_recipient->input();

		}

		parent::__construct( $params );

	}

	/**
	 * Prints repeater row
	 *
	 * @since  5.0.0
	 * @param  array   $values row values.
	 * @param  boolean $model  if this is a hidden model row.
	 * @return string          row HTML
	 */
	public function row( $values = [], $model = false ) {

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
			if ( isset( $sub_field->type ) && 'hidden' === $sub_field->type ) {
				$html .= $sub_field->field();
			} else {

				// swap the field to correct type.
				if ( isset( $recipient_type ) &&
					$recipient_type &&
					$sub_field->get_raw_name() === 'recipient' ) {

					$recipient = notification_get_recipient( $this->carrier, $recipient_type );

					if ( empty( $recipient ) ) {
						return '';
					}

					$sub_field = $recipient->input();

					// rewrite value and section.
					if ( isset( $values[ $sub_field->get_raw_name() ] ) ) {
						$sub_field->set_value( $values[ $sub_field->get_raw_name() ] );
					}
					$sub_field->section = $this->get_name() . '[' . $this->current_row . ']';

					// reset value for another type.
					$recipient_type = false;

				}

				$html .= '<td class="subfield ' . esc_attr( $sub_field->get_raw_name() ) . '">';

				if ( isset( $this->headers[ $sub_field->get_raw_name() ] ) ) {
					$html .= '<div class="row-header">' . $this->headers[ $sub_field->get_raw_name() ] . '</div>';
				}

				$html       .= '<div class="row-field">';
				$html       .= $sub_field->field();
				$description = $sub_field->get_description();

				if ( ! empty( $description ) ) {
					$html .= '<small class="description">' . $description . '</small>';
				}

				$html .= '</div>';
				$html .= '</td>';

			}
		}

		$html .= '<td class="trash"></td>';

		$html .= '</tr>';

		return $html;

	}

}
