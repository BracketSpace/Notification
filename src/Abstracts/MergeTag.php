<?php

/**
 * MergeTag abstract class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits;

/**
 * MergeTag abstract class
 */
abstract class MergeTag implements Interfaces\Taggable
{
	use Traits\ClassUtils;
	use Traits\HasDescription;
	use Traits\HasGroup;
	use Traits\HasName;
	use Traits\HasSlug;

	/**
	 * MergeTag resolved value
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * MergeTag value type
	 *
	 * @var string
	 */
	protected $valueType;

	/**
	 * Function which resolve the merge tag value
	 *
	 * @var callable
	 */
	protected $resolver;

	/**
	 * Resolving status
	 *
	 * @var bool
	 */
	protected $resolved = false;

	/**
	 * Trigger object, the Merge tag is assigned to
	 *
	 * @var \BracketSpace\Notification\Interfaces\Triggerable
	 */
	protected $trigger;

	/**
	 * If description is an example
	 *
	 * @var bool
	 */
	protected $descriptionExample = false;

	/**
	 * If merge tag is hidden
	 *
	 * @var bool
	 */
	protected $hidden = false;

	/**
	 * Trigger property name to get the comment data from
	 *
	 * @var string
	 */
	private $triggerPropertyName;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @since 7.0.0 The resolver closure context is static.
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] )
	{

		if (! isset($params['slug'], $params['name'], $params['resolver'])) {
			trigger_error('Merge tag requires resolver', E_USER_ERROR);
		}

		if (! empty($params['slug'])) {
			$this->setSlug($params['slug']);
		}

		if (! empty($params['name'])) {
			$this->setName($params['name']);
		}

		if (! empty($params['group'])) {
			$this->setGroup($params['group']);
		}

		// Change resolver context to static.
		if ($params['resolver'] instanceof \Closure) {
			$params['resolver']->bindTo($this);
		}

		$this->setResolver($params['resolver']);

		if (isset($params['description'])) {
			$this->descriptionExample = isset($params['example']) && $params['example'];
			$this->setDescription(sanitize_text_field($params['description']));
		}

		if (!isset($params['hidden'])) {
			return;
		}

		$this->hidden = (bool)$params['hidden'];
	}

	/**
	 * Checks if the value is the correct type
	 *
	 * @param  mixed $value tag value.
	 * @return bool
	 */
	abstract public function validate( $value );

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param  mixed $value tag value.
	 * @return mixed        sanitized value
	 */
	abstract public function sanitize( $value );

	/**
	 * Resolves the merge tag value
	 * It also check if the value is correct type
	 * and sanitizes it
	 *
	 * @return mixed the resolved value
	 */
	public function resolve()
	{

		if ($this->isResolved()) {
			return $this->getValue();
		}

		try {
			$value = call_user_func($this->resolver, $this->getTrigger());
		} catch (\Throwable $t) {
			$value = null;
			trigger_error(esc_html($t->getMessage()), E_USER_NOTICE);
		}

		if (! empty($value) && ! $this->validate($value)) {
			$errorType = ( defined('WP_DEBUG') && WP_DEBUG ) ? E_USER_ERROR : E_USER_NOTICE;
			trigger_error('Resolved value is a wrong type', $errorType);
		}

		$this->resolved = true;

		$this->value = apply_filters('notification/merge_tag/value/resolve', $this->sanitize($value));

		return $this->getValue();
	}

	/**
	 * Checks if merge tag is already resolved
	 *
	 * @return bool
	 */
	public function isResolved()
	{
		return $this->resolved;
	}

	/**
	 * Checks if description is an example
	 * If yes, there will be displayed additional label and type
	 *
	 * @return bool
	 */
	public function isDescriptionExample()
	{
		return $this->descriptionExample;
	}

	/**
	 * Gets merge tag resolved value
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return apply_filters('notification/merge_tag/' . $this->getSlug() . '/value', $this->value, $this);
	}

	/**
	 * Sets trigger object
	 *
	 * @since 5.0.0
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 */
	public function setTrigger( Interfaces\Triggerable $trigger )
	{
		$this->trigger = $trigger;
	}

	/**
	 * Sets resolver function
	 *
	 * @since 5.2.2
	 * @param mixed $resolver Resolver, can be either a closure or array or string.
	 */
	public function setResolver( $resolver )
	{

		if (! is_callable($resolver)) {
			trigger_error('Merge tag resolver has to be callable', E_USER_ERROR);
		}

		$this->resolver = $resolver;
	}

	/**
	 * Sets resolver function
	 *
	 * @since 8.0.12
	 *
	 * @param string $triggerPropertyName merge tag trigger property name.
	 *
	 * @return void
	 */
	public function setTriggerProp( string $triggerPropertyName )
	{
		$this->triggerPropertyName = $triggerPropertyName;
	}

	/**
	 * Get trigger property
	 *
	 * @since 8.0.12
	 *
	 * @return string
	 */
	public function getTriggerProp(): string
	{
		return $this->triggerPropertyName;
	}

	/**
	 * Gets trigger object
	 *
	 * @since 5.0.0
	 * @return \BracketSpace\Notification\Interfaces\Triggerable|null
	 */
	public function getTrigger()
	{
		return $this->trigger;
	}

	/**
	 * Gets value type
	 *
	 * @since 5.0.0
	 * @return string
	 */
	public function getValueType()
	{
		return $this->valueType;
	}

	/**
	 * Checks if merge tag is hidden
	 *
	 * @since 5.1.3
	 * @return bool
	 */
	public function isHidden()
	{
		return $this->hidden;
	}

	/**
	 * Cleans the value
	 *
	 * @since  5.2.2
	 * @return void
	 */
	public function cleanValue()
	{
		$this->resolved = false;
		$this->value = '';
	}
}
