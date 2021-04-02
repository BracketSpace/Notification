/* global jQuery */
(function($) {
	const __ = wp.i18n.__;

	$(document).ready(function() {
		const $imageField = $(".notification-image-field");
		let $clickedImageField = false;

		$("body").on(
			"click",
			".notification-image-field .image .preview, .notification-image-field .select-image",
			function(event) {
				event.preventDefault();

				$clickedImageField = $(this).parents(
					".notification-image-field"
				);

				if (wp.media.frames.frame) {
					wp.media.frames.frame.open();
					return;
				}

				wp.media.frames.frame = wp.media({
					title: __("Select image", "notification"),
					multiple: false,
					library: {
						type: "image"
					},
					button: {
						text: __("Use selected image", "notification")
					}
				});

				const mediaSetImage = () => {
					const selection = wp.media.frames.frame
						.state()
						.get("selection");

					if (!selection) {
						return;
					}

					selection.each(function(attachment) {
						$clickedImageField.addClass("selected");
						$clickedImageField
							.find(".image-input")
							.val(attachment.id);
						$clickedImageField
							.find(".image .preview")
							.attr(
								"src",
								attachment.attributes.sizes.thumbnail.url
							);
					});
				};

				wp.media.frames.frame.on("select", mediaSetImage);
				wp.media.frames.frame.open();
			}
		);

		$imageField.find(".image .clear").on("click", function(event) {
			event.preventDefault();
			$(this)
				.parents(".notification-image-field")
				.removeClass("selected");
			$(this)
				.parents(".notification-image-field")
				.find(".image-input")
				.val("");
			$(this)
				.parents(".notification-image-field")
				.find(".image .preview")
				.attr("src", "");
		});
	});
})(jQuery);
