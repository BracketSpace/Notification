<?php
/**
 * Trigger abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces\Taggable;
use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Traits;

/**
 * Trigger abstract class
 */
abstract class Trigger implements Triggerable {

	use Traits\ClassUtils, Traits\HasDescription, Traits\HasGroup, Traits\HasName, Traits\HasSlug;

	/**
	 * Flag indicating that trigger
	 * has been stopped
	 *
	 * @var bool
	 */
	protected $stopped = false;

	/**
	 * Bound actions
	 *
	 * @var array<int, array{tag: string, priority: int, accepted_args: int}>
	 */
	protected $actions = [];

	/**
	 * Merge tags
	 *
	 * @var array
	 */
	protected $merge_tags = [];

	/**
	 * Flag indicating that merge tags has been already added.
	 *
	 * @var bool
	 */
	protected $merge_tags_added = false;

	/**
	 * Trigger constructor
	 *
	 * @param string $slug Slug, optional.
	 * @param string $name Nice name, optional.
	 */
	public function __construct( $slug = null, $name = null ) {
		if ( null !== $slug ) {
			$this->set_slug( $slug );
		}

		if ( null !== $name ) {
			$this->set_name( $name );
		}
	}

	/**
	 * Used to register trigger merge tags
	 * Uses $this->add_merge_tag();
	 *
	 * @return void
	 */
	abstract public function merge_tags();

	/**
	 * Sets up the merge tags
	 *
	 * @return void
	 */
	public function setup_merge_tags() {

		if ( $this->merge_tags_added ) {
			return;
		}

		$this->merge_tags();

		$this->merge_tags_added = true;

		do_action( 'notification/trigger/merge_tags', $this );

	}

	/**
	 * Clears the merge tags
	 *
	 * @return $this
	 */
	public function clear_merge_tags() {
		$this->merge_tags_added = false;
		$this->merge_tags       = [];

		return $this;
	}

	/**
	 * Adds an action listener
	 *
	 * @since 6.0.0
	 * @since 6.3.0 Background processing action now accepts one more param for cache.
	 * @since 8.0.0 Only stores the action params in collection.
	 *
	 * @param string  $tag           action hook.
	 * @param integer $priority      action priority, default 10.
	 * @param integer $accepted_args how many args the action accepts, default 1.
	 */
	public function add_action( $tag, $priority = 10, $accepted_args = 1 ) {
		if ( empty( $tag ) ) {
			trigger_error( 'Action tag cannot be empty', E_USER_ERROR );
		}

		array_push( $this->actions, [
			'tag'           => $tag,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		] );
	}

	/**
	 * Removes the action from the actions library.
	 *
	 * @param string  $tag        action hook.
	 * @param integer $priority   action priority, default 10.
	 * @param mixed   $deprecated deprecated.
	 * @return void
	 */
	public function remove_action( $tag, $priority = 10, $deprecated = null ) {

		if ( empty( $tag ) ) {
			trigger_error( 'Action tag cannot be empty', E_USER_ERROR );
		}

		foreach ( $this->actions as $action_index => $action ) {
			if ( $action['tag'] === $tag && $action['priority'] === $priority ) {
				unset( $this->actions[ $action_index ] );
				break;
			}
		}

	}

	/**
	 * Gets Trigger actions
	 *
	 * @since 8.0.0
	 * @return array<int, array{tag: string, priority: int, accepted_args: int}>
	 */
	public function get_actions() : array {
		return $this->actions;
	}

	/**
	 * Adds Trigger's Merge Tag
	 *
	 * @param Taggable $merge_tag merge tag object.
	 * @return $this
	 */
	public function add_merge_tag( Taggable $merge_tag ) {
		$merge_tag->set_trigger( $this );
		array_push( $this->merge_tags, $merge_tag );
		return $this;
	}

	/**
	 * Quickly adds new Merge Tag
	 *
	 * @since 6.0.0
	 * @param string $property_name Trigger property name.
	 * @param string $label         Nice, translatable Merge Tag label.
	 * @param string $group         Optional, translatable group name.
	 */
	public function add_quick_merge_tag( $property_name, $label, $group = null ) {
		return $this->add_merge_tag( new \BracketSpace\Notification\Defaults\MergeTag\StringTag( [
			'slug'     => $property_name,
			'name'     => $label,
			'group'    => $group,
			'resolver' => function ( $trigger ) use ( $property_name ) {
				return $trigger->{ $property_name };
			},
		] ) );
	}

