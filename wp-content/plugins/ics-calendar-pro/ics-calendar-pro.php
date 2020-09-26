<?php
/*
Plugin Name: ICS Calendar Pro
Plugin URI:
Description: The "PRO" add-on to the free ICS Calendar plugin for WordPress adds advanced layout and customization options.
Version: 1.2.1
Author: Room 34 Creative Services, LLC
Author URI: https://room34.com
License: Copyright 2020 Room 34 Creative Services, LLC. All rights reserved.
Text Domain: r34icspro
Domain Path: /i18n/languages/
*/


// Don't load directly
if (!defined('ABSPATH')) { exit; }


// Plugin constants
define('R34ICSPRO_ENTIRELY_IN_RANGE', 1);
define('R34ICSPRO_ENTIRELY_OUT_OF_RANGE', 0);
define('R34ICSPRO_STARTS_OUT_OF_RANGE', -1);
define('R34ICSPRO_ENDS_OUT_OF_RANGE', -2);
define('R34ICSPRO_STARTS_AND_ENDS_OUT_OF_RANGE', -3);


// Load required files
require_once(plugin_dir_path(__FILE__) . 'functions.php');
// require_once(plugin_dir_path(__FILE__) . 'blocks/r34icspro-block.php');


// Load includes
include_once(plugin_dir_path(__FILE__) . 'customizer.php');


// Initialize plugin
add_action('plugins_loaded', function() {
	if (r34icspro_check_dependencies()) {
		global $R34ICSPro;
		require_once(plugin_dir_path(__FILE__) . 'class-r34icspro.php');
		$R34ICSPro = new R34ICSPro();
	}
});


// Load text domain for translations
add_action('plugins_loaded', function() {
	load_plugin_textdomain('r34icspro', FALSE, basename(plugin_dir_path(__FILE__)) . '/i18n/languages/');
});


// Check dependencies
$r34icspro_dependencies = array(
	'ICS Calendar' => 'ics-calendar/ics-calendar.php',
);
function r34icspro_check_dependencies($show_admin_notice=true) {
	global $r34icspro_dependencies, $r34icspro_missing_plugins;
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
	$r34icspro_missing_plugins = array();
	foreach ((array)$r34icspro_dependencies as $plugin_name => $plugin_path) {
		if (!is_plugin_active($plugin_path)) {
			$r34icspro_missing_plugins[] = $plugin_name;
		}
	}
	if (!empty($r34icspro_missing_plugins) && !empty($show_admin_notice)) {
		add_action('admin_notices', function() {
			global $r34icspro_missing_plugins;
			?>
			<div class="notice notice-error">
				<p><strong style="color: red;">IMPORTANT!</strong> The <strong>ICS Calendar Pro</strong> plugin requires the following missing plugin(s):</p>
				<p><strong><?php echo implode('<br />',$r34icspro_missing_plugins); ?></strong></p>
				<p>Please install (if necessary) and activate the missing plugin(s) to use ICS Calendar Pro</p>
			</div>
			<?php
		});
	}
	return empty($r34icspro_missing_plugins);
}
add_action('admin_init', 'r34icspro_check_dependencies');


// Flush rewrite rules when plugin is activated
register_activation_hook(__FILE__, function() { flush_rewrite_rules(); });


// Install/upgrade
add_action('plugins_loaded', function() {
	global $R34ICSPro;
	if (isset($R34ICSPro) && get_option('r34icspro_version') != @$R34ICSPro->version) {
		r34icspro_install();
	}
	if (is_plugin_active('advanced-custom-fields/acf.php')) {
		if (get_option('r34icspro_bypass_no_acf')) {
			r34icspro_mu_plugins_uninstall();
		}
		else {
			r34icspro_mu_plugins_install();
		}
	}
}, 11);


// Plugin installation
// See: https://codex.wordpress.org/Creating_Tables_with_Plugins
register_activation_hook(__FILE__, 'r34icspro_install');
function r34icspro_install() {
	global $R34ICSPro;
	// Update version
	if (isset($R34ICSPro)) {
		update_option('r34icspro_version', @$R34ICSPro->version);
	}
	// Install mu-plugins file to prevent free ACF from loading
	r34icspro_mu_plugins_install();
	// Set unique "instance" value for license management
	// Does not get altered once it has been created, even if plugin is deactivated/reactivated, but resets if server IP has changed
	if (!get_option('r34icspro_instance') || strpos(get_option('r34icspro_instance'), $_SERVER['SERVER_ADDR']) !== 0) {
		update_option('r34icspro_instance', $_SERVER['SERVER_ADDR'] . '-' . wp_generate_password(12, false));
	}
}


