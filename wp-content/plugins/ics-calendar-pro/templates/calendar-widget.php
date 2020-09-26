<?php
// Require object
if (empty($ics_data)) { return false; }

global $R34ICS;
global $wp_locale;

$days_of_week = $R34ICS->get_days_of_week('min');
$start_of_week = get_option('start_of_week', 0);

$date_format = r34ics_date_format($args['format']);

$today = date('Ymd', current_time('timestamp'));
$todays_events_html = '';

$ics_calendar_classes = array(
	'ics-calendar',
	'layout-widget',
	(!empty($args['hidetimes']) ? ' hide_times' : ''),
	(!empty($args['toggle']) ? ' r34ics_toggle' : ''),
	(!empty($args['nomobile']) ? ' nomobile' : ''),
	(count((array)$ics_data['urls']) > 1 ? ' multi-feed' : ''),
);

// Feed colors custom CSS
if (!empty($ics_data['colors']) && function_exists('r34ics_feed_colors_css')) {
	r34ics_feed_colors_css($ics_data, true);
}
?>

<section class="<?php echo esc_attr(implode(' ', $ics_calendar_classes)); ?>" id="<?php echo esc_attr($ics_data['guid']); ?>">

	<?php
	// Title and description
	if (!empty($ics_data['title'])) {
		?>
		<h3 class="ics-calendar-title"><?php echo $ics_data['title']; ?></h3>
		<?php
	}
	if (!empty($ics_data['description'])) {
		?>
		<p class="ics-calendar-description"><?php echo $ics_data['description']; ?></p>
		<?php
	}
	
	// Empty calendar message
	if (empty($ics_data['events'])) {
		?>
		<p class="ics-calendar-error"><?php _e('No events found.', 'r34ics'); ?></p>
		<?php
	}
	
	// Display calendar
	else {

		// Actions before rendering calendar wrapper (can include additional template output)
		do_action('r34ics_display_calendar_before_wrapper', $view, $args, $ics_data);

		// Color code key
		echo $R34ICS->color_key_html($args, $ics_data);		
		?>		

		<div class="ics-calendar-widget">

			<div class="ics-calendar-overview">
				
				<?php
				// Build monthly calendars
				foreach (array_keys((array)$ics_data['events']) as $year) {
					for ($m = 1; $m <= 12; $m++) {
						$month = $m < 10 ? '0' . $m : '' . $m;
						$ym = $year . $month;
						if (isset($ics_data['earliest']) && $ym < $ics_data['earliest']) { continue; }
						if (isset($ics_data['latest']) && $ym > $ics_data['latest']) { break(2); }
						$first_date = gmmktime(0,0,0,$month,1,$year);
						$month_label = ucwords(wp_date($args['formatmonthyear'], gmmktime(0,0,0,$month,1,$year), $R34ICS->tz));
				
						// Build month's calendar
						?>
						<div class="ics-calendar-widget-wrapper ics-calendar-month-wrapper" data-year-month="<?php echo date('Ym', gmmktime(0,0,0,$month,1,$year)); ?>" style="display: none;">
						
							<h4 class="ics-calendar-label">
								<div class="ics-calendar-pagination prev" title="Previous Month">&larr;</div>
								<?php echo $month_label; ?>
								<div class="ics-calendar-pagination next" title="Next Month">&rarr;</div>
							</h4>
							
							<table class="ics-calendar-widget-grid">
								<thead>
									<tr>
										<?php
										foreach ((array)$days_of_week as $w => $dow) {
											?>
											<th data-dow="<?php echo $w; ?>"><?php echo $dow; ?></th>
											<?php
										}
										?>
									</tr>
								</thead>

								<tbody>
									<tr>
										<?php
										$first_dow = $R34ICS->first_dow($first_date);
										if ($first_dow < $start_of_week) { $first_dow = $first_dow + 7; }
										for ($off_dow = $start_of_week; $off_dow < $first_dow; $off_dow++) {
											?>
											<td class="off" data-dow="<?php echo intval($off_dow); ?>"></td>
											<?php
										}
										for ($day = 1; $day <= date('t',$first_date); $day++) {
											$date = gmmktime(0,0,0,date('n',$first_date),$day,date('Y',$first_date));
											$dow = date('w',$date);
											$day_events = isset($ics_data['events'][$year][$month][date('d',$date)]) ? $ics_data['events'][$year][$month][date('d',$date)] : null;
											$comp_date = date('Ymd', $date);
											if ($dow == $start_of_week) {
												?>
												</tr><tr>
												<?php
											}
											$day_classes = array();
											if ($comp_date < $today) {
												$day_classes[] = 'past';
											}
											elseif ($comp_date == $today) {
												$day_classes[] = 'today';
											}
											else {
												$day_classes[] = 'future';
											}
											if (count((array)$day_events) == 0) {
												$day_classes[] = 'empty';
											}
											else {
												$day_classes[] = 'has_events';
											}
											?>
											<td data-dow="<?php echo intval($dow); ?>" data-formatted-date="<?php echo esc_attr(wp_date($date_format, $date, $R34ICS->tz)); ?>" class="<?php echo esc_attr(implode(' ', $day_classes)); ?>">
												<div class="day">
													<?php echo date('j', $date); ?>
												</div>
												<ul class="events hidden" data-event-count="<?php echo r34icspro_day_events_count($day_events); ?>"><?php
													if ($comp_date == $today) {
														ob_start();
													}
													foreach ((array)$day_events as $time => $events) {
														$all_day_indicator_shown = false;
														foreach ((array)$events as $event) {
															$has_desc = r34ics_has_desc($args, $event);
															if ($time == 'all-day') {
																?>
																<li class="<?php echo r34ics_event_css_classes($event, $time, $args); ?>" data-feed-key="<?php echo intval($event['feed_key']); ?>">
																	<?php
																	if (!$all_day_indicator_shown) {
																		?>
																		<span class="all-day-indicator"><?php _e('All Day', 'r34ics'); ?></span>
																		<?php
																		$all_day_indicator_shown = true;
																	}

																	// Event label (title)
																	echo $R34ICS->event_label_html($args, $event, (!empty($has_desc) ? array('has_desc') : null));

																	// Sub-label
																	echo $R34ICS->event_sublabel_html($args, $event, null);

																	// Description/Location/Organizer
																	echo $R34ICS->event_description_html($args, $event, array('sidebar_only'), $has_desc);
																	?>
																</li>
																<?php
															}
															else {
																?>
																<li class="<?php echo r34ics_event_css_classes($event, $time, $args); ?>" data-feed-key="<?php echo intval($event['feed_key']); ?>">
																	<?php
																	if (!empty($event['start'])) {
																		?>
																		<span class="time"><?php
																		echo $event['start'];
																		if (!empty($event['end']) && $event['end'] != $event['start']) {
																			if (empty($args['showendtimes'])) {
																				?>
																				<span class="show_on_hover">&#8211; <?php echo $event['end']; ?></span>
																				<?php
																			}
																			else {
																				?>
																				&#8211; <?php echo $event['end']; ?>
																				<?php
																			}
																		}
																		?></span>
																		<?php
																	}

																	// Event label (title)
																	echo $R34ICS->event_label_html($args, $event, (!empty($has_desc) ? array('has_desc') : null));

																	// Sub-label
																	echo $R34ICS->event_sublabel_html($args, $event, null);

																	// Description/Location/Organizer
																	echo $R34ICS->event_description_html($args, $event, array('sidebar_only'), $has_desc);
																	?>
																</li>
																<?php
															}
														}
													}
													if ($comp_date == $today) {
														$todays_events_html = ob_get_clean();
														echo $todays_events_html;
													}
												?></ul>
											</td>
											<?php
										}
										$calc_dow = ($start_of_week != 0 && $dow == 0) ? 7 : $dow;
										for ($off_dow = $calc_dow + 1; $off_dow < ($start_of_week + 7); $off_dow++) {
											?>
											<td class="off" data-dow="<?php echo intval($off_dow % 7); ?>"></td>
											<?php
										}
										?>
									</tr>
								</tbody>
							</table>
	
						</div>
						<?php
					}
				}
				?>
		
			
			</div><!-- .ics-calendar-overview -->
			
			<div class="ics-calendar-day-details">
				<div class="ics-calendar-day-details-content">
					<h4 class="ics-calendar-current-date"><?php echo wp_date($date_format, strtotime($today), $R34ICS->tz); ?></h4>
					<div class="ics-calendar-current-events" data-no-events-html="<?php echo esc_attr('<p class="ics-calendar-error">' . __('No events found.', 'r34ics') . '</p>'); ?>">
						<?php
						echo !empty($todays_events_html) ? '<ul class="events">' . $todays_events_html . '</ul>' : '<p class="ics-calendar-error">' . __('No events found.', 'r34ics') . '</p>';
						?>
					</div>
				</div>
			</div><!-- .ics-calendar-day-details -->
			
		</div><!-- .ics-calendar-widget -->
		
		<?php
		// Actions after rendering calendar wrapper (can include additional template output)
		do_action('r34ics_display_calendar_after_wrapper', $view, $args, $ics_data);

	}
	?>

</section>
