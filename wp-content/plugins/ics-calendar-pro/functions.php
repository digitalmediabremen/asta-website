<?php

function r34icspro_days_count($events) {
	$days_count = 0;
	foreach ((array)$events as $years) {
		foreach ((array)$years as $months) {
			foreach ((array)$months as $days) {
				$days_count++;
			}
		}
	}
	return $days_count;
}


function r34icspro_all_day_events_max($events) {
	$all_day_events_max = 0;
	foreach ((array)$events as $months) {
		foreach ((array)$months as $days) {
			foreach ((array)$days as $day_events) {
				if (!empty($day_events['all-day']) && count($day_events['all-day']) > $all_day_events_max) {
					$all_day_events_max = count($day_events['all-day']);
				}
			}
		}
	}
	return $all_day_events_max;
}


function r34icspro_calculate_offset($time, $base=0, $zoom=0.6) {
	if (strlen($time) < 4) {
		$time = str_repeat('0',4-strlen($time)) . $time;
	}
	elseif (strlen($time) > 4) {
		$time = substr($time,0,4);
	}
	$h = intval(substr($time,0,2));
	$m = intval(substr($time,2,2));
	return (($h * 100) + ($m * 10/6) - $base) * $zoom;
}


function r34icspro_day_events_count($day_events) {
	$day_events_count = 0;
	foreach ((array)$day_events as $time => $events) {
		$day_events_count = $day_events_count + count((array)$events);
	}
	return intval($day_events_count);
}


// Minify CSS
// Based on: http://manas.tungare.name/software/css-compression-in-php/
function r34icspro_minify_css($css) {
	// Remove comments
	$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
	// Remove space after colons
	$css = str_replace(': ', ':', $css);
	// Remove space around braces
	$css = str_replace(array(' {','{ '), '{', $css);
	$css = str_replace(array(' }','} '), '}', $css);
	// Remove whitespace
	$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
	return $css;
}


function r34icspro_system_report($echo=true) {
	global $R34ICSPro;
	
	$theme = wp_get_theme();
	$plugins = get_plugins();
	
	$plugin_list = array();
	foreach ((array)$plugins as $plugin) {
		$plugin_list[$plugin['Name']] = $plugin['Version'];
	}
	
	$report = array(
		'Site' => get_bloginfo('name'),
		'URL' => get_bloginfo('wpurl'),
		'ICS Calendar Pro' => get_option('r34icspro_version'),
		'ICS Calendar' => get_option('r34ics_version'),
		'ICS Transient Expiration' => intval(get_option('r34ics_transient_expiration')),
		'ICS Pro Bypass ACF Check' => intval(get_option('r34icspro_bypass_no_acf')),
		'ICS Pro Pre-cache' => intval(get_option('r34icspro_precache')),
		'ICS Pro Memory Limit' => intval(get_option('r34icspro_memory_limit')),
		'ICS Pro Max Execution Time' => intval(get_option('r34icspro_max_execution_time')),
		'ICS Pro Licensed' => intval($R34ICSPro->licensed()),
		'ICS Pro License Expires' => $R34ICSPro->license_expires('display'),
		'ICS Pro Instance' => get_option('r34icspro_instance'),
		'WordPress' => get_bloginfo('version'),
		'Multisite' => (function_exists('get_sites') ? count(get_sites()) : 0),
		'PHP' => phpversion(),
		'OS' => php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m'),
		'Language' => get_bloginfo('language'),
		'Charset' => get_bloginfo('charset'),
		'Timezone' => get_option('timezone_string'),
		'Theme' => $theme->__get('name') . ' v.' . $theme->__get('version') . ' (' . pathinfo($theme->__get('template_dir'), PATHINFO_BASENAME) . ')',
		'Plugins' => $plugin_list,
		'cURL' => curl_version(),
		'allow_url_fopen' => intval(ini_get('allow_url_fopen')),
	);

	if (!empty($echo)) {
		foreach ((array)$report as $key => $value) {
			echo $key . ':&nbsp;&nbsp;';
			if (is_array($value)) {
				echo "\n";
				foreach ((array)$value as $key2 => $value2) {
					echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $key2 . ':&nbsp;&nbsp;';
					if (is_array($value2)) {
						echo "\n";
						foreach ((array)$value2 as $key3 => $value3) {
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $key3 . ':&nbsp;&nbsp;';
							if (is_array($value3)) {
								print_r($value3);
							}
							else {
								echo $value3 . "\n";
							}
						}
					}
					else {
						echo $value2 . "\n";
					}
				}
			}
			else {
				echo $value . "\n";
			}
		}
		return true;
	}
	else {
		return $report;
	}
}


function r34icspro_time_out_of_range($event_start, $event_end, $range_start, $range_end) {
	if (intval($event_end) <= intval($range_start) || intval($event_start) >= intval($range_end)) {
		return R34ICSPRO_ENTIRELY_OUT_OF_RANGE;
	}
	if (intval($event_start) < intval($range_start) && intval($event_end) <= intval($range_end)) {
		return R34ICSPRO_STARTS_OUT_OF_RANGE;
	}
	if (intval($event_start) >= intval($range_start) && intval($event_end) > intval($range_end)) {
		return R34ICSPRO_ENDS_OUT_OF_RANGE;
	}
	if (intval($event_start) < intval($range_start) && intval($event_end) > intval($range_end)) {
		return R34ICSPRO_STARTS_AND_ENDS_OUT_OF_RANGE;
	}
	return R34ICSPRO_ENTIRELY_IN_RANGE;
}
