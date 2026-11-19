<?php
/**
 * Post triggers last editor Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\Trigger\Post;

use BracketSpace\Notification\Repository\MergeTag\User\UserLogin;
use BracketSpace\Notification\Repository\Trigger\Post\PostPublished;

/**
 * Post last editor test case.
 */
class TestPostLastEditor extends \WP_UnitTestCase {

	/**
	 * Posts without the _edit_last meta fall back to the author
	 */
	public function test_last_editor_falls_back_to_author() {
		$authorId = $this->factory->user->create(['user_login' => 'post_author']);
		$post = $this->factory->post->create_and_get(['post_author' => $authorId]);
		delete_post_meta($post->ID, '_edit_last');

		$trigger = new PostPublished('post');
		$trigger->context('publish', 'draft', $post);

		$this->assertInstanceOf(\WP_User::class, $trigger->lastEditor);
		$this->assertSame($authorId, $trigger->lastEditor->ID);

		$tag = new UserLogin(['property_name' => 'lastEditor']);
		$tag->setTrigger($trigger);
		$this->assertSame('post_author', $tag->resolve());
	}

	/**
	 * The _edit_last meta takes precedence
	 */
	public function test_last_editor_uses_meta() {
		$authorId = $this->factory->user->create();
		$editorId = $this->factory->user->create();
		$post = $this->factory->post->create_and_get(['post_author' => $authorId]);
		update_post_meta($post->ID, '_edit_last', $editorId);

		$trigger = new PostPublished('post');
		$trigger->context('publish', 'draft', $post);

		$this->assertSame($editorId, $trigger->lastEditor->ID);
	}
}
