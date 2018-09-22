<?php
/**
 * Theme trigger abstract.
 *
 * @package notification.
 */

namespace BracketSpace\Notification\Defaults\Trigger\Theme;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Theme trigger class.
 */
abstract class ThemeTrigger extends Abstracts\Trigger {

	/**
	 * Registers attached merge tags.
	 *
	 * @return void.
	 */
	public function merge_tags() {

		$this->add_merge_tag(
			new MergeTag\Theme\ThemeAuthor()
		);

		$this->add_merge_tag(
			new MergeTag\Theme\ThemeAuthorURI()
		);

		$this->add_merge_tag(
			new MergeTag\Theme\ThemeDescription()
		);

		$this->add_merge_tag(
			new MergeTag\Theme\ThemeName()
		);

		$this->add_merge_tag(
			new MergeTag\Theme\ThemeURI()
		);

		$this->add_merge_tag(
			new MergeTag\Theme\ThemeVersion()
		);

	}
}
