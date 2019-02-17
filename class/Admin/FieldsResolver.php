<?php
/**
 * Resolves the Carrier fields with Merge Tags
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Interfaces;

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
	 * Carrier object
	 *
	 * @var Notification
	 */
	protected $carrier;

	/**
	 * Merge Tags
	 *
	 * @var array
	 */
	protected $merge_tags;

	/**
	 * Fields Resolver constructor
	 *
	 * @param Interfaces\Sendable $carrier    Carrier object.
	 * @param array               $merge_tags Resolved Merge Tags array.
	 */
	public function __construct( Interfaces\Sendable $carrier, $merge_tags ) {

		$this->carrier = $carrier;

		// Sort merge tags.
		foreach ( $merge_tags as $merge_tag ) {
			$this->merge_tags[ $merge_tag->get_slug() ] = $merge_tag;
		}

	}

	/**
	 * Resolves all Carrier fields
	 *
	 * @return void
	 */
	public function resolve_fields() {

		foreach ( $this->carrier->get_form_fields() as $field ) {

			if ( ! $field->is_resolvable() ) {
				continue;
			}

			$resolved = $this->resolve_value( $field->get_value() );
			$field->set_value( $resolved );

		}

	}

	/**
	 * Resolves Merge Tags in field value
	 *
	 * @param  mixed $value String or array, field value.
	 * @return mixed
	 */
	public function resolve_value( $value ) {

		if ( is_array( $value ) ) {
			$resolved = [];

			foreach ( $value as $key => $val ) {
				$key              = $this->resolve_value( $key );
				$val              = $this->resolve_value( $val );
				$resolved[ $key ] = $val;
			}
		} else {

			$value = apply_filters_deprecated( 'notificaiton/notification/field/resolving', [
				$value,
				$this->merge_tags,
			], '[Next]', 'notification/carrier/field/resolving' );
			$value = apply_filters( 'notification/carrier/field/resolving', $value, $this->merge_tags );

			$resolved = preg_replace_callback( $this->merge_tag_pattern, [ $this, 'resolve_match' ], $value );

			$strip_merge_tags = notification_get_setting( 'general/content/strip_empty_tags' );
			$strip_merge_tags = apply_filters_deprecated( 'notification/value/strip_empty_mergetags', [
				$strip_merge_tags,
			], '[Next]', 'notification/carrier/field/value/strip_empty_mergetags' );

			if ( apply_filters( 'notification/carrier/field/value/strip_empty_mergetags', $strip_merge_tags ) ) {
				$resolved = preg_replace( $this->merge_tag_pattern, '', $resolved );
			}

			$strip_shortcodes = notification_get_setting( 'general/content/strip_shortcodes' );
			$strip_shortcodes = apply_filters_deprecated( 'notification/value/strip_shortcodes', [
				$strip_shortcodes,
			], '[Next]', 'notification/carrier/field/value/strip_shortcodes' );

			if ( apply_filters( 'notification/carrier/field/value/strip_shortcodes', $strip_shortcodes ) ) {
				$resolved = strip_shortcodes( $resolved );
			} else {
				$resolved = do_shortcode( $resolved );
			}

			$resolved = apply_filters_deprecated( 'notificaiton/notification/field/resolved', [
				$resolved,
				$this->merge_tags,
			], '[Next]', 'notification/carrier/field/value/resolved' );
			$resolved = apply_filters( 'notification/carrier/field/value/resolved', $resolved, $this->merge_tags );

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

		$resolved = apply_filters_deprecated( 'notificaiton/merge_tag/value/resolved', [
			$this->merge_tags[ $tag_slug ]->resolve(),
			$this->merge_tags[ $tag_slug ],
		], '[Next]', 'notification/merge_tag/value/resolved' );
		$resolved = apply_filters( 'notification/merge_tag/value/resolved', $resolved, $this->merge_tags[ $tag_slug ] );

		return $resolved;

	}

}
