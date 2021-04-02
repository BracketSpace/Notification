/* global notification, fetch */

export const init = {
	mounted() {
		this.setType();
		this.apiCall();
	},
	methods: {
		apiCall() {
			this.postID = notification.postId;

			fetch(`${notification.section_repeater_rest_url}${this.postID}`, {
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
					const { sections, values } = data;

					if (sections) {
						this.sections = sections;
						this.extractFields();
					}

					if (values) {
						this.values = values;
						this.addFieldSectionValues();
					}
				})
				//eslint-disable-next-line no-unused-vars
				.catch(err => {
					this.repeaterError = true;
				});
		},
		extractFields() {
			const baseFields = {};
			// eslint-disable-next-line no-unused-vars
			for (const [section, field] of Object.entries(this.sections)) {
				for (const [name, data] of Object.entries(field.fields)) {
					baseFields[name] = data;
				}
			}

			this.baseFields = baseFields;
		},
		setType() {
			const instance = this.$el;
			const fieldType = instance.getAttribute("data-field-name");
			const fieldCarrier = instance.getAttribute("data-carrier");

			this.type = {
				fieldType,
				fieldCarrier
			};
		}
	}
};
