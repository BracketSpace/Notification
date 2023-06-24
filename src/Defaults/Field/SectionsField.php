<?php

/**
 * Recipients field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

/**
 * Recipients field class
 */
class SectionsField extends InputField
{
	/**
	 * Possible values
	 *
	 * @var array<mixed>
	 */
	protected $sections = [];

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{

		if (!isset($params['sections'])) {
			trigger_error(
				'SectionsField requires sections param',
				E_USER_ERROR
			);
		}

		$this->sections = $params['sections'];

		parent::__construct($params);
	}

	/**
	 * Prints repeater row
	 *
	 * @return string          row HTML
	 * @since  5.0.0
	 */
	public function row()
	{

		return '<template v-for="( field, key ) in fields">
					<repeater-row
					:field="field"
					:fields="fields"
					:type="type"
					:key-index="key"
					:nested-fields="nestedFields"
					:nested-values="nestedValues"
					:nested-model="nestedModel"
					:nested-row-count="nestedRowCount"
					>
					</repeater-row>
				  </template>';
	}
}
