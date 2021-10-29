<?php
/**
 * MergeTag abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits;

/**
 * MergeTag abstract class
 */
abstract class MergeTag implements Interfaces\Taggable {

	use Traits\ClassUtils, Traits\HasDescription, Traits\HasGroup,  Traits\HasName, Traits\HasSlug;

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
	protected $value_type;

	/**
	 * Function which resolve the merge tag value
	 *
	 * @var callable
	 */
	protected $resolver;

	/**
	 * Resolving status
	 *
	 * @var boolean
	 */
	protected $resolved = false;

	/**
	 * Trigger object, the Merge tag is assigned to
	 *
	 * @var Interfaces\Triggerable
	 */
	protected $trigger;

	/**
	 * If description is an example
	 *
	 * @var boolean
	 */
	protected $description_example = false;

	/**
	 * If merge tag is hidden
	 *
	 * @var boolean
	 */
	protected $hidden = false;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @since 7.0.0 The resolver closure context is static.
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( ! isset( $params['slug'], $params['name'], $params['resolver'] ) ) {
			trigger_error( 'Merge tag requires resolver', E_USER_ERROR );
		}

		if ( ! empty( $params['slug'] ) ) {
			$this->set_slug( $params['slug'] );
		}

		if ( ! empty( $params['name'] ) ) {
			$this->set_name( $params['name'] );
		}

		if ( ! empty( $params['group'] ) ) {
			$this->set_group( $params['group'] );
		}

		// Change resolver context to static.
		if ( $params['resolver'] instanceof \Closure ) {
			$params['resolver']->bindTo( $this );
		}

		$this->set_resolver( $params['resolver'] );

		if ( isset( $params['description'] ) ) {
			$this->description_example = isset( $params['example'] ) && $params['example'];
			$this->set_description( sanitize_text_field( $params['description'] ) );
		}

		if ( isset( $params['hidden'] ) ) {
			$this->hidden = (bool) $params['hidden'];
		}

	}

	/**
	 * Checks if the value is the correct type
	 *
	 * @param  mixed $value tag value.
	 * @return boolean
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
	public function resolve() {

		if ( $this->is_resolved() ) {
			return $this->get_value();
		}

		try {
			$value = call_user_func( $this->resolver, $this->get_trigger() );
		} catch ( \Throwable $t ) {
			$value = null;
			trigger_error( esc_html( $t->getMessage() ), E_USER_NOTICE );
		}

		if ( ! empty( $value ) && ! $this->validate( $value ) ) {
			$error_type = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? E_USER_ERROR : E_USER_NOTICE;
			trigger_error( 'Resolved value is a wrong type', $error_type );
		}

		$this->resolved = true;

		$this->value = apply_filters( 'notification/merge_tag/value/resolve', $this->sanitize( $value ) );

		return $this->get_value();

	}

	/**
	 * Checks if merge tag is already resolved
	 *
	 * @return boolean
	 */
	public function is_resolved() {
		return $this->resolved;
	}

	/**
	 * Checks if description is an example
	 * If yes, there will be displayed additional label and type
	 *
	 * @return boolean
	 */
	public function is_description_example() {
		return $this->description_example;
	}

	/**
	 * Gets merge tag resolved value
	 *
	 * @return mixed
	 */
	public function get_value() {
		return apply_filters( 'notification/merge_tag/' . $this->get_slug() . '/value', $this->value, $this );
	}

	/**
	 * Sets trigger object
	 *
	 * @since 5.0.0
	 * @param Interfaces\Triggerable $trigger Trigger object.
	 */
	public function set_trigger( Interfaces\Triggerable $trigger ) {
		$this->trigger = $trigger;
	}

	/**
	 * Sets resolver function
	 *
	 * @since 5.2.2
	 * @param mixed $resolver Resolver, can be either a closure or array or string.
	 */
	public function set_resolver( $resolver ) {

		if ( ! is_callable( $resolver ) ) {
			trigger_error( 'Merge tag resolver has to be callable', E_USER_ERROR );
		}

		$this->resolver = $resolver;

	}

	/**
	 * Gets trigger object
	 *
	 * @since 5.0.0
	 * @return Interfaces\Triggerable|null
	 */
	public function get_trigger() {
		return $this->trigger;
	}

	/**
	 * Gets value type
	 *
	 * @since 5.0.0
	 * @return string
	 */
	public function get_value_type() {
		return $this->value_type;
	}

	/**
	 * Checks if merge tag is hidden
	 *
	 * @since 5.1.3
	 * @return boolean
	 */
	public function is_hidden() {
		return $this->hidden;
	}

	/**
	 * Cleans the value
	 *
	 * @since  5.2.2
	 * @return void
	 */
	public function clean_value() {
		$this->resolved = false;
		$this->value    = '';
	}

}
