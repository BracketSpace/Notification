<?php

/**
 * Database Service.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Database;

/**
 * This class describes a database service.
 */
class DatabaseService
{
	/**
	 * Gets wpdb object.
	 *
	 * @return \wpdb WPDB class instance
	 */
	public static function db(): \wpdb
	{
		global $wpdb;

		return $wpdb;
	}

	/**
	 * Prefixes the table name.
	 *
	 * @param string $tableName The table name
	 * @return string The prefixed table name.
	 */
	public static function prefixTable(string $tableName): string
	{
		return self::db()->prefix . $tableName;
	}
}
