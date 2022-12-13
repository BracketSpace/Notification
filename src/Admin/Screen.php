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
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function renderMainColumn($post)
	{
		if (get_post_type($post) !== 'notification') {
			return;
		}

		$notificationPost = notificationAdaptFrom(
			'WordPress',
			$post
		);

		do_action(
			'notification/post/column/main',
			$notificationPost
		);
	}

	/**
	 * Renders the trigger metabox
	 *
	 * @action notification/post/column/main
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notificationPost Notification Post object.
	 * @return void
	 */
	public function renderTriggerSelect($notificationPost)
	{
		$groupedTriggers = Store\Trigger::grouped();
		$trigger = $notificationPost->getTrigger();

		// Add merge tags.
		if ($trigger) {
			$trigger->setupMergeTags();
		}

		Templates::render(
			'trigger/metabox',
			[
				'selected' => $trigger
					? $trigger->getSlug()
					: '',
				'triggers' => $groupedTriggers,
				'has_triggers' => !empty($groupedTriggers),
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
	public function renderCarrierBoxes($notificationPost)
	{
		echo '<h3 class="carriers-section-title">' . esc_html__(
			'Carriers',
			'notification'
		) . '</h3>';

		do_action_deprecated(
			'notitication/admin/notifications/pre',
			[
				$notificationPost,
			],
			'6.0.0',
			'notification/admin/carriers/pre'
		);

		do_action(
			'notification/admin/carriers/pre',
			$notificationPost
		);

		echo '<div id="carrier-boxes">';

		foreach (Store\Carrier::all() as $_carrier) {
			$carrier = $notificationPost->getCarrier($_carrier->getSlug());

			// If Carrier wasn't set before, use the blank one.
			if (!$carrier) {
				$carrier = $_carrier;
			}

			// If Carrier is enabled then it must be activated as well.
			// Fix for previous versions when enabled but not activated Carrier was just wiped out.
			if ($carrier->isEnabled()) {
				$carrier->activate();
			}

			Templates::render(
				'box',
				[
					'slug' => $carrier->getSlug(),
					'carrier' => $carrier,
					'id' => 'notification-carrier-' . $carrier->getSlug() . '-box',
					'name' => 'notification_carrier_' . $carrier->getSlug() . '_enable',
					'active_name' => 'notification_carrier_' . $carrier->getSlug() . '_active',
					'title' => $carrier->getName(),
					'content' => $this->getCarrierForm($carrier),
					'open' => $carrier->isEnabled(),
					'active' => $carrier->isActive(),
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

		do_action(
			'notification/admin/carriers',
			$notificationPost
		);
	}

	/**
	 * Renders a widget for adding Carriers
	 *
	 * @action notification/admin/carriers
	 *
	 * @param object $notificationPost Notification Post object.
	 * @return void
	 */
	public function renderCarriersWidget($notificationPost)
	{
		$carriers = Store\Carrier::all();
		$exists = $notificationPost->getCarriers();

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
	 * @param \BracketSpace\Notification\Interfaces\Sendable $carrier Carrier object.
	 * @return string                       Form HTML.
	 * @since  6.0.0
	 */
	public function getCarrierForm(Interfaces\Sendable $carrier)
	{
		$fields = $carrier->getFormFields();

		// No fields available so return the default view.
		if (empty($fields) && !$carrier->hasRecipientsField()) {
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
	public function addSaveMetaBox()
	{
		add_meta_box(
			'notification_save',
			__(
				'Save',
				'notification'
			),
			[$this, 'renderSaveMetabox'],
			'notification',
			'side',
			'high'
		);

		// enable metabox.
		add_filter(
			'notification/admin/allow_metabox/notification_save',
			'__return_true'
		);
	}

	/**
	 * Renders Save metabox
	 *
	 * @param object $post current WP_Post.
	 * @return void
	 */
	public function renderSaveMetabox($post)
	{
		$deleteText = !EMPTY_TRASH_DAYS
			? __(
				'Delete Permanently',
				'notification'
			)
			: __(
				'Move to Trash',
				'notification'
			);

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
	public function addMergeTagsMetaBox()
	{
		add_meta_box(
			'notification_merge_tags',
			__(
				'Merge Tags',
				'notification'
			),
			[$this, 'renderMergeTagsMetabox'],
			'notification',
			'side',
			'default'
		);

		// enable metabox.
		add_filter(
			'notification/admin/allow_metabox/notification_merge_tags',
			'__return_true'
		);
	}

	/**
	 * Renders Merge Tags metabox
	 *
	 * @param object $post current WP_Post.
	 * @return void
	 */
	public function renderMergeTagsMetabox($post)
	{
		$notification = notificationAdaptFrom(
			'WordPress',
			$post
		);
		$trigger = $notification->getTrigger();
		$triggerSlug = $trigger
			? $trigger->getSlug()
			: false;

		if (!$triggerSlug) {
			Templates::render('mergetag/metabox-notrigger');
			return;
		}

		$this->renderMergeTagsList($triggerSlug);
	}

	/**
	 * Renders Merge Tags list
	 *
	 * @param string $triggerSlug Trigger slug.
	 * @return void
	 */
	public function renderMergeTagsList($triggerSlug)
	{
		$trigger = Store\Trigger::get($triggerSlug);

		if (empty($trigger)) {
			Templates::render('mergetag/metabox-nomergetags');
			return;
		}

		$tagGroups = $this->prepareMergeTagGroups($trigger);

		if (empty($tagGroups)) {
			Templates::render('mergetag/metabox-nomergetags');
			return;
		}

		$vars = [
			'trigger' => $trigger,
			'tags' => $trigger->getMergeTags('visible'),
			'tag_groups' => $tagGroups,
		];

		if (count($tagGroups) > 1) {
			Templates::render(
				'mergetag/metabox-accordion',
				$vars
			);
		} else {
			Templates::render(
				'mergetag/metabox-list',
				$vars
			);
		}
	}

	/**
	 * Prepates merge tag groups for provided Trigger.
	 *
	 * @param object $trigger Trigger object.
	 * @return array  $groups  Grouped tags.
	 */
	public function prepareMergeTagGroups($trigger)
	{
		$groups = [];
		$tags = $trigger->getMergeTags('visible');

		if (empty($tags)) {
			return $groups;
		}

		$otherKey = __(
			'Other',
			'notification'
		);

		foreach ($tags as $tag) {
			if ($tag->getGroup()) {
				$groups[$tag->getGroup()][] = $tag;
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

		return apply_filters(
			'notification/trigger/tags/groups',
			$groups,
			$trigger
		);
	}

	/**
	 * Cleans up all metaboxes to keep the screen nice and clean
	 *
	 * @action add_meta_boxes 999999999
	 *
	 * @return void
	 */
	public function metaboxCleanup()
	{
		global $wpMetaBoxes;

		if (!isset($wpMetaBoxes['notification'])) {
			return;
		}

		foreach ($wpMetaBoxes['notification'] as $contextName => $context) {
			foreach ($context as $priority => $boxes) {
				foreach ($boxes as $boxId => $box) {
					$allowBox = apply_filters(
						'notification/admin/allow_metabox/' . $boxId,
						false
					);

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
	 * @param object $screen current WP_Screen.
	 * @return void
	 */
	public function addHelp($screen)
	{
		if ($screen->post_type !== 'notification') {
			return;
		}

		$screen->addHelpTab(
			[
				'id' => 'notification_global_merge_tags',
				'title' => __(
					'Global Merge Tags',
					'notification'
				),
				'content' => Templates::get(
					'help/global-merge-tags',
					[
						'tags' => Store\GlobalMergeTag::all(),
					]
				),
			]
		);

		$screen->setHelpSidebar(Templates::get('help/sidebar'));
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
	public function ajaxRenderMergeTags()
	{
		check_ajax_referer('notification_csrf');

		$ajax = new Response();

		if (!isset($_POST['trigger_slug'])) {
			$ajax->error();
		}

		ob_start();

		$this->renderMergeTagsList(sanitize_text_field(wp_unslash($_POST['trigger_slug'])));

		$ajax->send(ob_get_clean());
	}
}
