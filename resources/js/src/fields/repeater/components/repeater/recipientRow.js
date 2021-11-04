import Vue from "vue/dist/vue.js";
import { fieldHandler } from "../../mixins/fieldHandler";
import { inputsHandler } from "../../mixins/inputsHandler";

Vue.component("recipient-row", {
	template: `
	<tr class="row">
		<td class="handle"><span class="handle-index">{{keyIndex + 1}}</span></td>
		<template v-for="( subfield, index ) in field">
			<td :class="'subfield ' + subfield.name">
				<div class="row-field">
					<template
						v-if="subfield.options"
					>
						<notification-select
						:subfield="subfield"
						:type="type"
						:field="field"
						:key-index="keyIndex"
						>
						</notification-select>
					</template>
					<input
					v-else
					:id="subfield.id"
					:class="subfield.css_class"
					type="text"
					:value="subfield.value"
					:disabled="subfield.disabled"
					:name="createFieldName(type, keyIndex, subfield) + '[' + subfield.name + ']'"
					:placeholder="subfield.placeholder"
					>
					<small class="description"
						v-if="subfield.description"
						v-html="subfield.description">
						{{ subfield.description }}
					</small>
				</div>
			</td>
		</template>
		<td class="trash" @click="removeField(keyIndex, fields)"></td>
	</tr>
	`,
	props: ["field", "keyIndex", "fields", "type"],
	mixins: [fieldHandler, inputsHandler]
});
