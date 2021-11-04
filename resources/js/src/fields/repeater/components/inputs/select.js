/* global jQuery, notification */
import Vue from "vue/dist/vue.js";
import { inputsHandler } from "../../mixins/inputsHandler";
import { fieldHandler } from "../../mixins/fieldHandler";

Vue.component("notification-select", {
	template: `<select
		:id="subfield.id"
		:name="createFieldName(type, keyIndex, subfield) + '[' + subfield.name + ']'"
		:class="subfield.css_class + ' ' + subfield.pretty + ' repeater-select'"
		@change="selectUpdate( subfield, field, $event )"
	>
		<template v-for="( option, key ) in subfield.options">
			<option :value="key" :selected="handleSelect( key, subfield.value )">{{option}}</option>
		</template>
	</select>
	`,
	props: ["field", "type", "keyIndex", "subfield"],
	mixins: [inputsHandler, fieldHandler],
	data() {
		return {
			selectized: null
		};
	},
	mounted() {
		this.initSelectize();
		notification.hooks.doAction(
			"notification.carrier.select.initialized",
			this
		);
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
