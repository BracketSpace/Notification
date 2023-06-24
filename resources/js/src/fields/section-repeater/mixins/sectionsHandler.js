/* global notification */

import Vue from "vue/dist/vue.js";

export const sectionsHandler = {
	data() {
		return {
			subRows: 0,
			rowName: "",
			secitioName: null
		};
	},
	mounted() {
		this.sectionName = Object.freeze(this.selectedSection);
	},
	methods: {
		addFieldSection(section) {
			const sectionFields = {};

			for (const field in section) {
				const fieldModel = Object.assign({}, section[field]);

				if ("message" === section.type) {
					this.rows[`section-${this.rowCount}`] = fieldModel;
				} else {
					sectionFields[section[field].name] = fieldModel;
				}
			}

			this.rows[`section-${this.rowCount}`] = sectionFields;

			this.rowCount++;

			notification.hooks.doAction("notification.section.row.added", this);
		},
		addSubFieldSection(name, value) {
			const fieldModel = Object.assign(
				{},
				this.baseFields[name.toLowerCase()]
			);

			this.selectedSection = fieldModel.label || fieldModel.name;

			const fieldData = [];

			if (value) {
				for (const data in value) {
					if (typeof value === "string" || value instanceof String) {
						fieldData.push(value);
					} else {
						fieldData.push(value[data]);
					}
				}
			}

			if (!fieldModel.fields) {
				fieldModel.value = fieldData.join("");
			} else {
				let counter = 0;

				for (const field in fieldModel.fields) {
					fieldModel.fields[field].value = fieldData[counter];
					counter++;
				}
			}

			this.rows.push(fieldModel);

			this.rowCount++;

			notification.hooks.doAction("notification.section.row.added", this);
		},
		addFieldSectionValues() {
			let sectionIndex = 0;

			this.values.forEach(value => {
				const sections = this.sections;

				for (const key in value) {
					const section = sections[key];

					this.savedSections.push(section.name);
					this.addFieldSection(section.fields);

					if (Array.isArray(value[key])) {
						this.subFieldValues[sectionIndex] = [];

						value[key].forEach(field => {
							this.subFieldValues[sectionIndex].push(field);
						});
						sectionIndex++;
					} else {
						this.subFieldValues.push(undefined);
						sectionIndex++;
					}
				}
			});
		},
		addSubField() {
			this.subRows++;
		},
		removeSection(index) {
			const keys = Object.keys(this.rows);

			Vue.delete(this.rows, keys[index]);
			Vue.delete(this.$parent.savedSections, index);
			Vue.delete(this.$parent.values, index);
			Vue.delete(this.$parent.subFieldValues, index);
		},
		createParentSectionName(type, index) {
			return (this.rowName = `notification_carrier_${type.fieldCarrier}[${type.fieldType}][${index}]`);
		}
	}
};
