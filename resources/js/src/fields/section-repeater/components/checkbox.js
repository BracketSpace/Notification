import Vue from "vue/dist/vue.js";
import { inputsHandler } from "../../repeater/mixins/inputsHandler";
import { fieldHandler } from "../../repeater/mixins/fieldHandler";
import { inputNameHandler } from "../mixins/inputNameHandler";

Vue.component("notification-checkbox", {
	template: `
	<div>
		<label>
			<input
			:id="subfield.id"
			:class="subfield.css_class"
			type="checkbox"
			:value="subfield.value"
			:checked="subfield.checked"
			:name="inputName"
			@click="checkboxHandler( subfield, $event )">
			{{ subfield.checkbox_label }}
		</label>
	</div>
	`,
	props: [
		"subfield",
		"rowIndex",
		"keyIndex",
		"type",
		"sectionName",
		"inputType",
		"parentField"
	],
	mixins: [inputsHandler, fieldHandler, inputNameHandler],
	computed: {
		inputName() {
			const baseFieldName = this.createFieldName(
				this.type,
				this.rowIndex,
				this.subfield
			);
			const fieldName = `[${this.parentFieldName}][${this.keyIndex}]`;
			if ("repeater" === this.inputType) {
				return `${baseFieldName}${fieldName.toLowerCase()}[${this.sectionName.toLowerCase()}][${this.subfield.name.toLowerCase()}]`;
			}
			return `${baseFieldName}${fieldName.toLowerCase()}[${this.subfield.name.toLowerCase()}]`;
		}
	}
});
