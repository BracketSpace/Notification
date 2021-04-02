/* global jQuery */

export default () => {
	jQuery(".fields-repeater-sortable > tbody").sortable({
		handle: ".handle",
		containment: "parent",
		axis: "y",
		start(e, ui) {
			ui.placeholder.height(ui.helper[0].scrollHeight);
		}
	});

	jQuery(".fields-repeater-nested-sortable").sortable({
		handle: ".sub-handle",
		connectWith: ".fields-repeater-nested-sortable",
		containment: "parent",
		items: "tr.row",
		placeholder: "tr.row",
		axis: "y",
		start(e, ui) {
			ui.placeholder.height(ui.helper[0].scrollHeight);
		}
	});
};
