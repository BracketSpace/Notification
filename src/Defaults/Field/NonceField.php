<?php

/**
 * Nonce field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Nonce field class
 */
class NonceField extends Field
{

	/**
	 * Nonce key
	 *
	 * @var string
	 */
	protected $nonceKey = '';

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{

		if (!isset($params['nonce_key'])) {
			trigger_error(
				'NonceField requires nonce_key param',
				E_USER_ERROR
			);
		}

		$this->nonceKey = $params['nonce_key'];

		parent::__construct($params);
	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field()
	{
		return wp_nonce_field(
			$this->nonceKey,
			$this->getName(),
			true,
			false
		);
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		return null;
	}
}
