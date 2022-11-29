<?php

/**
 * Repeater Handler class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Api\Controller;

use BracketSpace\Notification\Store;

/**
 * RepeaterHandler class
 *
 * @action
 */
class RepeaterController
{

	/**
	 * Post ID
	 *
	 * @var int
	 */
	public $postId;

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
	public function form_field_data( $data = null )
	{

		if (empty($data)) {
			/** @var \BracketSpace\Notification\Defaults\Field\RepeaterField */
			$carrierFields = $this->get_carrier_fields();
			$data = $carrierFields->fields;
		}

		$fields = [];

		foreach ($data as $field) {
			$subField = [];

			$subField['options'] = $field->options;
			$subField['pretty'] = $field->pretty;
			$subField['label'] = $field->label;
			$subField['checkbox_label'] = $field->checkbox_label;
			$subField['name'] = $field->name;
			$subField['description'] = $field->description;
			$subField['section'] = $field->section;
			$subField['disabled'] = $field->disabled;
			$subField['css_class'] = $field->css_class;
			$subField['id'] = $field->id;
			$subField['placeholder'] = $field->placeholder;
			$subField['nested'] = $field->nested;
			$subField['type'] = strtolower(str_replace('Field', '', $field->field_type_html));
			$subField['sections'] = $field->sections;
			$subField['message'] = $field->message;
			$subField['value'] = '';
			$subField['rows'] = $field->rows;
			$subField['multiple'] = $field->multiple_section;

			if ($field->fields) {
				$subField['fields'] = $this->form_field_data($field->fields);
			}

			array_push($fields, $subField);
		}

		return $fields;
	}

	/**
	 * Gets field values
	 *
	 * @since 7.0.0
	 * @param int    $postId Post id.
	 * @param string $carrier Carrier slug.
	 * @param string $field Field slug.
	 * @return array
	 */
	public function get_values( $postId, $carrier, $field )
	{
		$notification = notification_adapt_from('WordPress', $postId);
		$carrier = $notification->get_carrier($carrier);

		if ($carrier) {
			if ($carrier->has_recipients_field()) {
				$recipientsField = $carrier->get_recipients_field();

				if ($field === $recipientsField->get_raw_name()) {
					return $carrier->get_recipients();
				}
			}

			return $carrier->get_field_value($field);
		}

		return [];
	}

	/**
	 * Gets carrier fields
	 *
	 * @since 7.0.0
	 * @return array<mixed>
	 */
	public function get_carrier_fields()
	{
		$carrier = Store\Carrier::get($this->carrier);
		$carrierFields = [];

		if ($carrier === null) {
			return $carrierFields;
		}

		// Recipients field.
		$rf = $carrier->has_recipients_field() ? $carrier->get_recipients_field() : false;

		return $rf && $rf->get_raw_name() === $this->field ? $carrier->get_recipients_field() : $carrier->get_form_field($this->field);
	}

	/**
	 * Normalize values array
	 *
	 * @since 7.0.0
	 * @param array $values Field values.
	 * @return array
	 */
	public function normalize_values( $values )
	{
		foreach ($values as &$value) {
			if (!array_key_exists('nested_repeater', $value)) {
				continue;
			}

			$data = array_values($value['nested_repeater']);

			$value['nested_repeater'] = $data;
		}

		return array_values($values);
	}

	/**
	 * Gets request params
	 *
	 * @param array $params Request params.
	 * @return void
	 */
	public function parse_params( $params )
	{
		$this->post_id = intval($params['id']);
		$this->carrier = $params['fieldCarrier'];
		$this->field = $params['fieldType'];
	}

	/**
	 * Forms response data
	 *
	 * @since 7.0.0
	 * @return array
	 */
	public function form_data()
	{
		$values = $this->get_values($this->post_id, $this->carrier, $this->field) ?? [];
		$populatedFields = $this->form_field_data();

		return [
			'field' => $populatedFields,
			'values' => $this->normalize_values($values),
		];
	}

	/**
	 * Sends response
	 *
	 * @since 7.0.0
	 * @param \WP_REST_Request $request WP request instance.
	 * @return void
	 */
	public function send_response( \WP_REST_Request $request )
	{
		$this->parse_params($request->get_params());
		wp_send_json($this->form_data());
	}
}