// mu-plugins install
register_activation_hook(__FILE__, 'r34icspro_mu_plugins_install');
function r34icspro_mu_plugins_install() {
	if (get_option('r34icspro_bypass_no_acf')) {
		r34icspro_mu_plugins_uninstall();
		return;
	}
	if (is_plugin_active('advanced-custom-fields/acf.php') && !file_exists(WPMU_PLUGIN_DIR . '/r34icspro-no-acf.php')) {
		// Add setting to reactivate ACF if we're deactivated in the future
		update_option('r34icspro_reactivate_acf_on_mu_plugins_uninstall', true);
		// Create mu-plugins directory
		if (!is_dir(WPMU_PLUGIN_DIR)) {
			mkdir(WPMU_PLUGIN_DIR);
		}
		// Copy our file into mu-plugins
		if (!file_exists(WPMU_PLUGIN_DIR . '/r34icspro-no-acf.php') && file_exists(plugin_dir_path(__FILE__) . 'r34icspro-no-acf.php')) {
			copy(plugin_dir_path(__FILE__) . 'r34icspro-no-acf.php', WPMU_PLUGIN_DIR . '/r34icspro-no-acf.php');
		}
		// Add admin notice explaining what was done
		add_action('admin_notices', function() {
			if (file_exists(WPMU_PLUGIN_DIR . '/r34icspro-no-acf.php')) {
				?>
				<div class="notice notice-info">
					<p><strong>ICS Calendar Pro</strong> includes Advanced Custom Fields PRO for certain editing features. The free version of Advanced Custom Fields was detected in your installation. ICS Calendar Pro has installed a file located at <code><?php echo WPMU_PLUGIN_DIR . '/r34icspro-no-acf.php'; ?></code> to prevent a conflict between these two plugins. You will not lose any ACF functionality.</p>
					<p>If you wish to override this change, please turn on <strong>Bypass ACF Check</strong> on the <a href="<?php echo admin_url('edit.php?post_type=r34icspro_calendar&page=ics-calendar-pro-settings#admin'); ?>">ICS Calendar Pro Settings</a> page.</p>
					<p><strong style="color: red;">IMPORTANT:</strong> If you deactivate ICS Calendar Pro in the future, you may need to manually reactivate Advanced Custom Fields.</p>
				</div>
				<?php
			}
			else {
				?>
				<div class="notice notice-warning">
					<p><strong>ICS Calendar Pro</strong> includes Advanced Custom Fields PRO for certain editing features. The free version of Advanced Custom Fields was detected in your installation. Please <a href="<?php echo admin_url('plugins.php?s=Advanced%20Custom%20Fields&plugin_status=all'); ?>">deactivate your copy of Advanced Custom Fields</a> to use ICS Calendar Pro. You will not lose any ACF functionality.</p>
				</div>
				<?php
			}
		});
	}
}


// mu-plugins uninstall
register_deactivation_hook(__FILE__, 'r34icspro_mu_plugins_uninstall');
function r34icspro_mu_plugins_uninstall() {
	if (get_option('r34icspro_reactivate_acf_on_mu_plugins_uninstall')) {
		activate_plugin('advanced-custom-fields/acf.php');
		// @todo Find a different way to trigger this message; it won't actually display here
		?>
		<div class="notice notice-info">
			<p><strong>ICS Calendar Pro</strong> has been deactivated. If your site uses <strong>Advanced Custom Fields</strong> you may need to <a href="<?php echo admin_url('plugins.php?s=Advanced%20Custom%20Fields&plugin_status=all'); ?>">reactivate it</a>.</p>
		</div>
		<?php
	}
	if (file_exists(WPMU_PLUGIN_DIR . '/r34icspro-no-acf.php')) {
		unlink(WPMU_PLUGIN_DIR . '/r34icspro-no-acf.php');
	}
}


// WP-Cron jobs
register_activation_hook(__FILE__, 'r34icspro_wp_cron_activation');
function r34icspro_wp_cron_activation() {
	if (!wp_next_scheduled('r34icspro_wp_cron_hourly')) {
		wp_schedule_event(time(), 'hourly', 'r34icspro_wp_cron_hourly');
	}
}

register_deactivation_hook(__FILE__, 'r34icspro_wp_cron_deactivation');
function r34icspro_wp_cron_deactivation() {
	if ($next_scheduled = wp_next_scheduled('r34icspro_wp_cron_hourly')) {
		wp_unschedule_event($next_scheduled, 'r34icspro_wp_cron_hourly');
	}
}

function r34icspro_wp_cron_hourly() {
	// Pre-cache saved calendars
	if (get_option('r34icspro_precache')) {
		// Get all calendars
		if ($calendars = get_posts(array(
			'post_type' => 'r34icspro_calendar',
		))) {
			// This may require extra memory and time if the feed is large
			if (get_option('r34icspro_memory_limit')) {
				ini_set('memory_limit', get_option('r34icspro_memory_limit'));
			}
			if (get_option('r34icspro_max_execution_time')) {
				ini_set('max_execution_time', get_option('r34icspro_max_execution_time'));
			}
			foreach ((array)$calendars as $calendar) {
				// Skip calendars that are set to reload
				if (!function_exists('get_field') || !get_field('reload', $calendar->ID)) {
					// Run calendar shortcode (this will generate a new transient)
					do_shortcode('[ics_calendar id="' . $calendar->ID . '"]');
					// Log this for diagnostic purposes
					update_option('r34icspro_precached_' . $calendar->ID, wp_date('Y-m-d H:i:s'));
				}
			}
		}
	}
}
add_action('r34icspro_wp_cron_hourly', 'r34icspro_wp_cron_hourly');

