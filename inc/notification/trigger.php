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
	 * Trigger default tile
	 * @var string
	 */
	private $title;

	/**
	 * Trigger template
	 * @var string
	 */
	private $template;

	/**
	 * Trigger default recipients
	 * @var string
	 */
	private $recipients;

	/**
	 * Slug of objects which can disable trigger
	 * Either post, user or comment
	 * @var array
	 */
	private $disable_objects;

	/**
	 * Class constructor
	 * @param  array $trigger trigger parameters
	 * @return void
	 */
	public function __construct( $trigger ) {

		$this->slug            = $trigger['slug'];
		$this->name            = $trigger['name'];
		$this->tags            = $trigger['tags'];
		$this->group           = $trigger['group'];
		$this->title           = $trigger['title'];
		$this->template        = $trigger['template'];
		$this->recipients      = $trigger['recipients'];
		$this->disable_objects = $trigger['disable'];

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

			$error = 'Tag `' . $tag . '` in `' . $this->get_name() . '` trigger is not ' . $type;

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
	 * Return trigger default title
	 * @return string title
	 */
	public function get_default_title() {

		return apply_filters( 'notification/trigger/default_title', $this->title, $this->slug , $this->tags );

	}

	/**
	 * Return trigger template
	 * @return string template
	 */
	public function get_template() {

		return apply_filters( 'notification/trigger/template', $this->template, $this->slug , $this->tags );

	}

	/**
	 * Return trigger default recipients
	 * @return string recipients
	 */
	public function get_default_recipients() {

		return apply_filters( 'notification/trigger/default_recipients', $this->recipients, $this->slug , $this->tags );

	}

	/**
	 * Return disable objects for trigger
	 * @return array object slugs
	 */
	public function get_disable_objects() {

		return apply_filters( 'notification/trigger/disable_objects', $this->disable_objects, $this->slug , $this->tags );

	}

}
