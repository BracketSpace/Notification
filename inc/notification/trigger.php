<?php
/**
 * Notification Trigger class
 *
 * Do not instantine this class directly, use register_trigger() function instead
 */

namespace Notification\Notification;

class Trigger {

	/**
	 * Trigger slug
	 * @var string
	 */
	private $slug;

	/**
	 * Trigger friendly name
	 * @var string
	 */
	private $name;

	/**
	 * Trigger merge tags used by notification
	 * Format: tag_slug => type
	 * @var array
	 */
	private $tags;

	/**
	 * Trigger group friendly name
	 * @var string
	 */
	private $group;

	/**
	 * Trigger template
	 * @var string
	 */
	private $template;

	/**
	 * Class constructor
	 * @param  array $trigger trigger parameters
	 * @return void
	 */
	public function __construct( $trigger ) {

		$this->slug     = $trigger['slug'];
		$this->name     = $trigger['name'];
		$this->tags     = $trigger['tags'];
		$this->group    = $trigger['group'];
		$this->template = $trigger['template'];

	}

	/**
	 * Validate tags types
	 * @param  array $values values passed to notification() function
	 * @return mixed         message string on error or true on success
	 */
	public function validate_tags( $values ) {

		foreach ( $values as $tag => $value ) {

			if ( ! isset( $this->tags[ $tag ] ) || empty( $value ) ) {
				continue;
			}

			$type = $this->tags[ $tag ];

			$error = 'Tag `' . $tag . '` is not ' . $type;

			switch ( $type ) {
				case 'integer':
				case 'int':
					if ( filter_var( (int) $value, FILTER_VALIDATE_INT ) === false ) {
						return $error;
					}
					break;

				case 'float':
				case 'double':
					if ( filter_var( $value, FILTER_VALIDATE_FLOAT ) === false ) {
						return $error;
					}
					break;

				case 'url':
					if ( filter_var( $value, FILTER_VALIDATE_URL ) === false ) {
						return $error;
					}
					break;

				case 'email':
				case 'mail':
					if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) === false ) {
						return $error;
					}
					break;

				case 'boolean':
				case 'bool':
					if ( filter_var( $value, FILTER_VALIDATE_BOOLEAN ) === false ) {
						return $error;
					}
					break;

				case 'ip':
					if ( filter_var( $value, FILTER_VALIDATE_IP ) === false ) {
						return $error;
					}
					break;

				default:
					continue;
					break;
			}

		}

		return true;

	}

	/**
	 * Return tags array
	 * @return array tags
	 */
	public function get_tags() {

		return apply_filters( 'notification/trigger/tags', $this->tags, $this->slug );

	}

	/**
	 * Return trigger group name
	 * @return string group
	 */
	public function get_group() {

		return apply_filters( 'notification/trigger/group', $this->group, $this->slug );

	}

	/**
	 * Return trigger slug
	 * @return string slug
	 */
	public function get_slug() {

		return apply_filters( 'notification/trigger/slug', $this->slug );

	}

	/**
	 * Return trigger name
	 * @return string name
	 */
	public function get_name() {

		return apply_filters( 'notification/trigger/name', $this->name, $this->slug );

	}

	/**
	 * Return trigger template
	 * @return string template
	 */
	public function get_template() {

		return apply_filters( 'notification/trigger/template', $this->template, $this->slug , $this->tags );

	}

}
