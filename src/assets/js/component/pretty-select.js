/* global jQuery */

import "selectize";

(function($) {
	$(document).ready(function() {
		$(
			".notification-pretty-select:visible:not( repeater-select )"
		).selectize();
	});
})(jQuery);
