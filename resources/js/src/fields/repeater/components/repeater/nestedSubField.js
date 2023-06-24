import Vue from "vue/dist/vue.js";
import { fieldHandler } from "../../mixins/fieldHandler";
import { repeaterHandler } from "../../mixins/repeaterHandler";

Vue.component("nested-sub-field", {
	template: `<div class="nested-repeater-fields">
		<table class="fields-repeater-nested-sortable">
			<template v-for="( field, key ) in fields">
				<repeater-sub-row
				:field="field"
				:type="type"
				:row-index="rowIndex"
				:sub-name="subName"
				:key-index="key"
				:row="subRows"
				@sub-field-removed="removeSubField">
				</repeater-sub-row>
			</template>
		</table>
		<a href="#" class="button button-secondary add-new-repeater-field"
		@click="addNestedSubField( $event )"
		>Add sub field</a>
	</div>
	`,
	props: [
		"model",
		"nestedFields",
		"nestedValues",
		"subRows",
		"type",
		"rowIndex",
		"subName",
		"fieldName"
	],
	mixins: [fieldHandler, repeaterHandler]
});
