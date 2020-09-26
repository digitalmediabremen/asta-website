<?php

// Don't load directly
if (!defined('ABSPATH')) { exit; }

if (class_exists('R34ICS')) {
	class R34ICSPro extends R34ICS {

		public $version = '1.2.1';
		public $required_r34ics_version = '5.9.1';
		
		public $admin_sidebar_path = null;
		public $admin_user_guide_path = null;
				
		public $icon_logo = 'data:image/svg+xml;base64,PHN2ZyBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGZpbGwtcnVsZT0iZXZlbm9kZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgc3Ryb2tlLW1pdGVybGltaXQ9IjIiIHZpZXdCb3g9IjAgMCAzNTUgNDAwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Im0zMTQuMjY5IDM5OS44NTVoLTI3My44MDljLTIyLjMzIDAtNDAuNDYtMTguMTI5LTQwLjQ2LTQwLjQ1OXYtMjczLjgxYzAtMjIuMzMgMTguMTI5LTQwLjQ1OSA0MC40Ni00MC40NmgzOC4yODVjLS4xNjgtLjgxNS0uMjU3LTEuNjU5LS4yNTctMi41MjR2LTMwLjE2NGMwLTYuODY1IDUuNTczLTEyLjQzOCAxMi40MzgtMTIuNDM4aDI0Ljg3N2M2Ljg2NSAwIDEyLjQzOCA1LjU3MyAxMi40MzggMTIuNDM4djMwLjE2NGMwIC44NjUtLjA4OSAxLjcwOS0uMjU3IDIuNTI0aDk5LjU1NWMtLjE2OC0uODE1LS4yNTctMS42NTktLjI1Ny0yLjUyNHYtMzAuMTY0YzAtNi44NjUgNS41NzQtMTIuNDM4IDEyLjQzOS0xMi40MzhoMjQuODc2YzYuODY1IDAgMTIuNDM4IDUuNTczIDEyLjQzOCAxMi40Mzh2MzAuMTY0YzAgLjg2My0uMDg4IDEuNzA1LS4yNTcgMi41MjRoMzcuNDkxYzIyLjMzNC4wMDQgNDAuNDYgMTguMTMyIDQwLjQ2IDQwLjQ2djI3My44MWMwIDIyLjMyOC0xOC4xMjYgNDAuNDU2LTQwLjQ2IDQwLjQ1OXptOS4yNjktMjc0LjE4OWgtMjkyLjM0N3YyNDEuMTUyaDI5Mi4zNDd6bS0xNDcuMzE2IDIxNC4yOTZjLTIuMDM0LS4yNDQtNC4wMDQtMS4xNDUtNS41NjQtMi43MDVsLTY2LjMzMy02Ni4zMzRjLTMuNjk3LTMuNjk2LTMuNjk3LTkuNjk5IDAtMTMuMzk1bDEzLjM5NS0xMy4zOTZjMy42OTctMy42OTYgOS42OTktMy42OTYgMTMuMzk1IDBsNDYuMjUxIDQ2LjI1MSA0Ni4yNDgtNDYuMjQ4YzMuNjk2LTMuNjk3IDkuNjk5LTMuNjk3IDEzLjM5NSAwbDEzLjM5NiAxMy4zOTVjMy42OTYgMy42OTcgMy42OTYgOS42OTkgMCAxMy4zOTZsLTY2LjMzNCA2Ni4zMzNjLTIuMTQxIDIuMTQxLTUuMDU2IDMuMDQyLTcuODQ5IDIuNzAzem0wLTg2LjUxOGMtMi4wMzQtLjI0My00LjAwNC0xLjE0NS01LjU2NC0yLjcwNWwtNjYuMzMzLTY2LjMzM2MtMy42OTctMy42OTctMy42OTctOS42OTkgMC0xMy4zOTZsMTMuMzk1LTEzLjM5NWMzLjY5Ny0zLjY5NiA5LjY5OS0zLjY5NiAxMy4zOTUgMGw0Ni4yNTEgNDYuMjUgNDYuMjQ4LTQ2LjI0OGMzLjY5Ni0zLjY5NiA5LjY5OS0zLjY5NiAxMy4zOTUgMGwxMy4zOTYgMTMuMzk2YzMuNjk2IDMuNjk2IDMuNjk2IDkuNjk5IDAgMTMuMzk1bC02Ni4zMzQgNjYuMzM0Yy0yLjE0MSAyLjE0MS01LjA1NiAzLjA0Mi03Ljg0OSAyLjcwMnoiIGZpbGw9IiNmZmYiLz48L3N2Zz4=';
				
		private $pro_shortcode_defaults = array(
			'hours' => '0700-1900',
			'id' => null,
			'regex' => false,
			'regex_pattern' => null,
			'regex_replacement' => null,
			'regex_scope' => null,
			'showfilter' => false,
			'subscribelink' => false,
			'zoom' => 0.6,
		);

		private $_api_base_url = 'https://icscalendar.com/?wc-api=wc-am-api';
		private $_dev_mode = false;
		
		public function __construct() {
				
			// Set admin file paths
			$this->admin_sidebar_path = plugin_dir_path(__FILE__) . 'templates/admin/pro_sidebar.php';
			$this->admin_user_guide_path = plugin_dir_path(__FILE__) . 'templates/admin/pro_user_guide.php';
	
			// Enqueue admin scripts
			add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
			
			// Set up admin menu
			add_action('admin_menu', array(&$this, 'admin_menu'));

			// Add admin notices
			add_action('admin_notices', array(&$this, 'admin_notices'));
			
			// Register custom post type
			add_action('init', array(&$this, 'custom_post_type'));
			
			// Load ACF
			add_action('plugins_loaded', array(&$this, 'acf_init'), 99);

			// Load GitLab Updater
			add_action('plugins_loaded', array(&$this, 'puc_init'), 99);

			// R34ICS filters
			add_filter('r34ics_display_calendar_args', array(&$this, 'display_calendar_args'), 10, 2);
			add_filter('r34ics_display_calendar_filter_ics_data', array(&$this, 'display_calendar_filter_ics_data'), 10, 1);
			add_filter('r34ics_display_calendar_range_end', array(&$this, 'display_calendar_range_end'), 10, 2);
			add_filter('r34ics_display_calendar_range_start', array(&$this, 'display_calendar_range_start'), 10, 2);
			add_filter('r34ics_display_calendar_set_first_date', array(&$this, 'display_calendar_set_first_date'), 10, 3);
			add_filter('r34ics_display_calendar_set_limit_date', array(&$this, 'display_calendar_set_limit_date'), 10, 3);
			add_filter('r34ics_display_calendar_set_earliest', array(&$this, 'display_calendar_set_earliest'), 10, 3);
			add_filter('r34ics_display_calendar_set_latest', array(&$this, 'display_calendar_set_latest'), 10, 4);
			add_filter('r34ics_display_calendar_set_latest_limitdayscustom', array(&$this, 'display_calendar_set_latest_limitdayscustom'), 10, 3);
			add_filter('r3417_event_description_html_filter', array(&$this, 'event_description_html_filter'), 10, 2);
			add_filter('r3417_event_label_html_filter', array(&$this, 'event_label_html_filter'), 10, 2);
			add_filter('r3417_event_sublabel_html_filter', array(&$this, 'event_sublabel_html_filter'), 10, 2);

			// R34ICS actions
			add_action('r34ics_admin_add_calendar_settings_html', array(&$this, 'admin_add_calendar_settings_html'), 10, 0);
			add_action('r34ics_color_key_html_after_feed_title', array(&$this, 'color_key_html_after_feed_title'), 10, 3);
			add_action('r34ics_display_calendar_after_render_template', array(&$this, 'display_calendar_after_render_template'), 10, 3);
			add_action('r34ics_display_calendar_before_render_template', array(&$this, 'display_calendar_before_render_template'), 10, 3);
			add_action('r34ics_display_calendar_after_wrapper', array(&$this, 'display_calendar_after_wrapper'), 10, 3);
			add_action('r34ics_display_calendar_before_wrapper', array(&$this, 'display_calendar_before_wrapper'), 10, 3);
			add_action('r34ics_display_calendar_render_template', array(&$this, 'display_calendar_render_template'), 10, 3);

			// Enqueue scripts
			add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
		
		}
		
		
		public function acf_init() {
			// Only load our version of ACF PRO if the full version is not already installed on the site
			if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {
				include_once(plugin_dir_path(__FILE__) . 'vendors/acf/acf.php');
				add_filter('acf/settings/url', function($url) {
					return plugin_dir_url(__FILE__) . '/vendors/acf/';
				});
				// Hide ACF editing screens (EXCEPT during development, OR if the site also has ACF free installed)
				if (empty($this->_dev_mode) && !file_exists(WPMU_PLUGIN_DIR . '/r34icspro-no-acf.php')) {
					add_filter('acf/settings/show_admin', function($show_admin) {
						return false;
					});
				}
				// Save ACF JSON to our path (ONLY during development)
				if (!empty($this->_dev_mode)) {
					add_filter('acf/settings/save_json', function($path) {
						return plugin_dir_path(__FILE__) . 'assets/acf-json';
					});
				}
			}
			// Add our ACF JSON loading path
			add_filter('acf/settings/load_json', function($paths) {
				$paths[] = plugin_dir_path(__FILE__) . 'assets/acf-json';
				return $paths;
			});
		}
		
		
		public function admin_add_calendar_settings_html() {
			include_once(plugin_dir_path(__FILE__) . 'templates/admin/add_calendar_settings_html.php');
		}


		public function admin_enqueue_scripts() {
			wp_enqueue_script('ics-calendar-pro-admin', plugin_dir_url(__FILE__) . 'assets/admin-script.js', array('jquery'));
			wp_enqueue_style('ics-calendar-pro-admin', plugin_dir_url(__FILE__) . 'assets/admin-style.css', false, $this->version);
			// Hide "Add New" buttons if unlicensed
			if (!$this->licensed()) {
				wp_add_inline_style('ics-calendar-pro-admin', 'a[href*="post-new.php?post_type=r34icspro_calendar"] { display: none !important; }');
			}
		}
		
		
		public function admin_menu() {
			if (post_type_exists('r34icspro_calendar')) {
				add_submenu_page(
					'edit.php?post_type=r34icspro_calendar',
					'ICS Calendar Pro Settings &amp; Tools',
					'Settings &amp; Tools',
					'edit_posts',
					'ics-calendar-pro-settings',
					array(&$this, 'admin_settings')
				);
			}
			// Hide "Add New" option if unlicensed
			if (!$this->licensed()) {
				remove_submenu_page('edit.php?post_type=r34icspro_calendar','post-new.php?post_type=r34icspro_calendar');
			}
		}
		
		
		public function admin_notices() {
		
			// Only show these to administrators!
			if (current_user_can('manage_options')) {
			
				// Check expirations
				if ($this->license_modified()) {
					?>
					<div class="notice notice-error" style="background-image: url('<?php echo plugin_dir_url(__FILE__); ?>assets/ics-calendar-pro-icon.svg'); background-position: left 1em top 1em; background-repeat: no-repeat; background-size: 4em; min-height: 6em; padding-left: 6em;">
						<p>Due to detected site configuration changes, your <strong>ICS Calendar Pro</strong> license needs to be re-entered. Please check your order confirmation email or <a href="https://icscalendar.com/my-account/api-keys/" target="_blank">log into your account</a> to obtain your license key, then <a href="<?php echo admin_url('edit.php?post_type=r34icspro_calendar&page=ics-calendar-pro-settings#license'); ?>">enter it here</a>. Thank you!</p>
					</div>
					<?php
				}
				elseif (!$this->licensed()) {
					if ($this->license_expired()) {
						?>
						<div class="notice notice-error" style="background-image: url('<?php echo plugin_dir_url(__FILE__); ?>assets/ics-calendar-pro-icon.svg'); background-position: left 1em top 1em; background-repeat: no-repeat; background-size: 4em; min-height: 6em; padding-left: 6em;">
							<p>Your <strong>ICS Calendar Pro</strong> license has <strong style="color: red;">expired</strong>. Please <a href="https://icscalendar.com/shop/" target="_blank">renew your license</a> and update to the latest version to continue using this plugin. Existing calendars will continue to function, but you will no longer receive support/updates, and access to some admin tools may be restricted without an active license. Thank you!</p>
						</div>
						<?php
					}
					else {
						?>
						<div class="notice notice-error" style="background-image: url('<?php echo plugin_dir_url(__FILE__); ?>assets/ics-calendar-pro-icon.svg'); background-position: left 1em top 1em; background-repeat: no-repeat; background-size: 4em; min-height: 6em; padding-left: 6em;">
							<p>Your copy of <strong>ICS Calendar Pro</strong> is <strong style="color: red;">unlicensed</strong>. Without a valid license, some features will be limited and you will not have access to plugin updates or support.</p>
							<p><strong>If you have a license, please <a href="<?php echo admin_url('edit.php?post_type=r34icspro_calendar&page=ics-calendar-pro-settings#license'); ?>">enter it here</a>.</strong></p>
							<p>If you do not yet have a license, please purchase one at <a href="https://icscalendar.com/shop/" target="_blank">icscalendar.com</a> to continue using this plugin. Thank you!</p>
						</div>
						<?php
					}
				}
			
				// Check ICS Calendar version
				$current_r34ics_version = get_option('r34ics_version');
				if (version_compare($current_r34ics_version, $this->required_r34ics_version) < 0) {
					?>
					<div class="notice notice-error">
						<p><strong>ICS Calendar Pro</strong> <?php echo $this->version; ?> requires <strong>ICS Calendar</strong> version <?php echo $this->required_r34ics_version; ?> or later, but you are currently running version <?php echo $current_r34ics_version; ?>. Please <a href="plugins.php?s=ICS+Calendar">update your ICS Calendar plugin</a> to the newest version.</p>
					</div>
					<?php
				}
			
			}
			
		}
		
		
		public function admin_settings() {
		
			// Deactivate license
			if (isset($_POST['r34icspro-deactivate-license-nonce']) && wp_verify_nonce($_POST['r34icspro-deactivate-license-nonce'],'r34icspro')) {
				echo $this->_delete_license();
			}
			
			// Verify and activate license
			elseif (isset($_POST['r34icspro-activate-license-nonce']) && wp_verify_nonce($_POST['r34icspro-activate-license-nonce'],'r34icspro')) {
				echo $this->_set_license();
			}
			
			// Save admin settings
			elseif (isset($_POST['r34icspro-admin-options-nonce']) && wp_verify_nonce($_POST['r34icspro-admin-options-nonce'],'r34icspro')) {
			
				// bypass_no_acf
				if (!empty($_POST['bypass_no_acf'])) {
					update_option('r34icspro_bypass_no_acf', true);
					r34icspro_mu_plugins_uninstall();
				}
				else {
					update_option('r34icspro_bypass_no_acf', false);
					r34icspro_mu_plugins_install();
				}
				
				// precache
				update_option('r34icspro_precache', !empty($_POST['precache']));
				
				// memory_limit and max_execution_time
				if (!empty($_POST['precache'])) {
					$memory_limit_int = intval($_POST['memory_limit']);
					$max_execution_time_int = intval($_POST['max_execution_time']);
					if (!empty($memory_limit_int)) {
						update_option('r34icspro_memory_limit', $memory_limit_int . 'M');
					}
					else {
						delete_option('r34icspro_memory_limit');
					}
					if (!empty($max_execution_time_int)) {
						update_option('r34icspro_max_execution_time', $max_execution_time_int);
					}
					else {
						delete_option('r34icspro_max_execution_time');
					}
				}
				else {
					delete_option('r34icspro_memory_limit');
					delete_option('r34icspro_max_execution_time');
				}
				
				// transient_expiration
				if (!empty($_POST['transient_expiration'])) {
					update_option('r34ics_transient_expiration', intval($_POST['transient_expiration']));
				}
				
				?>
				<div class="notice notice-success">
					<p>Settings updated.</p>
				</div>
				<?php

			}
			
			// URL tester
			elseif (isset($_POST['r34icspro-url-tester-nonce']) && wp_verify_nonce($_POST['r34icspro-url-tester-nonce'],'r34icspro')) {
			
				if ($url_to_test = filter_input(INPUT_POST, 'url_to_test', FILTER_SANITIZE_URL)) {
					$url_tester_result = r34ics_url_get_contents($url_to_test);
				}
			
			}
		
			// Render template
			include(plugin_dir_path(__FILE__) . 'templates/admin/settings.php');

		}
	
	
		public function admin_shortcode_metabox($post=null) {
			$this->admin_shortcode_display($post);
			?>
			<p class="description">Copy and paste this shortcode into your content wherever you would like this calendar to display.</p>
			<?php
		}
		
		
		public function admin_shortcode_display($post=null) {
			$id = is_object($post) && isset($post->ID) ? $post->ID : intval($post);
			if (!empty($id)) {
				?>
				<div><input type="text" readonly="readonly" onclick="this.select();" value="[ics_calendar id=&quot;<?php echo intval($id); ?>&quot;]" /><span class="icon copy button"><span class="dashicons dashicons-admin-page" title="Copy to clipboard..."></span></span></div>
				<?php
			}
		}
		
		
		public function admin_support_sidebar() {
			include_once($this->admin_sidebar_path);
		}
		
		
		public function color_key_html_after_feed_title($feed_key, $args, $ics_data) {
			if (!empty($args['subscribelink']) && is_array($ics_data['urls']) && $feed_url = $ics_data['urls'][$feed_key]) {
				echo '&nbsp;&nbsp;' . $this->feed_subscribe_link($feed_url);
			}
		}
		
		
		public function custom_post_type() {
		
			// ICS Calendar Pro CPT
			register_post_type('r34icspro_calendar', array(
				'labels'			=>	array(
											'name'               => 'ICS Calendar',
											'singular_name'      => 'ICS Calendar',
											'menu_name'          => 'ICS Calendar',
											'name_admin_bar'     => 'ICS Calendar',
											'add_new'            => 'Add New',
											'add_new_item'       => 'Add New ICS Calendar',
											'new_item'           => 'New ICS Calendar',
											'edit_item'          => 'Edit ICS Calendar',
											'view_item'          => 'View ICS Calendar',
											'all_items'          => 'All ICS Calendars',
											'search_items'       => 'Search ICS Calendars',
											'parent_item_colon'  => false,
											'not_found'          => 'No ICS Calendars found.',
											'not_found_in_trash' => 'No ICS Calendars found in Trash.',
										),
				'capability_type'	=>	'post',
				'description'		=>	'Saved custom calendar views created with ICS Calendar Pro.',
				'has_archive'		=>	false,
				'hierarchical'		=>	false,
				'menu_icon'			=>	$this->icon_logo,
				'menu_position'		=>	58,
				'public'			=>	false,
				'show_ui'			=>	true,
				'supports'          =>	array('title', 'revisions'),
			));
			
			// Meta boxes for editing screen
			add_action('add_meta_boxes', function() {
				global $post;
				
				// Shortcode
				add_meta_box(
					'r34icspro-shortcode',
					'Shortcode',
					array(&$this, 'admin_shortcode_metabox'),
					'r34icspro_calendar',
					'normal',
					'high'
				);
				
				// Support sidebar
				add_meta_box(
					'r34icspro-support',
					'ICS Calendar Pro',
					array(&$this, 'admin_support_sidebar'),
					'r34icspro_calendar',
					'side',
					'low'
				);
				
			});
			
			// Custom admin column to display shortcodes
			add_filter('manage_r34icspro_calendar_posts_columns', function($d) {
				$offset = 2;
				$d1 =	array_slice($d, 0, $offset, true) +
						array('r34icspro_admin_columns_shortcode' => 'Shortcode') +
						array_slice($d, $offset, null, true);
				return $d1;
			});
			add_action('manage_r34icspro_calendar_posts_custom_column', function ($c, $id) {
				if ($c == 'r34icspro_admin_columns_shortcode') {
					$this->admin_shortcode_display($id);
				}
			}, 10, 2);
			
		}


		public function display_calendar_after_render_template($view, $args, $ics_data) {
			return;
		}
		
		
		public function display_calendar_after_wrapper($view, $args, $ics_data) {
			if (!empty($args['subscribelink']) && is_array($ics_data['urls']) && count((array)$ics_data['urls']) == 1) {
				echo $this->feed_subscribe_link($ics_data['urls'][0]);
			}
			return;
		}
		
		
		public function display_calendar_args($args, $atts) {
			foreach ((array)$this->pro_shortcode_defaults as $key => $value) {
				$args[$key] = isset($atts[$key]) ? $atts[$key] : $value;
			}
			// Retrieve saved arguments and fill in any that have not already been set
			if (!empty($args['id']) && function_exists('get_field')) {
				if (get_post_type($args['id']) == 'r34icspro_calendar' && get_post_status($args['id']) == 'publish' && $params = get_fields($args['id'])) {
					
					// Build array of arguments we've set
					// These will always be set before we loop through remaining settings
					$args_set = array('description', 'limitdays', 'pastdays', 'startdate', 'title', 'view', 'eventdesc'); 
				
					// Set feed URL, color and feed labels
					$url = array();
					$color = array();
					$feedlabel = array();
					foreach ((array)$params['feeds'] as $feed) {
						$url[] = $feed['url'];
						$color[] = $feed['color'];
						$feedlabel[] = $feed['feedlabel'];
					}
					if (empty($args['url'])) { $args['url'] = implode(' ', $url); $args_set[] = 'url'; }
					if (empty($args['color'])) { $args['color'] = implode(' ', $color); $args_set[] = 'color'; }
					if (empty($args['feedlabel'])) { $args['feedlabel'] = implode('|', $feedlabel); $args_set[] = 'feedlabel'; }
					
					// Customize title and description
					if (!empty($params['customize_title_and_description'])) {
						if (empty($args['title'])) { $args['title'] = $params['title']; }
						if (empty($args['description'])) { $args['description'] = $params['description']; }
					}
					
					// Force settings that don't default to null in the base plugin (these can't be overridden in shortcode if loading saved calendar)
					if (!empty($params['formatmonthyear'])) { $args['formatmonthyear'] = $params['formatmonthyear']; }
					if (!empty($params['limitdays'])) { $args['limitdays'] = $params['limitdays']; }
					if (!empty($params['view'])) { $args['view'] = $params['view']; }
					
					// Start on date (with backwards compatibility for start date without start_on present)
					if (isset($params['start_on'])) {
						switch ($params['start_on']) {
							case 'pastdays': $args['pastdays'] = $params['pastdays']; break;
							case 'startdate': $args['startdate'] = $params['startdate']; break;
							default: $args['pastdays'] = $args['startdate'] = null; break;
						}
					}
					elseif (!empty($params['startdate'])) {
						$args['startdate'] = $params['startdate'];
					}

					// Hours (start/end times)
					if (empty($args['hours']) && (!empty($params['hours_start']) || !empty($params['hours_end']))) {
						if (empty($params['hours_start'])) { $params['hours_start'] = '0700'; }
						if (empty($params['hours_end'])) { $params['hours_end'] = '0700'; }
						$args['hours'] = $params['hours_start'] . '-' . $params['hours_end']; $args_set[] = 'hours';
					}
					
					// Event descriptions (optional excerpts)
					if (!empty($params['eventdesc'])) {
						$args['eventdesc'] = !empty($params['eventdesc_length']) ? $params['eventdesc_length'] : $params['eventdesc'];
					}
					
					// Regular expressions
					if (empty($params['regex']) || empty(trim($args['regex_pattern'])) || empty(trim($args['regex_replacement'])) || empty($args['regex_scope'])) {
						$args['regex'] = $args['regex_pattern'] = $args['regex_replacement'] = $args['regex_scope'] = null;
					}
					else {
						$args['regex'] = !empty($params['regex']);
						$args['regex_pattern'] = $params['regex_pattern'];
						$args['regex_replacement'] = $params['regex_replacement'];
						$args['regex_scope'] = !is_array($params['regex_scope']) ? explode('|',$params['regex_scope']) : $params['regex_scope'];
					}
					
					// Apply all remaining settings
					foreach ((array)$args as $arg_k => $arg_v) {
						if (empty($arg_v) && !empty($params[$arg_k]) && !in_array($arg_k, $args_set)) {
							$args[$arg_k] = $params[$arg_k];
							$args_set[] = $arg_k;
						}
					}
				}
			}
			return $args;
		}
		
		
		public function display_calendar_before_render_template($view, $args, $ics_data) {
		}
		
		
		public function display_calendar_before_wrapper($view, $args, $ics_data) {
			// Show filter
			if (!empty($args['showfilter'])) {
				?>
				<div class="ics-calendar-filter">
					<label><?php _e('Show events matching:','r34icspro'); ?>
						<input type="text" class="ics-calendar-filter-text" />
						<input type="reset" />
						<span class="ics-calendar-filter-status"></span>
					</label>
				</div>
				<?php
			}
		}
		
		
		public function display_calendar_filter_ics_data($ics_data) {
			return $ics_data;
		}
		
		
		public function display_calendar_range_end($range_end, $args) {
			extract(array_merge($this->shortcode_defaults, $args));
			switch ($view) {

				case 'year-with-sidebar':
					$range_end = (intval(substr($range_end,0,4))+1) . '/01/01'; // Range needs extra day to account for timezones
					break;
					
				case 'grid':
				case 'month-with-sidebar':
				case 'widget':
				default:
					break;

			}
			return $range_end;
		}
		
		
		public function display_calendar_range_start($range_start, $args) {
			extract(array_merge($this->shortcode_defaults, $args));
			switch ($view) {

				case 'year-with-sidebar':
					$range_start = (intval(substr($range_start,0,4))-1) . '/12/31'; // Range needs extra day to account for timezones
					break;
					
				case 'grid':
				case 'month-with-sidebar':
				case 'widget':
				default:
					break;

			}
			return $range_start;
		}
		
		
		public function display_calendar_render_template($view, $args, $ics_data) {
			extract(array_merge($this->shortcode_defaults, $args));
			switch ($view) {

				case 'grid':
					// Set view-specific variables
					$all_day_events_max = r34icspro_all_day_events_max($ics_data['events']);
					$days_count = r34icspro_days_count($ics_data['events']);
					$feed_count = is_array($url) ? count(explode(' ', $url)) : 0;
					$hours = !empty($hours) ? explode('-',$hours) : array('0000','2300');
					$ics_calendar_classes = array(
						'ics-calendar',
						(!empty($args['hidetimes']) ? ' hide_times' : ''),
						(!empty($args['toggle']) ? ' r34ics_toggle' : ''),
						(count((array)$ics_data['urls']) > 1 ? ' multi-feed' : ''),
					);
					$zoom = floatval($zoom);
					
					// Fix hours
					// @todo Reformat hours parameter to remove minutes altogether
					foreach ((array)$hours as $key => $hour) {
						// Force integer
						$hour = intval($hour);
						// Don't allow partial hours
						if ($hour % 100 != 0) {
							// + ($key * 100) is effectively a floor() for [0] and ceil() for [1]
							$hour = $hour - ($hour % 100) + ($key * 100);
						}
						// Don't allow invalid hours
						if ($hour < 0) { $hour = 0; }
						if ($hour > 2400) { $hour = 2400; }
						// Apply processing back to array
						$hours[$key] = $hour;
					}
					// Don't allow end to be before start (force at least 1 hour)
					if ($hours[1] < $hours[0]) { $hours[1] = $hours[0] + 100; }
					
					// Render view
					include(plugin_dir_path(__FILE__) . 'templates/calendar-grid.php');
					break;
				
				case 'month-with-sidebar':
					// Render view
					include(plugin_dir_path(__FILE__) . 'templates/calendar-month-with-sidebar.php');
					break;
					
				case 'widget':
					// Render view
					include(plugin_dir_path(__FILE__) . 'templates/calendar-widget.php');
					break;
					
				case 'year-with-sidebar':
					// Render view
					include(plugin_dir_path(__FILE__) . 'templates/calendar-year-with-sidebar.php');
					break;
					
				default:
					break;

			}
		}
		
		
		public function display_calendar_set_earliest($view, $first_date) {
			switch ($view) {
				case 'grid':
					$earliest = $first_date;
					break;
				case 'month-with-sidebar':
				case 'widget':
					$earliest = substr($first_date,0,6);
					break;
				case 'year-with-sidebar':
					$earliest = substr($first_date,0,4) . '01';
				default:
					break;
			}
			return $earliest;
		}
			
	
		public function display_calendar_set_first_date($view, $first_date, $startdate=null, $pastdays=null) {
			switch ($view) {
				case 'grid':
					if (!empty($startdate) && intval($startdate) > 20000000) {
						$first_date = $startdate;
					}
					else {
						$first_date = date('Ymd');
					}
					break;
				case 'month-with-sidebar':
				case 'widget':
					if (!empty($startdate) && intval($startdate) > 20000000) {
						$first_date = $startdate;
					}
					elseif (!empty($pastdays)) {
						$first_date = date('Ymd',gmmktime(0,0,0,date('n'),date('j')-$pastdays,date('Y')));
					}
					else {
						$first_date = date('Ymd', r34ics_first_day_of_current('month'));
					}
					break;
				case 'year-with-sidebar':
					if (!empty($startdate) && intval($startdate) > 20000000) {
						$first_date = substr($startdate,0,4) . '0101';
					}
					else {
						$first_date = date('Y') . '0101';
					}
					break;
				default:
					break;
			}
			return $first_date;
		}
		
		
		public function display_calendar_set_latest($view, $max_date, $first_ts, $limitdays=null, $limitdayscustom=null) {
			switch ($view) {
				case 'grid':
					$latest = !empty($limitdayscustom) ? date('Ymd',gmmktime(0,0,0,date('n',$first_ts),date('j',$first_ts)+($limitdays-1),date('Y',$first_ts))) : $max_date;
					break;
				case 'month-with-sidebar':
				case 'widget':
					$latest = !empty($limitdayscustom) ? date('Ym',gmmktime(0,0,0,date('n',$first_ts),date('j',$first_ts)+($limitdays-1),date('Y',$first_ts))) : substr($max_date,0,6);
					break;
				case 'year-with-sidebar':
					$latest = !empty($limitdayscustom) ? date('Y',gmmktime(0,0,0,date('n',$first_ts),date('j',$first_ts)+($limitdays-1),date('Y',$first_ts))) . '12' : substr($max_date,0,4) . '12';
				default:
					break;
			}
			return $latest;
		}
			
	
		public function display_calendar_set_limit_date($view, $first_ts, $limitdays) {
			$limit_date = null;
			switch ($view) {
				case 'grid':
					$limit_date = date('Ymd', gmmktime(0,0,0,date('n',$first_ts),date('j',$first_ts)+($limitdays-1),date('Y',$first_ts)));
					break;
				case 'month-with-sidebar':
				case 'widget':
					$limit_date = date('Ymd', gmmktime(0,0,0,date('n',$first_ts),date('j',$first_ts)+($limitdays-1),date('Y',$first_ts)));
					break;
				case 'year-with-sidebar':
					$limit_date = date('Ymd', gmmktime(0,0,0,date('n',$first_ts),date('j',$first_ts)+($limitdays-1),date('Y',$first_ts)));
					// Push to end of year
					$limit_date = substr($limit_date,0,4) . '1231';
				default:
					break;
			}
			return $limit_date;
		}
					
	
		public function enqueue_scripts() {
			wp_enqueue_script('ics-calendar-pro', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'));
			wp_enqueue_style('ics-calendar-pro', plugin_dir_url(__FILE__) . 'assets/style.css', array('ics-calendar'), $this->version);
		}
		
		
		public function event_description_html_filter($descloc_content, $args) {
			if (!empty($args['regex']) && in_array('event_description_html', $args['regex_scope'])) {
				$descloc_content = preg_replace($args['regex_pattern'], $args['regex_replacement'], $descloc_content);
			}
			return $descloc_content;
		}
		
		
		public function event_label_html_filter($title_content, $args) {
			if (!empty($args['regex']) && in_array('event_label_html', $args['regex_scope'])) {
				$title_content = preg_replace($args['regex_pattern'], $args['regex_replacement'], $title_content);
			}
			return $title_content;
		}
		
		
		public function event_sublabel_html_filter($sublabel_content, $args) {
			if (!empty($args['regex']) && in_array('event_sublabel_html', $args['regex_scope'])) {
				$sublabel_content = preg_replace($args['regex_pattern'], $args['regex_replacement'], $sublabel_content);
			}
			return $sublabel_content;
		}
		
		
		public function feed_subscribe_link($feed_url) {
			if (filter_var($feed_url, FILTER_VALIDATE_URL)) {
				return '<a href="' . esc_url($feed_url) . '" download="download" class="button subscribe">Subscribe</a>';
			}
		}


		public function puc_init() {
			// Check if in admin
			if (!is_admin()) { return false; }
			// Do not activate GitLab Updater if unlicensed
			if (!$this->licensed()) { return false; }
			if (!is_plugin_active('plugin-update-checker/plugin-update-checker.php')) {
				require_once(plugin_dir_path(__FILE__) . 'vendors/plugin-update-checker/plugin-update-checker.php');
			}
			$license = $this->_get_license();
			$updateChecker = Puc_v4_Factory::buildUpdateChecker(
				'https://api.icscalendar.com/update.json.php?licensed=' . $this->licensed() .
					'&api_key=' . $license['key'] .
					'&instance=' . $license['instance'] .
					'&product_id=' . $license['product_id'],
				plugin_dir_path(__FILE__) . 'ics-calendar-pro.php',
				'ics-calendar-pro'
			);
			add_filter('puc_request_info_result-ics-calendar-pro', function($pluginInfo, $result) {
				$pluginInfo->icons = array(
					'x2' => 'https://api.icscalendar.com/assets/icon-256x256.png',
					'svg' => 'https://api.icscalendar.com/assets/icon.svg',
				);
				return $pluginInfo;
			}, 10, 2);
		}
		
		
		/* License handling methods */
		public function licensed() { return $this->_verify_license(); } // Copy is currently licensed
		public function license_expired() { return $this->_get_license() && !$this->_verify_license(); } // Copy *was* licensed but expired
		
		
		public function license_expires($format='timestamp') {
			$license = $this->_get_license();
			$exp = null;
			switch ($format) {
				case 'display':
					if (empty($license)) {
						$exp = 'unlicensed';
					}
					elseif ($license['exp'] == 0) {
						$exp = 'never';
					}
					else {
						$exp = wp_date(get_option('date_format'), $license['exp']);
					}
					break;
				case 'timestamp':
				default:
					$exp = $license['exp']; break;
			}
			return $exp;
		}
		
		
		public function license_modified() {
			$modified = false;
			if ($license = $this->_get_license()) {
				// Compare license values with current site values
				if (!empty($license['blog_id'])) { switch_to_blog($license['blog_id']); }
				if ($license['instance'] != get_option('r34icspro_instance')) { $modified = true; }
				elseif ($license['object'] != parse_url(get_bloginfo('url'), PHP_URL_HOST)) { $modified = true; }
				if (!empty($license['blog_id'])) { restore_current_blog(); }
				// License has been modified! Delete it!
				if (!empty($modified)) {
					$this->_delete_license($license);
				}
			}
			return $modified;
		}

		
		private function _delete_license($license=null) {
			if ($api_response_json = $this->_license_api_get_json('deactivate')) {
				if (!empty($api_response_json->success)) {
					?>
					<div class="notice notice-info">
						<p>Your <strong>ICS Calendar Pro</strong> license has been deactivated.</p>
					</div>
					<?php
				}
				elseif (!empty($api_response_json->error)) {
					?>
					<div class="notice notice-warning">
						<p>Your <strong>ICS Calendar Pro</strong> license has been deactivated locally. However, the licensing server returned an error:</p>
						<p><code><?php echo $api_response_json->error; ?></code></p>
						<p>This may be due to the license activation having already been deleted on the server. Please log into <a href="https://icscalendar.com/my-account/api-keys/" target="_blank">your account</a> if you have any concerns.</p>
					</div>
					<?php
				}
				delete_option('r34icspro_license');
				delete_transient('R34ICSPro::_verify_license');
				// Handle multisite
				if (function_exists('get_sites') && $sites = get_sites()) {
					foreach ((array)$sites as $site) {
						switch_to_blog($site->blog_id);
						delete_option('r34icspro_license');
						delete_transient('R34ICSPro::_verify_license');
						restore_current_blog();
					}
				}
			}
			else {
				?>
				<div class="notice notice-error">
					<p>Could not retrieve license data. If this problem persists, please <a href="https://icscalendar.com/pro-support/" target="_blank">contact customer support</a>.</p>
				</div>
				<?php
			}
		}


		private function _get_license() {
			$license = get_option('r34icspro_license');
			// Check multisite if no license found
			if (empty($license) && function_exists('get_sites') && $sites = get_sites()) {
				foreach ((array)$sites as $site) {
					switch_to_blog($site->blog_id);
					$license = get_option('r34icspro_license');
					restore_current_blog();
					if (!empty($license)) { break; }
				}
			}
			return !empty($license) ? unserialize(base64_decode($license)) : false;
		}
		
		
		private function _license_api_get_json($action, $api_key=null, $product_id=null, $instance=null, $object=null) {
			if (!empty($action)) {
				$license = $this->_get_license();
				if (empty($api_key)) { $api_key = $license['key']; }
				if (empty($product_id)) { $product_id = $license['product_id']; }
				if (empty($instance)) { $instance = $license['instance']; }
				if (empty($object)) { $object = $license['object']; }
				$api_request_url =	$this->_api_base_url . '&wc_am_action=' . $action .
									'&api_key=' . $api_key .
									'&instance=' . $instance .
									'&object=' . $object .
									'&plugin_name=ics-calendar-pro/ics-calendar-pro.php' .
									'&product_id=' . $product_id .
									'&version=' . $this->version;
				if ($api_response = r34ics_url_get_contents($api_request_url)) {
					return json_decode($api_response);
				}
			}
			return false;
		}

		
		private function _set_license() {
			delete_transient('R34ICSPro::_verify_license');
			// Connect to API and activate license
			$api_key = filter_input(INPUT_POST, 'api_key', FILTER_SANITIZE_STRING);
			$product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
			$instance = get_option('r34icspro_instance');
			$object = parse_url(get_bloginfo('url'), PHP_URL_HOST);
			if ($api_response_json = $this->_license_api_get_json('activate', $api_key, $product_id, $instance, $object)) {
				if (!empty($api_response_json->success)) {
					$license = array(
						'key' => $api_key,
						'product_id' => intval($product_id),
						'instance' => $instance,
						'object' => $object,
						'exp' => $api_response_json->data->resources[0]->access_expires,
					);
					// Add blog_id to license if multisite
					if (function_exists('get_sites')) {
						$license['blog_id'] = get_current_blog_id();
					}
					update_option('r34icspro_license', base64_encode(serialize($license)));
					?>
					<div class="notice notice-success">
						<p>Your copy of <strong>ICS Calendar Pro</strong> is now licensed. All features are enabled and you will receive access to updates and support<?php /* through <strong><?php echo $this->get_exp_display(); ?>*/ ?>.</strong></p>
					</div>
					<?php
				}
				elseif (!empty($api_response_json->error)) {
					?>
					<div class="notice notice-error">
						<p>The licensing server returned the following error:</p>
						<p><code><?php echo str_replace('No API resources exist.', 'Invalid license details.', $api_response_json->error); ?></code></p>
					</div>
					<?php
				}
			}
		}

		
		private function _verify_license($license=null) {
			$licensed = null;
			if ($transient = get_transient('R34ICSPro::_verify_license')) { return $transient; }
			if ($api_response_json = $this->_license_api_get_json('status')) {
				if (!empty($api_response_json->success)) {
					$licensed = ($api_response_json->status_check == 'active');
				}
				elseif (!empty($api_response_json->error)) {
					$licensed = false;
				}
			}
			set_transient('R34ICSPro::_verify_license', $licensed);
			return $licensed;
		}


	}
}