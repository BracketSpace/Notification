<?php
/**
 * Settings Field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings;

/**
 * Field class
 */
class Field {

	/**
	 * Settings handle
	 *
	 * @var string
	 */
	private $handle;

	/**
	 * Field name
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Field slug
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Field description
	 *
	 * @var string
	 */
	private $description = '';

	/**
	 * Field renderer method or function
	 * Used to render field
	 *
	 * @var mixed
	 */
	private $renderer = false;

	/**
	 * Field sanitizer method or function
	 * Dynamically sanitize field value
	 *
	 * @var mixed
	 */
	private $sanitizer = false;

	/**
	 * Field value
	 *
	 * @var mixed
	 */
	private $value;

	/**
	 * Field default value
	 *
	 * @var mixed
	 */
	private $default_value = '';

	/**
	 * Section slug
	 *
	 * @var string
	 */
	private $section;

	/**
	 * Group slug
	 *
	 * @var string
	 */
	private $group;

	/**
	 * Addons
	 *
	 * @var string
	 */
	private $addons;

	/**
	 * Group constructor
	 *
	 * @throws \Exception Exception.
	 * @param string $handle  Settings handle.
	 * @param string $name    Field name.
	 * @param string $slug    Field slug.
	 * @param string $section Section slug.
	 * @param string $group   Group slug.
	 */
	public function __construct( $handle, $name, $slug, $section, $group ) {

		if ( empty( $handle ) ) {
			throw new \Exception( 'Setting handle in Section instance cannot be empty' );
		}

		$this->handle = $handle;

		if ( empty( $name ) ) {
			throw new \Exception( 'Field name cannot be empty' );
		}

		$this->name( $name );

		if ( empty( $slug ) ) {
			throw new \Exception( 'Field slug cannot be empty' );
		}

		$this->slug( sanitize_title( $slug ) );

		if ( empty( $section ) || empty( $group ) ) {
			throw new \Exception( 'Field must belong to Section and Group' );
		}

		$this->section( $section );
		$this->group( $group );

	}

	/**
	 * Get or set name
	 *
	 * @param  string $name Name. Do not pass anything to get current value.
	 * @return string name
	 */
	public function name( $name = null ) {

		if ( null !== $name ) {
			$this->name = $name;
		}

		return apply_filters( $this->handle . '/settings/field/name', $this->name, $this );

	}

	/**
	 * Get or set slug
	 *
	 * @param  string $slug Slug. Do not pass anything to get current value.
	 * @return string slug
	 */
	public function slug( $slug = null ) {

		if ( null !== $slug ) {
			$this->slug = $slug;
		}

		return apply_filters( $this->handle . '/settings/field/slug', $this->slug, $this );

	}

	/**
	 * Get or set section
	 *
	 * @param  string $section Section. Do not pass anything to get current value.
	 * @return string section
	 */
	public function section( $section = null ) {

		if ( null !== $section ) {
			$this->section = $section;
		}

		return apply_filters( $this->handle . '/settings/field/section', $this->section, $this );

	}

	/**
	 * Get or set group
	 *
	 * @param  string $group Group. Do not pass anything to get current value.
	 * @return string group
	 */
	public function group( $group = null ) {

		if ( null !== $group ) {
			$this->group = $group;
		}

		return apply_filters( $this->handle . '/settings/field/group', $this->group, $this );

	}

	/**
	 * Set or get description
	 *
	 * @param  mixed $description string to set description, null to get it.
	 * @return string description
	 */
	public function description( $description = null ) {

		if ( null !== $description ) {
			$this->description = $description;
		}

		return apply_filters( $this->handle . '/settings/field/description', $this->description, $this );

	}

	/**
	 * Get or set field value
	 *
	 * @param mixed $value field value or null to get current.
	 * @return string value
	 */
	public function value( $value = null ) {

		if ( null !== $value ) {
			$this->value = $value;
		}

		return apply_filters( $this->handle . '/settings/field/value', $this->value, $this );

	}

	/**
	 * Set or get default value
	 *
	 * @param mixed $default_value field default value or null to get current.
	 * @return string              default value
	 */
	public function default_value( $default_value = null ) {

		if ( null !== $default_value ) {
			$this->default_value = $default_value;
		}

		return apply_filters( $this->handle . '/settings/field/default_value', $this->default_value, $this );

	}

	/**
	 * Set or get addons
	 *
	 * @param mixed $addons field additional settings or null to get them.
	 * @return array addons
	 */
	public function addons( $addons = null ) {

		if ( null !== $addons ) {
			$this->addons = $addons;
		}

		return apply_filters( $this->handle . '/settings/field/addons', $this->addons, $this );

	}

	/**
	 * Get addon
	 *
	 * @param mixed $addon field additional settings or null to get them.
	 * @return mixed addon value or null
	 */
	public function addon( $addon = null ) {

		$addons = $this->addons();

		if ( isset( $addons[ $addon ] ) ) {
			return apply_filters( $this->handle . '/settings/field/addon', $addons[ $addon ], $this );
		}

		return null;

	}

	/**
	 * Get Field input name
	 *
	 * @return string name
	 */
	public function input_name() {

		$name = $this->handle . '_settings[' . $this->section() . '][' . $this->group() . '][' . $this->slug() . ']';

		return apply_filters( $this->handle . '/settings/field/input/name', $name, $this );

	}

	/**
	 * Get Field input id
	 *
	 * @return string id
	 */
	public function input_id() {

		$id = $this->handle . '-setting-' . $this->section() . '-' . $this->group() . '-' . $this->slug();

		return apply_filters( $this->handle . '/settings/field/input/id', $id, $this );

	}

	/**
	 * Set field renderer
	 *
	 * @throws \Exception Exception.
	 * @param mixed $renderer array or string.
	 * @return Field
	 */
	public function set_renderer( $renderer ) {

		if ( ! is_callable( $renderer ) ) {
			throw new \Exception( 'Field renderer is not callable' );
		}

		$this->renderer = $renderer;

		return $this;

	}

	/**
	 * Set field sanitizer
	 *
	 * @throws \Exception Exception.
	 * @param mixed $sanitizer array or string.
	 * @return Field
	 */
	public function set_sanitizer( $sanitizer ) {

		if ( ! is_callable( $sanitizer ) ) {
			throw new \Exception( 'Field sanitizer is not callable' );
		}

		$this->sanitizer = $sanitizer;

		return $this;

	}

	/**
	 * Render field
	 *
	 * @return void
	 */
	public function render() {

		call_user_func( $this->renderer, $this );

	}

	/**
	 * Sanitize field value
	 *
	 * @param  mixed $value    raw value for sanitization.
	 * @return string sanitized value
	 */
	public function sanitize( $value ) {

		if ( $this->sanitizer ) {
			$this->value = call_user_func( $this->sanitizer, $value, $this );
		}

		return $this->value;

	}

}
