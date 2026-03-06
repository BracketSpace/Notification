<?php

use BracketSpace\Notification\Admin\ImportExport;

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
function callImportExportAjaxHandler(callable $handler): array
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

it('rejects import with invalid type', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	$_REQUEST['nonce'] = wp_create_nonce('import-notifications');
	$_POST['nonce'] = $_REQUEST['nonce'];
	$_POST['type'] = 'evil';

	// Create a valid temp file to pass the file checks
	$tmpFile = tempnam(sys_get_temp_dir(), 'test');
	file_put_contents($tmpFile, '[]');
	$_FILES = [
		'notification_import_file' => [
			'tmp_name' => $tmpFile,
			'error' => UPLOAD_ERR_OK,
			'size' => 2,
			'type' => 'application/json',
			'name' => 'test.json',
		],
	];

	$importExport = new ImportExport();
	$response = callImportExportAjaxHandler([$importExport, 'importRequest']);

	expect($response['success'])->toBeFalse();

	if (file_exists($tmpFile)) {
		unlink($tmpFile);
	}
	$_FILES = [];
});

it('rejects upload with file error', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	$_REQUEST['nonce'] = wp_create_nonce('import-notifications');
	$_POST['nonce'] = $_REQUEST['nonce'];
	$_POST['type'] = 'notifications';
	$_FILES = [
		'notification_import_file' => [
			'tmp_name' => '/tmp/nonexistent',
			'error' => UPLOAD_ERR_INI_SIZE,
			'size' => 999999,
			'type' => 'application/json',
			'name' => 'test.json',
		],
	];

	$importExport = new ImportExport();
	$response = callImportExportAjaxHandler([$importExport, 'importRequest']);

	expect($response['success'])->toBeFalse();

	$_FILES = [];
});

it('rejects upload with invalid mime type', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	$_REQUEST['nonce'] = wp_create_nonce('import-notifications');
	$_POST['nonce'] = $_REQUEST['nonce'];
	$_POST['type'] = 'notifications';

	// Create temp file with PHP content
	$tmpFile = tempnam(sys_get_temp_dir(), 'test');
	file_put_contents($tmpFile, '<?php echo "malicious"; ?>');
	$_FILES = [
		'notification_import_file' => [
			'tmp_name' => $tmpFile,
			'error' => UPLOAD_ERR_OK,
			'size' => filesize($tmpFile),
			'type' => 'application/x-php',
			'name' => 'evil.php',
		],
	];

	$importExport = new ImportExport();
	$response = callImportExportAjaxHandler([$importExport, 'importRequest']);

	expect($response['success'])->toBeFalse();

	if (file_exists($tmpFile)) {
		unlink($tmpFile);
	}
	$_FILES = [];
});

it('rejects upload exceeding max file size', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	$_REQUEST['nonce'] = wp_create_nonce('import-notifications');
	$_POST['nonce'] = $_REQUEST['nonce'];
	$_POST['type'] = 'notifications';

	$tmpFile = tempnam(sys_get_temp_dir(), 'test');
	file_put_contents($tmpFile, '[]');
	$_FILES = [
		'notification_import_file' => [
			'tmp_name' => $tmpFile,
			'error' => UPLOAD_ERR_OK,
			'size' => 2 * 1024 * 1024, // 2MB, exceeds 1MB limit
			'type' => 'application/json',
			'name' => 'large.json',
		],
	];

	$importExport = new ImportExport();
	$response = callImportExportAjaxHandler([$importExport, 'importRequest']);

	expect($response['success'])->toBeFalse();

	if (file_exists($tmpFile)) {
		unlink($tmpFile);
	}
	$_FILES = [];
});

it('rejects export with invalid type', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	$_REQUEST['nonce'] = wp_create_nonce('notification-export');
	$_GET['nonce'] = $_REQUEST['nonce'];
	$_GET['_wpnonce'] = $_REQUEST['nonce'];
	$_REQUEST['_wpnonce'] = $_REQUEST['nonce'];
	$_GET['type'] = 'evil';

	$importExport = new ImportExport();

	$dieHandler = function () {
		return static function ($message) {
			throw new \WPDieException($message);
		};
	};
	add_filter('wp_die_ajax_handler', $dieHandler);
	add_filter('wp_die_handler', $dieHandler);

	$died = false;
	$message = '';
	ob_start();
	try {
		$importExport->exportRequest();
	} catch (\WPDieException $e) {
		$died = true;
		$message = $e->getMessage();
	}
	ob_get_clean();

	remove_all_filters('wp_die_ajax_handler');
	remove_all_filters('wp_die_handler');

	expect($died)->toBeTrue();
	expect($message)->toContain('Invalid export type');
});

it('accepts export with valid notifications type', function () {
	$adminId = $this->factory()->user->create(['role' => 'administrator']);
	$admin = get_userdata($adminId);
	$admin->add_cap('manage_options');
	wp_set_current_user($adminId);

	$_REQUEST['nonce'] = wp_create_nonce('notification-export');
	$_GET['nonce'] = $_REQUEST['nonce'];
	$_GET['_wpnonce'] = $_REQUEST['nonce'];
	$_REQUEST['_wpnonce'] = $_REQUEST['nonce'];
	$_GET['type'] = 'notifications';
	// Don't set items, so prepareNotificationsExportData throws "No items selected"

	$importExport = new ImportExport();

	$dieHandler = function () {
		return static function ($message) {
			throw new \WPDieException($message);
		};
	};
	add_filter('wp_die_ajax_handler', $dieHandler);
	add_filter('wp_die_handler', $dieHandler);

	$diedWithTypeError = false;
	ob_start();
	try {
		$importExport->exportRequest();
	} catch (\WPDieException $e) {
		if (strpos($e->getMessage(), 'Invalid export type') !== false) {
			$diedWithTypeError = true;
		}
	} catch (\Throwable $e) {
		// "headers already sent" means it passed the type check — expected
	}
	ob_get_clean();

	remove_all_filters('wp_die_ajax_handler');
	remove_all_filters('wp_die_handler');

	expect($diedWithTypeError)->toBeFalse();
});
