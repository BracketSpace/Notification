<?php
/**
 * Notification Triggers class
 *
 * Contains all trigger functions
 */

namespace Notification\Notification;

use \Notification\Singleton;
use \Notification\Notification;
use \Notification\Notifications;
use \Notification\Notification\Trigger;

class Triggers extends Singleton {

	/**
	 * Triggers
	 * @var array
	 */
	protected $triggers = array();


	/**
	 * Class constructor
	 */
	public function __construct() {

	}

	/**
	 * Register trigger
	 *
	 * Please do not call this method directly, use register_trigger() instead
	 *
	 * @param  array $trigger trigger parameters
	 * @return $this
	 */
	public function register( $trigger ) {

		if ( isset( $this->triggers[ $trigger['slug'] ] ) ) {
			throw new \Exception( 'Trigger ' . $trigger['slug'] . ' already exists' );
		}

		$defaults = array(
			'tags'       => array(),
			'group'      => __( 'Other', 'notification' ),
			'title'      => '',
			'template'   => '',
			'recipients' => array(),
			'disable'    => array()
		);

		$trigger_args = wp_parse_args( $trigger, $defaults );

		$trigger = new Trigger( apply_filters( 'notification/trigger/new/args', $trigger_args ) );

		$this->triggers[ $trigger_args['slug'] ] = $trigger;

		do_action( 'notification/trigger/new', $trigger, $trigger_args );

		return $this;

	}

	/**
	 * Deregister trigger
	 *
	 * Please do not call this method directly, use deregister_trigger() instead
	 *
	 * @param  array $trigger trigger slug
	 * @return $this
	 */
	public function deregister( $trigger ) {

		if ( ! isset( $this->triggers[ $trigger ] ) ) {
			throw new \Exception( 'Trigger ' . $trigger . ' does not exists' );
		}

		unset( $this->triggers[ $trigger ] );

		return $this;

	}

	/**
	 * Send notification for defined trigger with populated tags
	 * @param  string $trigger          trigger slug
	 * @param  array  $tags             merge tags array
	 * @param  array  $affected_objects objects IDs which affect this notification
	 * @return $this
	 */
	public function notify( $trigger, $tags, $affected_objects ) {

		if ( isset( $this->triggers[ $trigger ] ) ) {

			$diff = array_diff( array_keys( $this->triggers[ $trigger ]->get_tags() ), array_keys( $tags ) );

			if ( ! empty( $diff ) ) {

				throw new \Exception( 'You must pass all defined merge tags to the trigger' );

			} else {

				// Check if trigger has been disabled
				if ( Notifications::get()->is_trigger_disabled( $trigger, $affected_objects ) ) {
					return $this;
				}

				$validation = $this->triggers[ $trigger ]->validate_tags( $tags );

				if ( $validation === true ) {

					do_action( 'notification/trigger/notify', $tags );
					do_action( 'notification/trigger/ ' . $trigger . '/notify', $tags );

					new Notification( $trigger, $tags );

				} else {
					throw new \Exception( $validation );
				}

			}

		}

		return $this;

	}

	/**
	 * Get formatted triggers array
	 * @return array triggers
	 */
	public function get_array() {

		$return = array();

		if ( empty( $this->triggers ) ) {
			return $return;
		}

		foreach ( $this->triggers as $trigger ) {

			if ( ! isset( $return[ $trigger->get_group() ] ) ) {
				$return[ $trigger->get_group() ] = array();
			}

			$return[ $trigger->get_group() ][ $trigger->get_slug() ] = $trigger->get_name();

		}

		// Only one group is defined so strip it unless filtered
		if ( count( $return ) == 1 && ! apply_filters( 'notification/triggers/one_group_strip', false ) ) {
			$group = key( $return );
			$return = $return[ $group ];
		}

		return $return;

	}

	/**
	 * Get merge tags for defined trigger
	 * @param  string $trigger trigger slug
	 * @return mixed           throws an Exception on error or array with tags on success
	 */
	public function get_trigger_tags( $trigger ) {

		if ( ! isset( $this->triggers[ $trigger ] ) ) {
			throw new \Exception( sprintf( __( 'No "%s" trigger defined', 'notification' ), $trigger ) );
		}

		return array_keys( $this->triggers[ $trigger ]->get_tags() );

	}

