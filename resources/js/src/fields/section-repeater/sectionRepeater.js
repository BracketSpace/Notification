import Vue from "vue/dist/vue.js";
import { init } from "./mixins/init";
import { sectionsModal } from "./mixins/sectionsModal";
import { sectionsHandler } from "./mixins/sectionsHandler";

document.addEventListener("DOMContentLoaded", () => {
	const sectionWrappers = document.querySelectorAll(".vue-section-repeater");
	const sectionRepeaters = {};

	for (const wrapper of sectionWrappers) {
		const wrapperId = wrapper.getAttribute("id");

		sectionRepeaters[wrapperId] = new Vue({
			el: `#${wrapperId}`,
			mixins: [init, sectionsModal, sectionsHandler],
			data: {
				type: {},
				sections: {},
				rows: {},
				rowCount: 0,
				selectedSection: null,
				savedSections: [],
				values: {},
				subFieldValues: [],
				baseFields: {},
				repeaterError: false
			}
		});
	}
});
