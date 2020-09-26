<?php
global $R34ICS, $R34ICSPro;
?>

<div class="wrap r34ics">

	<h1><?php echo get_admin_page_title(); ?></h1>
	
	<div class="metabox-holder columns-2">
	
		<div class="column-1">

			<h2 class="nav-tab-wrapper">
				<a href="#license" class="nav-tab nav-tab-active">License</a>
				<a href="#customize" class="nav-tab">Customize</a>
				<a href="#admin" class="nav-tab">Admin</a>
				<a href="#url-tester" class="nav-tab">URL Tester</a>
				<a href="#system-report" class="nav-tab">System Report</a>
			</h2><br />
			
			<div class="postbox" id="license">

				<h3 class="hndle"><span>License</span></h3>
		
				<div class="inside">
				
					<?php
					if (!empty($R34ICSPro->licensed())) {
						?>
						<form id="r34icspro-deactivate-license" method="post" action="" onsubmit="if (!confirm('<?php echo esc_attr(__('Are you sure?','r34icspro')); ?>')) { return false; }">
							<?php
							wp_nonce_field('r34icspro','r34icspro-deactivate-license-nonce');
							?>
							<p><input type="password" value="********" disabled="disabled" /> <input type="submit" class="button" value="Deactivate License" /></p>
							<p><em>Your license is valid. License expires: <?php echo $R34ICSPro->license_expires('display'); ?></em></p>
						</form>
						<?php
					}
					else {
						?>
						<form id="r34icspro-activate-license" method="post" action="">
							<?php
							wp_nonce_field('r34icspro','r34icspro-activate-license-nonce');
							?>
							<p><input type="text" name="api_key" value="" placeholder="Product Order API Key" />
							<input type="text" name="product_id" value="" placeholder="Product ID" />
							<input type="submit" class="button button-primary" value="Activate License" /></p>
							<p><em>Please enter a valid license number for updates and support. Licenses can be purchased at: <a href="https://icscalendar.com/" target="_blank">icscalendar.com</a></em></p>
						</form>
						<?php
					}
					?>
				
				</div>
			
			</div>

			<div class="postbox hidden" id="customize">

				<h3 class="hndle"><span>Customize</span></h3>
		
				<div class="inside">
					<p>Colors and other general appearance for <strong>ICS Calendar Pro</strong> can be managed in the <a href="<?php echo admin_url('customize.php?autofocus[panel]=r34icspro'); ?>">Customizer</a>.</p>
				</div>
	
			</div>
		
			<div class="postbox hidden" id="admin">

				<h3 class="hndle"><span>Administrative Options</span></h3>
		
				<div class="inside">
					<form id="r34icspro-admin-options" method="post" action="">
						<?php
						wp_nonce_field('r34icspro','r34icspro-admin-options-nonce');
						?>
						<p class="r34icspro-input">
							<label for="r34icspro-admin-options-precache"><input type="checkbox" name="precache" id="r34icspro-admin-options-precache"<?php if (get_option('r34icspro_precache')) { echo ' checked="checked"'; } ?> onchange="if (jQuery(this).prop('checked')) { jQuery('#r34icspro-precache-settings').slideDown(); } else { jQuery('#r34icspro-precache-settings').slideUp(); jQuery('#r34icspro-precache-settings input').each(function() { jQuery(this).val(''); }); }" /> <strong>Pre-cache Calendars</strong></label>
							<span class="description"><small class="help"><span class="help_content">Uses WP-Cron to preload and cache all of your saved calendars hourly, preventing situations where users may experience long load times when a calendar is not yet cached. Does not cache calendars with the <strong>Reload</strong> option set.</span></small></span>
						</p>
						
						<p class="r34icspro-input" <?php if (!get_option('r34icspro_precache')) { echo ' style="display: none;"'; } ?> id="r34icspro-precache-settings">
							<label for="r34icspro-admin-options-memory_limit"><strong>Memory Limit:</strong> <input type="number" name="memory_limit" id="r34icspro-admin-options-memory_limit" value="<?php echo intval(get_option('r34icspro_memory_limit') ? get_option('r34icspro_memory_limit') : ini_get('memory_limit')); ?>" min="0" max="8192" step="16" style="width: 100px;" /> <strong>MB</strong></label>
							&nbsp;&nbsp;
							<label for="r34icspro-admin-options-max_execution_time"><strong>Max Execution Time:</strong> <input type="number" name="max_execution_time" id="r34icspro-admin-options-max_execution_time" value="<?php echo intval(get_option('r34icspro_max_execution_time') ? get_option('r34icspro_max_execution_time') : ini_get('max_execution_time')); ?>" min="0" max="3600" step="15" style="width: 100px;" /> <strong>seconds</strong></label>
							<span class="description"><small class="help"><span class="help_content">If your calendar feed is large or has a very large number of recurring events, you may need to increase the memory limit and/or execution time for caching to function properly. <strong>Be sure not to exceed your server's available resources, especially if your site is on a shared server.</strong><br /><br />These settings apply to the pre-caching WP-Cron task only.</span></small></span>
						</p>
						
						<hr />

						<p class="r34icspro-input">
							<label for="r34icspro-admin-options-transient_expiration"><strong>Transient Expiration:</strong> <input type="number" name="transient_expiration" id="r34icspro-admin-options-transient_expiration" value="<?php echo esc_attr(get_option('r34ics_transient_expiration') ? get_option('r34ics_transient_expiration') : 3600); ?>" min="0" max="14400" step="60" style="width: 100px;" /> <strong>seconds</strong></label>
							<span class="description"><small class="help"><span class="help_content">Sets how long calendar feed data should be cached on the server (WordPress transients) before reloading. Default is <strong>3600</strong> (1 hour).<br /><br /><strong>Note:</strong> If you are using <strong>Pre-cache Calendars</strong>, you will want to set this to at least <strong>7200</strong> (2 hours), as pre-caching occurs hourly. (Lower values will defeat the purpose of pre-caching.)</span></small></span>
						</p>
						
						<hr />

						<p class="r34icspro-input">
							<label for="r34icspro-admin-options-bypass_no_acf"><input type="checkbox" name="bypass_no_acf" id="r34icspro-admin-options-bypass_no_acf"<?php if (get_option('r34icspro_bypass_no_acf')) { echo ' checked="checked"'; } ?> /> <strong>Bypass ACF Check</strong></label>
							<span class="description"><small class="help"><span class="help_content">This plugin includes an embedded copy of <a href="https://www.advancedcustomfields.com/pro/" target="_blank">Advanced Custom Fields PRO</a> to manage the editing screen for saved calendars. If your site has ACF PRO installed, that version will be used instead. If your site has the free version of ACF installed, ICS Calendar Pro's saved calendar editing screen will not function properly.<br /><br />By default, this plugin deactivates your installed copy of ACF (free) and uses its own ACF PRO instead. This will also allow your site's other ACF fields to function properly. <em>Check this box if you wish to bypass this and use your copy of ACF (free) instead.</em> <strong>IMPORTANT:</strong> Setting this option will cause ICS Calendar Pro's saved calendar editing screen not to function properly. All other features will continue to function, and you can insert calendars using manually entered shortcodes.</span></small></span>
						</p>

						<input type="submit" class="button button-primary" value="Save Changes" />
					</form>
				</div>
	
			</div>
		
			<div class="postbox hidden" id="url-tester">

				<h3 class="hndle"><span>ICS Feed URL Tester</span></h3>
		
				<div class="inside">
				
					<p>If you are concerned that the plugin is not properly retrieving your feed, you can test the URL here. The raw data received by the plugin will be displayed below.</p>

					<form id="r34icspro-url-tester" method="post" action="">
						<?php
						wp_nonce_field('r34icspro','r34icspro-url-tester-nonce');
						?>
						<div class="r34icspro-input">
							<label for="r34icspro-admin-options-url_to_test"><input type="text" name="url_to_test" id="r34icspro-admin-options-url_to_test" value="<?php if (!empty($url_to_test)) { echo esc_attr($url_to_test); } ?>" placeholder="Enter feed URL..." style="width: 50%;" /></label> <input type="submit" class="button button-primary" value="Test URL" />
						</div>
					</form>
					
					<?php
					if (!empty($url_tester_result)) {
						?>
						<p><?php echo size_format(strlen($url_tester_result), 2); ?> received.</p>
						<div class="diagnostics-window"><?php echo htmlentities($url_tester_result); ?></div>
						<?php
					}
					elseif (!empty($url_to_test)) {
						?>
						<p><strong style="color: red;">Could not retrieve data from the requested URL.</strong></p>
						<?php
					}
					?>
				
				</div>
	
			</div>
		
			<div class="postbox hidden" id="system-report">

				<h3 class="hndle"><span>System Report</span></h3>
		
				<div class="inside">
				
					<p>Please copy the following text and paste it into the <strong>System Report</strong> field of the <a href="https://icscalendar.com/pro-support/" target="_blank">support request form</a>.</p>

					<textarea class="diagnostics-window" style="cursor: copy;" onclick="this.select(); document.execCommand('copy');"><?php r34icspro_system_report(); ?></textarea>
				
				</div>
	
			</div>
		
		</div>
	
		<div class="column-2">

			<?php
			if (isset($R34ICSPro)) {
				include_once($R34ICSPro->admin_sidebar_path);
			}
			?>
	
		</div>
	
	</div>

</div>