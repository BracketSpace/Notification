<?php
/**
 * Editor field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Editor field class
 */
class EditorField extends Field {

	/**
	 * Editor settings
	 *
	 * @see https://codex.wordpress.org/Function_Reference/wp_editor#Arguments
	 * @var string
	 */
	protected $settings = 'text';

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['settings'] ) ) {
			$this->settings = $params['settings'];
		}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {

		$settings = wp_parse_args(
			$this->settings,
			[
				'textarea_name' => $this->get_name(),
				'textarea_rows' => 20,
				'editor_class'  => $this->css_class(),
			]
		);

		ob_start();

		wp_editor( $this->get_value(), $this->get_id(), $settings );

		return ob_get_clean();

	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {

		/**
		 * Fixes WPLinkPreview TinyMCE component which adds the https:// prefix to invalid URL.
		 */
		return str_replace( [ 'https://{', 'http://{' ], '{', $value );
	}

}
