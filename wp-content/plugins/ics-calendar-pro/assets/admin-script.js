jQuery(function() {

	// Copy text from input to clipboard
	jQuery('input + .icon.copy').on('click', function() {
		jQuery(this).siblings('input').select();
		document.execCommand('copy');
		jQuery(this).html('<span class="dashicons dashicons-yes" title="Copied"></span>');
		setTimeout(function() {
			jQuery('input + .icon.copy').html('<span class="dashicons dashicons-admin-page" title="Copy to clipboard..."></span>');
		}, 2000);
	});

	// Admin "Add ICS Calendar" button Pro options -- replace regular insertion fields with saved calendar selector
	if (jQuery('.field-block').length > 0) {
		jQuery('.field-block:not(.saved_calendar)').hide();
		jQuery('#insert_r34ics_form').addClass('saved_calendar');
	}

	jQuery('#insert_r34ics_form').on('submit', function() {
		
		// Saved calendars are handled by the Pro plugin
		if (!jQuery(this).hasClass('saved_calendar')) { return false; }
		
		// Validate required fields
		if (jQuery('#insert_r34ics_id').val() == '') {
			alert('Please select a saved calendar from the menu.');
			jQuery('#insert_r34ics_id').focus();
			return false;
		}
		
		// Concatenate shortcode
		var r34ics_shortcode = '[ics_calendar id="' + parseInt(jQuery('#insert_r34ics_id').val()) + '"]';
	
		// Insert shortcode and close window
		window.send_to_editor(r34ics_shortcode);
		jQuery('#insert_r34ics_form')[0].reset();
		jQuery('#r34ics_list_view_options').hide();
		jQuery('#insert_r34ics').removeClass('open');
		return false;
	});
	
});