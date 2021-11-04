import Vue from "vue/dist/vue.js";
import { fieldHandler } from "../../repeater/mixins/fieldHandler";
import { sectionsHandler } from "../mixins/sectionsHandler";

Vue.component("sections-row", {
	template: `
		<tr class="row">
			<td class="handle no-sortable">{{index + 1}}</td>
			<td>
				<label class="section-label">
					{{ sectionName }}
					<input
					:id="row.id"
					:class="row.css_class"
					type="hidden"
					:value="sectionName"
					:name="createParentSectionName( type, index ) + '[' + fieldName + ']'"
					>
					<small
						v-if="row.description"
					class="description"></small>
				</label>
			</td>
			<template v-if=" 'message' in row ">
				<td v-html="row.message.message"></td>
			</template>
			<template v-else>
				<nested-sub-section
					:row="row"
					:type="type"
					:row-index="index"
					:parent-field="sectionName"
					:sub-field-values="subFieldValues"
					:base-fields="baseFields"
				>
				</nested-sub-section>
			</template>
			<td class="trash" @click="removeSection(index)"></td>
		</tr>
	`,
	props: [
		"index",
		"rows",
		"row",
		"rowCount",
		"type",
		"selectedSection",
		"subFieldValues",
		"baseFields",
		"savedSections"
	],
	mixins: [fieldHandler, sectionsHandler],
	data() {
		return {
			sectionName: null
		};
	},
	computed: {
		fieldName() {
			if (this.sectionName) {
				return this.sectionName.toLowerCase();
			}
		}
	},
	mounted() {
		if (this.selectedSection) {
			this.sectionName = Object.freeze(this.selectedSection);
		} else {
			this.sectionName = this.savedSections[this.index];
		}
	},
	updated() {
		this.sectionName = this.savedSections[this.index];
	}
});
