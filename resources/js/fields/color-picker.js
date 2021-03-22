/* global notification, jQuery */
(function($) {
	$(document).ready(function() {
		$(".notification-color-picker:visible").wpColorPicker();
	});

	notification.hooks.addAction(
		"notification.carrier.repeater.row.added",
		function(repeater) {
			const colorPickers = repeater.$el.querySelectorAll(
				".notification-color-picker"
			);

			colorPickers.forEach(colorPicker => {
				colorPicker.wpColorPicker();
			});
		}
	);
})(jQuery);
