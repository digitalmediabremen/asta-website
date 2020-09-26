<?php

// Customizer... customizations
add_action('init', function() {
	global $R34ICS, $R34ICSPro, $r34icspro_customizer;

	$r34icspro_customizer = array(
		'panels' => array(
			'r34icspro' => array(
				'description' => '',
				'priority' => 200,
				'title' => __('ICS Calendar Pro','r34icspro'),
			),
		),

		'sections' => array(
			'r34icspro_colors' => array(
				'panel' => 'r34icspro',
				'priority' => 10,
				'title' => __('Colors','r34icspro'),
			),
			'r34icspro_layout' => array(
				'panel' => 'r34icspro',
				'priority' => 20,
				'title' => __('Layout','r34icspro'),
			),
			'r34icspro_text' => array(
				'panel' => 'r34icspro',
				'priority' => 30,
				'title' => __('Text','r34icspro'),
			),
		),

		'settings' => array(
		
			// COLORS
		
			'r34icspro_color_border' => array(
				'css' => array(
					'.ics-calendar-month-grid th, .ics-calendar-month-grid td { border-color: %1$s; }',
					'.ics-calendar-widget-grid td.today:not(.has_events) .day, .ics-calendar-widget-grid td.current:not(.has_events) .day { border-color: %1$s; }',
					'.ics-calendar-widget .ics-calendar-pagination { border-color: %1$s; }',
				),
				'default' => $R34ICS->colors['neutral'],
				'description' => null,
				'label' => __('Grid Borders','r34icspro'),
				'priority' => 10,
				'section' => 'r34icspro_colors',
				'type' => 'color',
			),
		
			'r34icspro_color_header_bg' => array(
				'css' => array(
					'.ics-calendar-month-grid th { background: %1$s; }',
				),
				'default' => $R34ICS->colors['neutral'],
				'description' => null,
				'label' => __('Header Background','r34icspro'),
				'priority' => 20,
				'section' => 'r34icspro_colors',
				'type' => 'color',
			),
		
			'r34icspro_color_header_text' => array(
				'css' => array(
					'.ics-calendar-month-grid th { color: %1$s; }',
					'.ics-calendar-widget .ics-calendar-pagination { color: %1$s !important; }',
				),
				'default' => $R34ICS->colors['black'],
				'description' => null,
				'label' => __('Header Text','r34icspro'),
				'priority' => 30,
				'section' => 'r34icspro_colors',
				'type' => 'color',
			),
		
			'r34icspro_color_day_bg' => array(
				'css' => array(
					'.ics-calendar-month-grid .day { background: %1$s; }',
					'.ics-calendar-month-grid .off { background: %1$s; }',
					'.ics-calendar-month-grid .past { background: %1$s; }',
					'.ics-calendar-month-grid ul.events li.all-day { background: %1$s; }',
					'.ics-calendar-widget .ics-calendar-pagination { background: %1$s; }',
				),
				'default' => $R34ICS->colors['offwhite'],
				'description' => null,
				'label' => __('Day Background','r34icspro'),
				'priority' => 40,
				'section' => 'r34icspro_colors',
				'type' => 'color',
			),
		
			'r34icspro_color_dividers' => array(
				'css' => array(
					'.ics-calendar-month-grid ul.events li { border-bottom-color: %1$s; }',
				),
				'default' => $R34ICS->colors['silver'],
				'description' => null,
				'label' => __('Event Dividers','r34icspro'),
				'priority' => 50,
				'section' => 'r34icspro_colors',
				'type' => 'color',
			),
		
			'r34icspro_color_accent_dark' => array(
				'css' => array(
					'.ics-calendar-month-grid .today .day, .ics-calendar-month-grid .current .day { background: %1$s; color: #ffffff; }',
					'.ics-calendar-main-with-sidebar .ics-calendar-main .event_count .badge { background: %1$s; }',
					'.ics-calendar-widget-grid td.has_events .day { border-color: %1$s; }',
					'.ics-calendar-widget-grid td.today.has_events .day, .ics-calendar-widget-grid td.current.has_events .day { background: %1$s; border-color: %1$s; }',
				),
				'default' => $R34ICS->colors['gray'],
				'description' => null,
				'label' => __('Dark Accent Color','r34icspro'),
				'priority' => 60,
				'section' => 'r34icspro_colors',
				'type' => 'color',
			),
		
			'r34icspro_color_accent_light' => array(
				'css' => array(
					'.ics-calendar-widget-grid td.has_events .day { background: %1$s; }',
				),
				'default' => $R34ICS->colors['silver'],
				'description' => null,
				'label' => __('Light Accent Color','r34icspro'),
				'priority' => 70,
				'section' => 'r34icspro_colors',
				'type' => 'color',
			),
			
			// LAYOUT
			
			'r34icspro_hide_weekends' => array(
				'conditional_css' => true,
				'css' => array(
					true => ".ics-calendar tr > *[data-dow='0'], .ics-calendar tr > *[data-dow='6'] { display: none; }",
				),
				'default' => false,
				'description' => '<small>Applies to grid-style (month, week) layouts only. If checked, Saturdays and Sundays will not display in the grid.</small>',
				'label' => __('Hide Weekends','r34icspro'),
				'priority' => 10,
				'section' => 'r34icspro_layout',
				'type' => 'checkbox',
			),
		
			'r34icspro_legend_style' => array(
				'choices' => array(
					0 => 'Show with checkboxes (default)',
					1 => 'Show without checkboxes',
					2 => 'Hide legend',
				),
				'conditional_css' => true,
				'css' => array(
					0 => null,
					1 => '.ics-calendar-color-key input[type=checkbox] { display: none; }',
					2 => '.ics-calendar-color-key { display: none; }',
				),
				'default' => false,
				'description' => '<small>Customizes appearance of the color key legend shown on calendars with multiple, color-coded feeds.</small>',
				'label' => __('Legend Style','r34icspro'),
				'priority' => 20,
				'section' => 'r34icspro_layout',
				'type' => 'select',
			),
		
			'r34icspro_sticky_sidebar_top' => array(
				'choices' => array(
					'0' => '0',
					'1em' => '1em',
					'2em' => '2em',
					'3em' => '3em',
					'4em' => '4em',
					'5em' => '5em',
					'6em' => '6em',
					'7em' => '7em',
					'8em' => '8em',
					'9em' => '9em',
					'10em' => '10em',
					'11em' => '11em',
					'12em' => '12em',
					'13em' => '13em',
					'14em' => '14em',
					'15em' => '15em',
				),
				'css' => array(
					'.ics-calendar-sidebar { top: %1$s; }',
				),
				'default' => '5em',
				'description' => '<small>Offset distance for sticky scrolling sidebars from top of window when scrolling.</small>',
				'label' => __('Sticky Sidebar Top','r34icspro'),
				'priority' => 30,
				'section' => 'r34icspro_layout',
				'type' => 'select',
			),

			// TEXT

			'r34icspro_font' => array(
				'choices' => array(
					'default' => '[Theme fonts]',
					'Georgia' => 'Georgia',
					'Helvetica/Arial' => 'Helvetica/Arial',
					'Lato' => 'Lato',
					'Merriweather' => 'Merriweather',
					'Montserrat' => 'Montserrat',
					'Open Sans' => 'Open Sans',
					'Open Sans Condensed' => 'Open Sans Condensed',
					'PT Sans' => 'PT Sans',
					'PT Serif' => 'PT Serif',
					'Raleway' => 'Raleway',
					'Roboto' => 'Roboto',
					'Roboto Condensed' => 'Roboto Condensed',
					'Source Sans Pro' => 'Source Sans Pro',
					'Times New Roman' => 'Times New Roman',
					'Trebuchet MS' => 'Trebuchet MS',
					'Verdana' => 'Verdana',
				),
				'conditional_css' => true,
				'css' => array(
					'default' =>			'',
					'Georgia' =>			'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: Georgia, serif !important; ' .
											'}',
					'Helvetica/Arial' =>	'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: Helvetica, Arial, sans-serif !important; ' .
											'}',
					'Lato' =>				'@import url("https://fonts.googleapis.com/css?family=Lato:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: Lato, sans-serif !important; ' .
											'}',
					'Merriweather' =>		'@import url("https://fonts.googleapis.com/css?family=Merriweather:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: Merriweather, serif !important; ' .
											'}',
					'Montserrat' =>			'@import url("https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: Montserrat, sans-serif !important; ' .
											'}',
					'Open Sans' =>			'@import url("https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: "Open Sans", sans-serif !important; ' .
											'}',
					'Open Sans Condensed' => '@import url("https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: "Open Sans Condensed", sans-serif !important; ' .
											'}',
					'PT Sans' =>			'@import url("https://fonts.googleapis.com/css?family=PT+Sans:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: "PT Sans", sans-serif !important; ' .
											'}',
					'PT Serif' =>			'@import url("https://fonts.googleapis.com/css?family=PT+Serif:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: "PT Serif", serif !important; ' .
											'}',
					'Raleway' =>			'@import url("https://fonts.googleapis.com/css?family=Raleway:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: Raleway, sans-serif !important; ' .
											'}',
					'Roboto' =>				'@import url("https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: Roboto, sans-serif !important; ' .
											'}',
					'Roboto Condensed' =>	'@import url("https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: "Roboto Condensed", sans-serif !important; ' .
											'}',
					'Source Sans Pro' =>	'@import url("https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700&display=swap");' .
											'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: "Source Sans Pro", sans-serif !important; ' .
											'}',
					'Trebuchet MS' =>			'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: "Trebuchet MS", sans-serif !important; ' .
											'}',
					'Verdana' =>			'.ics-calendar-month-grid *, .ics-calendar-grid-wrapper *, .ics-calendar-widget-grid * { ' .
												'font-family: Verdana, sans-serif !important; ' .
											'}',
				),
				'default' => '100%',
				'description' => '',
				'label' => __('Font','r34icspro'),
				'priority' => 10,
				'section' => 'r34icspro_text',
				'type' => 'select',
			),

			'r34icspro_text_scaling_grid' => array(
				'choices' => array(
					'50%' => '50%',
					'60%' => '60%',
					'70%' => '70%',
					'80%' => '80%',
					'90%' => '90%',
					'100%' => '100%',
					'110%' => '110%',
					'120%' => '120%',
					'130%' => '130%',
					'140%' => '140%',
					'150%' => '150%',
				),
				'css' => array(
					'.ics-calendar-month-grid, .ics-calendar-grid-wrapper, .ics-calendar-widget-grid { font-size: %1$s; }',
				),
				'default' => '100%',
				'description' => '',
				'label' => __('Grid Text Scaling','r34icspro'),
				'priority' => 20,
				'section' => 'r34icspro_text',
				'type' => 'select',
			),

			'r34icspro_text_scaling_list' => array(
				'choices' => array(
					'50%' => '50%',
					'60%' => '60%',
					'70%' => '70%',
					'80%' => '80%',
					'90%' => '90%',
					'100%' => '100%',
					'110%' => '110%',
					'120%' => '120%',
					'130%' => '130%',
					'140%' => '140%',
					'150%' => '150%',
				),
				'css' => array(
					'.ics-calendar-list-wrapper, .ics-calendar-sidebar, .ics-calendar-day-details { font-size: %1$s; }',
				),
				'default' => '100%',
				'description' => '',
				'label' => __('List Text Scaling','r34icspro'),
				'priority' => 30,
				'section' => 'r34icspro_text',
				'type' => 'select',
			),

			'r34icspro_hyphenation_off' => array(
				'choices' => array(
					0 => 'Hyphenate and forced breaks ON',
					1 => 'Hyphenate and forced breaks OFF',
					2 => 'Hyphenate ON; forced breaks OFF',
				),
				'conditional_css' => true,
				'css' => array(
					0 => null,
					1 => '.ics-calendar .event * { -ms-word-break: normal; word-break: normal; -webkit-hyphens: none; -moz-hyphens: none; -ms-hyphens: none; hyphens: none; }',
					2 => '.ics-calendar .event * { -ms-word-break: normal; word-break: normal; }',
				),
				'default' => false,
				'description' => '<small>By default, long words will be hyphenated, with forced breaks if needed, to prevent text overflowing small containers. Optionally you can turn off all hyphenation and forced breaks, or allow hyphenation but not forced breaks. <strong>Note:</strong> Hyphenation is not supported by some older browsers.</small>',
				'label' => __('Hyphenation','r34icspro'),
				'priority' => 100,
				'section' => 'r34icspro_text',
				'type' => 'select',
			),
		
		),
	);

});


