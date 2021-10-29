/* eslint no-alert: 0 */
/* global notification, jQuery, alert */
(function($) {
	const __ = wp.i18n.__;

	$(document).ready(function() {
		$(".group-sync .field-actions .notification-sync-all").on(
			"click",
			function(event) {
				event.preventDefault();

				const $masterButton = $(this);

				if ($masterButton.attr("disabled")) {
					return false;
				}

				$masterButton.attr("disabled", true);

				$(".group-sync .field-notifications tr").each(function(
					num,
					notificationRow
				) {
					const $button = $(notificationRow).find(
						".button.notification-sync"
					);

					if (
						$button.data("sync-type") === $masterButton.data("type")
					) {
						notification.hooks.doAction(
							"notification.sync.init",
							$button
						);
					}
				});
			}
		);

		$(".group-sync .field-notifications td > .button.notification-sync").on(
			"click",
			function(event) {
				event.preventDefault();
				notification.hooks.doAction("notification.sync.init", $(this));
			}
		);

		notification.hooks.addAction("notification.sync.init", function(
			$button
		) {
			if ($button.attr("disabled")) {
				return false;
			}

			const syncType = $button.data("sync-type"),
				hash = $button.data("sync-hash"),
				nonce = $button.data("nonce");

			$button.attr("disabled", true);
			$button.text(__("Synchronizing...", "notification"));

			const data = {
				action: "notification_sync",
				_ajax_nonce: notification.csrfToken,
				hash,
				type: syncType,
				nonce
			};

			$.post(notification.ajaxurl, data, function(response) {
				if (response.success === true) {
					const $notificationRow = $button.parent().parent();

					if ("wordpress" === syncType) {
						const $titleTd = $notificationRow.find("td.title");
						const $link = $("<a>", {
							text: $titleTd.text(),
							href: response.data
						});
						$titleTd.html($link);
					}

					$notificationRow
						.find("td.status")
						.text(__("Synchronized", "notification"));
					$button.remove();
				} else {
					alert(response.data);
				}

				$button.attr("disabled", false);
			});
		});
	});
})(jQuery);
