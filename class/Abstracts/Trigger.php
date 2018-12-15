<?php
/**
 * Trigger abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Interfaces\Sendable;
use BracketSpace\Notification\Admin\FieldsResolver;

/**
 * Trigger abstract class
 */
abstract class Trigger extends Common implements Interfaces\Triggerable {

	/**
	 * Storage for trigger's notifications
	 *
	 * @var array
	 */
	private $notification_storage = array();

	/**
	 * Group
	 *
	 * @var string
	 */
	protected $group = '';

	/**
	 * Short description of the Trigger
	 * No html tags allowed. Keep it tweet-short.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Flag indicating that trigger
	 * has been stopped
	 *
	 * @var boolean
	 */
	protected $stopped = false;

	/**
	 * Flag indicating that action
	 * has been postponed
	 *
	 * @var boolean
	 */
	protected $postponed = false;

	/**
	 * Bound actions
	 *
	 * @var array
	 */
	protected $actions = array();

	/**
	 * Merge tags
	 *
	 * @var array
	 */
	protected $merge_tags = array();

	/**
	 * Action's callback args
	 *
	 * @var array
	 */
	protected $callback_args = array();

	/**
	 * Trigger constructor
	 *
	 * @param string $slug slug.
	 * @param string $name nice name.
	 */
	public function __construct( $slug, $name ) {

		$this->slug = $slug;
		$this->name = $name;

		$this->merge_tags();

	}

	/**
	 * Used to register trigger merge tags
	 * Uses $this->add_merge_tag();
	 *
	 * @return void
	 */
	abstract public function merge_tags();

	/**
	 * Listens to an action
	 * This method just calls WordPress' add_action function,
	 * but it hooks the class' action method
	 *
	 * @param string  $tag           action hook.
	 * @param integer $priority      action priority, default 10.
	 * @param integer $accepted_args how many args the action accepts, default 1.
	 */
	public function add_action( $tag, $priority = 10, $accepted_args = 1 ) {

		if ( empty( $tag ) ) {
			trigger_error( 'Action tag cannot be empty', E_USER_ERROR );
		}

		array_push(
			$this->actions,
			array(
				'tag'           => $tag,
				'priority'      => $priority,
				'accepted_args' => $accepted_args,
			)
		);

		add_action( $tag, array( $this, '_action' ), $priority, $accepted_args );

	}

	/**
	 * Removes the action from the actions library.
	 *
	 * @param string  $tag           action hook.
	 * @param integer $priority      action priority, default 10.
	 * @param integer $accepted_args how many args the action accepts, default 1.
	 */
	public function remove_action( $tag, $priority = 10, $accepted_args = 1 ) {

		if ( empty( $tag ) ) {
			trigger_error( 'Action tag cannot be empty', E_USER_ERROR );
		}

		foreach ( $this->actions as $action_index => $action ) {

			if ( $action['tag'] === $tag && $action['priority'] === $priority && $action['accepted_args'] === $accepted_args ) {
				unset( $this->actions[ $action_index ] );
				break;
			}
		}

		remove_action( $tag, array( $this, '_action' ), $priority, $accepted_args );

	}

	/**
	 * Postpones the action with later hook
	 * It automatically stops the execution
	 *
	 * @param string  $tag           action hook.
	 * @param integer $priority      action priority, default 10.
	 * @param integer $accepted_args how many args the action accepts, default 1.
	 */
	public function postpone_action( $tag, $priority = 10, $accepted_args = 1 ) {

		$this->add_action( $tag, $priority, $accepted_args );

		$this->stopped   = true;
		$this->postponed = true;

	}

	/**
	 * Attaches the Notification to the Trigger
	 *
	 * @param  Sendable $notification Notification class.
	 * @return void
	 */
	public function attach( Sendable $notification ) {
		$this->notification_storage[ $notification->hash() ] = clone $notification;
	}

	/**
	 * Gets attached notifications
	 *
	 * @return array
	 */
	public function get_attached_notifications() {
		return $this->notification_storage;
	}

	/**
	 * Detaches the Notification from the Trigger
	 *
	 * @param  Sendable $notification Notification class.
	 * @return void
	 */
	public function detach( Sendable $notification ) {
		if ( isset( $this->notification_storage[ $notification->hash() ] ) ) {
			unset( $this->notification_storage[ $notification->hash() ] );
		}
	}

	/**
	 * Rolls out all the notifications
	 *
	 * @return void
	 */
	public function roll_out() {

		foreach ( $this->get_attached_notifications() as $notification ) {

			$notification->prepare_data();

			do_action( 'notification/notification/pre-send', $notification, $this );

			if ( ! $notification->is_suppressed() ) {
				$notification->send( $this );
				do_action( 'notification/notification/sent', $notification, $this );
			}
		}

	}

