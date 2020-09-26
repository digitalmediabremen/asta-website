<?php
/*
This file is installed automatically by ICS Calendar Pro.

ICS Calendar Pro requires Advanced Custom Fields PRO, which is included in the
vendors folder. If your site has the free version of Advanced Custom Fields
installed, ICS Calendar Pro will not function properly. However, the version
of ACF PRO we include supports all features of ACF (free) and should have no
impact on your site's use of ACF.

If you are no longer using ICS Calendar Pro, you can safely delete this file.

Please feel free to email support@room34.com for additional information.
*/

add_filter('option_active_plugins', function($plugins) {
	$key = array_search('advanced-custom-fields/acf.php',$plugins);
	if ($key !== false) {
		unset($plugins[$key]);
	}
	return $plugins;
});