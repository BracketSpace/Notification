<?php
/**
 * Notification Triggers class
 *
 * Contains all trigger functions
 */

namespace Notification\Notification;

use \Notification\Singleton;
use \Notification\Notification;
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
			'tags'     => array(),
			'group'    => __( 'Other', 'notification' ),
			'template' => ''
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
	 * Deregister trigger
	 *
	 * Please do not call this method directly, use deregister_trigger() instead
	 *
	 * @param  array $trigger trigger slug
	 * @return $this
	 */
	public function notify( $trigger, $tags ) {

		if ( isset( $this->triggers[ $trigger ] ) ) {

			$diff = array_diff( array_keys( $this->triggers[ $trigger ]->get_tags() ), array_keys( $tags ) );

			if ( ! empty( $diff ) ) {

				throw new \Exception( 'You must pass all defined merge tags to the trigger' );

			} else {

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

}