	/**
	 * Get merge tags with their types for defined trigger
	 * @param  string $trigger trigger slug
	 * @return mixed           throws an Exception on error or array with tags on success
	 */
	public function get_trigger_tags_types( $trigger ) {

		if ( ! isset( $this->triggers[ $trigger ] ) ) {
			throw new \Exception( sprintf( __( 'No "%s" trigger defined', 'notification' ), $trigger ) );
		}

		return $this->triggers[ $trigger ]->get_tags();

	}

	/**
	 * Get trigger name
	 * @param  string $trigger trigger slug
	 * @return mixed           throws an Exception on error or array with tags on success
	 */
	public function get_trigger_name( $trigger ) {

		if ( ! isset( $this->triggers[ $trigger ] ) ) {
			throw new \Exception( sprintf( __( 'No "%s" trigger defined', 'notification' ), $trigger ) );
		}

		return $this->triggers[ $trigger ]->get_name();

	}

	/**
	 * Get trigger template
	 * @param  string $trigger trigger slug
	 * @return mixed           throws an Exception on error or string template on success
	 */
	public function get_trigger_template( $trigger ) {

		if ( ! isset( $this->triggers[ $trigger ] ) ) {
			throw new \Exception( sprintf( __( 'No "%s" trigger defined', 'notification' ), $trigger ) );
		}

		return $this->triggers[ $trigger ]->get_template();

	}

	/**
	 * Get trigger object
	 * @param  string $trigger trigger slug
	 * @return mixed           throws an Exception on error or return Trigger instance on success
	 */
	public function get_trigger( $trigger ) {

		if ( ! isset( $this->triggers[ $trigger ] ) ) {
			throw new \Exception( sprintf( __( 'No "%s" trigger defined', 'notification' ), $trigger ) );
		}

		return $this->triggers[ $trigger ];

	}

	/**
	 * Get trigger disable objects
	 * @param  string $trigger trigger slug
	 * @return mixed           throws an Exception on error or string template on success
	 */
	public function get_trigger_disable_objects( $trigger ) {

		if ( ! isset( $this->triggers[ $trigger ] ) ) {
			throw new \Exception( sprintf( __( 'No "%s" trigger defined', 'notification' ), $trigger ) );
		}

		return $this->triggers[ $trigger ]->get_disable_objects();

	}

	/**
	 * Render Chosen select with all triggers listed
	 * @param  mixed   $val          select current value
	 * @param  string  $name         select name
	 * @param  boolean $multiple     determine if select should be multiple, default false
	 * @param  string  $disable_type type of object which allows trigger to be disabled, default all triggers are listed
	 * @return void
	 */
	public function render_triggers_select( $val = '', $name = 'notification_trigger', $multiple = false, $disable_type = '' ) {

		if ( $multiple ) {
			$multiple = 'multiple="multiple"';
		}

		echo '<select id="' . $name . '_select" name="' . $name . '" class="pretty-select" data-placeholder="' . __( 'Select trigger', 'notification' ) . '" ' . $multiple . '>';

			echo '<option value=""></option>';

			foreach ( $this->get_array() as $group => $subtriggers ) {

				if ( ! is_array( $subtriggers ) ) {

					if ( ! empty( $disable_type ) && ! in_array( $disable_type, $this->get_trigger_disable_objects( $group ) ) ) {
						continue;
					}

					if ( is_array( $val ) ) {
						$selected = in_array( $group, $val ) ? 'selected="selected"' : '';
					} else {
						$selected = selected( $val, $group, false );
					}

					echo '<option value="' . $group . '" ' . $selected . '>' . $subtriggers . '</option>';

				} else {

					echo '<optgroup label="' . $group . '">';

					foreach ( $subtriggers as $slug => $name ) {

						if ( ! empty( $disable_type ) && ! in_array( $disable_type, $this->get_trigger_disable_objects( $slug ) ) ) {
							continue;
						}

						if ( is_array( $val ) ) {
							$selected = in_array( $slug, $val ) ? 'selected="selected"' : '';
						} else {
							$selected = selected( $val, $slug, false );
						}

						echo '<option value="' . $slug . '" ' . $selected . '>' . $name . '</option>';

					}

					echo '</optgroup>';

				}

			}

		echo '</select>';

	}

}
