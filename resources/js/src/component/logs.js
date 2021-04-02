/* global jQuery */
(function($) {
	$(document).ready(function() {
		$(".log-container .log-item .log-handle").on("click", function(event) {
			event.preventDefault();
			$(this)
				.parent()
				.toggleClass("expanded");
			$(this)
				.find(".indicator")
				.toggleClass("dashicons-arrow-down dashicons-arrow-up");
		});
	});
})(jQuery);

(function($) {
	$(document).ready(function() {
		function toggleSuppressingSetting() {
			const $log = $(
				"#notification-setting-debugging-settings-debug_log"
			);
			const $suppressing = $(
				".notification-settings .field-debug_suppressing"
			);

			if ($log.is(":checked")) {
				$suppressing.show();
			} else {
				$suppressing.hide();
			}
		}

		toggleSuppressingSetting();

		$("#notification-setting-debugging-settings-debug_log").change(
			toggleSuppressingSetting
		);
	});
})(jQuery);
