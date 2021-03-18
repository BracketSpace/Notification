<?php
/**
 * Code Editor field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Editor field class
 */
class CodeEditorField extends Field {

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

		$settings = wp_parse_args( $this->settings, [
			'indentUnit' => 4,
			'tabSize'    => 4,
		] );

		wp_enqueue_script( 'code-editor' );
		wp_enqueue_style( 'code-editor' );

		return '<textarea
			id="' . esc_attr( $this->get_id() ) . '"
			class="widefat notification-field notification-code-editor-field"
			data-settings="' . esc_attr( wp_json_encode( $settings ) ) . '"
			rows="10"
			name="' . esc_attr( $this->get_name() ) . '"
		>' . esc_attr( $this->get_value() ) . '</textarea>';

	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {
		add_filter( 'wp_kses_allowed_html', [ $this, 'allow_html_tags' ] );
		$sanitized = wp_kses_data( $value );
		remove_filter( 'wp_kses_allowed_html', [ $this, 'allow_html_tags' ] );

		return $sanitized;
	}

	/**
	 * Allows for specific tags to be used within the editor
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_kses_allowed_html/
	 * @since  [Next]
	 * @param  array $allowed_tags Allowed tags list.
	 * @return array
	 */
	public function allow_html_tags( $allowed_tags ) {
		$allowed_atts = [
			'class' => true,
			'style' => true,
		];

		$extended = [
			'style' => [],
			'div'   => $allowed_atts,
			'span'  => $allowed_atts,
			'table' => $allowed_atts,
			'td'    => $allowed_atts,
			'tr'    => $allowed_atts,
		];

		return array_merge_recursive( $allowed_tags, $extended );
	}

}
