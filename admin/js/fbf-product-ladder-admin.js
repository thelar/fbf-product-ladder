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
		console.log('acf exists here');

		// extend the acf.ajax object
		// you should probably rename this var
		var myACFextension = acf.ajax.extend({
			events: {
				// this data-key must match the field key for the state field on the post page where
				// you want to dynamically load the cities when the state is changed
				'change [data-key="field_579376f522130"] select': '_state_change',
				// this entry is to cause the city field to be updated when the page is loaded
				'ready [data-key="field_579376f522130"] select': '_state_change',
			},

			// this is our function that will perform the
			// ajax request when the state value is changed
			_state_change: function(e){
				console.log('state change');
			},
		});

		// triger the ready action on page load
		$('[data-key="field_579376f522130"] select').trigger('ready');
	});
})( jQuery );
