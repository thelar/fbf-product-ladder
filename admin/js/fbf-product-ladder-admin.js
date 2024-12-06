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
		let budget_models_at_mt_field_id = 'field_673ce4867f6ba';
		let mid_range_models_at_mt_field_id = 'field_673ce4ba7f6bf';
		let premium_models_at_mt_field_id = 'field_673ce5217f6c4';
		let budget_models_non_at_mt_field_id = 'field_673e4a53a7f01';
		let mid_range_models_non_at_mt_field_id = 'field_673e4c54d49a9';
		let premium_models_non_at_mt_field_id = 'field_673e4c95d49ab';

		acf.add_filter('select2_ajax_data', function( data, args, $input, field, instance ){
			console.log(data.field_key);
			console.log('$input');
			console.log($input);
			// do something to data
			if(
				data.field_key===budget_models_at_mt_field_id ||
				data.field_key===mid_range_models_at_mt_field_id ||
				data.field_key===premium_models_at_mt_field_id ||
				data.field_key===budget_models_non_at_mt_field_id ||
				data.field_key===mid_range_models_non_at_mt_field_id ||
				data.field_key===premium_models_non_at_mt_field_id
			){
				let brands_field = $input.parents('.acf-row').find('.acf-taxonomy-field[data-taxonomy=pa_brand-name] select');
				//let brands_field = acf.getField(brands_field_id);
				/*console.log(brands_field);
				console.log(brands_field.val());*/
				console.log('brand_field');
				console.log(brands_field);
				data.brand_id = brands_field.val();
			}
			// return
			return data;
		});
	});
})( jQuery );
