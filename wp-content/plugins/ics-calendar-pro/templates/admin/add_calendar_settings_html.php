<?php
$saved_calendars = get_posts(array('post_type' => 'r34icspro_calendar', 'post_status' => 'publish'));

if (empty($saved_calendars)) {
	?>
	<p>You have no saved calendars.</p>
	<?php
}
else {
	?>
	<p class="field-block saved_calendar">
		<label for="insert_r34ics_id"><strong>Load Saved Calendar:</strong></label><br />
		<select id="insert_r34ics_id" name="insert_r34ics_id">
			<option value="">Select one...</option>
			<?php
			foreach ((array)$saved_calendars as $saved_calendar) {
				?>
				<option value="<?php echo intval($saved_calendar->ID); ?>"><?php echo get_the_title($saved_calendar); ?></option>
				<?php
			}
			?>
		</select>
	</p>
	<?php
}
?>

<p><a href="<?php echo admin_url('post-new.php?post_type=r34icspro_calendar'); ?>">Create a new calendar...</a></p>
