<?php
    
    namespace Wpo\Core;

    use \Wpo\Core\Permissions_Helpers;

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die( );

    if ( !class_exists( '\Wpo\Core\Wp_Hooks' ) ) {
    
        class Wp_Hooks {

            public static function add_wp_hooks() {

                // Do super admin stuff
                if ( ( is_admin() || is_network_admin() ) && Permissions_Helpers::user_is_admin( \wp_get_current_user() ) ) {

                    if ( class_exists( '\Wpo\Core\EDD_SL_Plugin_Updater' ) ) {
                        add_action( 'admin_init', '\Wpo\Core\EDD_SL_Plugin_Updater::check_for_updates' );
                    }

                    if ( class_exists( '\Wpo\Sync\Sync_Admin_Page' ) ) {
                        add_action( 'admin_menu', '\Wpo\Sync\Sync_Admin_Page::add_plugin_page', 10 );
                        add_action( 'init', '\Wpo\Sync\Sync_Admin_Page::init', 10, 0 );
                    }

                    // Add and hide wizard (page)
                    add_action( 'admin_menu', '\Wpo\Pages\Wizard_Page::add_management_page' );
                    add_action( 'network_admin_menu', '\Wpo\Pages\Wizard_Page::add_management_page' );

                    // Show admin notification when WPO365 not properly configured
                    add_action( 'admin_notices', '\Wpo\Services\Notifications_Service::show_admin_notices', 10, 0 );
                    add_action( 'network_admin_notices', '\Wpo\Services\Notifications_Service::show_admin_notices', 10, 0 );
                    add_action( 'admin_init', '\Wpo\Services\Notifications_Service::dismiss_admin_notices', 10, 0 );

                    // Wire up AJAX backend services
                    add_action( 'wp_ajax_delete_tokens', '\Wpo\Services\Ajax_Service::delete_tokens' );
                    add_action( 'wp_ajax_get_settings', '\Wpo\Services\Ajax_Service::get_settings' );
                    add_action( 'wp_ajax_update_settings', '\Wpo\Services\Ajax_Service::update_settings' );
                    add_action( 'wp_ajax_activate_license', '\Wpo\Services\Ajax_Service::activate_license' );
                    add_action( 'wp_ajax_get_log', '\Wpo\Services\Ajax_Service::get_log' );
                    add_action( 'wp_ajax_get_self_test_results', '\Wpo\Services\Ajax_Service::get_self_test_results' );
                    
                    // Show settings link
                    add_filter( ( is_network_admin() ? 'network_admin_' : '' ) . 'plugin_action_links_' . $GLOBALS[ 'WPO_CONFIG' ][ 'plugin' ], '\Wpo\Core\Plugin_Helpers::get_configuration_action_link', 10, 1 );
                }

                // Add custom cron schedule for user sync
                if ( class_exists( '\Wpo\Core\Cron_Helpers' ) ) {
                    // Filter to add custom cron schedules
                    add_filter( 'cron_schedules', '\Wpo\Core\Cron_Helpers::add_cron_schedules', 10, 1 );
                }

                // Hooks used by cron jobs to schedule user synchronization events
                if ( class_exists( '\Wpo\Sync\Sync_Manager' ) ) {
                    add_action( 'wpo_sync_users', '\Wpo\Sync\Sync_Manager::fetch_users', 10, 3 );
                    add_action( 'wpo_sync_users_start', '\Wpo\Sync\Sync_Manager::fetch_users', 10, 2 );
                }
               
                // Ensure session is valid and remains valid
                add_action( 'destroy_wpo365_session', '\Wpo\Services\Authentication_Service::destroy_session' );

                // Prevent email address update
                add_action( 'personal_options_update', '\Wpo\Core\Permissions_Helpers::prevent_email_change', 10, 1 );

                // Add short code(s)
                add_action( 'init', 'Wpo\Core\Shortcode_Helpers::ensure_pintra_short_code' );
                add_action( 'init', 'Wpo\Core\Shortcode_Helpers::ensure_display_error_message_short_code' );
                add_action( 'init', 'Wpo\Core\Shortcode_Helpers::ensure_login_button_short_code' );
                add_action( 'init', 'Wpo\Core\Shortcode_Helpers::ensure_login_button_short_code_V2' );

                // Wire up AJAX backend services
                add_action( 'wp_ajax_get_tokencache', '\Wpo\Services\Ajax_Service::get_tokencache' );
                add_action( 'wp_ajax_cors_proxy', '\Wpo\Services\Ajax_Service::cors_proxy' );

                // Clean up on shutdown
                add_action( 'shutdown', '\Wpo\Services\Request_Service::shutdown' );

                // Add pintraredirectjs
                add_action( 'wp_enqueue_scripts', '\Wpo\Core\Script_Helpers::enqueue_pintra_redirect', 10, 0 );
                add_action( 'login_enqueue_scripts', '\Wpo\Core\Script_Helpers::enqueue_pintra_redirect', 10, 0 );
                add_action( 'admin_enqueue_scripts', '\Wpo\Core\Script_Helpers::enqueue_pintra_redirect', 10, 0 );

                // Adds the login button
                add_action( 'login_form', '\Wpo\Core\Shortcode_Helpers::login_button', 10, 1 );

                if ( class_exists( '\Wpo\Services\User_Custom_Fields_Service' ) ) {
                    // Add extra user profile fields
                    add_action( 'show_user_profile', '\Wpo\Services\User_Custom_Fields_Service::show_extra_user_fields', 10, 1 );
                    add_action( 'edit_user_profile', '\Wpo\Services\User_Custom_Fields_Service::show_extra_user_fields', 10, 1 );
                    add_action( 'personal_options_update', '\Wpo\Services\User_Custom_Fields_Service::save_user_details', 10, 1 );
                    add_action( 'edit_user_profile_update', '\Wpo\Services\User_Custom_Fields_Service::save_user_details', 10, 1 );
                }

                if ( class_exists( '\Wpo\Services\Login_Service' ) ) {
                    // Prevent WP default login for O365 accounts
                    add_action( 'wp_authenticate', '\Wpo\Services\Login_Service::prevent_default_login_for_o365_users', 11, 1 );
                }

                if ( class_exists( '\Wpo\SCIM\SCIM_Controller' ) ) {
                    // Init the custom REST API for SCIM
                    add_action( 'rest_api_init', function () {
                        $scim_controller = new \Wpo\SCIM\SCIM_Controller();
                        $scim_controller->register_routes();
                    } );
                }

                if ( class_exists( '\Wpo\Services\BuddyPress_Service' ) ) {
                    // Add extra user profile fields to Buddy Press
                    add_action( 'bp_after_profile_loop_content', '\Wpo\Services\BuddyPress_Service::bp_show_extra_user_fields', 10, 1 );
                    // Replace avatar with O365 avatar (if available)
                    add_filter( 'bp_core_fetch_avatar', '\Wpo\Services\BuddyPress_Service::fetch_buddy_press_avatar', 99, 2 );
                }

                // Only allow password changes for non-O365 users and only when already logged on to the system
                add_filter( 'show_password_fields',  '\Wpo\Core\Permissions_Helpers::show_password_fields', 10, 2 );
                add_filter( 'allow_password_reset', '\Wpo\Core\Permissions_Helpers::allow_password_reset', 10, 2 );
                    
                // Enable login message output
                add_filter( 'login_message', '\Wpo\Services\Error_Service::check_for_login_messages' );

                // Add custom wp query vars
                add_filter( 'query_vars', '\Wpo\Core\Url_Helpers::add_query_vars_filter' );

                if ( class_exists( '\Wpo\Services\Avatar_Service' ) ) {
                    // Replace avatar with O365 avatar (if available)
                    add_filter( 'get_avatar', '\Wpo\Services\Avatar_Service::get_O365_avatar', 99, 3 );
                }

                if ( class_exists( '\Wpo\Services\Mail_Notifications_Service' ) ) {
                    // Filter to change the new user email notification
                    add_filter( 'wp_new_user_notification_email', '\Wpo\Services\Mail_Notifications_Service::new_user_notification_email', 99, 3 );
                    add_action( 'wp_mail_failed', '\Wpo\Services\Mail_Notifications_Service::log_mail_error', 10, 1 );
                }

                if ( class_exists( '\Wpo\Services\Logout_Service' ) ) {
                    add_action( 'wp_logout', '\Wpo\Services\Logout_Service::logout_O365', 1, 0 );
                }

                // To support single sign out without user confirmation
                if ( class_exists( '\Wpo\Services\Redirect_Service' ) ) {
                    add_action( 'check_admin_referer', '\Wpo\Services\Redirect_Service::logout_without_confirmation', 10, 2 );
                }
            }
        }
    }