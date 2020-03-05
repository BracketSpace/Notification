export const sectionsModal = {
	data(){
		return {
			modalOpen: false,
		}
	},
	methods: {
		addSection( e ){
			if(e){
				e.preventDefault();
			}

			this.modalOpen = true;

		},
		createSection( e, section ){
			e.preventDefault();
			e.stopPropagation();

			this.selectedSection = section;
			this.addField();
			this.modalOpen = false;
		}
	}
}
