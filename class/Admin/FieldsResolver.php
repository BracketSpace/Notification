<?php
/**
 * Resolves the Notification fields with Merge Tags
 */

namespace underDEV\Notification\Admin;
use underDEV\Notification\Abstracts\Notification;

class FieldsResolver {

	/**
	 * Notification objecy
	 *
	 * @var Notification
	 */
	protected $notification;

	/**
	 * Tags to replace
	 * Numeric array with full tags including {}
	 *
	 * @var array
	 */
	protected $tags;

	/**
	 * Replacements for tags
	 * Numeric array with tags values
	 *
	 * @var array
	 */
	protected $replacements;

	/**
	 * Contructor
	 * @param Notification $notification Notification object
	 * @param array        $merge_tags   resolved merge tags array
	 */
	public function __construct( Notification $notification, $merge_tags ) {

		$this->notification = $notification;

		foreach ( $merge_tags as $merge_tag ) {
			$this->tags[]         = '{' . $merge_tag->get_slug() . '}';
			$this->replacements[] = $merge_tag->get_value();
		}

	}

	/**
	 * Resolves all notification fields
	 * @return void
	 */
	public function resolve_fields() {

		foreach ( $this->notification->get_form_fields() as $field ) {

			if ( ! $field->is_resolvable() ) {
				continue;
			}

			$resolved = $this->resolve_value( $field->get_value() );
			$field->set_value( $resolved );

		}

	}

	/**
	 * Resolves merge tags in a value
	 * @param  mixed $value string or array, field value
	 * @return mixed
	 */
	public function resolve_value( $value ) {

		if ( is_array( $value ) ) {

			$resolved = array();

			foreach ( $value as $key => $val ) {
				$key = $this->resolve_value( $key );
				$val = $this->resolve_value( $val );
				$resolved[ $key ] = $val;
			}

		} else {
			$resolved = str_replace( $this->tags, $this->replacements, $value );
		}

		return $resolved;

	}

}
