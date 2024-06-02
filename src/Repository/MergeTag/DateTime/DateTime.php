<?php

/**
 * DateTime merge tag
 *
 * Requirements:
 * - Trigger property of the merge tag slug with timestamp
 * - or 'timestamp' parameter in arguments with timestamp
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\DateTime;

use BracketSpace\Notification\Repository\MergeTag\StringTag;
use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Helpers\CaseHelper;

/**
 * DateTime merge tag class
 */
class DateTime extends StringTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since [Next] The automatic property lookup searches for camelCase prop first.
	 * @since 7.0.0 Expects the timestamp without an offset.
	 *               You can pass timezone argument as well, use GMT if timestamp is with offset.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		$args = wp_parse_args(
			$params,
			[
				'slug' => 'datetime',
				'name' => __('Date and Time', 'notification'),
				'time_format' => get_option('time_format'),
				'date_format' => get_option('date_format'),
				'timezone' => null,
				'example' => true,
				'group' => __('Date', 'notification'),
			]
		);

		if (!isset($args['description'])) {
			$args['description'] = wp_date($args['date_format'] . ' ' . $args['time_format']) . '. ';
			$args['description'] .= __(
				'You can change the format in General WordPress Settings.',
				'notification'
			);
		}

		if (!isset($args['resolver'])) {
			$args['resolver'] = function ($trigger) use ($args) {

				if (isset($args['timestamp'])) {
					$timestamp = $args['timestamp'];
				} elseif (isset($trigger->{CaseHelper::toCamel($this->getSlug())})) {
					$timestamp = $trigger->{CaseHelper::toCamel($this->getSlug())};
				} elseif (isset($trigger->{$this->getSlug()})) {
					$timestamp = $trigger->{$this->getSlug()};
				} else {
					$timestamp = null;
				}

				return wp_date(
					$args['date_format'] . ' ' . $args['time_format'],
					$timestamp,
					$args['timezone']
				);
			};
		}

		parent::__construct($args);
	}
}
