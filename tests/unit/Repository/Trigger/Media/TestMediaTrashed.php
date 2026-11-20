<?php
/**
 * Media trashed trigger Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\Trigger\Media;

use BracketSpace\Notification\Repository\Trigger\Media\MediaTrashed;

/**
 * Media trashed trigger test case.
 */
class TestMediaTrashed extends \WP_UnitTestCase {

	/**
	 * Trigger under test
	 *
	 * @var MediaTrashed
	 */
	private $trigger;

	/**
	 * Context results collected from the hooks
	 *
	 * @var array<int, mixed>
	 */
	private $results = [];

	public function setUp() : void {
		parent::setUp();
		wp_set_current_user($this->factory->user->create(['role' => 'administrator']));
		$this->trigger = new MediaTrashed();
		$this->results = [];
	}

	/**
	 * Runs the trigger context on the given action, like the plugin does
	 *
	 * @param string $tag action name.
	 */
	private function listen($tag) {
		add_action($tag, function ($postId) {
			$this->results[] = $this->trigger->context($postId);
		}, 10, 1);
	}

	public function test_listens_to_trash_and_delete() {
		$tags = array_column($this->trigger->getActions(), 'tag');

		$this->assertContains('wp_trash_post', $tags);
		$this->assertContains('delete_attachment', $tags);
	}

	public function test_fires_when_attachment_is_moved_to_trash() {
		$attachmentId = $this->factory->attachment->create(['post_mime_type' => 'application/zip']);
		$this->listen('wp_trash_post');

		wp_trash_post($attachmentId);

		$this->assertCount(1, $this->results);
		$this->assertNotFalse($this->results[0]);
		$this->assertSame($attachmentId, $this->trigger->attachment->ID);
	}

	public function test_does_not_fire_again_when_trashed_attachment_is_deleted() {
		$attachmentId = $this->factory->attachment->create(['post_mime_type' => 'application/zip']);
		wp_trash_post($attachmentId);
		$this->listen('delete_attachment');

		wp_delete_attachment($attachmentId, true);

		$this->assertSame([false], $this->results);
	}

	public function test_fires_when_attachment_is_deleted_without_trash() {
		$attachmentId = $this->factory->attachment->create(['post_mime_type' => 'application/zip']);
		$this->listen('delete_attachment');

		wp_delete_attachment($attachmentId, true);

		$this->assertCount(1, $this->results);
		$this->assertNotFalse($this->results[0]);
		$this->assertSame($attachmentId, $this->trigger->attachment->ID);
	}

	public function test_ignores_other_post_types_moved_to_trash() {
		$postId = $this->factory->post->create();
		$this->listen('wp_trash_post');

		wp_trash_post($postId);

		$this->assertSame([false], $this->results);
	}
}
