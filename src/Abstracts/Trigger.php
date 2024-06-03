<?php

/**
 * Trigger abstract class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Core\Settings;
use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
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
	use Casegnostic;

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
	 * @var array<mixed>
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
	public function __construct($slug = null, $name = null)
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
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		if (!method_exists($this, 'merge_tags')) {
			return;
		}

		_deprecated_function(__METHOD__, '[Next]', 'Trigger::mergeTags');

		$this->merge_tags();
	}

	/**
	 * Sets up the merge tags
	 *
	 * @return void
	 */
	public function setupMergeTags()
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
	public function clearMergeTags()
	{
		$this->mergeTagsAdded = false;
		$this->mergeTags = [];

		return $this;
	}

	/**
	 * Adds an action listener
	 *
	 * @param string $tag action hook.
	 * @param int $priority action priority, default 10.
	 * @param int $acceptedArgs how many args the action accepts, default 1.
	 * @since 6.0.0
	 * @since 6.3.0 Background processing action now accepts one more param for cache.
	 * @since 8.0.0 Only stores the action params in collection.
	 * @return void
	 *
	 */
	public function addAction($tag, $priority = 10, $acceptedArgs = 1)
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
	 * @param string $tag action hook.
	 * @param int $priority action priority, default 10.
	 * @param mixed $deprecated deprecated.
	 * @return void
	 */
	public function removeAction($tag, $priority = 10, $deprecated = null)
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
	 * @return array<int, array{tag: string, priority: int, accepted_args: int}>
	 * @since 8.0.0
	 */
	public function getActions(): array
	{
		return $this->actions;
	}

	/**
	 * Adds Trigger's Merge Tag
	 *
	 * @param \BracketSpace\Notification\Interfaces\Taggable $mergeTag merge tag object.
	 * @return $this
	 */
	public function addMergeTag(Taggable $mergeTag)
	{
		$mergeTag->setTrigger($this);
		array_push(
			$this->mergeTags,
			$mergeTag
		);
		return $this;
	}

	/**
	 * Quickly adds new Merge Tag
	 *
	 * @param string $propertyName Trigger property name.
	 * @param string $label Nice, translatable Merge Tag label.
	 * @param string $group Optional, translatable group name.
	 * @since 6.0.0
	 * @return $this
	 *
	 */
	public function addQuickMergeTag($propertyName, $label, $group = null)
	{
		return $this->addMergeTag(
			new \BracketSpace\Notification\Repository\MergeTag\StringTag(
				[
					'slug' => $propertyName,
					'name' => $label,
					'group' => $group,
					'resolver' => static function ($trigger) use ($propertyName) {
						return $trigger->{$propertyName};
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
	public function removeMergeTag($mergeTagSlug)
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
	 * @param string $type Optional, all|visible|hidden, default: all.
	 * @param bool $grouped Optional, default: false.
	 * @return array<\BracketSpace\Notification\Interfaces\Taggable>
	 * @since 6.0.0 Added param $grouped which makes the array associative
	 *               with merge tag slugs as keys.
	 */
	public function getMergeTags($type = 'all', $grouped = false)
	{
		if (!$this->mergeTagsAdded) {
			$this->setupMergeTags();
		}

		if ($type === 'all') {
			$tags = $this->mergeTags;
		} else {
			$tags = [];

			foreach ($this->mergeTags as $mergeTag) {
				if ($type === 'visible' && !$mergeTag->isHidden()) {
					array_push(
						$tags,
						$mergeTag
					);
				} elseif ($type === 'hidden' && $mergeTag->isHidden()) {
					array_push(
						$tags,
						$mergeTag
					);
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
	 * @return void
	 * @since 6.2.0
	 */
	public function stop()
	{
		$this->stopped = true;
	}

	/**
	 * Resumes the trigger.
	 *
	 * @return void
	 * @since 6.2.0
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
	public function isStopped(): bool
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
	 * @param string $tag action hook.
	 * @param int $priority action priority, default 10.
	 * @param int $acceptedArgs how many args the action accepts, default 1.
	 * @return void
	 * @since 6.2.0 Action cannot be postponed if background processing is active.
	 * @since 8.0.0 Deprecated
	 * @since 6.1.0 The postponed action have own method.
	 */
	public function postponeAction($tag, $priority = 10, $acceptedArgs = 1)
	{
		_deprecated_function(
			__METHOD__,
			'8.0.0'
		);
	}

	/**
	 * All triggers can be considered postponed as of v8.0.0
	 * as they are processed on the `shutdown` hook.
	 *
	 * @return bool
	 * @since 8.0.0 Deprecated
	 */
	public function isPostponed()
	{
		_deprecated_function(
			__METHOD__,
			'8.0.0'
		);
		return true;
	}

	/**
	 * Checks if this trigger has background processing active.
	 *
	 * @return bool
	 * @since 8.0.0 Deprecated
	 * @since 7.2.3
	 */
	public function hasBackgroundProcessingEnabled()
	{
		_deprecated_function(
			__METHOD__,
			'8.0.0'
		);

		return apply_filters(
			'notification/trigger/process_in_background',
			\Notification::component(Settings::class)->getSetting('general/advanced/background_processing'),
			$this
		);
	}

	/**
	 * Gets action arguments.
	 *
	 * @return array<mixed>
	 * @since 8.0.0 Deprecated
	 * @since 6.2.0
	 */
	public function getActionArgs()
	{
		_deprecated_function(
			__METHOD__,
			'8.0.0'
		);

		return [];
	}

	/**
	 * Always returns an empty array
	 *
	 * @return array<mixed>
	 * @since 8.0.0 Deprecated
	 * @since  6.3.0
	 */
	public function getCache()
	{
		_deprecated_function(
			__METHOD__,
			'8.0.0'
		);

		return [];
	}

	/**
	 * Doesn't do anything
	 *
	 * @param array<mixed> $cache Array with cached vars.
	 * @return $this
	 * @since  6.3.0
	 * @since 8.0.0 Deprecated
	 */
	public function setCache($cache)
	{
		_deprecated_function(
			__METHOD__,
			'8.0.0'
		);

		return $this;
	}

	/**
	 * Always returns the $default value
	 *
	 * @param string $key Cache key.
	 * @param mixed $default Default value.
	 * @return mixed
	 * @since  8.0.0 Deprecated
	 * @since  6.3.0
	 */
	public function cache($key, $default = '')
	{
		_deprecated_function(
			__METHOD__,
			'8.0.0'
		);

		return $default;
	}
}
