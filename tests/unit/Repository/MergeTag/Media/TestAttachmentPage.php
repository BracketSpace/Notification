<?php
/**
 * Attachment Page MergeTag Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\MergeTag\Media;

use BracketSpace\Notification\Repository\MergeTag\Media\AttachmentPage;
use BracketSpace\Notification\Tests\Helpers\Objects\SimpleTrigger;

/**
 * Attachment Page test case.
 */
class TestAttachmentPage extends \WP_UnitTestCase {

	/**
	 * Resolves the attachment permalink
	 */
	public function test_resolves_permalink() {
		$attachmentId = $this->factory->attachment->create_object(
			'image.jpg',
			0,
			['post_mime_type' => 'image/jpeg', 'post_title' => 'Image']
		);

		$trigger = new class('test/trigger') extends SimpleTrigger {
			public $attachment;
		};
		$trigger->attachment = get_post($attachmentId);

		$tag = new AttachmentPage();
		$tag->setTrigger($trigger);

		$this->assertSame(get_permalink($attachmentId), $tag->resolve());
		$this->assertNotEmpty($tag->resolve());
	}
}
