<?php
// Require object
if (empty($ics_data)) { return false; }

global $R34ICS;
global $wp_locale;

$hour_format = r34ics_hour_format();

$ics_calendar_classes = array(
	'ics-calendar',
	'layout-grid',
	(!empty($args['hidetimes']) ? ' hide_times' : ''),
	(!empty($args['toggle']) ? ' r34ics_toggle' : ''),
	(!empty($args['nomobile']) ? ' nomobile' : ''),
	(count((array)$ics_data['urls']) > 1 ? ' multi-feed' : ''),
);

// Feed colors custom CSS
if (!empty($ics_data['colors']) && function_exists('r34ics_feed_colors_css')) {
	r34ics_feed_colors_css($ics_data, false, true);
}

// Additional PHP-dependent CSS
?>
<style type="text/css">
	tbody.ics-calendar-grid-body .ics-calendar-grid-all-day {
		height: auto;
	}
	tbody.ics-calendar-grid-body .ics-calendar-grid-hours {
		height: <?php echo ((100 + $hours[1] - $hours[0]) * floatval($zoom)); ?>px;
	}
	tbody.ics-calendar-grid-body .ics-calendar-grid-hours .ics-calendar-grid-hour {
		height: <?php echo (100 * floatval($zoom)); ?>px;
	}
	
	<?php
	if (!empty($days_count)) {
		?>
		tbody.ics-calendar-grid-body .ics-calendar-grid-day, tbody.ics-calendar-grid-body .ics-calendar-grid-all-day {
			width: calc(<?php echo floatval(100/$days_count); ?>% - <?php echo floatval(60/$days_count); ?>px) !important;
		}
		<?php
	}
	?>
</style>

