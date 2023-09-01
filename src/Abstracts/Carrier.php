<?php

/**
 * Carrier abstract class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Core\Resolver;
use BracketSpace\Notification\Defaults\Field;
use BracketSpace\Notification\Defaults\Field\RecipientsField;
use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Store\Recipient as RecipientStore;
use BracketSpace\Notification\Traits;
use function BracketSpace\Notification\getSetting;

/**
 * Carrier abstract class
 */
abstract class Carrier implements Interfaces\Sendable
{
	use Casegnostic;
	use Traits\ClassUtils;
	use Traits\HasName;
	use Traits\HasSlug;

	/**
	 * Form fields
	 *
	 * @var array<mixed>
	 */
	public $formFields = [];

	/**
	 * Recipients form field closure
	 *
	 * @var callable(): \BracketSpace\Notification\Defaults\Field\RecipientsField|null
	 */
	protected $recipientsField;

	/**
	 * Recipients form field index
	 *
	 * @var int
	 */
	public $recipientsFieldIndex = 0;

	/**
	 * Recipients form field raw data
	 *
	 * @var mixed
	 */
	public $recipientsData;

	/**
	 * Recipients form field resolved data
	 *
	 * @var mixed
	 */
	public $recipientsResolvedData;

	/**
	 * Fields data for send method
	 *
	 * @var array<mixed>
	 */
	public $data = [];

	/**
	 * Restricted form field keys
	 *
	 * @var array<mixed>
	 */
	public $restrictedFields = ['_nonce', 'activated', 'enabled'];

	/**
	 * If is suppressed
	 *
	 * @var bool
	 */
	protected $suppressed = false;

	/**
	 * Carrier icon
	 *
	 * @var string SVG
	 */
	//phpcs:ignore Generic.Files.LineLength.TooLong
	public $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 143.3 152.5"><path d="M119.8,120.8V138a69.47,69.47,0,0,1-43.2,14.5q-32.4,0-55-22.2Q-1.05,108-1,75.9c0-15.6,3.9-29.2,11.8-40.7A82,82,0,0,1,40.7,8.3,74,74,0,0,1,75.6,0a71.79,71.79,0,0,1,31,6.6,69.31,69.31,0,0,1,25.3,21.8c6.9,9.6,10.4,21.2,10.4,34.8,0,13.8-3.3,25.5-9.9,35.3s-14.3,14.7-23.1,14.7c-10.6,0-16-6.9-16-20.6V82.3C93.3,63.4,86.4,54,72.5,54c-6.2,0-11.2,2.2-14.8,6.5a23.85,23.85,0,0,0-5.4,15.8,19.46,19.46,0,0,0,6.2,14.9,21.33,21.33,0,0,0,15.1,5.7,21.75,21.75,0,0,0,13.8-4.7v16.6a27.67,27.67,0,0,1-15.5,4.3q-15.3,0-25.8-10.2t-10.5-27c0-15.5,6.8-26.7,20.4-33.8a36.74,36.74,0,0,1,17.9-4.3c12.2,0,21.7,4.5,28.5,13.6,5.2,6.9,7.9,17.4,7.9,31.5v8.5c0,3.1,1,4.7,3,4.7,3,0,5.7-3.2,8.3-9.6A56.78,56.78,0,0,0,125.4,65q0-28.95-23.6-42.9h.2c-8.1-4.3-17.4-6.4-28.1-6.4a57.73,57.73,0,0,0-28.7,7.7A58.91,58.91,0,0,0,24,45.1a61.18,61.18,0,0,0-8.2,31.5c0,17.2,5.7,31.4,17,42.7s25.7,16.9,43,16.9c9.6,0,17.5-1.2,23.6-3.5S112.3,126.5,119.8,120.8Z" transform="translate(1)"/></svg>';

