<?php

/**
 * Screen class
 * Renders the Notification screens.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response;

/**
 * Screen class
 */
class Screen
{

	/**
	 * TABLE OF CONTENTS: -------------------------------
	 * - Display.
	 * - Screen Help.
	 * - AJAX Renders.
	 * --------------------------------------------------
	 */

	/**
	 * --------------------------------------------------
	 * Display.
	 * --------------------------------------------------
	 */

	/**
	 * Renders main column on the Notification edit screen.
	 *
	 * @action edit_form_after_title 1
	 *
	 * @param  \WP_Post $post Post object.
	 * @return void
	 */
	public function render_main_column( $post )
	{
		if (get_post_type($post) !== 'notification') {
			return;
		}

		$notificationPost = notification_adapt_from('WordPress', $post);

		do_action('notification/post/column/main', $notificationPost);
	}

	/**
	 * Renders the trigger metabox
	 *
	 * @action notification/post/column/main
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notificationPost Notification Post object.
	 * @return void
	 */
	public function render_trigger_select( $notificationPost )
	{
		$groupedTriggers = Store\Trigger::grouped();
		$trigger = $notificationPost->get_trigger();

		// Add merge tags.
		if ($trigger) {
			$trigger->setup_merge_tags();
		}

		Templates::render(
			'trigger/metabox',
			[
			'selected' => $trigger ? $trigger->get_slug() : '',
			'triggers' => $groupedTriggers,
			'has_triggers' => ! empty($groupedTriggers),
			'select_name' => 'notification_trigger',
			'notification' => $notificationPost,
			]
		);
	}

	/**
	 * Adds Carriers section title on post edit screen,
	 * just under the Trigger and prints Carrier boxes
	 *
	 * @action notification/post/column/main 20
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notificationPost Notification Post object.
	 * @return void
	 */
	public function render_carrier_boxes( $notificationPost )
	{
		echo '<h3 class="carriers-section-title">' . esc_html__('Carriers', 'notification') . '</h3>';

		do_action_deprecated(
			'notitication/admin/notifications/pre',
			[
			$notificationPost,
			],
			'6.0.0',
			'notification/admin/carriers/pre'
		);

		do_action('notification/admin/carriers/pre', $notificationPost);

		echo '<div id="carrier-boxes">';

		foreach (Store\Carrier::all() as $_carrier) {
			$carrier = $notificationPost->get_carrier($_carrier->get_slug());

			// If Carrier wasn't set before, use the blank one.
			if (! $carrier) {
				$carrier = $_carrier;
			}

			// If Carrier is enabled then it must be activated as well.
			// Fix for previous versions when enabled but not activated Carrier was just wiped out.
			if ($carrier->is_enabled()) {
				$carrier->activate();
			}

			Templates::render(
				'box',
				[
				'slug' => $carrier->get_slug(),
				'carrier' => $carrier,
				'id' => 'notification-carrier-' . $carrier->get_slug() . '-box',
				'name' => 'notification_carrier_' . $carrier->get_slug() . '_enable',
				'active_name' => 'notification_carrier_' . $carrier->get_slug() . '_active',
				'title' => $carrier->get_name(),
				'content' => $this->get_carrier_form($carrier),
				'open' => $carrier->is_enabled(),
				'active' => $carrier->is_active(),
				]
			);
		}

		echo '</div>';

		do_action_deprecated(
			'notitication/admin/notifications',
			[
			$notificationPost,
			],
			'6.0.0',
			'notification/admin/carriers'
		);

		do_action('notification/admin/carriers', $notificationPost);
	}

	/**
	 * Renders a widget for adding Carriers
	 *
	 * @action notification/admin/carriers
	 *
	 * @param  object $notificationPost Notification Post object.
	 * @return void
	 */
	public function render_carriers_widget( $notificationPost )
	{
		$carriers = Store\Carrier::all();
		$exists = $notificationPost->get_carriers();

		Templates::render(
			'carriers/widget-add',
			[
			'carriers_added_count' => count($carriers),
			'carriers_exists_count' => count($exists),
			'carriers' => $carriers,
			'carriers_exists' => (array)$exists,
			]
		);
	}

	/**
	 * Gets Carrier config form
	 *
	 * @since  6.0.0
	 * @param \BracketSpace\Notification\Interfaces\Sendable $carrier Carrier object.
	 * @return string                       Form HTML.
	 */
	public function get_carrier_form( Interfaces\Sendable $carrier )
	{
		$fields = $carrier->get_form_fields();

		// No fields available so return the default view.
		if (empty($fields) && ! $carrier->has_recipients_field()) {
			return Templates::get('form/empty-form');
		}

		// Setup the fields and return form.
		return Templates::get(
			'form/table',
			[
			'carrier' => $carrier,
			]
		);
	}

	/**
	 * Adds metabox with Save button
	 *
	 * @action add_meta_boxes
	 *
	 * @return void
	 */
	public function add_save_meta_box()
	{
		add_meta_box(
			'notification_save',
			__('Save', 'notification'),
			[ $this, 'render_save_metabox' ],
			'notification',
			'side',
			'high'
		);

		// enable metabox.
		add_filter('notification/admin/allow_metabox/notification_save', '__return_true');
	}

