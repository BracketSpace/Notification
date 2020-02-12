export const inputsHandler = {
	methods: {
		selectCheckbox( checkbox, e ){
			const checkboxInput = e.target;

			checkbox.value = !checkbox.value;
			if( ! checkbox.value ) {
				checkbox.checked = '';
				checkboxInput.setAttribute( 'checked', '' );
				checkboxInput.setAttribute( 'value', 0 );

			} else {
				checkbox.checked = 'checked';
				checkboxInput.setAttribute( 'checked', 'checked' );
				checkboxInput.setAttribute( 'value', 1 );
			}
		}
	}
}
