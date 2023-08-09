<?php

/**
 * Enqueues admin scripts
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem;

/**
 * Scripts class
 */
class Scripts
{
	/**
	 * Filesystem object
	 *
	 * @var \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * Scripts constructor
	 *
	 * @param \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem $fs Assets filesystem object.
	 * @since 5.0.0
	 */
	public function __construct(Filesystem $fs)
	{
		$this->filesystem = $fs;
	}

	/**
	 * Enqueue scripts and styles for admin
	 *
	 * @action admin_enqueue_scripts
	 *
	 * @param string $pageHook current page hook.
	 * @return void
	 */
	public function enqueueScripts($pageHook)
	{

		$allowedHooks = apply_filters(
			'notification/scripts/allowed_hooks',
			[
				\Notification::component('admin_extensions')->pageHook,
				\Notification::component('core_settings')->pageHook,
				\Notification::component('admin_wizard')->pageHook,
				'plugins.php',
				'post-new.php',
				'post.php',
				'edit.php',
			]
		);

		$allowedPostTypes = apply_filters(
			'notification/scripts/allowed_post_types',
			[
				'notification',
			]
		);

		if (
			!in_array(
				$pageHook,
				$allowedHooks,
				true
			)
		) {
			return;
		}

		// Check if we are on a correct post type if we edit the post.
		if (
			in_array(
				$pageHook,
				['post-new.php', 'post.php', 'edit.php'],
				true
			) && !in_array(
				get_post_type(),
				$allowedPostTypes,
				true
			)
		) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'notification',
			$this->filesystem->url('resources/js/dist/scripts.js'),
			[
				'jquery', 'wp-color-picker', 'wp-i18n', 'wp-hooks', 'jquery-ui-sortable', 'wp-polyfill', 'wp-tinymce',
				'wplink',
			],
			$this->filesystem->mtime('resources/js/dist/scripts.js'),
			true
		);

		wp_enqueue_style(
			'notification',
			$this->filesystem->url('resources/css/dist/style.css'),
			[],
			$this->filesystem->mtime('resources/css/dist/style.css')
		);

		wp_set_script_translations(
			'notification',
			'notification'
		);

		wp_localize_script(
			'notification',
			'notification',
			[
				'ajaxurl' => admin_url('admin-ajax.php'),
				'postId' => get_the_ID(),
				'rest_nonce' => wp_create_nonce('wp_rest'),
				'csrfToken' => wp_create_nonce('notification_csrf'),
				'select_rest_url' => get_rest_url(
					null,
					'notification/v1/repeater-field/select/'
				),
				'repeater_rest_url' => get_rest_url(
					null,
					'notification/v1/repeater-field/'
				),
				'section_repeater_rest_url' => get_rest_url(
					null,
					'notification/v1/section-repeater-field/'
				),
			]
		);

		// Remove TinyMCE styles as they are not applied to any frontend content.
		remove_editor_styles();

		do_action('notification/scripts', $pageHook);
	}
}
