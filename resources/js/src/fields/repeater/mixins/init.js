/* global notification, fetch */
import sortableHandle from "./sortableHandle";

export const init = {
	mounted() {
		this.setType();
		this.apiCall();
		this.sortable();
	},
	methods: {
		apiCall() {
			this.postID = notification.postId;

			fetch(`${notification.repeater_rest_url}${this.postID}`, {
				method: "POST",
				headers: {
					Accept: "application/json",
					"Content-Type": "application/json",
					"X-WP-Nonce": notification.rest_nonce
				},
				body: JSON.stringify(this.type)
			})
				.then(res => res.json())
				.then(data => {
					// eslint-disable-next-line camelcase
					const { field, values } = data;

					if (field) {
						this.addNestedModel(field);
						this.addModel(field);

						if (values) {
							this.values = values;
							this.rowCount = this.values.length;
							this.addFields(this.rowCount, this.model);
							this.addFieldValues();
						}
					}
				})
				//eslint-disable-next-line no-unused-vars
				.catch(err => {
					this.repeaterError = true;
				});
		},
		setType() {
			const instance = this.$el;
			const fieldType = instance.getAttribute("data-field-name");
			const fieldCarrier = instance.getAttribute("data-carrier");

			this.type = {
				fieldType,
				fieldCarrier
			};
		},
		sortable() {
			sortableHandle();
		}
	}
};
