<?php
/**
 * Repeater Handler class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Api\Handlers;

use BracketSpace\Notification\Defaults\Field;
/**
 * RepeaterHandler class
 *
 * @action
 */
class RepeaterHandler {

	/**
	 * Forms field data
	 *
	 * @since [Next]
	 * @param array $data Field data.
	 * @return array
	 */
	public function form_field_data( $data ) {

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
	 * @since [Next]
	 * @param int    $post_id Post id.
	 * @param string $carrier Carrier slug.
	 * @param string $field Field slug.
	 * @return array
	 */
	public function get_values( $post_id, $carrier, $field ) {
		$notification = notification_adapt_from( 'WordPress', $post_id );

		$carrier = $notification->get_carrier( $carrier );

		if ( $carrier ) {
			return $carrier->get_field_value( $field );
		}

		return [];
	}

	/**
	 * Checks if field is instance of repeater field
	 *
	 * @since [Next]
	 * @param \BracketSpace\Notification\Abstracts\Field $field Form field type.
	 * @return boolean
	 */
	public function check_repeater( \BracketSpace\Notification\Abstracts\Field $field ) {

		if ( $field instanceof Field\RecipientsField ) {
			return false;
		}

		if ( $field instanceof Field\RepeaterField || $field instanceof Field\SectionRepeater ) {
			return true;
		}

		return false;
	}

	/**
	 * Normalize values array
	 *
	 * @since [Next]
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
	 * Sends response
	 *
	 * @since [Next]
	 * @param \WP_REST_Request $request WP request instance.
	 * @return void
	 */
	public function send_response( \WP_REST_Request $request ) {

		$params  = $request->get_params();
		$post_id = intval( $params['id'] );
		$carrier = $params['fieldCarrier'];
		$field   = $params['fieldType'];
		$data    = [];
		$values  = $this->get_values( $post_id, $carrier, $field );

		$carriers = notification_get_carriers();

		$field = $carriers[ $carrier ]->get_form_field( $field );

		if ( ! empty( $field->sections ) ) {
			$data['field_sections'] = $field->sections;
		}

		$field = $this->form_field_data( $field->fields );

		$field_data = [
			'field'  => $field,
			'values' => $this->normalize_values( $values ),
		];

		$data = array_merge( $data, $field_data );

		wp_send_json( $data );
	}

}
