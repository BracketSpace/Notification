<?php

/**
 * WordPress Object Helper class
 *
 * Provides static methods used to easily get defined content type objects.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils;

use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;

/**
 * WpObjectHelper class
 */
class WpObjectHelper
{

	/**
	 * Gets post type object
	 *
	 * @since  8.0.0
	 * @param  string $postTypeSlug Post type slug.
	 * @return \WP_Post_Type|null
	 */
	public static function getPostType( $postTypeSlug )
	{
		return get_post_type_object($postTypeSlug);
	}

	/**
	 * Gets registered post types in slug => name format
	 *
	 * @since  8.0.0
	 * @param  array<mixed> $args Query args.
	 * @return array<string,string>
	 */
	public static function getPostTypes( $args = [] ): array
	{
		$postTypes = [];
		foreach (get_post_types($args, 'objects') as $postType) {
			if (! $postType instanceof \WP_Post_Type) {
				continue;
			}

			$postTypes[$postType->name] = $postType->labels->singularName;
		}

		return $postTypes;
	}

	/**
	 * Gets post type object name
	 *
	 * @since  8.0.0
	 * @param  string $postTypeSlug Post type slug.
	 * @return string|null
	 */
	public static function getPostTypeName( $postTypeSlug )
	{
		$postType = self::getPostType($postTypeSlug);
		return $postType->labels->singularName ?? null;
	}

	/**
	 * Gets taxonomy object
	 *
	 * @since  8.0.0
	 * @param  string $taxonomySlug Taxonomy slug.
	 * @return \WP_Taxonomy|null
	 */
	public static function getTaxonomy( $taxonomySlug )
	{
		$taxonomy = get_taxonomy($taxonomySlug);
		return $taxonomy ? $taxonomy : null;
	}

	/**
	 * Gets registered taxonomies in slug => name format
	 *
	 * @since  8.0.0
	 * @param  array<mixed> $args Query args.
	 * @return array<string,\WP_Taxonomy>
	 */
	public static function getTaxonomies( $args = [] ): array
	{
		$taxonomies = [];

		foreach (get_taxonomies($args, 'objects') as $taxonomy) {
			if ($taxonomy->name === 'post_format') {
				continue;
			}

			$taxonomies[$taxonomy->name] = $taxonomy->labels->singularName;
		}

		return $taxonomies;
	}

	/**
	 * Gets taxonomy object name
	 *
	 * @since  8.0.0
	 * @param  string $taxonomySlug Taxonomy slug.
	 * @return string|null
	 */
	public static function getTaxonomyName( $taxonomySlug )
	{
		$taxonomy = self::getTaxonomy($taxonomySlug);
		return $taxonomy->labels->singularName ?? null;
	}

	/**
	 * Gets comment type name
	 *
	 * @since  8.0.0
	 * @param  string $commentTypeSlug Comment type slug.
	 * @return string|null
	 */
	public static function getCommentTypeName( $commentTypeSlug )
	{
		$commentTypes = self::getCommentTypes();
		return $commentTypes[$commentTypeSlug] ?? null;
	}

	/**
	 * Gets comment types from database
	 *
	 * @since  8.0.0
	 * @return array<string,string>
	 */
	public static function getCommentTypes(): array
	{
		$driver = new CacheDriver\ObjectCache('notification');
		$cache = new Cache($driver, 'comment_types');

		return $cache->collect(
			static function () {
				global $wpdb;

				$commentTypes = [
				'comment' => __('Comment', 'notification'),
				'pingback' => __('Pingback', 'notification'),
				'trackback' => __('Trackback', 'notification'),
				];

				// There's no other way to get comment types and we're using the cache lib.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$dbTypes = $wpdb->getCol(
					"SELECT DISTINCT comment_type
				FROM $wpdb->comments
				WHERE 1=1"
				);

				foreach ($dbTypes as $type) {
					if (isset($commentTypes[$type])) {
						continue;
					}

					// Dynamically generated and translated name.
					$name = ucfirst(str_replace([ '_', '-' ], ' ', $type));

					$commentTypes[(string)$type] = __($name);
				}

				return $commentTypes;
			}
		);
	}
}
