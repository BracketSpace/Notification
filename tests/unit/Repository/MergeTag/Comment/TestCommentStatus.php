<?php
/**
 * Comment Status MergeTag Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\MergeTag\Comment;

use BracketSpace\Notification\Repository\MergeTag\Comment\CommentStatus;
use BracketSpace\Notification\Tests\Helpers\Objects\SimpleTrigger;

/**
 * Comment Status test case.
 */
class TestCommentStatus extends \WP_UnitTestCase {

	/**
	 * @dataProvider statusProvider
	 */
	public function test_resolves_status($approved, $expected) {
		$trigger = new class('test/trigger') extends SimpleTrigger {
			public $comment;
		};
		$trigger->comment = (object)['comment_approved' => $approved];

		$tag = new CommentStatus();
		$tag->setTrigger($trigger);

		$this->assertSame($expected, $tag->resolve());
	}

	public function statusProvider() {
		return [
			'approved' => ['1', 'Approved'],
			'unapproved' => ['0', 'Unapproved'],
			'spam' => ['spam', 'Marked as spam'],
			'trash' => ['trash', 'Trashed'],
		];
	}
}
