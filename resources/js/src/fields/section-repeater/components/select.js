/* global jQuery, notification */
import Vue from "vue/dist/vue.js";
import { inputsHandler } from "../../repeater/mixins/inputsHandler";
import { fieldHandler } from "../../repeater/mixins/fieldHandler";
import { inputNameHandler } from "../mixins/inputNameHandler";

Vue.component("notification-section-select", {
	template: `<select
		:id="subfield.id"
		:name="inputName"
		:class="subfield.css_class + ' ' + subfield.pretty + ' repeater-select'"
		@change="selectUpdate( subfield, field, $event )"
	>
		<template v-for="( option, key ) in subfield.options">
			<option :value="key" :selected="handleSelect( key, subfield.value )">{{option}}</option>
		</template>
	</select>
	`,
	props: [
		"field",
		"type",
		"keyIndex",
		"subfield",
		"sectionName",
		"inputType",
		"parentField"
	],
	mixins: [inputsHandler, fieldHandler, inputNameHandler],
	data() {
		return {
			selectized: null
		};
	},
	mounted() {
		this.initSelectize();
	},
	beforeUpdate() {
		this.destroySelectize();
	},
	updated() {
		if (this.subfield.value) {
			this.$el.value = this.subfield.value;
		}

		this.initSelectize();
		notification.hooks.doAction(
			"notification.carrier.select.changed",
			this
		);
	},
	beforeDestroy() {
		this.destroySelectize();
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
	methods: {
		selectUpdate(subfield, field, $event) {
			if (field) {
				this.selectChange(subfield, field, $event);
			}
		},
		destroySelectize() {
			if (this.selectized) {
				const control = this.selectized[0].selectize;
				control.destroy();
			}
		},
		initSelectize() {
			if (this.$el.classList.contains("notification-pretty-select")) {
				this.selectized = jQuery(this.$el).selectize();
			}
		}
	}
});
