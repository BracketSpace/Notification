<?php
/**
 * Resolves the Notification fields with Merge Tags
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Abstracts\Notification;

/**
 * FieldsResolver class
 */
class FieldsResolver {

	/**
	 * Regex pattern for merge tags
	 *
	 * @var string
	 */
	private $merge_tag_pattern = '/\{([^\}]*)\}/';

	/**
	 * Notification object
	 *
	 * @var Notification
	 */
	protected $notification;

	/**
	 * Merge Tags
	 *
	 * @var array
	 */
	protected $merge_tags;

	/**
	 * FieldsResolver contructor
	 *
	 * @param Notification $notification Notification object.
	 * @param array        $merge_tags   resolved merge tags array.
	 */
	public function __construct( Notification $notification, $merge_tags ) {

		$this->notification = $notification;

		// Sort merge tags.
		foreach ( $merge_tags as $merge_tag ) {
			$this->merge_tags[ $merge_tag->get_slug() ] = $merge_tag;
		}

	}

	/**
	 * Resolves all notification fields
	 *
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
	 *
	 * @param  mixed $value string or array, field value.
	 * @return mixed
	 */
	public function resolve_value( $value ) {

		if ( is_array( $value ) ) {

			$resolved = array();

			foreach ( $value as $key => $val ) {
				$key              = $this->resolve_value( $key );
				$val              = $this->resolve_value( $val );
				$resolved[ $key ] = $val;
			}
		} else {

			$value = apply_filters( 'notificaiton/notification/field/resolving', $value, $this->merge_tags );

			$resolved = preg_replace_callback( $this->merge_tag_pattern, array( $this, 'resolve_match' ), $value );

			$strip_metgetags = notification_get_setting( 'general/content/strip_empty_tags' );
			if ( apply_filters( 'notification/value/strip_empty_mergetags', $strip_metgetags ) ) {
				$resolved = preg_replace( $this->merge_tag_pattern, '', $resolved );
			}

			$strip_shortcodes = notification_get_setting( 'general/content/strip_shortcodes' );
			if ( apply_filters( 'notification/value/strip_shortcodes', $strip_shortcodes ) ) {
				$resolved = strip_shortcodes( $resolved );
			} else {
				$resolved = do_shortcode( $resolved );
			}

			$resolved = apply_filters( 'notificaiton/notification/field/resolved', $resolved, $this->merge_tags );

		}

		return $resolved;

	}

	/**
	 * Resolves the Merge Tag with a real value
	 *
	 * @since  5.2.0
	 * @param  array $matches Matches from preg_replace.
	 * @return string
	 */
	public function resolve_match( $matches ) {

		$tag_slug = $matches[1];

		if ( ! isset( $this->merge_tags[ $tag_slug ] ) ) {
			return '';
		}

		$resolved = apply_filters( 'notificaiton/merge_tag/value/resolved', $this->merge_tags[ $tag_slug ]->resolve(), $this->merge_tags[ $tag_slug ] );

		return $resolved;

	}

}
