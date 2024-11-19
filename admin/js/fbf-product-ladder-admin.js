(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {
		// make sure acf is loaded, it should be, but just in case
		if (typeof acf == 'undefined'){
			return;
		}
		let brands_field_id = 'field_6733716272bd9';
		let models_field_id = 'field_673371aa72bda';

		acf.add_filter('select2_ajax_data', function( data, args, $input, field, instance ){
			// do something to data
			if(data.field_key===models_field_id){
				let brands_field = acf.getField(brands_field_id);
				console.log(brands_field);
				console.log(brands_field.val());
				data.brand_id = brands_field.val();
			}
			// return
			return data;
		});
	});
})( jQuery );
