/* global jQuery, jQueryCollapse */

import "selectize";
import "jquery-collapse/src/jquery.collapse.js";

(function($) {
	$(document).ready(function() {
		$(".underdev-settings .pretty-select").selectize();
		new jQueryCollapse($(".underdev-settings .setting-group"), {
			open() {
				this.slideDown(100);
			},
			close() {
				this.slideUp(100);
			}
		});
		$(".setting-group-header").click(function() {
			const wrapper = $(this)
				.parents(".setting-group")
				.find(".form-table");
			wrapper.trigger("toggle");
		});
	});
})(jQuery);
