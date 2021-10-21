<?php
/**
 * Repeater Handler class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Api\Controller;

use BracketSpace\Notification\Interfaces\Fillable;
use BracketSpace\Notification\Store;

/**
 * RepeaterHandler class
 *
 * @action
 */
class RepeaterController {

	/**
	 * Post ID
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Carrier slug
	 *
	 * @var string
	 */
	public $carrier;

	/**
	 * Field slug
	 *
	 * @var string
	 */
	public $field;


	/**
	 * Forms field data
	 *
	 * @since 7.0.0
	 * @param array $data Field data.
	 * @return array
	 */
	public function form_field_data( $data = null ) {

		if ( empty( $data ) ) {
			/** @var \BracketSpace\Notification\Defaults\Field\RepeaterField */
			$carrier_fields = $this->get_carrier_fields();
			$data           = $carrier_fields->fields;
		}

		$fields = [];

		foreach ( $data as $field ) {

			$sub_field = [];

			$sub_field['options']        = $field->options;
			$sub_field['pretty']         = $field->pretty;
			$sub_field['label']          = $field->label;
			$sub_field['checkbox_label'] = $field->checkbox_label;
			$sub_field['name']           = $field->name;
			$sub_field['description']    = $field->description;
			$sub_field['section']        = $field->section;
			$sub_field['disabled']       = $field->disabled;
			$sub_field['css_class']      = $field->css_class;
			$sub_field['id']             = $field->id;
			$sub_field['placeholder']    = $field->placeholder;
			$sub_field['nested']         = $field->nested;
			$sub_field['type']           = strtolower( str_replace( 'Field', '', $field->field_type_html ) );
			$sub_field['sections']       = $field->sections;
			$sub_field['message']        = $field->message;
			$sub_field['value']          = '';
			$sub_field['rows']           = $field->rows;
			$sub_field['multiple']       = $field->multiple_section;

			if ( $field->fields ) {
				$sub_field['fields'] = $this->form_field_data( $field->fields );
			}

			array_push( $fields, $sub_field );

		}

		return $fields;

	}

	/**
	 * Gets field values
	 *
	 * @since 7.0.0
	 * @param int    $post_id Post id.
	 * @param string $carrier Carrier slug.
	 * @param string $field Field slug.
	 * @return array
	 */
	public function get_values( $post_id, $carrier, $field ) {
		$notification = notification_adapt_from( 'WordPress', $post_id );
		$carrier      = $notification->get_carrier( $carrier );

		if ( $carrier ) {
			if ( $carrier->has_recipients_field() ) {
				$recipients_field = $carrier->get_recipients_field();

				if ( $field === $recipients_field->get_raw_name() ) {
					return $carrier->get_recipients();
				}
			}

			return $carrier->get_field_value( $field );
		}

		return [];
	}

	/**
	 * Gets carrier fields
	 *
	 * @since 7.0.0
	 * @return array<mixed>
	 */
	public function get_carrier_fields() {
		$carrier        = Store\Carrier::get( $this->carrier );
		$carrier_fields = [];

		if ( null === $carrier ) {
			return $carrier_fields;
		}

		// Recipients field.
		$rf = $carrier->has_recipients_field() ? $carrier->get_recipients_field() : false;

		if ( $rf && $rf->get_raw_name() === $this->field ) {
			$carrier_fields = $carrier->get_recipients_field();
		} else {
			$carrier_fields = $carrier->get_form_field( $this->field );
		}

		return $carrier_fields;
	}

	/**
	 * Normalize values array
	 *
	 * @since 7.0.0
	 * @param array $values Field values.
	 * @return array
	 */
	public function normalize_values( $values ) {
		foreach ( $values as &$value ) {

			if ( array_key_exists( 'nested_repeater', $value ) ) {
				$data = array_values( $value['nested_repeater'] );

				$value['nested_repeater'] = $data;

			}
		}

		return array_values( $values );
	}

	/**
	 * Gets request params
	 *
	 * @param array $params Request params.
	 * @return void
	 */
	public function parse_params( $params ) {
		$this->post_id = intval( $params['id'] );
		$this->carrier = $params['fieldCarrier'];
		$this->field   = $params['fieldType'];
	}

	/**
	 * Forms response data
	 *
	 * @since 7.0.0
	 * @return array
	 */
	public function form_data() {
		$values           = $this->get_values( $this->post_id, $this->carrier, $this->field ) ?? [];
		$populated_fields = $this->form_field_data();

		$data = [
			'field'  => $populated_fields,
			'values' => $this->normalize_values( $values ),
		];

		return $data;
	}

	/**
	 * Sends response
	 *
	 * @since 7.0.0
	 * @param \WP_REST_Request $request WP request instance.
	 * @return void
	 */
	public function send_response( \WP_REST_Request $request ) {
		$this->parse_params( $request->get_params() );
		wp_send_json( $this->form_data() );
	}

}
