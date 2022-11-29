<?php

/**
 * Privacy ereased trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Privacy;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Data exported trigger class
 */
class DataExported extends PrivacyTrigger
{

	/**
	 * Archive package path
	 *
	 * @var string
	 */
	public $archivePath;

	/**
	 * Archive package URL
	 *
	 * @var string
	 */
	public $archiveUrl;

	/**
	 * HTML report path
	 *
	 * @var string
	 */
	public $htmlReportPath;

	/**
	 * JSON report pathname
	 *
	 * @var string
	 */
	public $jsonReportPathname;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct('privacy/data-exported', __('Personal Data Exported', 'notification'));

		$this->add_action('wp_privacy_personal_data_export_file_created', 10, 5);

		$this->set_description(__('Fires when user personal data is exported', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string  $archivePathname Archive pathname.
	 * @param string  $archiveUrl Archive url.
	 * @param string  $htmlReportPathname Html report pathname.
	 * @param int $requestId Request id.
	 * @param string  $jsonReportPathname Json report pathname.
	 */
	public function context( $archivePathname, $archiveUrl, $htmlReportPathname, $requestId, $jsonReportPathname = null )
	{

		$this->request = wp_get_user_request($requestId);
		$this->user_object = get_userdata($this->request->user_id);
		$this->archive_path = $archivePathname;
		$this->archive_url = $archiveUrl;
		$this->html_report_path = $htmlReportPathname;
		$this->json_report_pathname = $jsonReportPathname;
		$this->data_operation_time = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags()
	{

		parent::merge_tags();

		$this->add_merge_tag(
			new MergeTag\UrlTag(
				[
				'slug' => 'archive_url',
				'name' => __('User data archive URL', 'notification'),
				'description' => __('https://example.com/wp-content/uploads/wp-personal-data-exports/wp-personal-data-file-f3563fe4.zip', 'notification'),
				'example' => true,
				'group' => __('Archive', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->archive_url;
				},
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\StringTag(
				[
				'slug' => 'archive_pathname',
				'name' => __('User data archive pathname', 'notification'),
				'description' => __('/var/www/html/wp-content/uploads/wp-personal-data-exports/wp-personal-data-file-test.zip', 'notification'),
				'example' => true,
				'group' => __('Archive', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->archive_path;
				},
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\StringTag(
				[
				'slug' => 'html_report_pathname',
				'name' => __('User data html report pathname', 'notification'),
				'description' => __('/var/www/html/wp-content/uploads/wp-personal-data-exports/wp-personal-data-file-test.html', 'notification'),
				'example' => true,
				'group' => __('Archive', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->html_report_path;
				},
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\StringTag(
				[
				'slug' => 'json_report_pathname',
				'name' => __('User data JSON report pathname', 'notification'),
				'description' => __('/var/www/html/wp-content/uploads/wp-personal-data-exports/wp-personal-data-file-test.JSON', 'notification'),
				'example' => true,
				'group' => __('Archive', 'notification'),
				'resolver' => static function ( $trigger ) {
					return $trigger->json_report_pathname;
				},
				]
			)
		);
	}
}
