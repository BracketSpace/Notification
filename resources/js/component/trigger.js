/* global notification, jQuery */

import "selectize";

(function($) {
	$(document).ready(function() {
		$("#notification_trigger_select").selectize({
			render: {
				item(item) {
					return (
						"<div>" +
						item.text.replace(/\[\[(.*)\]\]/g, "") +
						"</div>"
					);
				},
				option(item) {
					return (
						"<div>" +
						item.text.replace(
							/(.*)\[\[(.*)\]\]/g,
							'<span class="label">$1</span><span class="caption">$2</span>'
						) +
						"</div>"
					);
				}
			}
		});

		$("#notification_trigger_select")
			.selectize()
			.change(function() {
				notification.hooks.doAction(
					"notification.trigger.changed",
					$(this)
				);
			});
	});
})(jQuery);
