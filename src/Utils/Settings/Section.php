<?php
/**
 * Settings Section class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings;

/**
 * Settings class
 */
class Section {

	/**
	 * Settings handle
	 *
	 * @var string
	 */
	private $handle;

	/**
	 * Section name
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Section slug
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Section groups
	 *
	 * @var array
	 */
	private $groups = [];

	/**
	 * Section constructor
	 *
	 * @throws \Exception Exception.
	 * @param string $handle Settings handle.
	 * @param string $name   Section name.
	 * @param string $slug   Section slug.
	 */
	public function __construct( $handle, $name, $slug ) {

		if ( empty( $handle ) ) {
			throw new \Exception( 'Setting handle in Section instance cannot be empty' );
		}

		$this->handle = $handle;

		if ( empty( $name ) ) {
			throw new \Exception( 'Section name cannot be empty' );
		}

		$this->name( $name );

		if ( empty( $slug ) ) {
			throw new \Exception( 'Section slug cannot be empty' );
		}

		$this->slug( sanitize_title( $slug ) );

	}

	/**
	 * Get or set name
	 *
	 * @param  string $name Name. Do not pass anything to get current value.
	 * @return string name
	 */
	public function name( $name = null ) {

		if ( null !== $name ) {
			$this->name = $name;
		}

		return apply_filters( $this->handle . '/settings/section/name', $this->name, $this );

	}

	/**
	 * Get or set slug
	 *
	 * @param  string $slug Slug. Do not pass anything to get current value.
	 * @return string slug
	 */
	public function slug( $slug = null ) {

		if ( null !== $slug ) {
			$this->slug = $slug;
		}

		return apply_filters( $this->handle . '/settings/section/slug', $this->slug, $this );

	}

	/**
	 * Add Group to the section
	 *
	 * @throws \Exception Exception.
	 * @param string $name Group name.
	 * @param string $slug Group slug.
	 * @return Group
	 */
	public function add_group( $name, $slug ) {

		if ( empty( $name ) || empty( $slug ) ) {
			throw new \Exception( 'Group name and slug cannot be empty' );
		}

		if ( ! isset( $this->groups[ $slug ] ) ) {
			$this->groups[ $slug ] = new Group( $this->handle, $name, $slug, $this->slug() );
			do_action( $this->handle . '/settings/group/added', $this->groups[ $slug ], $this );
		}

		return $this->groups[ $slug ];

	}

	/**
	 * Get all registered Groups
	 *
	 * @return array
	 */
	public function get_groups() {

		return apply_filters( $this->handle . '/settings/section/groups', $this->groups, $this );

	}

	/**
	 * Get group by group slug
	 *
	 * @param  string $slug group slug.
	 * @return mixed        group object or false if no group defined
	 */
	public function get_group( $slug = '' ) {

		if ( isset( $this->groups[ $slug ] ) ) {
			return apply_filters( $this->handle . '/settings/group', $this->groups[ $slug ], $this );
		}

		return false;

	}

}
