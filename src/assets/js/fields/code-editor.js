/* global _, notification, jQuery */

(function($) {
	function initCodeEditor($elem) {
		const editorSettings = wp.codeEditor.defaultSettings
			? _.clone(wp.codeEditor.defaultSettings)
			: {};
		editorSettings.codemirror = _.extend(
			{},
			editorSettings.codemirror,
			$elem.data("settings")
		);
		let editor = wp.codeEditor.initialize($elem, editorSettings); // eslint-disable-line prefer-const

		notification.hooks.addAction("notification.carrier.toggled", () => {
			editor.codemirror.refresh();
		});
	}

	$(document).ready(function() {
		$(".notification-code-editor-field").each(function() {
			initCodeEditor($(this));
		});
	});
})(jQuery);
