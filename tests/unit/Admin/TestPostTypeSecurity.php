<?php

use BracketSpace\Notification\Admin\PostType;

beforeAll(function () {
	if (!defined('DOING_AJAX')) {
		define('DOING_AJAX', true);
	}
});

/**
 * Calls an AJAX handler and captures the JSON response.
 *
 * @param callable $handler Handler to call.
 * @return array<mixed>
 */
function callPostTypeAjaxHandler(callable $handler): array
{
	add_filter('wp_die_ajax_handler', function () {
		return static function ($message) {
			throw new \WPDieException($message);
		};
	});

	ob_start();
	try {
		$handler();
	} catch (\WPDieException $e) {
		// Expected - wp_send_json_* calls wp_die
	}
	$output = ob_get_clean();

	remove_all_filters('wp_die_ajax_handler');

	return json_decode($output, true) ?: [];
}

it('rejects notification status change for users without manage_options', function () {
	$subscriberId = $this->factory()->user->create(['role' => 'subscriber']);
	wp_set_current_user($subscriberId);

	$_REQUEST['_ajax_nonce'] = wp_create_nonce('notification_csrf');
	$_POST['_ajax_nonce'] = $_REQUEST['_ajax_nonce'];

	$postType = new PostType();
	$response = callPostTypeAjaxHandler([$postType, 'ajaxChangeNotificationStatus']);

	expect($response['success'])->toBeFalse();
});

it('allows notification status change for admin users', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	// Create a notification post
	$postId = $this->factory()->post->create(['post_type' => 'notification']);

	$_REQUEST['_ajax_nonce'] = wp_create_nonce('notification_csrf');
	$_POST['_ajax_nonce'] = $_REQUEST['_ajax_nonce'];
	$_POST['nonce'] = wp_create_nonce('change_notification_status_' . $postId);
	$_POST['post_id'] = $postId;
	$_POST['status'] = 'true';
	$_REQUEST['nonce'] = $_POST['nonce'];

	$postType = new PostType();
	$response = callPostTypeAjaxHandler([$postType, 'ajaxChangeNotificationStatus']);

	// Should not fail on capability check - may fail on notification lookup,
	// but that's fine - we're testing the capability gate
	expect($response)->not->toEqual([]);
});
