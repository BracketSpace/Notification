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
use BracketSpace\Notification\Store\Carrier as CarrierStore;

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

	/**
	 * Adds missing Carriers to the List.
	 *
	 * @action notification/carrier/list/after
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function carriers_list() {
		Templates::render( 'upsell/carriers-list', [
			'carriers' => self::get_missing_carriers(),
		] );
	}

	/**
	 * Gets the missing carriers
	 *
	 * @since  [Next]
	 * @return array<string,array{name: string, pro: bool, link: string, icon: string}>
	 */
	public static function get_missing_carriers() {
		$carriers = [];

		if ( ! CarrierStore::has( 'buddypress-notification' ) ) {
			$carriers['buddypress-notification'] = [
				'name' => 'BuddyPress Notification',
				'pro'  => false,
				'link' => 'https://wordpress.org/plugins/notification-buddypress/',
				'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" preserveAspectRatio="xMidYMid meet" enable-background="new 0 0 128 128"><g transform="translate(0,-924.36218)"><path d="m 126.5,988.37986 a 62.5,62.5 0 0 1 -124.999995,0 62.5,62.5 0 1 1 124.999995,0 z" style="fill:#ffffff" /><g transform="matrix(0.02335871,0,0,-0.02334121,-0.11965895,1052.4471)" style="fill:#d84800"><path d="M 2515,5484 C 1798,5410 1171,5100 717,4595 332,4168 110,3689 23,3105 -1,2939 -1,2554 24,2385 111,1783 363,1266 774,842 1492,102 2529,-172 3521,116 c 448,130 858,379 1195,726 413,426 667,949 751,1548 24,173 24,548 -1,715 -91,625 -351,1150 -781,1580 -425,425 -943,685 -1555,780 -101,16 -520,29 -615,19 z m 611,-143 C 4158,5186 4999,4440 5275,3435 5501,2611 5302,1716 4747,1055 4319,547 3693,214 3028,141 c -125,-14 -441,-14 -566,0 -140,15 -338,55 -468,95 C 722,621 -58,1879 161,3188 c 41,249 115,474 234,717 310,631 860,1110 1528,1330 213,70 374,102 642,129 96,10 436,-4 561,-23 z" /><path d="M 2575,5090 C 1629,5020 813,4386 516,3490 384,3089 362,2641 456,2222 643,1386 1307,696 2134,479 c 233,-61 337,-73 611,-73 274,0 378,12 611,73 548,144 1038,500 1357,986 193,294 315,629 363,995 20,156 15,513 -10,660 -42,241 -108,448 -215,665 -421,857 -1325,1375 -2276,1305 z m 820,-491 c 270,-48 512,-261 608,-537 26,-76 31,-104 35,-222 4,-115 1,-149 -17,-220 -62,-250 -237,-457 -467,-553 -63,-27 -134,-48 -134,-41 0,2 15,35 34,72 138,274 138,610 0,883 -110,220 -334,412 -564,483 -30,10 -62,20 -70,23 -21,7 77,56 175,88 126,41 255,49 400,24 z m -610,-285 c 310,-84 541,-333 595,-641 18,-101 8,-278 -20,-368 -75,-236 -220,-401 -443,-505 -109,-51 -202,-70 -335,-70 -355,0 -650,217 -765,563 -28,84 -31,104 -31,232 -1,118 3,152 22,220 89,306 335,528 650,585 67,13 257,3 327,-16 z M 4035,2940 c 301,-95 484,-325 565,-710 21,-103 47,-388 37,-414 -6,-14 -30,-16 -182,-16 -96,0 -175,3 -175,6 0,42 -37,236 -60,313 -99,334 -315,586 -567,661 -24,7 -43,17 -43,21 0,5 32,45 72,90 l 72,82 106,-6 c 67,-3 130,-13 175,-27 z m -1703,-510 258,-255 92,90 c 51,49 183,178 293,286 l 200,197 75,-9 c 207,-26 404,-116 547,-252 170,-161 267,-361 308,-632 15,-100 21,-394 9,-454 l -6,-31 -1519,0 c -1074,0 -1520,3 -1524,11 -14,21 -18,297 -6,407 59,561 364,896 866,950 97,10 55,41 407,-308 z" /></g></g></svg>',
			];
		}

		if ( ! CarrierStore::has( 'discord' ) ) {
			$carriers['discord'] = [
				'name' => 'Discord',
				'pro'  => true,
				'link' => 'https://bracketspace.com/downloads/notification-discord/?utm_source=wp&utm_medium=notification-carriers&utm_id=upsell',
				'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 245 240"><path d="M104.4 103.9c-5.7 0-10.2 5-10.2 11.1s4.6 11.1 10.2 11.1c5.7 0 10.2-5 10.2-11.1.1-6.1-4.5-11.1-10.2-11.1zM140.9 103.9c-5.7 0-10.2 5-10.2 11.1s4.6 11.1 10.2 11.1c5.7 0 10.2-5 10.2-11.1s-4.5-11.1-10.2-11.1z"/><path d="M189.5 20h-134C44.2 20 35 29.2 35 40.6v135.2c0 11.4 9.2 20.6 20.5 20.6h113.4l-5.3-18.5 12.8 11.9 12.1 11.2 21.5 19V40.6c0-11.4-9.2-20.6-20.5-20.6zm-38.6 130.6s-3.6-4.3-6.6-8.1c13.1-3.7 18.1-11.9 18.1-11.9-4.1 2.7-8 4.6-11.5 5.9-5 2.1-9.8 3.5-14.5 4.3-9.6 1.8-18.4 1.3-25.9-.1-5.7-1.1-10.6-2.7-14.7-4.3-2.3-.9-4.8-2-7.3-3.4-.3-.2-.6-.3-.9-.5-.2-.1-.3-.2-.4-.3-1.8-1-2.8-1.7-2.8-1.7s4.8 8 17.5 11.8c-3 3.8-6.7 8.3-6.7 8.3-22.1-.7-30.5-15.2-30.5-15.2 0-32.2 14.4-58.3 14.4-58.3 14.4-10.8 28.1-10.5 28.1-10.5l1 1.2c-18 5.2-26.3 13.1-26.3 13.1s2.2-1.2 5.9-2.9c10.7-4.7 19.2-6 22.7-6.3.6-.1 1.1-.2 1.7-.2 6.1-.8 13-1 20.2-.2 9.5 1.1 19.7 3.9 30.1 9.6 0 0-7.9-7.5-24.9-12.7l1.4-1.6s13.7-.3 28.1 10.5c0 0 14.4 26.1 14.4 58.3 0 0-8.5 14.5-30.6 15.2z"/></svg>',
			];
		}

		if ( ! CarrierStore::has( 'filelog' ) ) {
			$carriers['filelog'] = [
				'name' => __( 'File Log', 'notification' ),
				'pro'  => true,
				'link' => 'https://bracketspace.com/downloads/notification-file-log/?utm_source=wp&utm_medium=notification-carriers&utm_id=upsell',
				'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 143.3 152.5"><path d="M119.8,120.8V138a69.47,69.47,0,0,1-43.2,14.5q-32.4,0-55-22.2Q-1.05,108-1,75.9c0-15.6,3.9-29.2,11.8-40.7A82,82,0,0,1,40.7,8.3,74,74,0,0,1,75.6,0a71.79,71.79,0,0,1,31,6.6,69.31,69.31,0,0,1,25.3,21.8c6.9,9.6,10.4,21.2,10.4,34.8,0,13.8-3.3,25.5-9.9,35.3s-14.3,14.7-23.1,14.7c-10.6,0-16-6.9-16-20.6V82.3C93.3,63.4,86.4,54,72.5,54c-6.2,0-11.2,2.2-14.8,6.5a23.85,23.85,0,0,0-5.4,15.8,19.46,19.46,0,0,0,6.2,14.9,21.33,21.33,0,0,0,15.1,5.7,21.75,21.75,0,0,0,13.8-4.7v16.6a27.67,27.67,0,0,1-15.5,4.3q-15.3,0-25.8-10.2t-10.5-27c0-15.5,6.8-26.7,20.4-33.8a36.74,36.74,0,0,1,17.9-4.3c12.2,0,21.7,4.5,28.5,13.6,5.2,6.9,7.9,17.4,7.9,31.5v8.5c0,3.1,1,4.7,3,4.7,3,0,5.7-3.2,8.3-9.6A56.78,56.78,0,0,0,125.4,65q0-28.95-23.6-42.9h.2c-8.1-4.3-17.4-6.4-28.1-6.4a57.73,57.73,0,0,0-28.7,7.7A58.91,58.91,0,0,0,24,45.1a61.18,61.18,0,0,0-8.2,31.5c0,17.2,5.7,31.4,17,42.7s25.7,16.9,43,16.9c9.6,0,17.5-1.2,23.6-3.5S112.3,126.5,119.8,120.8Z" transform="translate(1)"/></svg>',
			];
		}

		if ( ! CarrierStore::has( 'mailgun' ) ) {
			$carriers['mailgun'] = [
				'name' => 'Mailgun',
				'pro'  => true,
				'link' => 'https://bracketspace.com/downloads/notification-mailgun/?utm_source=wp&utm_medium=notification-carriers&utm_id=upsell',
				'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1000 1000" style="enable-background:new 0 0 1000 1000;" xml:space="preserve"><path class="st0" d="M493,305.7c-88.9,0-161,72.1-161,161c0,88.9,72.1,161,161,161c88.9,0,161-72.1,161-161C654,377.8,582,305.7,493,305.7z M242,466.7c0-138.7,112.4-251,251-251c138.7,0,251.1,112.4,251.1,251c0,9.2-0.5,18.2-1.4,27.1c-1.9,24.5,16.1,43.2,40.4,43.2c41.3,0,45.7-53.2,45.7-70.3c0-185.4-150.3-335.6-335.6-335.6S157.4,281.4,157.4,466.7c0,185.4,150.3,335.6,335.6,335.6c98.4,0,187-42.4,248.4-109.9l69,57.9c-77.9,87.1-191.3,142-317.4,142c-235.1,0-425.7-190.6-425.7-425.7S257.9,41,493,41c235.1,0,425.7,190.6,425.7,425.7c0,94.5-45,171.2-135.4,171.2c-39.8,0-64-18.2-77.2-38.6C661.9,670.5,583,717.8,493,717.8C354.4,717.8,242,605.4,242,466.7z M493,393.1c40.7,0,73.7,33,73.7,73.7c0,40.7-33,73.7-73.7,73.7c-40.7,0-73.7-33-73.7-73.7S452.3,393.1,493,393.1z"/></svg>',
			];
		}

		if ( ! CarrierStore::has( 'pushbullet/push' ) ) {
			$carriers['pushbullet/push'] = [
				'name' => 'Pushbullet - push',
				'pro'  => true,
				'link' => 'https://bracketspace.com/downloads/notification-pushbullet/?utm_source=wp&utm_medium=notification-carriers&utm_id=upsell',
				'icon' => '<svg width="2500" height="2500" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid"><defs><path id="a" d="M256 128c0 70.692-57.308 128-128 128C57.308 256 0 198.692 0 128 0 57.308 57.308 0 128 0c70.692 0 128 57.308 128 128"/><linearGradient x1="8.59%" y1="1.954%" x2="77.471%" y2="73.896%" id="c"><stop stop-color="#4CB36B" offset="0%"/><stop stop-color="#3EA16F" offset="100%"/></linearGradient></defs><mask id="b" fill="#fff"><use xlink:href="#a"/></mask><use fill="#67BF79" xlink:href="#a"/><path d="M256 128c0 70.692-57.308 128-128 128C57.308 256 0 198.692 0 128 0 57.308 57.308 0 128 0c70.692 0 128 57.308 128 128" fill="#67BF79" mask="url(#b)"/><path d="M63.111 187.022L96.178 72l64.533 60.978L200 90.133l87.533 86.289-110.844 124.889L63.111 187.022" fill="url(#c)" mask="url(#b)"/><path d="M77 189.6c-16.733 0-16.733 0-16.733-16.733V81c0-16.733 0-16.733 16.733-16.733h3.334c16.733 0 16.733 0 16.733 16.733v91.867c0 16.733 0 16.733-16.733 16.733H77zM121.041 189.6c-5.699 0-8.508-2.809-8.508-8.508V72.774c0-5.698 2.809-8.507 8.508-8.507h37.537c32.178 0 52.628 32.273 52.628 63.025 0 30.752-20.628 62.308-52.628 62.308h-37.537z" fill="#FFF" style="fill: #fff"/></svg>',
			];
		}

		if ( ! CarrierStore::has( 'pushbullet/sms' ) ) {
			$carriers['pushbullet/sms'] = [
				'name' => 'Pushbullet - SMS',
				'pro'  => true,
				'link' => 'https://bracketspace.com/downloads/notification-pushbullet/?utm_source=wp&utm_medium=notification-carriers&utm_id=upsell',
				'icon' => '<svg width="2500" height="2500" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid"><defs><path id="a" d="M256 128c0 70.692-57.308 128-128 128C57.308 256 0 198.692 0 128 0 57.308 57.308 0 128 0c70.692 0 128 57.308 128 128"/><linearGradient x1="8.59%" y1="1.954%" x2="77.471%" y2="73.896%" id="c"><stop stop-color="#4CB36B" offset="0%"/><stop stop-color="#3EA16F" offset="100%"/></linearGradient></defs><mask id="b" fill="#fff"><use xlink:href="#a"/></mask><use fill="#67BF79" xlink:href="#a"/><path d="M256 128c0 70.692-57.308 128-128 128C57.308 256 0 198.692 0 128 0 57.308 57.308 0 128 0c70.692 0 128 57.308 128 128" fill="#67BF79" mask="url(#b)"/><path d="M63.111 187.022L96.178 72l64.533 60.978L200 90.133l87.533 86.289-110.844 124.889L63.111 187.022" fill="url(#c)" mask="url(#b)"/><path d="M77 189.6c-16.733 0-16.733 0-16.733-16.733V81c0-16.733 0-16.733 16.733-16.733h3.334c16.733 0 16.733 0 16.733 16.733v91.867c0 16.733 0 16.733-16.733 16.733H77zM121.041 189.6c-5.699 0-8.508-2.809-8.508-8.508V72.774c0-5.698 2.809-8.507 8.508-8.507h37.537c32.178 0 52.628 32.273 52.628 63.025 0 30.752-20.628 62.308-52.628 62.308h-37.537z" fill="#FFF" style="fill: #fff"/></svg>',
			];
		}

		if ( ! CarrierStore::has( 'pushover/push' ) ) {
			$carriers['pushover/push'] = [
				'name' => 'Pushover - push',
				'pro'  => true,
				'link' => 'https://bracketspace.com/downloads/notification-pushover/?utm_source=wp&utm_medium=notification-carriers&utm_id=upsell',
				'icon' => '<?xml version="1.0" encoding="utf-8"?><svg width="602px" height="602px" viewBox="57 57 602 602" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="layer1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" transform="translate(58.964119, 58.887520)" opacity="0.91"><ellipse style="fill: rgb(36, 157, 241); fill-rule: evenodd; stroke: rgb(255, 255, 255); stroke-width: 0;" transform="matrix(-0.674571, 0.73821, -0.73821, -0.674571, 556.833239, 241.613465)" cx="216.308" cy="152.076" rx="296.855" ry="296.855"/><path d="M 280.949 172.514 L 355.429 162.714 L 282.909 326.374 L 282.909 326.374 C 295.649 325.394 308.142 321.067 320.389 313.394 L 320.389 313.394 L 320.389 313.394 C 332.642 305.714 343.916 296.077 354.209 284.484 L 354.209 284.484 L 354.209 284.484 C 364.496 272.884 373.396 259.981 380.909 245.774 L 380.909 245.774 L 380.909 245.774 C 388.422 231.561 393.812 217.594 397.079 203.874 L 397.079 203.874 L 397.079 203.874 C 399.039 195.381 399.939 187.214 399.779 179.374 L 399.779 179.374 L 399.779 179.374 C 399.612 171.534 397.569 164.674 393.649 158.794 L 393.649 158.794 L 393.649 158.794 C 389.729 152.914 383.766 148.177 375.759 144.584 L 375.759 144.584 L 375.759 144.584 C 367.759 140.991 356.899 139.194 343.179 139.194 L 343.179 139.194 L 343.179 139.194 C 327.172 139.194 311.409 141.807 295.889 147.034 L 295.889 147.034 L 295.889 147.034 C 280.376 152.261 266.002 159.857 252.769 169.824 L 252.769 169.824 L 252.769 169.824 C 239.542 179.784 228.029 192.197 218.229 207.064 L 218.229 207.064 L 218.229 207.064 C 208.429 221.924 201.406 238.827 197.159 257.774 L 197.159 257.774 L 197.159 257.774 C 195.526 263.981 194.546 268.961 194.219 272.714 L 194.219 272.714 L 194.219 272.714 C 193.892 276.474 193.812 279.577 193.979 282.024 L 193.979 282.024 L 193.979 282.024 C 194.139 284.477 194.462 286.357 194.949 287.664 L 194.949 287.664 L 194.949 287.664 C 195.442 288.971 195.852 290.277 196.179 291.584 L 196.179 291.584 L 196.179 291.584 C 179.519 291.584 167.349 288.234 159.669 281.534 L 159.669 281.534 L 159.669 281.534 C 151.996 274.841 150.119 263.164 154.039 246.504 L 154.039 246.504 L 154.039 246.504 C 157.959 229.191 166.862 212.694 180.749 197.014 L 180.749 197.014 L 180.749 197.014 C 194.629 181.334 211.122 167.531 230.229 155.604 L 230.229 155.604 L 230.229 155.604 C 249.342 143.684 270.249 134.214 292.949 127.194 L 292.949 127.194 L 292.949 127.194 C 315.656 120.167 337.789 116.654 359.349 116.654 L 359.349 116.654 L 359.349 116.654 C 378.296 116.654 394.219 119.347 407.119 124.734 L 407.119 124.734 L 407.119 124.734 C 420.026 130.127 430.072 137.234 437.259 146.054 L 437.259 146.054 L 437.259 146.054 C 444.446 154.874 448.936 165.164 450.729 176.924 L 450.729 176.924 L 450.729 176.924 C 452.529 188.684 451.959 200.934 449.019 213.674 L 449.019 213.674 L 449.019 213.674 C 445.426 229.027 438.646 244.464 428.679 259.984 L 428.679 259.984 L 428.679 259.984 C 418.719 275.497 406.226 289.544 391.199 302.124 L 391.199 302.124 L 391.199 302.124 C 376.172 314.697 358.939 324.904 339.499 332.744 L 339.499 332.744 L 339.499 332.744 C 320.066 340.584 299.406 344.504 277.519 344.504 L 277.519 344.504 L 275.069 344.504 L 212.839 484.154 L 142.279 484.154 L 280.949 172.514 Z" transform="matrix(1, 0, 0, 1, 0, 0)" style="fill: rgb(255, 255, 255); fill-rule: nonzero; white-space: pre;"/></g></svg>',
			];
		}

		if ( ! CarrierStore::has( 'sendgrid' ) ) {
			$carriers['sendgrid'] = [
				'name' => 'SendGrid',
				'pro'  => true,
				'link' => 'https://bracketspace.com/downloads/notification-sendgrid/?utm_source=wp&utm_medium=notification-carriers&utm_id=upsell',
				'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 127.3 127.3"><g style="isolation:isolate"><g id="Layer_1" data-name="Layer 1"><polygon points="127.3 0 42.43 0 42.43 42.43 0 42.43 0 127.3 84.87 127.3 84.87 84.87 127.3 84.87 127.3 0" fill="#fff"/><polygon points="0 42.43 0 84.87 42.43 84.87 42.43 127.3 84.87 127.3 84.87 42.43 0 42.43" fill="#00b2e3" opacity="0.4"/><rect y="84.87" width="42.43" height="42.43" fill="#1a82e2"/><polygon points="84.87 42.43 84.87 0 42.43 0 42.43 42.43 42.43 84.87 84.87 84.87 127.3 84.87 127.3 42.43 84.87 42.43" fill="#00b2e3" style="mix-blend-mode:multiply"/><rect x="84.87" width="42.43" height="42.43" fill="#1a82e2"/></g></g></svg>',
			];
		}

		if ( ! CarrierStore::has( 'slack_api' ) ) {
			$carriers['slack_api'] = [
				'name' => 'Slack',
				'pro'  => true,
				'link' => 'https://bracketspace.com/downloads/notification-slack/?utm_source=wp&utm_medium=notification-carriers&utm_id=upsell',
				'icon' => '<svg viewBox="0 0 124 124" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M26.3996 78.2C26.3996 85.3 20.5996 91.1001 13.4996 91.1001C6.39961 91.1001 0.599609 85.3 0.599609 78.2C0.599609 71.1 6.39961 65.3 13.4996 65.3H26.3996V78.2Z" fill="black"/><path d="M32.8994 78.2C32.8994 71.1 38.6994 65.3 45.7994 65.3C52.8994 65.3 58.6994 71.1 58.6994 78.2V110.5C58.6994 117.6 52.8994 123.4 45.7994 123.4C38.6994 123.4 32.8994 117.6 32.8994 110.5V78.2Z" fill="black"/><path d="M45.7994 26.4001C38.6994 26.4001 32.8994 20.6001 32.8994 13.5001C32.8994 6.4001 38.6994 0.600098 45.7994 0.600098C52.8994 0.600098 58.6994 6.4001 58.6994 13.5001V26.4001H45.7994Z" fill="black"/><path d="M45.7996 32.9001C52.8996 32.9001 58.6996 38.7001 58.6996 45.8001C58.6996 52.9001 52.8996 58.7001 45.7996 58.7001H13.4996C6.39961 58.7001 0.599609 52.9001 0.599609 45.8001C0.599609 38.7001 6.39961 32.9001 13.4996 32.9001H45.7996Z" fill="black"/><path d="M97.5996 45.8001C97.5996 38.7001 103.4 32.9001 110.5 32.9001C117.6 32.9001 123.4 38.7001 123.4 45.8001C123.4 52.9001 117.6 58.7001 110.5 58.7001H97.5996V45.8001Z" fill="black"/><path d="M91.0998 45.8001C91.0998 52.9001 85.2998 58.7001 78.1998 58.7001C71.0998 58.7001 65.2998 52.9001 65.2998 45.8001V13.5001C65.2998 6.4001 71.0998 0.600098 78.1998 0.600098C85.2998 0.600098 91.0998 6.4001 91.0998 13.5001V45.8001Z" fill="black"/><path d="M78.1998 97.6001C85.2998 97.6001 91.0998 103.4 91.0998 110.5C91.0998 117.6 85.2998 123.4 78.1998 123.4C71.0998 123.4 65.2998 117.6 65.2998 110.5V97.6001H78.1998Z" fill="black"/><path d="M78.1998 91.1001C71.0998 91.1001 65.2998 85.3 65.2998 78.2C65.2998 71.1 71.0998 65.3 78.1998 65.3H110.5C117.6 65.3 123.4 71.1 123.4 78.2C123.4 85.3 117.6 91.1001 110.5 91.1001H78.1998Z" fill="black"/></svg>',
			];
		}

		if ( ! CarrierStore::has( 'twilio/sms' ) ) {
			$carriers['twilio/sms'] = [
				'name' => 'Twilio - SMS',
				'pro'  => true,
				'link' => 'https://bracketspace.com/downloads/notification-twilio/?utm_source=wp&utm_medium=notification-carriers&utm_id=upsell',
				'icon' => '<svg fill="#0D122B" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"><path d="M15 0C6.7 0 0 6.7 0 15s6.7 15 15 15 15-6.7 15-15S23.3 0 15 0zm0 26C8.9 26 4 21.1 4 15S8.9 4 15 4s11 4.9 11 11-4.9 11-11 11zm6.8-14.7c0 1.7-1.4 3.1-3.1 3.1s-3.1-1.4-3.1-3.1 1.4-3.1 3.1-3.1 3.1 1.4 3.1 3.1zm0 7.4c0 1.7-1.4 3.1-3.1 3.1s-3.1-1.4-3.1-3.1c0-1.7 1.4-3.1 3.1-3.1s3.1 1.4 3.1 3.1zm-7.4 0c0 1.7-1.4 3.1-3.1 3.1s-3.1-1.4-3.1-3.1c0-1.7 1.4-3.1 3.1-3.1s3.1 1.4 3.1 3.1zm0-7.4c0 1.7-1.4 3.1-3.1 3.1S8.2 13 8.2 11.3s1.4-3.1 3.1-3.1 3.1 1.4 3.1 3.1z"/></svg>',
			];
		}

		return $carriers;
	}

}
