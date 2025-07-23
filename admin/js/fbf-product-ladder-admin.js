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

		let override_chassis_field_id = 'field_6878cdef98ede';
		let override_brand_field_id = 'field_6878d159bea8a';
		let override_product_field_id = 'field_6878d347bea8c';

		acf.addAction('select2_init', function( $select, args, settings, field ){
			if(field.data.key===override_chassis_field_id){
				/*console.log('select2_init');
				console.log('field:');
				console.log(field);*/
				/*console.log('settings:');
				console.log(settings);
				console.log('args:');
				console.log(args);
				console.log('$select:');
				console.log($select);*/

				//let $repeater = $select.parents('.acf-repeater');
				let index = $select.parents('.acf-row').index();
				console.log(index);

				let data = {
					action: 'fbf_product_ladder_populate_chassis',
					ajax_nonce: fbf_product_ladder_admin.ajax_nonce,
					row: index,
				};

				$.ajax({
					// eslint-disable-next-line no-undef
					url: fbf_product_ladder_admin.ajax_url,
					type: 'POST',
					data: data,
					dataType: 'json',
					success: function(response) {
						if(response.status==='success'){
							let $option = `<option value="${response.chassis_id}">${response.name}</option>`;
							$select.append($option);
						}else{
							console.log('Error getting Chassis: ' + response.error);
						}
					}
				});
			}else if(field.data.key===override_product_field_id){
				console.log('select2_init');
				console.log('field:');
				console.log(field);

				let product_brand_row_index = $select.parents('.acf-row').index();
				let $product_brand_row = $select.parents('.acf-row');

				let chassis_row_index = $product_brand_row.parents('.acf-row').index();
				let $chassis_row = $product_brand_row.parents('.acf-row');

				console.log('order index: ' + product_brand_row_index);
				console.log('chassis index: ' + chassis_row_index);

				let data = {
					action: 'fbf_product_ladder_populate_order',
					ajax_nonce: fbf_product_ladder_admin.ajax_nonce,
					order_index: product_brand_row_index,
					chassis_index: chassis_row_index,
				};

				$.ajax({
					// eslint-disable-next-line no-undef
					url: fbf_product_ladder_admin.ajax_url,
					type: 'POST',
					data: data,
					dataType: 'json',
					success: function(response) {
						if(response.status==='success'){
							console.log(response);
							let $option = `<option value="${response.data.id}">${response.data.name}</option>`;
							$select.append($option);
						}else{
							console.log('Error getting Product: ' + response.error);
						}
					}
				});
			}else if(field.data.key===override_brand_field_id){
				console.log('select2_init');
				console.log('field:');
				console.log(field);

				let product_brand_row_index = $select.parents('.acf-row').index();
				let $product_brand_row = $select.parents('.acf-row');

				let chassis_row_index = $product_brand_row.parents('.acf-row').index();
				let $chassis_row = $product_brand_row.parents('.acf-row');

				console.log('order index: ' + product_brand_row_index);
				console.log('chassis index: ' + chassis_row_index);

				let data = {
					action: 'fbf_product_ladder_populate_order',
					ajax_nonce: fbf_product_ladder_admin.ajax_nonce,
					order_index: product_brand_row_index,
					chassis_index: chassis_row_index,
				};

				$.ajax({
					// eslint-disable-next-line no-undef
					url: fbf_product_ladder_admin.ajax_url,
					type: 'POST',
					data: data,
					dataType: 'json',
					success: function(response) {
						if(response.status==='success'){
							console.log(response);
							let $option = `<option value="${response.data.id}">${response.data.name}</option>`;
							$select.append($option);
						}else{
							console.log('Error getting Brand: ' + response.error);
						}
					}
				});
			}
		});

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
			}else if(
				data.field_key===override_chassis_field_id
			){
				console.log('chassis_field');
				let manufacturer_field = $input.parents('.acf-row').find('.acf-field-select[data-name=manufacturer] select');
				console.log(manufacturer_field);
				data.manufacturer_id = manufacturer_field.val();
			}else if(
				data.field_key===override_brand_field_id
			){
				console.log('brand_field');
				let chassis_field = $input.parents('.acf-row').find('.acf-field-select[data-name=chassis] select');
				console.log(chassis_field);
				console.log('selected chassis: ' + chassis_field.val());
				data.chassis_id = chassis_field.val();
			}else if(data.field_key===override_product_field_id){
				console.log('product_field');
				let chassis_field = $input.parents('.acf-row').find('.acf-field-select[data-name=chassis] select');
				console.log(chassis_field);
				console.log('selected chassis: ' + chassis_field.val());
				data.chassis_id = chassis_field.val();
			}
			// return
			return data;
		});
	});
})( jQuery );
