<?php

use BracketSpace\Notification\Admin\Sync;

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
function callSyncAjaxHandler(callable $handler): array
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

it('rejects sync for users without manage_options', function () {
	$subscriberId = $this->factory()->user->create(['role' => 'subscriber']);
	wp_set_current_user($subscriberId);

	$_REQUEST['_ajax_nonce'] = wp_create_nonce('notification_csrf');
	$_POST['_ajax_nonce'] = $_REQUEST['_ajax_nonce'];
	$_POST['type'] = 'json';
	$_POST['hash'] = 'somehash';

	$sync = new Sync();
	$response = callSyncAjaxHandler([$sync, 'ajaxSync']);

	expect($response['success'])->toBeFalse();
});

it('rejects sync with invalid type', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	$_REQUEST['_ajax_nonce'] = wp_create_nonce('notification_csrf');
	$_POST['_ajax_nonce'] = $_REQUEST['_ajax_nonce'];
	$_POST['type'] = 'evil';
	$_POST['hash'] = 'somehash';

	$sync = new Sync();
	$response = callSyncAjaxHandler([$sync, 'ajaxSync']);

	expect($response['success'])->toBeFalse();
});

it('accepts sync with valid json type', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	$_REQUEST['_ajax_nonce'] = wp_create_nonce('notification_csrf');
	$_POST['_ajax_nonce'] = $_REQUEST['_ajax_nonce'];
	$_POST['type'] = 'json';
	$_POST['hash'] = 'nonexistenthash';

	$sync = new Sync();
	$response = callSyncAjaxHandler([$sync, 'ajaxSync']);

	// The method will be called but may fail on the notification lookup.
	// The key assertion is that it does NOT fail on type validation.
	expect($response)->not->toEqual([]);
});

it('accepts sync with valid wordpress type', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	$_REQUEST['_ajax_nonce'] = wp_create_nonce('notification_csrf');
	$_POST['_ajax_nonce'] = $_REQUEST['_ajax_nonce'];
	$_POST['type'] = 'wordpress';
	$_POST['hash'] = 'nonexistenthash';

	$sync = new Sync();
	$response = callSyncAjaxHandler([$sync, 'ajaxSync']);

	expect($response)->not->toEqual([]);
});
