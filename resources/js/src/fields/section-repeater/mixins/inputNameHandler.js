export const inputNameHandler = {
	data() {
		return {
			parentFieldName: null,
			nested: false
		};
	},
	mounted() {
		this.setProps();
	},
	methods: {
		setProps() {
			const parent = this.$parent;

			this.parentFieldName = Object.freeze(
				this.parentField.toLowerCase()
			);
			this.nested = Object.freeze(parent.nested);
		}
	}
};