	/**
	 * Removes Trigger's merge tag
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
	 * Gets Trigger's merge tags
	 *
	 * @since 6.0.0 Added param $grouped which makes the array associative
	 *               with merge tag slugs as keys.
	 * @param string $type    Optional, all|visible|hidden, default: all.
	 * @param bool   $grouped Optional, default: false.
	 * @return array<Taggable>
	 */
	public function get_merge_tags( $type = 'all', $grouped = false ) {

		if ( ! $this->merge_tags_added ) {
			$this->setup_merge_tags();
		}

		if ( 'all' === $type ) {
			$tags = $this->merge_tags;
		} else {
			$tags = [];

			foreach ( $this->merge_tags as $merge_tag ) {
				if ( 'visible' === $type && ! $merge_tag->is_hidden() ) {
					array_push( $tags, $merge_tag );
				} elseif ( 'hidden' === $type && $merge_tag->is_hidden() ) {
					array_push( $tags, $merge_tag );
				}
			}
		}

		// Group the tags if needed.
		if ( $grouped ) {
			$grouped_tags = [];
			foreach ( $tags as $merge_tag ) {
				$grouped_tags[ $merge_tag->get_slug() ] = $merge_tag;
			}
			return $grouped_tags;
		}

		return $tags;

	}

	/**
	 * Stops the trigger.
	 *
	 * @since 6.2.0
	 * @return void
	 */
	public function stop() {
		$this->stopped = true;
	}

	/**
	 * Checks if trigger has been stopped
	 *
	 * @return boolean
	 */
	public function is_stopped() : bool {
		return (bool) $this->stopped;
	}

	/***********************************
	 *        DEPRECATED METHODS
	 ***********************************/

	/**
	 * All triggers can be considered postponed as of v8.0.0
	 * as they are processed on the `shutdown` hook.
	 *
	 * @since 6.1.0 The postponed action have own method.
	 * @since 6.2.0 Action cannot be postponed if background processing is active.
	 * @since 8.0.0 Deprecated
	 * @param string  $tag           action hook.
	 * @param integer $priority      action priority, default 10.
	 * @param integer $accepted_args how many args the action accepts, default 1.
	 * @return void
	 */
	public function postpone_action( $tag, $priority = 10, $accepted_args = 1 ) {
		_deprecated_function( __METHOD__, '8.0.0' );
	}

	/**
	 * All triggers can be considered postponed as of v8.0.0
	 * as they are processed on the `shutdown` hook.
	 *
	 * @since 8.0.0 Deprecated
	 * @return boolean
	 */
	public function is_postponed() {
		_deprecated_function( __METHOD__, '8.0.0' );
		return true;
	}

	/**
	 * Checks if this trigger has background processing active.
	 *
	 * @since 7.2.3
	 * @since 8.0.0 Deprecated
	 * @return bool
	 */
	public function has_background_processing_enabled() {
		_deprecated_function( __METHOD__, '8.0.0' );

		return apply_filters(
			'notification/trigger/process_in_background',
			notification_get_setting( 'general/advanced/background_processing' ),
			$this
		);
	}

	/**
	 * Gets action arguments.
	 *
	 * @since 6.2.0
	 * @since 8.0.0 Deprecated
	 * @return array
	 */
	public function get_action_args() {
		_deprecated_function( __METHOD__, '8.0.0' );

		return [];
	}

	/**
	 * Always returns an empty array
	 *
	 * @since  6.3.0
	 * @since 8.0.0 Deprecated
	 * @return array
	 */
	public function get_cache() {
		_deprecated_function( __METHOD__, '8.0.0' );

		return [];
	}

	/**
	 * Doesn't do anything
	 *
	 * @since  6.3.0
	 * @since 8.0.0 Deprecated
	 * @param  array $cache Array with cached vars.
	 * @return $this
	 */
	public function set_cache( $cache ) {
		_deprecated_function( __METHOD__, '8.0.0' );

		return $this;
	}

	/**
	 * Always returns the $default value
	 *
	 * @since  6.3.0
	 * @since  8.0.0 Deprecated
	 * @param  string $key     Cache key.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 */
	public function cache( $key, $default = '' ) {
		_deprecated_function( __METHOD__, '8.0.0' );

		return $default;
	}
}
