export const repeaterHandler = {
	data() {
		return {
			fields: [],
			values: this.nestedValues[this.rowIndex],
			subModel: [],
			subRowName: null
		};
	},
	mounted() {
		this.$emit("add-nested-field");
		this.addSubFieldRows();
		this.addFieldValues();
	},
	methods: {
		addNestedSubField(e) {
			e.preventDefault();
			this.addField();
			this.$emit("add-nested-field");
		},
		removeSubField(index) {
			this.removeField(index, this.fields);
		},
		addSubFieldRows() {
			if (this.values) {
				this.rowCount = this.values.length;
				this.addFields(this.rowCount, this.model);
			}
		}
	}
};
