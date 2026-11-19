<?php
/**
 * Term Permalink MergeTag Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\MergeTag\Taxonomy;

use BracketSpace\Notification\Repository\MergeTag\Taxonomy\TermPermalink;
use BracketSpace\Notification\Tests\Helpers\Objects\SimpleTrigger;

/**
 * Term Permalink test case.
 */
class TestTermPermalink extends \WP_UnitTestCase {

	/**
	 * Resolves the term permalink stored on the trigger
	 */
	public function test_resolves_permalink() {
		$trigger = new class('test/trigger') extends SimpleTrigger {
			public $termPermalink = '';
		};
		$trigger->termPermalink = 'http://example.com/category/nature';

		$tag = new TermPermalink();
		$tag->setTrigger($trigger);

		$this->assertSame('http://example.com/category/nature', $tag->resolve());
	}
}
