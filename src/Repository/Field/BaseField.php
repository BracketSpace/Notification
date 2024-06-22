<?php

/**
 * Field abstract class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Field;

use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
use BracketSpace\Notification\Interfaces;

/**
 * Field abstract class
 */
abstract class BaseField implements Interfaces\Fillable
{
	use Casegnostic;

	/**
	 * Field unique ID
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Field value
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * Field label
	 *
	 * @var mixed
	 */
	protected $label;

	/**
	 * Field name
	 *
	 * @var mixed
	 */
	protected $name;

	/**
	 * Short description
	 * Limited HTML support
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * If field is resolvable with merge tags
	 * Default: true
	 *
	 * @var bool
	 */
	protected $resolvable = true;

	/**
	 * Field section name
	 *
	 * @var string
	 */
	protected $section = '';

	/**
	 * If field is disabled
	 *
	 * @var bool
	 */
	public $disabled = false;

	/**
	 * Additional css classes for field
	 *
	 * @var string
	 */
	public $cssClass = 'widefat notification-field '; // space here on purpose.

	/**
	 * If field can be used multiple times in Section Repeater row
	 *
	 * @var bool
	 */
	public $multipleSection = false;

	/**
	 * Field type used in HTML attribute.
	 *
	 * @var string
	 */
	public $fieldTypeHtml = '';

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration params.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		if (!isset($params['label'], $params['name'])) {
			trigger_error('Field requires label and name', E_USER_ERROR);
		}

		$this->fieldTypeHtml = substr(strrchr(static::class, '\\'), 1);

		$this->label = $params['label'];
		$this->name = $params['name'];
		$this->id = $this->name . '_' . uniqid();

		if (isset($params['description'])) {
			$this->description = wp_kses(
				$params['description'],
				wp_kses_allowed_html('data')
			);
		}

		if (isset($params['resolvable'])) {
			$this->resolvable = (bool)$params['resolvable'];
		}

		if (isset($params['value'])) {
			$this->setValue($params['value']);
		}

		if (isset($params['disabled']) && $params['disabled']) {
			$this->disabled = true;
		}

		if (isset($params['css_class'])) {
			$this->cssClass .= $params['css_class'];
		}

		if (!isset($params['multiple_section'])) {
			return;
		}

		$this->multipleSection = $params['multiple_section'];
	}

	/**
	 * Returns field data
	 *
	 * @param string $param Field data name.
	 * @return  array
	 * @since 7.0.0
	 */
	public function __get($param)
	{
		return $this->$param ?? null;
	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	abstract public function field();

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	abstract public function sanitize($value);

	/**
	 * Gets description
	 *
	 * @return string description
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Gets field value
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		$value = is_string($this->value)
			? stripslashes($this->value)
			: $this->value;
		return apply_filters('notification/field/' . $this->getRawName() . '/value', $value, $this);
	}

	/**
	 * Sets field value
	 *
	 * @param mixed $value value from DB.
	 * @return void
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * Gets field section name
	 *
	 * @return string
	 */
	public function getSection()
	{
		return $this->section;
	}

	/**
	 * Sets field section name
	 *
	 * @param string $value assigned value
	 * @return void
	 */
	public function setSection($value)
	{
		$this->section = $value;
	}

	/**
	 * Gets field name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->getSection() . '[' . $this->name . ']';
	}

	/**
	 * Gets field raw name
	 *
	 * @return string
	 */
	public function getRawName()
	{
		return $this->name;
	}

	/**
	 * Gets field label
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Gets field ID
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Checks if field should be resolved with merge tags
	 *
	 * @return bool
	 */
	public function isResolvable()
	{
		return $this->resolvable;
	}

	/**
	 * Checks if field is disabled
	 *
	 * @return bool
	 */
	public function isDisabled()
	{
		return $this->disabled;
	}

	/**
	 * Returns the disable HTML tag if field is disabled
	 *
	 * @return string
	 */
	public function maybeDisable()
	{
		return $this->isDisabled()
			? 'disabled="disabled"'
			: '';
	}

	/**
	 * Returns the additional field's css classes
	 *
	 * @return string
	 */
	public function cssClass()
	{
		return $this->cssClass;
	}

	/**
	 * Returns rest API error message
	 *
	 * @return string
	 * @since 7.1.0
	 */
	public function restApiError()
	{
		return esc_html__(
			'The REST API is required to display this field, but it has been blocked.
			Please unlock the /notification REST API endpoint.',
			'notification'
		);
	}
}
