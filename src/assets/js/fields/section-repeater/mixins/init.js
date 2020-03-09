/* global notification, fetch */
export const init = {
	mounted(){
		this.setType();
		this.apiCall();
		// this.sortable();
	},
	methods: {
		apiCall(){
			this.postID = notification.postId;

			fetch( `${notification.section_repeater_rest_url}${this.postID}`,{
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json'
				},
				body: JSON.stringify( this.type )
			} )
			.then( res => res.json() )
			.then( data => {
				const { sections, values } = data;

				if( sections ){
					this.sections = sections;
				}
			}
			);

		},
		setType(){
			const instance = this.$el;
			const fieldType = instance.getAttribute( 'data-field-name' );
			const fieldCarrier = instance.getAttribute( 'data-carrier' );

			this.type = {
				fieldType,
				fieldCarrier
			}
		},
		// sortable(){
		// 	sortableHandle();
		// }
	}
}
