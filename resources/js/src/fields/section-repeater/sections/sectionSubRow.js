import Vue from "vue/dist/vue.js";
import { fieldHandler } from "../../repeater/mixins/fieldHandler";
import { inputsHandler } from "../../repeater/mixins/inputsHandler";

Vue.component("section-sub-row", {
	template: `<table class="row">
		<tr>
			<td class="section-name-field">{{sectionName}}</td>
			<td class="section-content">
				<template v-if="inputType === 'textarea'">
					<notification-textarea
						:subfield="row"
						:type="type"
						:key-index="keyIndex"
						:row-index="rowIndex"
						:parent-field="parentField"
						:input-type="inputType"
						:section-name="sectionName"
					/>
				</template>
				<template v-else-if="inputType === 'select'">
					<notification-section-select
					:subfield="row"
					:type="type"
					:key-index="keyIndex"
					:row-index="rowIndex"
					:parent-field="parentField"
					:input-type="inputType"
					:section-name="sectionName"
					/>
				</template>
				<template v-else-if="inputType === 'input'">
					<notification-text
						:subfield="row"
						:type="type"
						:key-index="keyIndex"
						:row-index="rowIndex"
						:parent-field="parentField"
						:input-type="inputType"
						:section-name="sectionName"
						:values="values"
					/>
				</template>
				<template v-else-if="inputType === 'checkbox'">
					<notification-checkbox
						:subfield="row"
						:type="type"
						:key-index="keyIndex"
						:row-index="rowIndex"
						:parent-field="parentField"
						:input-type="inputType"
						:section-name="sectionName"
					/>
				</template>
				<template v-else>
					<template v-for="(field, index) in row.fields">
						<notification-text
						v-if="sectionName"
						:subfield="field"
						:type="type"
						:key-index="keyIndex"
						:row-index="rowIndex"
						:parent-field="parentField"
						:input-type="inputType"
						:section-name="sectionName"
						:values="values"
						:multiple="row.multiple"
						/>
					</template>
				</template>
			</td>
			<td class="trash" @click="removeSubfield(keyIndex)"></td>
		</tr>
	</table>`,
	props: [
		"row",
		"keyIndex",
		"rowIndex",
		"selectedSection",
		"type",
		"parentField",
		"baseFields",
		"values"
	],
	mixins: [fieldHandler, inputsHandler],
	data() {
		return {
			sectionName: null,
			inputType: null
		};
	},
	mounted() {
		this.setInputData();
	},
	updated() {
		this.setInputData();
	},
	methods: {
		removeSubfield(index) {
			this.$emit("sub-field-removed", index);
		},
		setInputData() {
			const input = Object.freeze(this.row);

			this.sectionName = input.name || input.label;

			if (input.type) {
				this.inputType = input.type;
			} else {
				this.inputType = "repeater";
			}
		}
	}
});
