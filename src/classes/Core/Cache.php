<?php
/**
 * Runtime Cache class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Utils\Cache\Transient;

/**
 * Cache class
 */
class Cache {

	/**
	 * Plugin cache keys
	 *
	 * @var array
	 */
	protected $caches = [];

	/**
	 * Cache expiration in seconds
	 *
	 * @var integer
	 */
	protected $cache_expiration = 0;

	/**
	 * Cache constructor
	 *
	 * @since [Next]
	 */
	public function __construct() {

		$this->caches = [
			'post_types',
			'comment_types',
			'taxonomies',
			'settings_config',
		];

	}

	/**
	 * Caches the objects automatically if
	 * the cache is empty.
	 *
	 * @action wp_loaded 20
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function audo_cache_objects() {

		foreach ( $this->caches as $cache_key ) {
			if ( false === $this->get( $cache_key ) ) {
				$this->cache( $cache_key );
			}
		}

	}

	/**
	 * Caches the objects for later use
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function cache_objects() {

		foreach ( $this->caches as $cache_key ) {
			$this->cache( $cache_key );
		}

	}

	/**
	 * Caches single value
	 *
	 * @since  [Next]
	 * @param  string $cache_key Object name to cache.
	 * @return void
	 */
	public function cache( $cache_key ) {
		$transient_key = sprintf( 'notification_%s', $cache_key );
		$cache         = new Transient( $transient_key, $this->cache_expiration );

		$cache->set( call_user_func( [ $this, sprintf( 'get_%s', $cache_key ) ] ) );
	}

	/**
	 * Gets single value from cache
	 *
	 * @since  [Next]
	 * @param  string $cache_key Object name to cache.
	 * @return mixed
	 */
	public function get( $cache_key ) {
		$transient_key = sprintf( 'notification_%s', $cache_key );
		$cache         = new Transient( $transient_key, $this->cache_expiration );
		return $cache->get();
	}

	/**
	 * Gets post types
	 *
	 * @since  [Next]
	 * @return array
	 */
	protected function get_post_types() {

		$post_types = [ 'dupa' => 'Dupa' ];

		foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $post_type ) {
			$post_types[ $post_type->name ] = $post_type->labels->singular_name;
		}

		return $post_types;

	}

	/**
	 * Gets comment types for cache
	 *
	 * @since  [Next]
	 * @return array
	 */
	protected function get_comment_types() {

		global $wpdb;

		$comment_types = [
			'comment'   => __( 'Comment', 'notification' ),
			'pingback'  => __( 'Pingback', 'notification' ),
			'trackback' => __( 'Trackback', 'notification' ),
		];

		$db_types = $wpdb->get_col( // phpcs:ignore
			"SELECT DISTINCT comment_type
			FROM   $wpdb->comments
			WHERE  1=1"
		);

		foreach ( $db_types as $type ) {
			if ( ! isset( $comment_types[ $type ] ) ) {
				$name                   = ucfirst( str_replace( [ '_', '-' ], ' ', $type ) );
				$comment_types[ $type ] = __( $name, 'notification' );
			}
		}

		return $comment_types;

	}

	/**
	 * Gets taxonomies for cache
	 *
	 * @since  [Next]
	 * @return array
	 */
	protected function get_taxonomies() {

		$taxonomies = [];

		foreach ( get_taxonomies( [ 'public' => true ], 'objects' ) as $taxonomy ) {

			if ( 'post_format' === $taxonomy->name ) {
				continue;
			}

			$taxonomies[ $taxonomy->name ] = $taxonomy->labels->name;

		}

		return $taxonomies;

	}

	/**
	 * Gets settings for cache
	 *
	 * @since  [Next]
	 * @return array
	 */
	protected function get_settings_config() {

		$config = [];

		foreach ( notification_runtime( 'core_settings' )->get_sections() as $section_slug => $section ) {
			$config[ $section_slug ] = [];

			foreach ( $section->get_groups() as $group_slug => $group ) {
				$config[ $section_slug ][ $group_slug ] = [];

				foreach ( $group->get_fields() as $field_slug => $field ) {
					$config[ $section_slug ][ $group_slug ][ $field_slug ] = $field->default_value();
				}
			}
		}

		return $config;

	}

}