	/**
	 * Carrier constructor
	 *
	 * @param string $slug Slug, optional.
	 * @param string $name Nice name, optional.
	 */
	public function __construct($slug = null, $name = null)
	{
		if ($slug !== null) {
			$this->setSlug($slug);
		}

		if ($name !== null) {
			$this->setName($name);
		}

		// Form nonce.
		$nonceField = new Field\NonceField(
			[
				'label' => '',
				'name' => '_nonce',
				'nonce_key' => $this->getSlug() . '_carrier_security',
				'resolvable' => false,
			]
		);

		$nonceField->setSection('notification_carrier_' . $this->getSlug());

		$this->formFields[$nonceField->getRawName()] = $nonceField;

		// Carrier active.
		$activatedField = new Field\InputField(
			[
				'type' => 'hidden',
				'label' => '',
				'name' => 'activated',
				'value' => '0',
				'resolvable' => false,
				'atts' => 'data-nt-carrier-input-active',
			]
		);

		$activatedField->setSection('notification_carrier_' . $this->getSlug());

		$this->formFields[$activatedField->getRawName()] = $activatedField;

		// Carrier status.
		$enabledField = new Field\InputField(
			[
				'type' => 'hidden',
				'label' => '',
				'name' => 'enabled',
				'value' => '0',
				'resolvable' => false,
				'atts' => 'data-nt-carrier-input-enable',
			]
		);

		$enabledField->setSection('notification_carrier_' . $this->getSlug());

		$this->formFields[$enabledField->getRawName()] = $enabledField;

		$this->formFields();
	}

	/**
	 * Clone method
	 * Copies the fields to new Carrier instance
	 *
	 * @return void
	 * @since  5.1.6
	 */
	public function __clone()
	{
		$fields = [];

		foreach ($this->formFields as $rawName => $field) {
			$fields[$rawName] = clone $field;
		}

		$this->formFields = $fields;
	}

	/**
	 * Used to register Carrier form fields
	 * Uses $this->addFormField();
	 *
	 * @return void
	 */
	public function formFields()
	{
		if (!method_exists($this, 'form_fields')) {
			return;
		}

		_deprecated_function(__METHOD__, '[Next]', 'Carrier::formFields');

		$this->form_fields();
	}

	/**
	 * Sends the Carrier
	 *
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger trigger object.
	 * @return void
	 */
	abstract public function send(Triggerable $trigger);

	/**
	 * Generates an unique hash for Carrier instance
	 *
	 * @return string
	 */
	public function hash()
	{
		return md5((string)wp_json_encode($this));
	}

	/**
	 * Adds form field to collection
	 *
	 * @param \BracketSpace\Notification\Interfaces\Fillable $field Field object.
	 * @return $this
	 * @throws \Exception When restricted name is used.
	 * @since  6.0.0 Added restricted field check.
	 */
	public function addFormField(Interfaces\Fillable $field)
	{
		if (in_array($field->getRawName(), $this->restrictedFields, true)) {
			throw new \Exception(
				sprintf(
					'%s %s, %s',
					'You cannot use restricted field name.',
					'Restricted names:',
					implode(', ', $this->restrictedFields)
				)
			);
		}

		$addingField = clone $field;
		$addingField->setSection('notification_carrier_' . $this->getSlug());

		$this->formFields[$field->getRawName()] = $addingField;

		if (!$this->hasRecipientsField()) {
			$this->recipientsFieldIndex++;
		}

		return $this;
	}

	/**
	 * Adds recipients form field
	 *
	 * @param array<mixed> $params Recipients field params.
	 * @return $this
	 * @throws \Exception When recipients fields was already added.
	 * @since  8.0.0
	 */
	public function addRecipientsField(array $params = [])
	{
		if ($this->hasRecipientsField()) {
			throw new \Exception('Recipient field has been already added');
		}

		$this->recipientsField = function () use ($params) {
			return new RecipientsField(
				[
					'carrier' => $this->getSlug(),
				] + $params
			);
		};

		return $this;
	}

	/**
	 * Checks if the recipients field was added
	 *
	 * @return bool
	 * @since  8.0.0
	 */
	public function hasRecipientsField()
	{
		return $this->recipientsField !== null;
	}

