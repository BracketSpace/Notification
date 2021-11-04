import Vue from "vue/dist/vue.js";
import { fieldHandler } from "../../mixins/fieldHandler";
import { inputsHandler } from "../../mixins/inputsHandler";

Vue.component("repeater-row", {
	template: `
		<tr class="row">
			<td class="handle"><span class="handle-index">{{keyIndex + 1}}</span></td>
			<template v-for="( subfield, index ) in field">
				<td :class="'subfield ' + subfield.name">
					<div class="row-field">
						<label
							v-if="subfield.checkbox_label"
						>
						<input
						:id="subfield.id"
						:class="subfield.css_class"
						type="checkbox"
						:value="subfield.value"
						:checked="subfield.checked"
						:name="createFieldName(type, keyIndex, subfield) + '[' + subfield.name + ']'"
						@click="checkboxHandler( field[index], $event )">
						{{ subfield.checkbox_label }}
						</label>
						<template
						v-else-if="subfield.options"
						>
							<notification-select
							:subfield="subfield"
							:type="type"
							:field="field"
							:key-index="keyIndex"
							>
							</notification-select>
						</template>
						<template v-else-if="subfield.rows">
							<textarea
							:id="subfield.id"
							:class="subfield.css_class"
							:value="subfield.value"
							:rows="subfield.rows || 20"
							:name="createFieldName(type, keyIndex, subfield) + '[' + subfield.name + ']'"
							:placeholder="subfield.placeholder"
							></textarea>
						</template>
						<template
						v-else-if="subfield.type === 'repeater'"
						>
						<nested-sub-field
						:model="nestedModel"
						:nested-fields="nestedFields"
						:nested-values="nestedValues"
						:sub-rows="subRows"
						:row-index="keyIndex"
						:sub-name="subfield.name"
						:type="type"
						@add-nested-field="addSubField">
						</nested-sub-field>
						</template>
						<input
						v-else
						:id="subfield.id"
						:class="subfield.css_class"
						type="text"
						:value="subfield.value"
						:name="createFieldName(type, keyIndex, subfield) + '[' + subfield.name + ']'"
						:placeholder="subfield.placeholder"
						>
						<small
							v-if="field.description"
						class="description">
							{{ field.description }}
						</small>
					</div>
				</td>
			</template>
			<td class="trash" @click="removeField(keyIndex, fields)"></td>
		</tr>
	`,
	props: [
		"field",
		"keyIndex",
		"fields",
		"type",
		"nestedFields",
		"nestedValues",
		"nestedModel",
		"nestedRowCount"
	],
	mixins: [fieldHandler, inputsHandler],
	data() {
		return {
			subRows: 0,
			rowName: ""
		};
	},
	methods: {
		addSubField() {
			this.subRows++;
		}
	}
});
