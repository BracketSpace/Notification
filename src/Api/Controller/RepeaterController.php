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
	public function formFieldData( $data = null )
	{

		if (empty($data)) {
			/** @var \BracketSpace\Notification\Defaults\Field\RepeaterField */
			$carrierFields = $this->getCarrierFields();
			$data = $carrierFields->fields;
		}

		$fields = [];

		foreach ($data as $field) {
			$subField = [];

			$subField['options'] = $field->options;
			$subField['pretty'] = $field->pretty;
			$subField['label'] = $field->label;
			$subField['checkbox_label'] = $field->checkboxLabel;
			$subField['name'] = $field->name;
			$subField['description'] = $field->description;
			$subField['section'] = $field->section;
			$subField['disabled'] = $field->disabled;
			$subField['css_class'] = $field->cssClass;
			$subField['id'] = $field->id;
			$subField['placeholder'] = $field->placeholder;
			$subField['nested'] = $field->nested;
			$subField['type'] = strtolower(str_replace('Field', '', $field->fieldTypeHtml));
			$subField['sections'] = $field->sections;
			$subField['message'] = $field->message;
			$subField['value'] = '';
			$subField['rows'] = $field->rows;
			$subField['multiple'] = $field->multipleSection;

			if ($field->fields) {
				$subField['fields'] = $this->formFieldData($field->fields);
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
	public function getValues( $postId, $carrier, $field )
	{
		$notification = notification_adapt_from('WordPress', $postId);
		$carrier = $notification->getCarrier($carrier);

		if ($carrier) {
			if ($carrier->hasRecipientsField()) {
				$recipientsField = $carrier->getRecipientsField();

				if ($field === $recipientsField->getRawName()) {
					return $carrier->getRecipients();
				}
			}

			return $carrier->getFieldValue($field);
		}

		return [];
	}

	/**
	 * Gets carrier fields
	 *
	 * @since 7.0.0
	 * @return array<mixed>
	 */
	public function getCarrierFields()
	{
		$carrier = Store\Carrier::get($this->carrier);
		$carrierFields = [];

		if ($carrier === null) {
			return $carrierFields;
		}

		// Recipients field.
		$rf = $carrier->hasRecipientsField() ? $carrier->getRecipientsField() : false;

		return $rf && $rf->getRawName() === $this->field ? $carrier->getRecipientsField() : $carrier->getFormField($this->field);
	}

	/**
	 * Normalize values array
	 *
	 * @since 7.0.0
	 * @param array $values Field values.
	 * @return array
	 */
	public function normalizeValues( $values )
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
	public function parseParams( $params )
	{
		$this->postId = intval($params['id']);
		$this->carrier = $params['fieldCarrier'];
		$this->field = $params['fieldType'];
	}

	/**
	 * Forms response data
	 *
	 * @since 7.0.0
	 * @return array
	 */
	public function formData()
	{
		$values = $this->getValues($this->postId, $this->carrier, $this->field) ?? [];
		$populatedFields = $this->formFieldData();

		return [
			'field' => $populatedFields,
			'values' => $this->normalizeValues($values),
		];
	}

	/**
	 * Sends response
	 *
	 * @since 7.0.0
	 * @param \WP_REST_Request $request WP request instance.
	 * @return void
	 */
	public function sendResponse( \WP_REST_Request $request )
	{
		$this->parseParams($request->getParams());
		wp_send_json($this->formData());
	}
}
