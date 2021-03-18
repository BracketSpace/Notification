export const sectionsModal = {
	data() {
		return {
			modalOpen: false
		};
	},
	methods: {
		addSection(e) {
			if (e) {
				e.preventDefault();
			}

			this.modalOpen = true;

			window.addEventListener("click", event => {
				if (
					!event.target.classList.contains("add-new-sections-field")
				) {
					this.modalOpen = false;
				}
			});
		},
		createSection(e, section) {
			e.preventDefault();
			e.stopPropagation();

			this.selectedSection = section.name;
			this.savedSections.push(section.name);
			this.addFieldSection(section.fields);
			this.modalOpen = false;
		},
		createSubSection(section) {
			this.addSubFieldSection(section.name);
			this.modalOpen = false;
		}
	}
};