	/**
	 * Gets the recipients field
	 * Calls the field closure.
	 *
	 * @return \BracketSpace\Notification\Defaults\Field\RecipientsField|null
	 * @since  8.0.0
	 */
	public function getRecipientsField()
	{
		if (!$this->hasRecipientsField() || !is_callable($this->recipientsField)) {
			return null;
		}

		$closure = $this->recipientsField;
		$field = $closure();

		// Setup the field data if it's available.
		if (!empty($this->recipientsResolvedData)) {
			$this->setFieldData($field, $this->recipientsResolvedData);
		} else {
			$this->setFieldData($field, $this->recipientsData);
		}

		return $field;
	}

	/**
	 * Gets the saved recipients
	 *
	 * @return mixed
	 */
	public function getRecipients()
	{
		$recipientsField = $this->getRecipientsField();
		return $recipientsField
			? $recipientsField->getValue()
			: null;
	}

	/**
	 * Gets form fields array
	 *
	 * @return array<\BracketSpace\Notification\Interfaces\Fillable> fields
	 */
	public function getFormFields()
	{
		return $this->formFields;
	}

	/**
	 * Gets form fields array
	 *
	 * @param string $fieldName Field name.
	 * @return mixed              Field object or null.
	 * @since  6.0.0
	 */
	public function getFormField($fieldName)
	{
		return $this->formFields[$fieldName] ?? null;
	}

	/**
	 * Gets field value
	 *
	 * @param string $fieldSlug field slug.
	 * @return mixed              value or null if field not available
	 */
	public function getFieldValue($fieldSlug)
	{
		if (!isset($this->formFields[$fieldSlug])) {
			return null;
		}

		return $this->formFields[$fieldSlug]->getValue();
	}

	/**
	 * Resolves all fields
	 *
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return void
	 * @since  6.0.0
	 */
	public function resolveFields(Triggerable $trigger)
	{
		// Regular fields.
		foreach ($this->getFormFields() as $field) {
			if (!$field->isResolvable()) {
				continue;
			}

			$resolved = $this->resolveValue($field->getValue(), $trigger);
			$field->setValue($resolved);
		}

		// Recipients field.
		if (!$this->hasRecipientsField()) {
			return;
		}

		$recipientsField = $this->getRecipientsField();

		if (!$recipientsField) {
			return;
		}

		$this->recipientsResolvedData = $this->resolveValue(
			$recipientsField->getValue(),
			$trigger
		);
	}

	/**
	 * Resolves Merge Tags in field value
	 *
	 * @param mixed $value String or array, field value.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return mixed
	 * @since 6.0.0
	 */
	protected function resolveValue($value, Triggerable $trigger)
	{
		if (is_array($value)) {
			$resolved = [];

			foreach ($value as $key => $val) {
				$key = $this->resolveValue($key, $trigger);
				$val = $this->resolveValue($val, $trigger);
				$resolved[$key] = $val;
			}

			return $resolved;
		}

		$value = apply_filters('notification/carrier/field/resolving', $value);

		$resolved = Resolver::resolve($value, $trigger);

		// Unused tags.
		$stripMergeTags = apply_filters(
			'notification/resolve/strip_empty_mergetags',
			getSetting('general/content/strip_empty_tags')
		);

		if ($stripMergeTags) {
			$resolved = Resolver::clear($resolved);
		}

		// Shortcodes.
		$stripShortcodes = apply_filters(
			'notification/carrier/field/value/strip_shortcodes',
			getSetting('general/content/strip_shortcodes')
		);

		$resolved = $stripShortcodes
			? preg_replace(
				'@\[([^<>&/\[\]\x00-\x20=]++)@',
				'',
				$resolved
			)
			: do_shortcode($resolved);

		// Unescape escaped {.
		$resolved = str_replace('!{', '{', $resolved);

		return apply_filters('notification/carrier/field/value/resolved', $resolved, null);
	}

	/**
	 * Prepares saved data for easy use in send() method
	 * Saves all the values in $data property
	 *
	 * @return void
	 * @since  5.0.0
	 */
	public function prepareData()
	{
		$this->data = $this->getData();

		// If there's set recipients field, parse them into a nice array.
		// Parsed recipients are saved to key named `parsed_{recipients_field_slug}`.
		if (!$this->hasRecipientsField()) {
			return;
		}

		$recipientsField = $this->getRecipientsField();

		if (!$recipientsField) {
			return;
		}

		$this->data['parsed_' . $recipientsField->getRawName()] = $this->parseRecipients();
	}

