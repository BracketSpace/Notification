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
