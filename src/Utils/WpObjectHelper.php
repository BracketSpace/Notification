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
	 * @param string $postTypeSlug Post type slug.
	 * @return \WP_Post_Type|null
	 * @since  8.0.0
	 */
	public static function getPostType($postTypeSlug)
	{
		return get_post_type_object($postTypeSlug);
	}

	/**
	 * Gets registered post types in slug => name format
	 *
	 * @param array<mixed> $args Query args.
	 * @return array<string,string>
	 * @since  8.0.0
	 */
	public static function getPostTypes($args = []): array
	{
		$postTypes = [];
		foreach (
			get_post_types(
				$args,
				'objects'
			) as $postType
		) {
			if (!$postType instanceof \WP_Post_Type) {
				continue;
			}

			$postTypes[$postType->name] = $postType->labels->singular_name;
		}

		return $postTypes;
	}

	/**
	 * Gets post type object name
	 *
	 * @param string $postTypeSlug Post type slug.
	 * @return string|null
	 * @since  8.0.0
	 */
	public static function getPostTypeName($postTypeSlug)
	{
		$postType = self::getPostType($postTypeSlug);
		return $postType->labels->singular_name ?? null;
	}

	/**
	 * Gets taxonomy object
	 *
	 * @param string $taxonomySlug Taxonomy slug.
	 * @return \WP_Taxonomy|null
	 * @since  8.0.0
	 */
	public static function getTaxonomy($taxonomySlug)
	{
		$taxonomy = get_taxonomy($taxonomySlug);
		return $taxonomy
			? $taxonomy
			: null;
	}

	/**
	 * Gets registered taxonomies in slug => name format
	 *
	 * @param array<mixed> $args Query args.
	 * @return array<string,\WP_Taxonomy>
	 * @since  8.0.0
	 */
	public static function getTaxonomies($args = []): array
	{
		$taxonomies = [];

		foreach (
			get_taxonomies(
				$args,
				'objects'
			) as $taxonomy
		) {
			if ($taxonomy->name === 'post_format') {
				continue;
			}

			$taxonomies[$taxonomy->name] = $taxonomy->labels->singular_name;
		}

		return $taxonomies;
	}

	/**
	 * Gets taxonomy object name
	 *
	 * @param string $taxonomySlug Taxonomy slug.
	 * @return string|null
	 * @since  8.0.0
	 */
	public static function getTaxonomyName($taxonomySlug)
	{
		$taxonomy = self::getTaxonomy($taxonomySlug);
		return $taxonomy->labels->singularName ?? null;
	}

	/**
	 * Gets comment type name
	 *
	 * @param string $commentTypeSlug Comment type slug.
	 * @return string|null
	 * @since  8.0.0
	 */
	public static function getCommentTypeName($commentTypeSlug)
	{
		$commentTypes = self::getCommentTypes();
		return $commentTypes[$commentTypeSlug] ?? null;
	}

	/**
	 * Gets comment types from database
	 *
	 * @return array<string,string>
	 * @since  8.0.0
	 */
	public static function getCommentTypes(): array
	{
		$driver = new CacheDriver\ObjectCache('notification');
		$cache = new Cache(
			$driver,
			'comment_types'
		);

		return $cache->collect(
			static function () {
				global $wpdb;

				$commentTypes = [
					'comment' => __(
						'Comment',
						'notification'
					),
					'pingback' => __(
						'Pingback',
						'notification'
					),
					'trackback' => __(
						'Trackback',
						'notification'
					),
				];

				// There's no other way to get comment types and we're using the cache lib.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$dbTypes = $wpdb->get_col(
					"SELECT DISTINCT comment_type
				FROM $wpdb->comments
				WHERE 1=1"
				);

				foreach ($dbTypes as $type) {
					if (isset($commentTypes[$type])) {
						continue;
					}

					// Dynamically generated and translated name.
					$name = ucfirst(
						str_replace(
							['_', '-'],
							' ',
							$type
						)
					);

					$commentTypes[(string)$type] = __($name);
				}

				return $commentTypes;
			}
		);
	}
}
