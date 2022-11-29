<?php

/**
 * Input field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Input field class
 */
class InputField extends Field
{

	/**
	 * Field type
	 * possible values are valid HTML5 types except file or checkbox
	 *
	 * @var string
	 */
	public $type = 'text';

	/**
	 * Field placeholder
	 *
	 * @var string
	 */
	protected $placeholder = '';

	/**
	 * Field attributes
	 *
	 * @var string
	 */
	protected $atts = '';

	/**
	 * Allow for line breaks while sanitizing
	 *
	 * @since 6.3.1
	 * @var bool
	 */
	protected $allowLinebreaks = false;

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @since 6.3.1 Allow for whitespace characters.
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] )
	{

		if (isset($params['type'])) {
			$this->type = $params['type'];
		}

		if (isset($params['placeholder'])) {
			$this->placeholder = $params['placeholder'];
		}

		if (isset($params['atts'])) {
			$this->atts = $params['atts'];
		}

		if (isset($params['allow_linebreaks'])) {
			$this->allowLinebreaks = $params['allow_linebreaks'];
		}

		parent::__construct($params);
	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field()
	{
		return '<input type="' . esc_attr($this->type) . '" name="' . esc_attr($this->getName()) . '" id="' . esc_attr($this->getId()) . '" value="' . esc_attr($this->getValue()) . '" placeholder="' . esc_attr($this->placeholder) . '" class="widefat ' . esc_attr($this->cssClass()) . '" ' . $this->maybeDisable() . ' ' . esc_attr($this->atts) . '>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value )
	{
		$value = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $value); // Remove script and style tags.
		if ($this->allowLinebreaks !== true) {
			$value = preg_replace('/[\r\n\t ]+/', ' ', $value); // Remove line breaks.
		}
		$value = trim($value); // Remove whitespace.
		return $value;
	}
}
