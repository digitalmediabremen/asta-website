<?php
$current_screen = get_current_screen();
?>

<a href="https://icscalendar.com/pro" target="_blank"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))); ?>assets/ics-calendar-pro-logo.svg" alt="ICS Calendar Pro" style="display: block; height: auto; margin: 0 auto 1.5em auto; width: 200px;" /></a>

<h3><span>Support</span></h3>

<?php
if (strpos($current_screen->base, 'ics-calendar') === false) {
	?>
	<p>Before contacting support, please review the <strong><a href="<?php echo admin_url('edit.php?post_type=r34icspro_calendar&page=ics-calendar-user-guide'); ?>" target="_blank">User Guide</a></strong> for an overview of usage and options.</p>
	<?php
}
?>

<p>Log into <a href="https://icscalendar.com/my-account/" target="_blank">your account</a> to check license status or to obtain your license API key.</p>

<p>For priority support with <strong>ICS Calendar Pro</strong>, please use our <a href="https://icscalendar.com/pro-support/" target="_blank">support request form</a>.</p>

<p>For general support with features of the free ICS Calendar plugin, please use the <a href="https://wordpress.org/support/plugin/ics-calendar" target="_blank">WordPress support forums</a>.</p>

<p><small><strong>Please note:</strong> Per WordPress.org guidelines, we cannot provide support for the Pro version on the WordPress forums.</small></p>
		
<h3><span>Additional Resources</span></h3>

<h4>ICS documentation and tools:</h4>

<nav>
	<ul>
		<li><a href="https://github.com/u01jmg3/ics-parser" target="_blank">ICS Parser at GitHub</a></li>
		<li><a href="https://icalendar.org/validator.html" target="_blank">iCal Validator</a></li>
		<li><a href="https://jakubroztocil.github.io/rrule/" target="_blank">Recurrence Rule Tester</a></li>
	</ul>
</nav>

<h4>Miscellaneous reference and tools:</h4>

<nav>
	<ul>
		<li><a href="https://www.w3schools.com/colors/colors_picker.asp" target="_blank">HTML Color Picker</a></li>
		<li><a href="https://www.unixtimestamp.com/" target="_blank">Unix Timestamp Converter</a></li>
	</ul>
</nav>

<hr />
		
<a href="https://room34.com/" target="_blank"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))); ?>assets/room34-logo-on-white.svg" alt="Room 34 Creative Services" style="display: block; height: auto; margin: 1.5em auto; width: 200px;" /></a> 
				
<p><strong>Thank you for using ICS Calendar Pro!</strong></p>

<p><small>ICS Calendar Pro v. <?php echo get_option('r34icspro_version'); ?><br />
ICS Calendar v. <?php echo get_option('r34ics_version'); ?></small></p>
