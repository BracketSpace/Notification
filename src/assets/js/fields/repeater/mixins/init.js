/* global notification, fetch */
import sortableHandle from './sortableHandle';

export const init = {
	mounted(){
		this.setType();
		this.apiCall();
		this.sortable();
	},
	methods: {
		apiCall(){
			this.postID = notification.postId;

			fetch( `${notification.repeater_rest_url}${this.postID}`,{
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json'
				},
				body: JSON.stringify( this.type )
			} )
			.then( res => res.json() )
			.then( data => {
				// eslint-disable-next-line camelcase
				const {field, field_sections, values, } = data;

				this.addNestedModel( field );
				this.addModel( field );

				if( values ){
					this.values = values
					this.rowCount = this.values.length;
					this.addFields( this.rowCount, this.model );
					this.addFieldValues();
				}

				// eslint-disable-next-line camelcase
				if( field_sections ){
					// eslint-disable-next-line camelcase
					this.sections = field_sections;
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
		sortable(){
			sortableHandle();
		}
	}
}
