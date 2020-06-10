/* global Vue */

import { inputsHandler } from "../../repeater/mixins/inputsHandler";
import { fieldHandler } from "../../repeater/mixins/fieldHandler";
import { inputNameHandler } from "../mixins/inputNameHandler";

Vue.component("notification-text", {
	template: `
	<div>
		<label>
			{{ subfield.label }}
		</label>
		<input
			:id="subfield.id"
			:class="subfield.css_class"
			type="text"
			:value="value"
			:name="inputName"
			:placeholder="subfield.placeholder"
			:row-index="rowIndex"
			>
		<small
			v-if="subfield.description"
		class="description">
			{{ subfield.description }}
		</small>
	</div>
	`,
	props: [
		"subfield",
		"rowIndex",
		"keyIndex",
		"type",
		"sectionName",
		"inputType",
		"parentField",
		"values",
		"multiple"
	],
	mixins: [inputsHandler, fieldHandler, inputNameHandler],
	data() {
		return {
			value: null
		};
	},
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
	},
	mounted() {
		if (this.multiple && this.values[this.rowIndex][this.keyIndex]) {
			this.value = this.values[this.rowIndex][this.keyIndex].field[
				this.subfield.name.toLowerCase()
			];
		} else {
			this.value = Object.freeze(this.subfield.value);
		}
	}
});
