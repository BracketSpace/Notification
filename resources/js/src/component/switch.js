/* eslint no-alert: 0 */
/* global notification, jQuery, alert */
(function($) {
	$(document).ready(function() {
		$(".column-switch .onoffswitch").on("click", function(event) {
			const $switch = $(this),
				postId = $switch.data("postid");

			event.preventDefault();

			notification.hooks.doAction(
				"notification.status.changed",
				$switch,
				postId
			);
		});

		notification.hooks.addAction("notification.status.changed", function(
			$switch,
			postId
		) {
			const status = !$switch.find("input").attr("checked");

			$switch.addClass("loading");

			const data = {
				action: "change_notification_status",
				_ajax_nonce: notification.csrfToken,
				post_id: postId,
				status,
				nonce: $switch.data("nonce")
			};

			$.post(notification.ajaxurl, data, function(response) {
				if (response.success === true) {
					$switch.removeClass("loading");
					$switch.find("input").attr("checked", status);
				} else {
					alert(response.data);
				}
			});
		});
	});
})(jQuery);
