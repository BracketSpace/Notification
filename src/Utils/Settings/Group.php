<?php

/**
 * Settings Group class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings;

/**
 * Group class
 */
class Group
{

	/**
	 * Settings handle
	 *
	 * @var string
	 */
	private $handle;

	/**
	 * Group name
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Group slug
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Group fields
	 *
	 * @var array<mixed>
	 */
	private $fields = [];

	/**
	 * Group description
	 *
	 * @var string
	 */
	private $description = '';

	/**
	 * Section slug
	 *
	 * @var string
	 */
	private $section;

	/**
	 * Section collapsed
	 *
	 * @var bool
	 */
	private $collapsed;

	/**
	 * Group constructor
	 *
	 * @param string $handle Settings handle.
	 * @param string $name Group name.
	 * @param string $slug Group slug.
	 * @param string $section Section slug.
	 * @throws \Exception Exception.
	 */
	public function __construct($handle, $name, $slug, $section)
	{

		if (empty($handle)) {
			throw new \Exception('Setting handle in Section instance cannot be empty');
		}

		$this->handle = $handle;

		if (empty($name)) {
			throw new \Exception('Group name cannot be empty');
		}

		$this->name($name);

		if (empty($slug)) {
			throw new \Exception('Group slug cannot be empty');
		}

		$this->slug(sanitize_title($slug));

		if (empty($section)) {
			throw new \Exception('Group must belong to Section');
		}

		$this->section($section);
	}

	/**
	 * Get or set name
	 *
	 * @param string $name Name. Do not pass anything to get current value.
	 * @return string name
	 */
	public function name($name = null)
	{

		if ($name !== null) {
			$this->name = $name;
		}

		return apply_filters(
			$this->handle . '/settings/group/name',
			$this->name,
			$this
		);
	}

	/**
	 * Get or set slug
	 *
	 * @param string $slug Slug. Do not pass anything to get current value.
	 * @return string slug
	 */
	public function slug($slug = null)
	{

		if ($slug !== null) {
			$this->slug = $slug;
		}

		return apply_filters(
			$this->handle . '/settings/group/slug',
			$this->slug,
			$this
		);
	}

	/**
	 * Get or set section
	 *
	 * @param string $section Section. Do not pass anything to get current value.
	 * @return string section
	 */
	public function section($section = null)
	{

		if ($section !== null) {
			$this->section = $section;
		}

		return apply_filters(
			$this->handle . '/settings/group/section',
			$this->section,
			$this
		);
	}

	/**
	 * Set or get description
	 *
	 * @param mixed $description string to set description, null to get it.
	 * @return string|\BracketSpace\Notification\Utils\Settings\Group
	 * String when using getter and Group when using setter
	 */
	public function description($description = null)
	{

		if ($description !== null) {
			$this->description = $description;
			return $this;
		}

		return apply_filters(
			$this->handle . '/settings/group/description',
			$this->description,
			$this
		);
	}

	/**
	 * Set or get collapsed
	 *
	 * @param bool|null $collapsed Bool to set collapsed, null to get it.
	 * @return bool|\BracketSpace\Notification\Utils\Settings\Group String when using getter and Group when using setter
	 */
	public function collapsed($collapsed = null)
	{

		if ($collapsed !== null) {
			$this->collapsed = $collapsed;
			return $this;
		}

		return apply_filters(
			$this->handle . '/settings/group/collapsed',
			$this->collapsed,
			$this
		);
	}

	/**
	 * Set collapsed alias
	 *
	 * @return \BracketSpace\Notification\Utils\Settings\Group $this
	 */
	public function collapse()
	{
		return $this->collapsed(true);
	}

	/**
	 * Add Field to the Group
	 *
	 * @param array $args field args.
	 * @return \BracketSpace\Notification\Utils\Settings\Group $this
	 * @throws \Exception Exception.
	 */
	public function addField($args)
	{

		if (!isset($args['name'], $args['slug'], $args['render'])) {
			throw new \Exception('You must define field name, slug and render callback');
		}

		$field = new Field(
			$this->handle,
			$args['name'],
			$args['slug'],
			$this->section,
			$this->slug()
		);

		$field->setRenderer($args['render']);

		if (isset($args['sanitize'])) {
			$field->setSanitizer($args['sanitize']);
		}

		if (isset($args['default'])) {
			$field->defaultValue($args['default']);
		}

		if (isset($args['description'])) {
			$field->description($args['description']);
		}

		if (isset($args['addons'])) {
			$field->addons($args['addons']);
		}

		$this->fields[$args['slug']] = $field;

		do_action(
			$this->handle . '/settings/field/added',
			$this->fields[$args['slug']],
			$this
		);

		return $this;
	}

	/**
	 * Get all registered Fields
	 *
	 * @return array
	 */
	public function getFields()
	{

		return apply_filters(
			$this->handle . '/settings/group/fields',
			$this->fields
		);
	}
}
