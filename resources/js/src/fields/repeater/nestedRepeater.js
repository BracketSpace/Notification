import Vue from "vue/dist/vue.js";
import { init } from "./mixins/init";
import { fieldHandler } from "./mixins/fieldHandler";
import { inputsHandler } from "./mixins/inputsHandler";
import { sectionsModal } from "../section-repeater/mixins/sectionsModal";

document.addEventListener("DOMContentLoaded", () => {
	const vueWrappers = document.querySelectorAll(".vue-repeater");
	const vueInstances = {};

	for (const wrapper of vueWrappers) {
		const wrapperId = wrapper.getAttribute("id");

		vueInstances[wrapperId] = new Vue({
			el: `#${wrapperId}`,
			mixins: [init, fieldHandler, inputsHandler, sectionsModal],
			data: {
				model: [],
				nestedModel: [],
				type: {},
				fields: [],
				nestedFields: [],
				rowCount: 0,
				nestedRowCount: 0,
				values: [],
				nestedValues: [],
				postID: "",
				nestedRepeater: false,
				repeaterError: false
			}
		});
	}
});
