<?php
/**
 * Upsell class
 * Used to promote free and paid extensions.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Settings;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Upsell class
 */
class Upsell {

	/**
	 * Adds conditionals metabox
	 *
	 * @action add_meta_boxes
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function add_conditionals_meta_box() {
		if ( class_exists( 'NotificationConditionals' ) ) {
			return;
		}

		add_meta_box(
			'notification_conditionals',
			__( 'Conditionals', 'notification' ),
			[ $this, 'conditionals_metabox' ],
			'notification',
			'advanced',
			'default'
		);

		// Enable metabox.
		add_filter( 'notification/admin/allow_metabox/notification_conditionals', '__return_true' );
	}

	/**
	 * Conditionals metabox content
	 *
	 * @since  [Next]
	 * @param  object $post current WP_Post.
	 * @return void
	 */
	public function conditionals_metabox( $post ) {
		Templates::render( 'upsell/conditionals-metabox' );
	}

	/**
	 * Prints additional Merge Tag group in Merge Tags metabox
	 * Note: Used when there are Merge Tag groups
	 *
	 * @action notification/metabox/trigger/tags/groups/after
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function custom_fields_merge_tag_group() {
		if ( class_exists( 'NotificationCustomFields' ) ) {
			return;
		}

		Templates::render( 'upsell/custom-fields-mergetag-group' );
	}

	/**
	 * Renders review queue switch
	 *
	 * @action notification/admin/metabox/save/post
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function review_queue_switch() {
		if ( class_exists( 'NotificationReviewQueue' ) ) {
			return;
		}

		Templates::render( 'upsell/review-queue-switch' );
	}

	/**
	 * Registers Scheduled Triggers settings
	 *
	 * @action notification/settings/register 200
	 *
	 * @since  [Next]
	 * @param  Settings $settings Settings API object.
	 * @return void
	 */
	public function scheduled_triggers_settings( $settings ) {
		if ( class_exists( 'NotificationScheduledTriggers' ) ) {
			return;
		}

		$section = $settings->add_section( __( 'Triggers', 'notification' ), 'triggers' );

		$section->add_group( __( 'Scheduled Triggers', 'notification' ), 'scheduled_triggers' )
			->add_field( [
				'name'     => __( 'Features', 'notification' ),
				'slug'     => 'upsell',
				'addons'   => [
					'message' => Templates::get( 'upsell/scheduled-triggers-setting' ),
				],
				'render'   => [ new CoreFields\Message(), 'input' ],
				'sanitize' => [ new CoreFields\Message(), 'sanitize' ],
			] );

	}

	/**
	 * Adds Trigger upselling.
	 *
	 * @action notification/settings/section/triggers/before
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function triggers_settings_upsell() {
		Templates::render( 'upsell/triggers-upsell' );
	}

	/**
	 * Adds Carrier upselling.
	 *
	 * @action notification/settings/section/carriers/before
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function carriers_settings_upsell() {
		Templates::render( 'upsell/carriers-upsell' );
	}

}
