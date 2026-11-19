<?php

use BracketSpace\Notification\Repository\Field\EditorField;

it('renders the editor with the field value', function () {
	$field = new EditorField(['label' => 'Body', 'name' => 'body', 'value' => '<p>Hello there</p>']);
	$output = $field->field();

	expect($output)->not->toBeEmpty();
	expect($output)->toContain('Hello there');
});

it('uses default editor settings', function () {
	$field = new EditorField(['label' => 'Body', 'name' => 'body']);

	expect($field->field())->toContain('rows="20"');
});

it('respects the media_buttons setting', function () {
	$field = new EditorField([
		'label' => 'Body',
		'name' => 'body',
		'settings' => ['media_buttons' => false],
	]);

	expect($field->field())->not->toContain('insert-media');
});
