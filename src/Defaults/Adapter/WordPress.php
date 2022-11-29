<?php

/**
 * WordPress Adapter class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Adapter;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Core\Notification;

/**
 * WordPress Adapter class
 *
 * @method void set_source_post_id(int $postId)
 */
class WordPress extends Abstracts\Adapter
{

	/**
	 * Notification post
	 *
	 * @var \WP_Post
	 */
	protected $post;

	/**
	 * Notification post type slug
	 *
	 * @var string
	 */
	protected $postType = 'notification';

	/**
	 * {@inheritdoc}
	 *
	 * @param mixed $input Input data.
	 * @return $this
	 * @throws \Exception If wrong input param provided.
	 */
	public function read($input = null)
	{

		if ($input instanceof \WP_Post) {
			$this->setPost($input);
		} elseif (is_integer($input)) {
			$this->setPost(get_post($input));
		} else {
			throw new \Exception('Read method of WordPress adapter expects the post ID or WP_Post object');
		}

		try {
			$jsonAdapter = notification_adapt_from(
				'JSON',
				wp_specialchars_decode(
					$this->post->postContent,
					ENT_COMPAT
				)
			);
			$this->setupNotification(notification_convert_data($jsonAdapter->getNotification()->toArray()));
		} catch (\Throwable $e) {
			$doNothing = true;
		}

		// Hash sync with WordPress post.
		$this->setHash($this->post->postName);

		// Source.
		$this->setSource('WordPress');
		$this->setSourcePostId($this->getId());

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return $this|\WP_Error
	 */
	public function save()
	{

		// Update version as WordPress automatically does this while updating the post.
		$versionBackup = $this->getVersion();
		$this->setVersion(time());

		$data = $this->getNotification()->toArray();

		/** @var \BracketSpace\Notification\Defaults\Adapter\JSON */
		$jsonAdapter = notification_swap_adapter(
			'JSON',
			$this
		);
		$json = $jsonAdapter->save(JSON_UNESCAPED_UNICODE);

		// Update the hash.
		if (
			!preg_match(
				'/notification_[a-z0-9]{13}/',
				$data['hash']
			)
		) {
			$data['hash'] = Notification::createHash();
		}

		// Fix WordPress balance tags filter.
		remove_filter(
			'content_save_pre',
			'balanceTags',
			50
		);

		// WordPress post related: Title, Hash, Status, Version.
		$postId = wp_insert_post(
			[
				'ID' => $this->getId(),
				'post_content' => wp_slash($json), // Cache.
				'post_type' => $this->postType,
				'post_title' => $data['title'],
				'post_name' => $data['hash'],
				'post_status' => $data['enabled']
					? 'publish'
					: 'draft',
			],
			true
		);

		add_filter(
			'content_save_pre',
			'balanceTags',
			50
		);

		if (is_wp_error($postId)) {
			$this->setVersion($versionBackup);
			return $postId;
		}

		if (!$this->hasPost()) {
			$this->setPost(get_post($postId));
		}

		return $this;
	}

	/**
	 * Checks if notification post has been just started
	 *
	 * @return bool
	 * @since 6.0.0
	 */
	public function isNew()
	{
		return empty($this->post) || $this->post->postDateGmt === '0000-00-00 00:00:00';
	}

	/**
	 * Gets notification post ID
	 *
	 * @return int post ID
	 * @since 6.0.0
	 */
	public function getId()
	{
		return !empty($this->post)
			? $this->post->ID
			: 0;
	}

	/**
	 * Gets post
	 *
	 * @return \WP_Post|null
	 * @since 6.0.0
	 */
	public function getPost()
	{
		return $this->post;
	}

	/**
	 * Sets post
	 *
	 * @param \WP_Post $post WP Post to set.
	 * @return $this
	 * @since 6.0.0
	 */
	public function setPost(\WP_Post $post)
	{
		$this->post = $post;
		return $this;
	}

	/**
	 * Sets post type
	 *
	 * @param string $postType WP Post Type.
	 * @return $this
	 * @since 6.0.0
	 */
	public function setPostType($postType)
	{
		$this->postType = $postType;
		return $this;
	}

	/**
	 * Checks if adapter already have the post
	 *
	 * @return bool
	 * @since 6.0.0
	 */
	public function hasPost()
	{
		return !empty($this->getPost());
	}
}
