<?php
/**
 * Trigger abstract class
 *
 * @package notification
 */

namespace underDEV\Notification\Abstracts;

use underDEV\Notification\Interfaces;
use underDEV\Notification\Interfaces\Sendable;
use underDEV\Notification\Admin\FieldsResolver;

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

		array_push( $this->actions, array(
			'tag'           => $tag,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		) );

		add_action( $tag, array( $this, '_action' ), $priority, $accepted_args );

	}

	/**
	 * Attaches the Notification to the Trigger
     *
	 * @param  Sendable $notification Notification class.
	 * @return void
	 */
	public function attach( Sendable $notification ) {
		$this->notification_storage[ $notification->hash() ] = $notification;
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
		foreach ( $this->notification_storage as $notification ) {
			$notification->prepare_data();
			do_action( 'notification/notification/pre-send', $notification, $this );
			$notification->send( $this );
			do_action( 'notification/notification/sent', $notification, $this );
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
		array_push( $this->merge_tags, $merge_tag );
		return $this;
	}

	/**
	 * Gets trigger's merge tags
     *
	 * @return $array merge tags
	 */
	public function get_merge_tags() {
		return $this->merge_tags;
	}

	/**
	 * Resolves all registered merge tags
     *
	 * @return void
	 */
	private function resolve_merge_tags() {

		foreach ( $this->get_merge_tags() as $tag ) {
			if ( $tag->check_requirements() ) {
				$tag->resolve();
			} else {
				trigger_error( 'Requirements for the `' . $tag->get_slug() . '` merge tag hasn\'t been met', E_USER_ERROR );
			}
		}

	}

	/**
	 * Resolves all notifications fields with merge tags
     *
	 * @return void
	 */
	private function resolve_fields() {

		foreach ( $this->notification_storage as $notification ) {
			$resolver = new FieldsResolver( $notification, $this->get_merge_tags() );
			$resolver->resolve_fields();
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

		$runtime = notification_runtime();
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
	 * Action callback
	 * It's strongly recommended to add this function in a child class
	 * and set all the class parameters you need or are required
	 * by merge tags you are using
     *
	 * @return void
	 */
	public function action() {}

	/**
	 * Action callback
     *
	 * @return void
	 */
	public function _action() {
		$this->callback_args = func_get_args();
		$this->action();
		$this->resolve_merge_tags();
		$this->set_notifications();
		$this->resolve_fields();
		$this->roll_out();
	}

}