// Register settings
add_action('customize_register', function($wp_customize) {
	global $R34ICS, $R34ICSPro, $r34icspro_customizer;
	if (!$R34ICSPro->licensed()) { return false; }

	// Add panels
	foreach ((array)$r34icspro_customizer['panels'] as $panel => $values) {
		$wp_customize->add_panel($panel, $values);
	}

	// Add/modify sections
	foreach ((array)$r34icspro_customizer['sections'] as $section => $values) {
		$wp_customize->add_section($section, $values);
	}

	// Add/modify settings and controls
	foreach ((array)$r34icspro_customizer['settings'] as $setting => $values) {

		// Color controls
		if ($values['type'] == 'color') {
			$wp_customize->add_setting($setting, array(
				'default' => @$values['default'],
				'sanitize_callback' => 'sanitize_hex_color',
			));
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $setting, $values));
		}

		// Image controls
		elseif ($values['type'] == 'image') {
			$wp_customize->add_setting($setting, array(
				'default' => @$values['default'],
				'sanitize_callback' => 'r34icspro_sanitize_image_file',
			));
			$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $setting, $values));
		}

		// All other controls
		else {
			$wp_customize->add_setting($setting, array(
				'default' => @$values['default'],
				'sanitize_callback' => !empty($values['sanitize_callback']) ? $values['sanitize_callback'] : 'sanitize_text_field',
			));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, $setting, $values));
		}

	}

}, 99);


