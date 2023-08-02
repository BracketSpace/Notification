<?php

/**
 * Post trigger abstract
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post trigger class
 */
abstract class PostTrigger extends Abstracts\Trigger
{
	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	public $postType;

	/**
	 * Post author user object
	 *
	 * @var \WP_User|false
	 */
	public $author;

	/**
	 * Post last editor user object
	 *
	 * @var \WP_User|false
	 */
	public $lastEditor;

	/**
	 * Post in subject.
	 *
	 * @var \WP_Post
	 */
	public $post;

	/**
	 * Post creation timestamp.
	 *
	 * @var int|false
	 */
	public $postCreationDatetime;

	/**
	 * Post publication timestamp.
	 *
	 * @var int|false
	 */
	public $postPublicationDatetime;

	/**
	 * Post modification timestamp.
	 *
	 * @var int|false
	 */
	public $postModificationDatetime;

	/**
	 * Constructor
	 *
	 * @param array<mixed> $params trigger configuration params.
	 */
	public function __construct($params = [])
	{
		if (!isset($params['post_type'], $params['slug'])) {
			trigger_error(
				'PostTrigger requires post_type and slug params.',
				E_USER_ERROR
			);
		}

		$this->postType = $params['post_type'];

		parent::__construct($params['slug']);
	}

	/**
	 * Lazy loads group name
	 *
	 * @return string|null Group name
	 */
	public function getGroup()
	{
		return WpObjectHelper::getPostTypeName($this->postType);
	}

	/**
	 * Gets Post Type slug
	 *
	 * @return string Post Type slug
	 */
	public function getPostType(): string
	{
		return $this->postType;
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{

		$postTypeName = WpObjectHelper::getPostTypeName($this->postType);

		$this->addMergeTag(
			new MergeTag\Post\PostID(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\PostPermalink(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\PostTitle(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\PostSlug(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\PostContent(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\PostContentHtml(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\PostExcerpt(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\PostStatus(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\ThumbnailUrl(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\FeaturedImageUrl(
				[
					'post_type' => $this->postType,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Post\FeaturedImageId(
				[
					'post_type' => $this->postType,
				]
			)
		);

		if ($this->postType === 'post') {
			$this->addMergeTag(
				new MergeTag\StringTag(
					[
						'slug' => sprintf(
							'%s_sticky',
							$this->postType
						),
						'name' => sprintf(
						// translators: singular post name.
							__(
								'%s sticky status',
								'notification'
							),
							$postTypeName
						),
						'group' => $postTypeName,
						'resolver' => function ($trigger) {
							return is_sticky($trigger->{$this->postType}->ID)
								? __(
									'Sticky',
									'notification'
								)
								: __(
									'Not sticky',
									'notification'
								);
						},
					]
				)
			);
		}

		$taxonomies = get_object_taxonomies(
			$this->postType,
			'objects'
		);

		if (!empty($taxonomies)) {
			foreach ($taxonomies as $taxonomy) {
				// Post format special treatment.
				$group = $taxonomy->name === 'post_format'
					? $postTypeName
					: __(
						'Taxonomies',
						'notification'
					);

				$this->addMergeTag(
					new MergeTag\Post\PostTerms(
						[
							'post_type' => $this->postType,
							'taxonomy' => $taxonomy,
							'group' => $group,
						]
					)
				);
			}
		}

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => sprintf(
						'%s_creation_datetime',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s creation date and time',
							'notification'
						),
						$postTypeName
					),
					'timestamp' => $this->postCreationDatetime,
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => sprintf(
						'%s_modification_datetime',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s modification date and time',
							'notification'
						),
						$postTypeName
					),
					'timestamp' => $this->postModificationDatetime,
				]
			)
		);

		// Author.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
					'slug' => sprintf(
						'%s_author_user_ID',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s author user ID',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'author',
					'group' => __(
						'Author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLogin(
				[
					'slug' => sprintf(
						'%s_author_user_login',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s author user login',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'author',
					'group' => __(
						'Author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
					'slug' => sprintf(
						'%s_author_user_email',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s author user email',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'author',
					'group' => __(
						'Author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNicename(
				[
					'slug' => sprintf(
						'%s_author_user_nicename',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s author user nicename',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'author',
					'group' => __(
						'Author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
					'slug' => sprintf(
						'%s_author_user_display_name',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s author user display name',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'author',
					'group' => __(
						'Author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserFirstName(
				[
					'slug' => sprintf(
						'%s_author_user_firstname',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s author user first name',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'author',
					'group' => __(
						'Author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLastName(
				[
					'slug' => sprintf(
						'%s_author_user_lastname',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s author user last name',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'author',
					'group' => __(
						'Author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\Avatar(
				[
					'slug' => sprintf(
						'%s_author_user_avatar',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s author user avatar',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'author',
					'group' => __(
						'Author',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserRole(
				[
					'slug' => sprintf(
						'%s_author_user_role',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s author user role',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'author',
					'group' => __(
						'Author',
						'notification'
					),
				]
			)
		);

		// Last updated by.
		$this->addMergeTag(
			new MergeTag\User\UserID(
				[
					'slug' => sprintf(
						'%s_last_editor_ID',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s last editor ID',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'last_editor',
					'group' => __(
						'Last editor',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLogin(
				[
					'slug' => sprintf(
						'%s_last_editor_login',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s last editor login',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'last_editor',
					'group' => __(
						'Last editor',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserEmail(
				[
					'slug' => sprintf(
						'%s_last_editor_email',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s last editor email',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'last_editor',
					'group' => __(
						'Last editor',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserNicename(
				[
					'slug' => sprintf(
						'%s_last_editor_nicename',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s last editor nicename',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'last_editor',
					'group' => __(
						'Last editor',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserDisplayName(
				[
					'slug' => sprintf(
						'%s_last_editor_display_name',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s last editor display name',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'last_editor',
					'group' => __(
						'Last editor',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserFirstName(
				[
					'slug' => sprintf(
						'%s_last_editor_firstname',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s last editor first name',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'last_editor',
					'group' => __(
						'Last editor',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserLastName(
				[
					'slug' => sprintf(
						'%s_last_editor_lastname',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s last editor last name',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'last_editor',
					'group' => __(
						'Last editor',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\Avatar(
				[
					'slug' => sprintf(
						'%s_last_editor_avatar',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s last editor avatar',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'last_editor',
					'group' => __(
						'Last editor',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\User\UserRole(
				[
					'slug' => sprintf(
						'%s_last_editor_role',
						$this->postType
					),
					'name' => sprintf(
					// translators: singular post name.
						__(
							'%s last editor role',
							'notification'
						),
						$postTypeName
					),
					'property_name' => 'last_editor',
					'group' => __(
						'Last editor',
						'notification'
					),
				]
			)
		);
	}
}
