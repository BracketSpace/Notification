/* global notification, jQuery, FormData */
(function($) {
	const __ = wp.i18n.__;

	$(document).ready(function() {
		const $button = $("#export-notifications .button");
		const $items = $(
			'#export-notifications ul li input[type="checkbox"]:not(.select-all)'
		);
		const link = $button.prop("href");

		function getSelectedItems() {
			const items = [];
			$.each($items, function(index, item) {
				const $item = $(item);
				if ($item.is(":checked")) {
					items.push($item.val());
				}
			});
			return items.join();
		}

		$('#export-notifications input[type="checkbox"]').change(function() {
			if ($(this).hasClass("select-all")) {
				if ($(this).is(":checked")) {
					$items.prop("checked", true);
				} else {
					$items.prop("checked", false);
				}
			}

			$button.prop("href", link + getSelectedItems());
		});
	});

	$(document).ready(function() {
		const $button = $("#import-notifications .button");
		const $file = $('#import-notifications input[type="file"]');
		let files = [];
		const $message = $("#import-notifications .message");

		function clearMessage() {
			$message
				.removeClass("success")
				.removeClass("error")
				.text("");
		}

		function addMessage(type, message) {
			clearMessage();
			$message.addClass(type).text(message);
		}

		$file.on("change", function(event) {
			files = event.target.files;
			$.each(files, function(key, value) {
				if ("application/json" !== value.type) {
					addMessage(
						"error",
						__(
							"Please upload only valid JSON files",
							"notification"
						)
					);
					$file.val("");
				} else {
					clearMessage();
				}
			});
		});

		$button.on("click", function(event) {
			if ("true" === $button.data("processing")) {
				return false;
			}

			event.preventDefault();

			const data = new FormData();

			$.each(files, function(key, value) {
				data.append(key, value);
			});

			data.append("action", "notification_import_json");
			data.append("type", "notifications");
			data.append("nonce", $button.data("nonce"));

			addMessage("neutral", __("Importing data...", "notification"));
			$button.data("processing", "true");

			$.ajax({
				url: notification.ajaxurl,
				type: "POST",
				data,
				cache: false,
				dataType: "json",
				processData: false,
				contentType: false,
				success(response) {
					if (response.success) {
						addMessage("success", response.data);
						$file.val("");
					} else {
						addMessage("error", response.data);
					}
					$button.data("processing", "false");
				},
				error(jqXHR, textStatus, errorThrown) {
					addMessage("error", errorThrown);
				}
			});
		});
	});
})(jQuery);
