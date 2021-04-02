<?php
/**
 * Recipients field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

/**
 * Recipients field class
 */
class SectionsField extends InputField {


	/**
	 * Possible values
	 *
	 * @var array
	 */
	protected $sections = [];

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( ! isset( $params['sections'] ) ) {
			trigger_error( 'SectionsField requires sections param', E_USER_ERROR );
		}

		$this->sections = $params['sections'];

		parent::__construct( $params );

	}

	/**
	 * Prints repeater row
	 *
	 * @since  5.0.0
	 * @return string          row HTML
	 */
	public function row() {

		$html = '<template v-for="( field, key ) in fields">
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

		return $html;
	}

}
