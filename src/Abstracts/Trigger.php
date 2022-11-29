<?php

/**
 * Trigger abstract class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces\Taggable;
use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Traits;

/**
 * Trigger abstract class
 */
abstract class Trigger implements Triggerable
{
	use Traits\ClassUtils;
	use Traits\HasDescription;
	use Traits\HasGroup;
	use Traits\HasName;
	use Traits\HasSlug;

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
	protected $mergeTags = [];

	/**
	 * Flag indicating that merge tags has been already added.
	 *
	 * @var bool
	 */
	protected $mergeTagsAdded = false;

	/**
	 * Trigger constructor
	 *
	 * @param string $slug Slug, optional.
	 * @param string $name Nice name, optional.
	 */
	public function __construct( $slug = null, $name = null )
	{
		if ($slug !== null) {
			$this->setSlug($slug);
		}

		if ($name === null) {
			return;
		}

		$this->setName($name);
	}

	/**
	 * Used to register trigger merge tags
	 * Uses $this->addMergeTag();
	 *
	 * @return void
	 */
	abstract public function merge_tags();

	/**
	 * Sets up the merge tags
	 *
	 * @return void
	 */
	public function setup_merge_tags()
	{

		if ($this->mergeTagsAdded) {
			return;
		}

		$this->mergeTags();

		$this->mergeTagsAdded = true;

		do_action('notification/trigger/merge_tags', $this);
	}

	/**
	 * Clears the merge tags
	 *
	 * @return $this
	 */
	public function clear_merge_tags()
	{
		$this->mergeTagsAdded = false;
		$this->mergeTags = [];

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
	 * @param int $priority action priority, default 10.
	 * @param int $acceptedArgs how many args the action accepts, default 1.
	 */
	public function add_action( $tag, $priority = 10, $acceptedArgs = 1 )
	{
		if (empty($tag)) {
			trigger_error('Action tag cannot be empty', E_USER_ERROR);
		}

		array_push(
			$this->actions,
			[
			'tag' => $tag,
			'priority' => $priority,
			'accepted_args' => $acceptedArgs,
			]
		);
	}

	/**
	 * Removes the action from the actions library.
	 *
	 * @param string  $tag        action hook.
	 * @param int $priority action priority, default 10.
	 * @param mixed   $deprecated deprecated.
	 * @return void
	 */
	public function remove_action( $tag, $priority = 10, $deprecated = null )
	{

		if (empty($tag)) {
			trigger_error('Action tag cannot be empty', E_USER_ERROR);
		}

		foreach ($this->actions as $actionIndex => $action) {
			if ($action['tag'] === $tag && $action['priority'] === $priority) {
				unset($this->actions[$actionIndex]);
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
	public function get_actions(): array
	{
		return $this->actions;
	}

	/**
	 * Adds Trigger's Merge Tag
	 *
	 * @param \BracketSpace\Notification\Interfaces\Taggable $mergeTag merge tag object.
	 * @return $this
	 */
	public function add_merge_tag( Taggable $mergeTag )
	{
		$mergeTag->setTrigger($this);
		array_push($this->mergeTags, $mergeTag);
		return $this;
	}

	/**
	 * Quickly adds new Merge Tag
	 *
	 * @since 6.0.0
	 * @param string $propertyName Trigger property name.
	 * @param string $label         Nice, translatable Merge Tag label.
	 * @param string $group         Optional, translatable group name.
	 */
	public function add_quick_merge_tag( $propertyName, $label, $group = null )
	{
		return $this->addMergeTag(
			new \BracketSpace\Notification\Defaults\MergeTag\StringTag(
				[
				'slug' => $propertyName,
				'name' => $label,
				'group' => $group,
				'resolver' => static function ( $trigger ) use ( $propertyName ) {
					return $trigger->{ $propertyName };
				},
				]
			)
		);
	}

	/**
	 * Removes Trigger's merge tag
	 *
	 * @param string $mergeTagSlug Merge Tag slug.
	 * @return $this
	 */
	public function remove_merge_tag( $mergeTagSlug )
	{

		foreach ($this->mergeTags as $index => $mergeTag) {
			if ($mergeTag->getSlug() === $mergeTagSlug) {
				unset($this->mergeTags[$index]);
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
	 * @return array<\BracketSpace\Notification\Interfaces\Taggable>
	 */
	public function get_merge_tags( $type = 'all', $grouped = false )
	{

		if (! $this->mergeTagsAdded) {
			$this->setupMergeTags();
		}

		if ($type === 'all') {
			$tags = $this->mergeTags;
		} else {
			$tags = [];

			foreach ($this->mergeTags as $mergeTag) {
				if ($type === 'visible' && ! $mergeTag->isHidden()) {
					array_push($tags, $mergeTag);
				} elseif ($type === 'hidden' && $mergeTag->isHidden()) {
					array_push($tags, $mergeTag);
				}
			}
		}

		// Group the tags if needed.
		if ($grouped) {
			$groupedTags = [];
			foreach ($tags as $mergeTag) {
				$groupedTags[$mergeTag->getSlug()] = $mergeTag;
			}
			return $groupedTags;
		}

		return $tags;
	}

	/**
	 * Stops the trigger.
	 *
	 * @since 6.2.0
	 * @return void
	 */
	public function stop()
	{
		$this->stopped = true;
	}

	/**
	 * Resumes the trigger.
	 *
	 * @since 6.2.0
	 * @return void
	 */
	public function resume()
	{
		$this->stopped = false;
	}

	/**
	 * Checks if trigger has been stopped
	 *
	 * @return bool
	 */
	public function is_stopped(): bool
	{
		return (bool)$this->stopped;
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
	 * @param int $priority action priority, default 10.
	 * @param int $acceptedArgs how many args the action accepts, default 1.
	 * @return void
	 */
	public function postpone_action( $tag, $priority = 10, $acceptedArgs = 1 )
	{
		_deprecated_function(__METHOD__, '8.0.0');
	}

	/**
	 * All triggers can be considered postponed as of v8.0.0
	 * as they are processed on the `shutdown` hook.
	 *
	 * @since 8.0.0 Deprecated
	 * @return bool
	 */
	public function is_postponed()
	{
		_deprecated_function(__METHOD__, '8.0.0');
		return true;
	}

	/**
	 * Checks if this trigger has background processing active.
	 *
	 * @since 7.2.3
	 * @since 8.0.0 Deprecated
	 * @return bool
	 */
	public function has_background_processing_enabled()
	{
		_deprecated_function(__METHOD__, '8.0.0');

		return apply_filters(
			'notification/trigger/process_in_background',
			notification_get_setting('general/advanced/background_processing'),
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
	public function get_action_args()
	{
		_deprecated_function(__METHOD__, '8.0.0');

		return [];
	}

	/**
	 * Always returns an empty array
	 *
	 * @since  6.3.0
	 * @since 8.0.0 Deprecated
	 * @return array
	 */
	public function get_cache()
	{
		_deprecated_function(__METHOD__, '8.0.0');

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
	public function set_cache( $cache )
	{
		_deprecated_function(__METHOD__, '8.0.0');

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
	public function cache( $key, $default = '' )
	{
		_deprecated_function(__METHOD__, '8.0.0');

		return $default;
	}
}
