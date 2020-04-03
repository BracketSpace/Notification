/* eslint no-alert: 0 */
/* global jQuery, confirm */
(function($) {
	const __ = wp.i18n.__;

	$(document).ready(function() {
		$(".notification-delete-post").click(function(e) {
			if (
				!confirm(
					__(
						"Are you sure you want to permanently delete this notification?",
						"notification"
					)
				)
			) {
				e.preventDefault();
			}
		});
	});
})(jQuery);
