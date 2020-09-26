jQuery(function() {

	// ALL VIEWS
	
	// Filter
	if (jQuery('.ics-calendar-filter').length > 0) {
		jQuery('.ics-calendar-filter-text').on('keyup change', function() {
			var guid = jQuery(this).closest('.ics-calendar').attr('id');
			var match_text = jQuery(this).val().toLowerCase();
			var hidden_events_count = 0;
			jQuery('.ics-calendar#' + guid + ' .event').each(function() {
				var event_text = jQuery(this).text().toLowerCase();
				if (event_text.indexOf(match_text) == -1) {
					jQuery(this).hide().addClass('hidden_in_main').prev('.time').hide();
					hidden_events_count++;
				}
				else {
					jQuery(this).show().removeClass('hidden_in_main').prev('.time').show();
				}
			});
			if (hidden_events_count > 0) {
				jQuery('.ics-calendar#' + guid + ' .more_events').hide();
				jQuery(this).siblings('.ics-calendar-filter-status').text(hidden_events_count + ' hidden');
			}
			else {
				jQuery('.ics-calendar#' + guid + ' .more_events').show();
				jQuery(this).siblings('.ics-calendar-filter-status').text('');
				jQuery('.ics-calendar#' + guid + ' .event.hidden_in_main_on_load').addClass('hidden_in_main');
				jQuery('.ics-calendar#' + guid + ' .event:not(.hidden_in_main_on_load)').removeClass('hidden_in_main');
			}
		});
		jQuery('.ics-calendar-filter input[type=reset]').on('click', function() {
			var guid = jQuery(this).closest('.ics-calendar').attr('id');
			jQuery('.ics-calendar#' + guid + ' .ics-calendar-filter-text').val('');
			jQuery('.ics-calendar#' + guid + ' .event').show().prev('.time').show();
			jQuery('.ics-calendar#' + guid + ' .event.hidden_in_main_on_load').addClass('hidden_in_main');
			jQuery('.ics-calendar#' + guid + ' .event:not(.hidden_in_main_on_load)').removeClass('hidden_in_main');
			jQuery(this).siblings('.ics-calendar-filter-status').text('');
			jQuery('.ics-calendar#' + guid + ' .more_events').show();
		});
	}

	// VIEW: GRID
	// Outer section wrapper has classes .ics-calendar.layout-grid
	
	if (jQuery('.ics-calendar.layout-grid').length > 0) {
		
		// Toggle days
		jQuery('.ics-calendar.layout-grid .toggle_day').parent().on('click', function() {
			var day_ts = jQuery(this).attr('data-day-ts');
			if (jQuery(this).hasClass('expanded')) {
				jQuery('*[data-day-ts]').show();
				jQuery(this).removeClass('expanded');
			}
			else {
				jQuery('*[data-day-ts]').hide();
				jQuery('*[data-day-ts="' + day_ts + '"]').show();
				jQuery(this).addClass('expanded');
			}
		});
		
	}
	
	// VIEW: MONTH WITH SIDEBAR
	// Outer section wrapper has classes .ics-calendar.layout-month-with-sidebar
	// Some of this is shared with VIEW: WIDGET
	
	if (jQuery('.ics-calendar.layout-month-with-sidebar').length > 0) {
			
		// Update sidebar to clicked date
		jQuery('.ics-calendar.layout-month-with-sidebar .ics-calendar-month-grid td[data-formatted-date]').on('click', function() {
			var ics_guid = jQuery(this).closest('.ics-calendar').attr('id');
			// Set current indicator on clicked date
			jQuery('#' + ics_guid + ' .ics-calendar-month-grid td[data-formatted-date]').removeClass('current');
			jQuery(this).addClass('current');
			jQuery('#' + ics_guid + ' .ics-calendar-sidebar-content .ics-calendar-current-date').html(jQuery(this).attr('data-formatted-date'));
			var day_events_html = jQuery(this).children('.events').html();
			if (day_events_html.length > 0) {
				jQuery('#' + ics_guid + ' .ics-calendar-sidebar-content .ics-calendar-current-events').html('<ul class="events">' + day_events_html + '</ul>');
			}
			else {
				jQuery('#' + ics_guid + ' .ics-calendar-sidebar-content .ics-calendar-current-events').html(jQuery('#' + ics_guid + ' .ics-calendar-sidebar-content .ics-calendar-current-events').attr('data-no-events-html'));
			}
			// Scroll to top of sidebar
			var extra_top_offset = jQuery('.ics-calendar-sidebar').css('top');
			if (extra_top_offset.indexOf('em') != -1) { extra_top_offset = parseInt(extra_top_offset) * 16; }
			else { extra_top_offset = parseInt(extra_top_offset); }
			jQuery('html, body').animate({ scrollTop: jQuery(this).closest('.ics-calendar').find('.ics-calendar-sidebar').offset().top - extra_top_offset }, 500);
		});

		// Show first month
		jQuery('.ics-calendar.layout-month-with-sidebar .ics-calendar-select').show();
		jQuery('.ics-calendar.layout-month-with-sidebar .ics-calendar-month-wrapper[data-year-month="' + jQuery('.ics-calendar-select').val() + '"]').show();
		jQuery('.ics-calendar.layout-month-with-sidebar .ics-calendar-select').on('change', function() {
			jQuery('.ics-calendar.layout-month-with-sidebar .ics-calendar-month-wrapper').hide();
			jQuery('.ics-calendar.layout-month-with-sidebar .ics-calendar-month-wrapper[data-year-month="' + jQuery(this).val() + '"]').show();
		});

	}
	
	// VIEW: WIDGET
	// Outer section wrapper has classes .ics-calendar.layout-widget
	
	if (jQuery('.ics-calendar.layout-widget').length > 0) {
	
		// Update list to clicked date
		jQuery('.ics-calendar.layout-widget .ics-calendar-widget-grid td[data-formatted-date]').on('click', function() {
			var ics_guid = jQuery(this).closest('.ics-calendar').attr('id');
			// Set current indicator on clicked date
			jQuery('#' + ics_guid + ' .ics-calendar-widget-grid td[data-formatted-date]').removeClass('current');
			jQuery(this).addClass('current');
			jQuery('#' + ics_guid + ' .ics-calendar-day-details-content .ics-calendar-current-date').html(jQuery(this).attr('data-formatted-date'));
			var day_events_html = jQuery(this).children('.events').html();
			if (jQuery(this).hasClass('has_events')) {
				jQuery('#' + ics_guid + ' .ics-calendar-day-details-content .ics-calendar-current-events').html('<ul class="events">' + day_events_html + '</ul>');
			}
			else {
				jQuery('#' + ics_guid + ' .ics-calendar-day-details-content .ics-calendar-current-events').html(jQuery('#' + ics_guid + ' .ics-calendar-day-details-content .ics-calendar-current-events').attr('data-no-events-html'));
			}
		});
		
		// Disable first prev link and last next link
		jQuery('.ics-calendar.layout-widget .ics-calendar-month-wrapper:first-child').find('.ics-calendar-pagination.prev').attr('disabled','disabled');
		jQuery('.ics-calendar.layout-widget .ics-calendar-month-wrapper:last-child').find('.ics-calendar-pagination.next').attr('disabled','disabled');
		
		// Switch month on prev/next click
		jQuery('.ics-calendar.layout-widget .ics-calendar-pagination').on('click', function() {
			var ics_guid = jQuery(this).closest('.ics-calendar').attr('id');
			var dir = jQuery(this).hasClass('prev') ? 'prev' : 'next';
			var current = jQuery(this).closest('.ics-calendar-month-wrapper');
			var next = dir == 'prev' ? current.prev() : current.next();
			if (next.length != 1) {
				jQuery(this).attr('disabled','disabled');
				return false;
			}
			else {
				var next_next = dir == 'prev' ? next.prev() : next.next();
				if (next_next.length != 1) {
					next.find('.ics-calendar-pagination.' + dir).attr('disabled','disabled');
				}
				current.hide();
				next.show();
			}
		});
		
		// Show first month
		jQuery('.ics-calendar.layout-widget .ics-calendar-widget > .ics-calendar-overview > .ics-calendar-month-wrapper:first-child').show();

		// Toggle event description excerpts
		jQuery('.ics-calendar.layout-widget .ics-calendar-day-details-content .ics-calendar-current-events').on('click', '.descloc_toggle_excerpt', function() {
			jQuery(this).hide().siblings('.descloc_toggle_full').show();
		});

	}

	// VIEW: YEAR WITH SIDEBAR
	// Outer section wrapper has classes .ics-calendar.layout-year-with-sidebar
	// Some of this is shared with VIEW: WIDGET
	
	if (jQuery('.ics-calendar.layout-year-with-sidebar').length > 0) {
	
		// Update sidebar to clicked date
		jQuery('.ics-calendar.layout-year-with-sidebar .ics-calendar-year-month-grid td[data-formatted-date]').on('click', function() {
			var ics_guid = jQuery(this).closest('.ics-calendar').attr('id');
			// Set current indicator on clicked date
			jQuery('#' + ics_guid + ' .ics-calendar-year-month-grid td[data-formatted-date]').removeClass('current');
			jQuery(this).addClass('current');
			jQuery('#' + ics_guid + ' .ics-calendar-sidebar-content .ics-calendar-current-date').html(jQuery(this).attr('data-formatted-date'));
			var day_events_html = jQuery(this).children('.events').html();
			if (day_events_html.length > 0) {
				jQuery('#' + ics_guid + ' .ics-calendar-sidebar-content .ics-calendar-current-events').html('<ul class="events">' + day_events_html + '</ul>');
			}
			else {
				jQuery('#' + ics_guid + ' .ics-calendar-sidebar-content .ics-calendar-current-events').html(jQuery('#' + ics_guid + ' .ics-calendar-sidebar-content .ics-calendar-current-events').attr('data-no-events-html'));
			}
		});

		// Show first year
		if (jQuery('.ics-calendar.layout-year-with-sidebar .ics-calendar-select').length > 0) {
			jQuery('.ics-calendar.layout-year-with-sidebar .ics-calendar-select').show();
			jQuery('.ics-calendar.layout-year-with-sidebar .ics-calendar-year-wrapper[data-year="' + jQuery('.ics-calendar-select').val() + '"]').show();
			jQuery('.ics-calendar.layout-year-with-sidebar .ics-calendar-select').on('change', function() {
				jQuery('.ics-calendar.layout-year-with-sidebar .ics-calendar-year-wrapper').hide();
				jQuery('.ics-calendar.layout-year-with-sidebar .ics-calendar-year-wrapper[data-year="' + jQuery(this).val() + '"]').show();
			});
		}

	}
		
});