<section class="<?php echo esc_attr(implode(' ', $ics_calendar_classes)); ?>" id="<?php echo esc_attr($ics_data['guid']); ?>">

	<?php
	// Title and description
	if (!empty($ics_data['title'])) {
		?>
		<h2 class="ics-calendar-title"><?php echo $ics_data['title']; ?></h2>
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

		<table class="ics-calendar-grid-wrapper">
			<thead class="ics-calendar-grid-header">
				<tr>
					<th class="ics-calendar-grid-tz">
						<?php
						/* Commented out because calendar's overall TZ may not be accurate to the events within
						@todo Add timezone indicator
						*/
						?>
					</th>
					<?php
					foreach ((array)$ics_data['events'] as $year => $year_data) {
						foreach ((array)$year_data as $month => $month_data) {
							foreach ((array)$month_data as $day => $day_data) {
								$day_ts = gmmktime(0,0,0,$month,$day,$year);
								?>
								<th class="ics-calendar-grid-day" data-day-ts="<?php echo esc_attr($day_ts); ?>">
									<?php
									if ($days_count > 1) {
										?>
										<span class="toggle_day"><span class="assistive-text">Expand/Collapse</span></span>
										<?php
									}
									?>
									<span class="dow">
										<?php echo wp_date('D', $day_ts, $R34ICS->tz); ?>
									</span>
									<span class="date">
										<?php echo wp_date('M j', $day_ts, $R34ICS->tz); ?>
									</span>
								</th>
								<?php
							}
						}
					}
					?>
				</tr>
			</thead>
			<tbody class="ics-calendar-grid-body">
				<?php
				// All-day events filter to top
				if (!empty($all_day_events_max)) {
					?>
					<tr>
						<td class="ics-calendar-grid-all-day">
							<span class="all-day-indicator"><?php _e('All Day', 'r34ics'); ?></span>
						</td>
						<?php
						foreach ((array)$ics_data['events'] as $year => $months) {
							foreach ((array)$months as $month => $days) {
								foreach ((array)$days as $day => $day_events) {
									$day_ts = strtotime($year . $month . $day);
									?>
									<td class="ics-calendar-grid-all-day" data-day-ts="<?php echo esc_attr($day_ts); ?>">
										<?php
										if (!empty($day_events['all-day'])) {
											foreach ((array)$day_events['all-day'] as $event) {
												$has_desc = r34ics_has_desc($args, $event);
												?>
												<div class="<?php echo r34ics_event_css_classes($event, 'all-day', $args); ?>" data-feed-key="<?php echo intval($event['feed_key']); ?>">
													<?php
													// Event label (title)
													echo $R34ICS->event_label_html($args, $event, (!empty($has_desc) ? array('has_desc') : null));

													// Sub-label
													echo $R34ICS->event_sublabel_html($args, $event, null);

													// Description/Location
													echo $R34ICS->event_description_html($args, $event, array('show_on_hover'), $has_desc);
													?>
												</div>
												<?php
											}
										}
									}
								}
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td class="ics-calendar-grid-hours">
						<?php
						for ($hour = intval($hours[0]); $hour <= intval($hours[1]); $hour = $hour + 100) {
							$hour_start_offset = r34icspro_calculate_offset($hour, $hours[0], $zoom);
							?>
							<hr class="ics-calendar-grid-hour-marker" data-hour-start-offset="<?php echo $hour_start_offset; ?>" style="top: <?php echo ($hour_start_offset + 25); ?>px;" />
							<div class="ics-calendar-grid-hour" data-hour-start-offset="<?php echo $hour_start_offset; ?>" style="top: <?php echo $hour_start_offset; ?>px;">
								<span class="hour"><?php echo date($hour_format,gmmktime(intval($hour/100),0)); ?></span>
							</div>
							<?php
						}
						?>
					</td>
					<?php
					foreach ((array)$ics_data['events'] as $year => $months) {
						foreach ((array)$months as $month => $days) {
							foreach ((array)$days as $day => $day_events) {
								$day_ts = strtotime($year . $month . $day);
								?>
								<td class="ics-calendar-grid-day" data-day-ts="<?php echo esc_attr($day_ts); ?>">
									<?php
									
									// Hour markers
									for ($hour = intval($hours[0]); $hour <= intval($hours[1]); $hour = $hour + 100) {
										$hour_start_offset = r34icspro_calculate_offset($hour, $hours[0], $zoom);
										?>
										<hr class="ics-calendar-grid-hour-marker" data-hour-start-offset="<?php echo $hour_start_offset; ?>" style="top: <?php echo ($hour_start_offset + 25); ?>px;" />
										<?php
									}
								
									// Keep track of overlapping events
									$overlapping_events = array();

									// Event blocks
									foreach ((array)$day_events as $time => $events) {

										// Skip all-day events
										if ($time == 'all-day') { continue; }

										// Keep track of overlapping events
										foreach ((array)$overlapping_events as $key => $item) {
											if (intval($item['dtend_time']) <= intval(str_replace('t','',$time))) {
												unset($overlapping_events[$key]);
											}
										}
										$overlapping_events = array_merge($overlapping_events,$events);
										
										// Loop through events
										foreach ((array)$events as $event_key => $event) {
										
											// Handle events that fall partially or completely out of hour range
											$event_range = r34icspro_time_out_of_range(substr($event['dtstart_time'],0,4), substr($event['dtend_time'],0,4), $hours[0], $hours[1]);
											switch ($event_range) {
												case R34ICSPRO_ENTIRELY_OUT_OF_RANGE:
													continue(2); // Skip this event
													break;
												case R34ICSPRO_STARTS_OUT_OF_RANGE:
													// Truncate start
													$event['dtstart_time'] = intval($hours[0] - 55) . '00';
													break;
												case R34ICSPRO_ENDS_OUT_OF_RANGE:
													// Truncate end
													$event['dtend_time'] = intval($hours[1] + 15) . '00';
													break;
												case R34ICSPRO_STARTS_AND_ENDS_OUT_OF_RANGE:
													// Truncate start and end
													$event['dtstart_time'] = intval($hours[0] - 55) . '00';
													$event['dtend_time'] = intval($hours[1] + 15) . '00';
													break;
												case R34ICSPRO_ENTIRELY_IN_RANGE:
												default:
													// Do nothing
													break;
											}

											// Event properties
											$has_desc = r34ics_has_desc($args, $event);
											$event_start_offset = r34icspro_calculate_offset(@$event['dtstart_time'], $hours[0], $zoom);
											$event_end_offset = r34icspro_calculate_offset(@$event['dtend_time'], $hours[0], $zoom);
											
											// Build event position CSS
											$event_top = $event_start_offset + 25; // px
											$event_left = (100/count($events)) * $event_key; // %
											$event_min_height = $event_end_offset - $event_start_offset - 2; // px
											$event_height = $event_end_offset - $event_start_offset - 2; // px
											$event_width = 100/count($events); // %
											
											// Force a bare minimum height
											if ($event_height < 18) { $event_height = 20; }

											// Extra indent on overlap with earlier events
											// * @todo This needs more sophistication in recognizing where the earlier overlapping events are located in the grid
											if (count($overlapping_events) > count($events)) {
												/*$event_left = $event_left + 2;
												$event_width = $event_width - 2;*/
											}
											?>
											<div class="<?php echo r34ics_event_css_classes($event, $time, $args); ?>" data-feed-key="<?php echo intval($event['feed_key']); ?>" data-hour-start-offset="<?php echo $event_start_offset; ?>" data-hour-end-offset="<?php echo $event_end_offset; ?>" style="top: <?php echo $event_top; ?>px; left: <?php echo $event_left; ?>%; min-height: <?php echo $event_min_height; ?>px; height: <?php echo $event_height; ?>px; width: calc(<?php echo $event_width; ?>% - 2px);">
												<?php
												// Event label (title)
												echo $R34ICS->event_label_html($args, $event, (!empty($has_desc) ? array('has_desc') : null));

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
												
												// Sub-label
												echo $R34ICS->event_sublabel_html($args, $event, null);

												// Description/Location
												echo $R34ICS->event_description_html($args, $event, array('show_on_hover'), $has_desc);
												?>
											</div>
											<?php
										}
									}
									
									?>
								</td>
								<?php
							}
						}
					}
					?>
				</tr>
			</tbody>
		</table>
		
		<?php
		// Actions after rendering calendar wrapper (can include additional template output)
		do_action('r34ics_display_calendar_after_wrapper', $view, $args, $ics_data);

	}
	?>


</section>
