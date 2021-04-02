/* global jQuery */
(function($) {
	$(document).ready(function() {
		let count = $("#notifications-wizard").data(
			"selected-notifications-count"
		)
			? $("#notifications-wizard").data("selected-notifications-count")
			: 0;

		$("#notifications-wizard .notifications-tile").on("click", function(e) {
			e.preventDefault();

			if ($(this).hasClass("selected")) {
				$(this).removeClass("selected");
				const checkbox = $(this)[0].querySelector("input");
				checkbox.checked = false;
				count = count - 1;
			} else {
				$(this).addClass("selected");
				const checkbox = $(this)[0].querySelector("input");
				checkbox.checked = true;
				count = count + 1;
			}

			$("#notifications-wizard").data(
				"selected-notifications-count",
				count
			);

			if (count > 0) {
				const text = wp.i18n.sprintf(
					wp.i18n._n(
						"Create %d notification",
						"Create %d notifications",
						count,
						"notification"
					),
					count
				);
				$(".create-notifications")
					.removeClass("hidden")
					.text(text);
				$(".skip-wizard")
					.removeClass("button")
					.removeClass("button-secondary")
					.addClass("as-link");
			} else {
				$(".create-notifications").addClass("hidden");
				$(".skip-wizard")
					.addClass("button")
					.addClass("button-secondary")
					.removeClass("as-link");
			}
		});
	});
})(jQuery);