	/**
	 * Gets description
	 *
	 * @return string description
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Sets description
	 *
	 * @param string $description description.
	 * @return $this
	 */
	public function set_description( $description ) {
		$this->description = sanitize_text_field( $description );
		return $this;
	}

	/**
	 * Gets group
	 *
	 * @return string group
	 */
	public function get_group() {
		return $this->group;
	}

	/**
	 * Sets group
	 *
	 * @param string $group group.
	 * @return $this
	 */
	public function set_group( $group ) {
		$this->group = sanitize_text_field( $group );
		return $this;
	}

	/**
	 * Adds trigger's merge tag
	 *
	 * @param Interfaces\Taggable $merge_tag merge tag object.
	 * @return $this
	 */
	public function add_merge_tag( Interfaces\Taggable $merge_tag ) {
		$merge_tag->set_trigger( $this );
		array_push( $this->merge_tags, $merge_tag );
		return $this;
	}

	/**
	 * Removes trigger's merge tag
	 *
	 * @param string $merge_tag_slug Merge Tag slug.
	 * @return $this
	 */
	public function remove_merge_tag( $merge_tag_slug ) {

		foreach ( $this->merge_tags as $index => $merge_tag ) {
			if ( $merge_tag->get_slug() === $merge_tag_slug ) {
				unset( $this->merge_tags[ $index ] );
				break;
			}
		}

		return $this;

	}

	/**
	 * Gets trigger's merge tags
	 *
	 * @param string $type optional, all|visible|hidden, default: all.
	 * @return $array merge tags
	 */
	public function get_merge_tags( $type = 'all' ) {

		if ( 'all' === $type ) {
			return $this->merge_tags;
		}

		$tags = array();

		foreach ( $this->merge_tags as $merge_tag ) {

			if ( 'visible' === $type && ! $merge_tag->is_hidden() ) {
				array_push( $tags, $merge_tag );
			} elseif ( 'hidden' === $type && $merge_tag->is_hidden() ) {
				array_push( $tags, $merge_tag );
			}
		}

		return $tags;

	}

	/**
	 * Resolves all notifications fields with merge tags
	 *
	 * @return void
	 */
	private function resolve_fields() {

		foreach ( $this->get_attached_notifications() as $notification ) {
			$resolver = new FieldsResolver( $notification, $this->get_merge_tags() );
			$resolver->resolve_fields();
		}

	}

	/**
	 * Cleans the merge tags.
	 *
	 * @since 5.2.2
	 * @return void
	 */
	private function clean_merge_tags() {

		foreach ( $this->get_merge_tags() as $merge_tag ) {
			$merge_tag->clean_value();
		}

	}

	/**
	 * Gets CPT Notification from databse
	 * Gets their enabled Notifications
	 * Populates the Notification form data
	 * Attaches the Notification to trigger
	 *
	 * @return void
	 */
	public function set_notifications() {

		$runtime  = notification_runtime();
		$postdata = $runtime->post_data;

		// Get all notification posts bound with this trigger.
		$notification_posts = $postdata->get_trigger_posts( $this->get_slug() );

		// Attach notifications for each post.
		foreach ( $notification_posts as $notification_post ) {

			$notifications = $postdata->get_populated_notifications_for_post( $notification_post->ID );

			// attach every enabled notification.
			foreach ( $notifications as $notification ) {
				$this->attach( $notification );
			}
		}

	}

	/**
	 * Checks if trigger has been stopped
	 *
	 * @return boolean
	 */
	public function is_stopped() {
		return $this->stopped;
	}

	/**
	 * Checks if action has been postponed
	 *
	 * @return boolean
	 */
	public function is_postponed() {
		return $this->postponed;
	}

	/**
	 * Action callback
	 *
	 * @return void
	 */
	public function _action() {

		// reset the state.
		$this->stopped = false;

		// setup the arguments.
		$this->callback_args = func_get_args();

		// call the action.
		if ( $this->is_postponed() && method_exists( $this, 'postponed_action' ) ) {
			$result = call_user_func_array( array( $this, 'postponed_action' ), $this->callback_args );
		} elseif ( ! $this->is_postponed() && method_exists( $this, 'action' ) ) {
			$result = call_user_func_array( array( $this, 'action' ), $this->callback_args );
		} else {
			$result = true;
		}

		if ( false === $result ) {
			$this->stopped = true;
		}

		do_action( 'notification/trigger/action/did', $this );

		if ( $this->is_stopped() ) {
			return;
		}

		$this->set_notifications();
		$this->resolve_fields();
		$this->roll_out();
		$this->clean_merge_tags();

	}

}
