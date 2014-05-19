jQuery( '._limited_dates_to_field input, ._limited_dates_from_field input' ).datetimepicker({
	defaultDate: '',
	dateFormat: 'yy-mm-dd',
	numberOfMonths: 1,
	showButtonPanel: true,
	buttonImage: woocommerce_admin_meta_boxes.calendar_image,
	buttonImageOnly: true,
});

jQuery('input#_limited').change(function(){

	jQuery('._limited_dates_to_field, ._limited_dates_from_field, ._limited_deals_url_field, ._limited_deals_button_text_field').hide();

	if ( jQuery('#_limited').is(':checked') ) {
		jQuery('._limited_dates_to_field, ._limited_dates_from_field, ._limited_deals_url_field, ._limited_deals_button_text_field').show();
	}

}).change();