// Associated callbacks

// Image file uploads
// Based on: https://divpusher.com/blog/wordpress-customizer-sanitization-examples#file
function r34icspro_sanitize_image_file($file, $setting) {
	$mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif' => 'image/gif',
		'png' => 'image/png',
	);
	// Also allow SVGs if Room 34's Enable SVG plugin is active
	if (class_exists('R34SVG')) {
		$mimes['svg|svgz'] = 'image/svg+xml';
	}
	$ext = wp_check_filetype($file,$mimes);
	return !empty($ext['ext']) ? $file : $setting->default;
}


// Add CSS and JavaScript
add_action('wp_enqueue_scripts', function() {
	global $R34ICS, $R34ICSPro, $r34icspro_customizer;

	// Add CSS from Customizer settings
	$customizer_css = array();
	foreach ((array)$r34icspro_customizer['settings'] as $setting => $values) {
		$setting_default = isset($values['default']) ? $values['default'] : null;
		if (!empty($values['css']) && $setting_value = get_theme_mod($setting, $setting_default)) {
			// Conditional CSS
			if (!empty($values['conditional_css'])) {
				$matched = false;
				foreach ((array)$values['css'] as $cond => $css) {
					if	(
							($values['conditional_css'] === true && $setting_value == $cond) ||
							($values['conditional_css'] !== true && get_theme_mod($values['conditional_css']) == $cond)
						)
					{
						$customizer_css[] = sprintf($css, $setting_value);
						$matched = true;
					}
				}
				if (!$matched && $css = $values['css']['default']) {
					$customizer_css[] = sprintf($css, $setting_value);
				}
			}

			// General CSS
			else {
				foreach ((array)$values['css'] as $css) {
					$customizer_css[] = sprintf($css, $setting_value);
				}
			}
		}
	}
	
	// Implode CSS array into string
	$css = implode("\n", (array)$customizer_css);
	
	// Move all @import directives to beginning of string
	if (strpos($css, '@import') !== false) {
		preg_match_all('/@import url\([^\)]+\);/', $css, $matches);
		if (!empty($matches)) {
			foreach ((array)$matches as $match) {
				$css = str_replace($match, '', $css);
			}
			$css = implode("\n", (array)$matches[0]) . $css;
		}
	}
		
	// Minify CSS (unless debugging is on)
	if (!defined('WP_DEBUG') || !WP_DEBUG) { $css = r34icspro_minify_css($css); }
	
	// Output CSS as inline style
	wp_add_inline_style('ics-calendar-pro', $css);

}, 12);