	/**
	 * Parses the recipients to a flat array.
	 *
	 * It needs recipients_resolved_data property so the
	 * resolve_fields method needs to be called beforehand.
	 *
	 * @return array<int,mixed>
	 * @since  8.0.0
	 */
	public function parseRecipients()
	{
		if (!$this->recipientsResolvedData) {
			return [];
		}

		$parsedRecipients = [];

		foreach ($this->recipientsResolvedData as $recipient) {
			$parsedRecipients = array_merge(
				$parsedRecipients,
				(array)RecipientStore::get(
					$this->getSlug(),
					$recipient['type']
				)->parseValue($recipient['recipient']) ?? []
			);
		}

		// Remove duplicates.
		return array_unique($parsedRecipients);
	}

	/**
	 * Sets data from array
	 *
	 * @param array<mixed> $data Data with keys matched with Field names.
	 * @return $this
	 * @since  6.0.0
	 */
	public function setData($data)
	{
		// Set fields data.
		foreach ($this->getFormFields() as $field) {
			if (!isset($data[$field->getRawName()])) {
				continue;
			}

			$this->setFieldData($field, $data[$field->getRawName()]);
		}

		// Set recipients data.
		if ($this->hasRecipientsField()) {
			$recipientsField = $this->getRecipientsField();
			if ($recipientsField && isset($data[$recipientsField->getRawName()])) {
				$this->recipientsData = $data[$recipientsField->getRawName()];
			}
		}

		return $this;
	}

	/**
	 * Sets field data
	 *
	 * @param \BracketSpace\Notification\Interfaces\Fillable $field Field.
	 * @param mixed $data Field data.
	 * @return void
	 * @since  8.0.0
	 */
	protected function setFieldData(Interfaces\Fillable $field, $data)
	{
		$field->setValue($field->sanitize($data));
	}

	/**
	 * Gets data
	 *
	 * @return array<mixed>
	 * @since  6.0.0
	 */
	public function getData()
	{
		$data = [];

		// Get fields data.
		foreach ($this->getFormFields() as $field) {
			if ($field instanceof Field\NonceField) {
				continue;
			}

			$data[$field->getRawName()] = $field->getValue();
		}

		// Get recipients data.
		if ($this->hasRecipientsField()) {
			$recipientsField = $this->getRecipientsField();

			if ($recipientsField) {
				$data[$recipientsField->getRawName()] = $recipientsField->getValue();
			}
		}

		return $data;
	}

	/**
	 * Checks if Carrier is active
	 *
	 * @return bool
	 * @since  6.3.0
	 */
	public function isActive()
	{
		return !empty($this->getFieldValue('activated'));
	}

	/**
	 * Activates the Carrier
	 *
	 * @return $this
	 * @since  6.3.0
	 */
	public function activate()
	{
		$this->getFormField('activated')->setValue(true);
		return $this;
	}

	/**
	 * Deactivates the Carrier
	 *
	 * @return $this
	 * @since  6.3.0
	 */
	public function deactivate()
	{
		$this->getFormField('activated')->setValue(false);
		return $this;
	}

	/**
	 * Checks if Carrier is enabled
	 *
	 * @return bool
	 * @since  6.0.0
	 */
	public function isEnabled()
	{
		return !empty($this->getFieldValue('enabled'));
	}

	/**
	 * Enables the Carrier
	 *
	 * @return $this
	 * @since  6.0.0
	 */
	public function enable()
	{
		$this->getFormField('enabled')->setValue(true);
		return $this;
	}

	/**
	 * Disables the Carrier
	 *
	 * @return $this
	 * @since  6.0.0
	 */
	public function disable()
	{
		$this->getFormField('enabled')->setValue(false);
		return $this;
	}

	/**
	 * Checks if Carrier is suppressed
	 *
	 * @return bool
	 * @since  5.1.2
	 */
	public function isSuppressed()
	{
		return $this->suppressed;
	}

	/**
	 * Suppresses the Carrier
	 *
	 * @return void
	 * @since  5.1.2
	 */
	public function suppress()
	{
		$this->suppressed = true;
	}
}