	/**
	 * Renders Save metabox
	 *
	 * @param  object $post current WP_Post.
	 * @return void
	 */
	public function render_save_metabox( $post )
	{
		$deleteText = ! EMPTY_TRASH_DAYS ? __('Delete Permanently', 'notification') : __('Move to Trash', 'notification');

		// New posts has the status auto-draft and in this case the Notification should be enabled.
		$enabled = get_post_status($post->ID) !== 'draft';

		Templates::render(
			'save-metabox',
			[
			'enabled' => $enabled,
			'post_id' => $post->ID,
			'delete_link_label' => $deleteText,
			]
		);
	}

	/**
	 * Adds metabox with Merge Tags.
	 *
	 * @action add_meta_boxes
	 *
	 * @return void
	 */
	public function add_merge_tags_meta_box()
	{
		add_meta_box(
			'notification_merge_tags',
			__('Merge Tags', 'notification'),
			[ $this, 'render_merge_tags_metabox' ],
			'notification',
			'side',
			'default'
		);

		// enable metabox.
		add_filter('notification/admin/allow_metabox/notification_merge_tags', '__return_true');
	}

	/**
	 * Renders Merge Tags metabox
	 *
	 * @param  object $post current WP_Post.
	 * @return void
	 */
	public function render_merge_tags_metabox( $post )
	{
		$notification = notification_adapt_from('WordPress', $post);
		$trigger = $notification->get_trigger();
		$triggerSlug = $trigger ? $trigger->get_slug() : false;

		if (! $triggerSlug) {
			Templates::render('mergetag/metabox-notrigger');
			return;
		}

		$this->render_merge_tags_list($triggerSlug);
	}

	/**
	 * Renders Merge Tags list
	 *
	 * @param  string $triggerSlug Trigger slug.
	 * @return void
	 */
	public function render_merge_tags_list( $triggerSlug )
	{
		$trigger = Store\Trigger::get($triggerSlug);

		if (empty($trigger)) {
			Templates::render('mergetag/metabox-nomergetags');
			return;
		}

		$tagGroups = $this->prepare_merge_tag_groups($trigger);

		if (empty($tagGroups)) {
			Templates::render('mergetag/metabox-nomergetags');
			return;
		}

		$vars = [
			'trigger' => $trigger,
			'tags' => $trigger->get_merge_tags('visible'),
			'tag_groups' => $tagGroups,
		];

		if (count($tagGroups) > 1) {
			Templates::render('mergetag/metabox-accordion', $vars);
		} else {
			Templates::render('mergetag/metabox-list', $vars);
		}
	}

	/**
	 * Prepates merge tag groups for provided Trigger.
	 *
	 * @param  object $trigger Trigger object.
	 * @return array  $groups  Grouped tags.
	 */
	public function prepare_merge_tag_groups( $trigger )
	{
		$groups = [];
		$tags = $trigger->get_merge_tags('visible');

		if (empty($tags)) {
			return $groups;
		}

		$otherKey = __('Other', 'notification');

		foreach ($tags as $tag) {
			if ($tag->get_group()) {
				$groups[$tag->get_group()][] = $tag;
			} else {
				$groups[$otherKey][] = $tag;
			}
		}

		ksort($groups);

		if (isset($groups[$otherKey])) {
			$others = $groups[$otherKey];
			unset($groups[$otherKey]);
			$groups[$otherKey] = $others;
		}

		return apply_filters('notification/trigger/tags/groups', $groups, $trigger);
	}

	/**
	 * Cleans up all metaboxes to keep the screen nice and clean
	 *
	 * @action add_meta_boxes 999999999
	 *
	 * @return void
	 */
	public function metabox_cleanup()
	{
		global $wpMetaBoxes;

		if (! isset($wpMetaBoxes['notification'])) {
			return;
		}

		foreach ($wpMetaBoxes['notification'] as $contextName => $context) {
			foreach ($context as $priority => $boxes) {
				foreach ($boxes as $boxId => $box) {
					$allowBox = apply_filters('notification/admin/allow_metabox/' . $boxId, false);

					if ($allowBox) {
						continue;
					}

					unset($wpMetaBoxes['notification'][$contextName][$priority][$boxId]);
				}
			}
		}
	}

	/**
	 * --------------------------------------------------
	 * Screen Help.
	 * --------------------------------------------------
	 */

	/**
	 * Adds help tabs and useful links
	 *
	 * @action current_screen
	 *
	 * @param  object $screen current WP_Screen.
	 * @return void
	 */
	public function add_help( $screen )
	{
		if ($screen->post_type !== 'notification') {
			return;
		}

		$screen->add_help_tab(
			[
			'id' => 'notification_global_merge_tags',
			'title' => __('Global Merge Tags', 'notification'),
			'content' => Templates::get(
				'help/global-merge-tags',
				[
				'tags' => Store\GlobalMergeTag::all(),
				]
			),
			]
		);

		$screen->set_help_sidebar(Templates::get('help/sidebar'));
	}

	/**
	 * --------------------------------------------------
	 * AJAX Renders.
	 * --------------------------------------------------
	 */

	/**
	 * Renders Merge Tags metabox for AJAX call.
	 *
	 * @action wp_ajax_get_merge_tags_for_trigger
	 *
	 * @return void
	 */
	public function ajax_render_merge_tags()
	{
		check_ajax_referer('notification_csrf');

		$ajax = new Response();

		if (! isset($_POST['trigger_slug'])) {
			$ajax->error();
		}

		ob_start();

		$this->render_merge_tags_list(sanitize_text_field(wp_unslash($_POST['trigger_slug'])));

		$ajax->send(ob_get_clean());
	}
}
