<?php

    namespace Wpo\Sync;
        
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Core\Permissions_Helpers;
    use \Wpo\Services\Graph_Service;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;
    use \Wpo\Services\User_Create_Update_Service;
    use \Wpo\Services\User_Service;
    use \Wpo\Services\User_Details_Service;
    use \Wpo\Sync\Sync_Db;

    if ( !class_exists( '\Wpo\Sync\Sync_Manager' ) ) {

        class Sync_Manager {

            const OPTION_JOB_ID = 'wpo_sync_users_job_id';
            const OPTION_LAST_RUN = 'wpo_sync_users_job_last_run';

            private $sync_job_id = null;
            private $create_users = false;
            private $update_users = false;
            private $delete_users = false;
            private $only_internal_users = true;
            private $require_email_address = true;

            /**
             * Creates manually selected users in WordPress when the user clicked the "create users" button.
             * 
             * @since 3.0
             * 
             * @param array $upns array of selected user principal names
             */
            public static function create_users( $upns ) {

                if ( !is_array( $upns ) ) {

                    return new \WP_Error( '6000', 'Error occurred whilst trying to create users: argument exception' );
                }

                global $wpdb;

                $table_name = Sync_Db::get_user_sync_table_name();
                $upns_with_quotes = array_map( function( $value ) {

                    return '\''. $value . '\'';

                }, $upns );

                $upns_as_string = join( ',', $upns_with_quotes);
                $records = $wpdb->get_results( "SELECT * FROM $table_name WHERE upn IN ( $upns_as_string )", ARRAY_A );

                foreach ( $records as $record ) {

                    $graph_resource = User_Details_Service::get_graph_user( $record[ 'upn' ] );
                    $wpo_usr = User_Service::user_from_graph_user( $graph_resource );
                    $wp_id = User_Create_Update_Service::create_user( $wpo_usr, true );
                    $sync_job_status = empty( $wp_id ) ? 'error' : 'created';
                    $wpdb->update( $table_name, array( 'sync_job_status' => $sync_job_status, 'wp_id' => $wp_id ), array( 'upn' => $record[ 'upn' ] ) );
                    
                    Log_Service::write_log( 'DEBUG',  __METHOD__ . ' -> Status after adding new WordPress user for ' . $wpo_usr->preferred_username . ': ' . $sync_job_status );
                    
                    if ( empty( $wp_id ) ) {
                        continue;
                    }
                    
                    User_Create_Update_Service::update_user( $wp_id, $wpo_usr );
                }
            }

            /**
             * Deletes manually selected users  from WordPress when the user clicked the "delete users" button.
             * 
             * @since 3.0
             * 
             * @param array $wp_ids array of selected user ids
             */
            public static function delete_users( $wp_ids ) {

                if ( !is_array( $wp_ids ) ) {
                    return new \WP_Error( '6010', 'Error occurred whilst trying to delete users: argument exception' );
                }

                global $wpdb;

                $table_name = Sync_Db::get_user_sync_table_name();
                include_once( ABSPATH . 'wp-admin/includes/user.php' );

                foreach ( $wp_ids as $wp_id ) {
                    $user = get_user_by( 'ID', $wp_id );

                    if ( is_wp_error( $user ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Cannot delete user with ID ' . $wp_id . ' because user cannot be found' );
                        continue;
                    }

                    if ( Permissions_Helpers::user_is_admin( $user ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Not deleting user with ID ' . $wp_id . ' because user has administrator capabilities' );
                        $sync_job_status = 'is_admin';
                    }
                    else {
                        if ( Options_Service::get_global_boolean_var( 'user_sync_allow_delete' ) ) {
                            require_once( ABSPATH . 'wp-admin/includes/user.php' );
                            $sync_job_status =  wp_delete_user( intval( $wp_id ) ) ? 'deleted' : 'error';
                        }
                        else {
                            $wp_usr = \get_user_by( 'ID', $wp_id );
                            \update_user_meta( $wp_id, 'wpo365_active', 'deactivated' );
                            $sync_job_status = 'deleted';

                            // Remove all roles of the deactivated user
                            foreach ( $wp_usr->roles as $current_user_role ) {
                                $wp_usr->remove_role( $current_user_role );
                            }
                        }
                    }
                    
                    $wpdb->update( $table_name, array( 'sync_job_status' => $sync_job_status ), array( 'wp_id' => $wp_id ) );
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Status after deleting WordPress user with ID ' . $wp_id . ': ' . $sync_job_status );
                }
            }

            /**
             * Defines and schedules a new cron job (and delets existing ones) to starts the user 
             * synchronization by calling the first collection / page of 
             * users from Office 365 and then recursively creating new single events for each
             * next collection / page until finished. The results are stored in custom WordPress table.
             * 
             * @since 10.0
             * 
             * @return mixed(bool|WP_Error) true if synchronization was successful otherwise WP_Error
             */
            public static function schedule_sync() {

                if ( !isset( $_POST[ 'wpo_use_cron' ] ) || $_POST[ 'wpo_use_cron' ] != "on" ) {
                    Log_Service::write_log( 'WARN', 'Attempting to schedule user synchronization but could not find the flag for it' );
                    return;
                }

                try {
                    $now = time();
                    $day_of_the_week = intval( date( 'N', $now ) );
                    $sel_day_of_the_week = intval( intval( $_POST[ 'wpo_cron_schedule_on' ] ) );
                    $hours_of_the_day = intval( date( 'H', $now ) );
                    $sel_hours_of_the_day = intval( intval( $_POST[ 'wpo_cron_schedule_at' ] ) );
                    $recurrence = $sel_day_of_the_week < 7 ? 'wpo_weekly' : 'wpo_daily';
                    $diff_days = 0;
                    $diff_hours = 0;
                    
                    $seconds_in_an_hour = 60 * 60;
                    $seconds_in_a_day = 24 * $seconds_in_an_hour;
                    $treshold = 300;

                    if ( $sel_day_of_the_week < 7 ) {
                        
                        if ( $sel_day_of_the_week > $day_of_the_week ) {
                            $diff_days = $sel_day_of_the_week - $day_of_the_week;
                        }

                        if ( $sel_day_of_the_week < $day_of_the_week ) {
                            $diff_days = (7 + $sel_day_of_the_week - $day_of_the_week );
                        }
                    }

                    if ( $sel_hours_of_the_day > 0 && $sel_hours_of_the_day != $hours_of_the_day ) {
                        $diff_hours = $sel_hours_of_the_day - $hours_of_the_day;
                    }

                    if ( $diff_days === 0 && $sel_hours_of_the_day > 0 && $sel_hours_of_the_day < $hours_of_the_day ) {
                        $diff_days = 7;
                    }

                    $first_time = time() + ( $diff_days * $seconds_in_a_day ) + ( $diff_hours * $seconds_in_an_hour ) + $treshold;
                }
                catch (Exception $e) {
                    Log_Service::write_log( 'WARN', __METHOD__ . ' -> Atttempting to schedule user synchronization but failed to parse first time and recurrence values' );
                    return;
                }

                $settings = array();

                // read the posted variable
                $settings[ 'create_users' ] = isset( $_POST[ 'wpo_create_users' ] ) && $_POST[ 'wpo_create_users' ] == 'on' ? true : false;
                $settings[ 'update_users' ] = isset( $_POST[ 'wpo_update_users' ] ) && $_POST[ 'wpo_update_users' ] == 'on' ? true : false;
                $settings[ 'only_internal_users' ] = isset( $_POST[ 'wpo_internal_users' ] ) && $_POST[ 'wpo_internal_users' ] == 'on' ? true : false;
                $settings[ 'delete_users' ] = isset( $_POST[ 'wpo_delete_users' ] ) && $_POST[ 'wpo_delete_users' ] == 'on' ? true : false;
                $settings[ 'sync_user_id' ] = get_current_user_id();

                // Since v9.1 admins can define their own sync query
                $query = ltrim( Options_Service::get_global_string_var( 'user_sync_query' ), '/' );
                
                if ( empty( $query ) ) {
                    $query = 'myorganization/users?$filter=accountEnabled+eq+true+and+userType+eq+%27member%27&$top=10';
                }
                
                // Delete any existing schedules before creating a new one
                self::get_scheduled_events( true );
                $result = wp_schedule_event( $first_time, $recurrence, 'wpo_sync_users_start', [ "/$query", $settings ]);
                
                if ( true === $result ) {
                    Options_Service::add_update_option( 'use_cron', true );
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Scheduled a new cron job to synchronize users for the first time at ' . $first_time . ' and then ' . $recurrence . ' (result: ' . $result . ')' );
                }
                else {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' ->Could not schedule a new cron job to synchronize users for the first time at ' . $first_time . ' and then ' . $recurrence );
                }
            }

            /**
             * @since 10.0
             * 
             * @param $delete bool Whether or not the scheduled events should be deleted
             * @return array Collection of scheduled events
             */
            public static function get_scheduled_events( $delete = false ) {
                $cron_jobs = _get_cron_array();
                $wpo_sync_jobs = array();

                foreach ( $cron_jobs as $timestamp => $array_of_jobs ) {
                    
                    foreach ( $array_of_jobs as $hook => $jobs ) {

                        if ( $hook == 'wpo_sync_users_start' ) {
                            
                            foreach ( $jobs as $id => $job ) {
                                $wpo_sync_jobs[ $timestamp ] = $job;

                                if ( $delete ) {
                                    $nr_of_unscheduled_events = wp_clear_scheduled_hook( 'wpo_sync_users_start', $job[ 'args' ] );
                                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Unscheduled ' . $nr_of_unscheduled_events . ' cron jobs [hook: wpo_sync_users_start]');
                                }
                            }
                        }
                    }
                }

                if ( true === $delete ) {
                    Options_Service::add_update_option( 'use_cron', false );
                }

                return $wpo_sync_jobs;
            }

            /**
             * Starts the user synchronization by calling the first collection / page of 
             * users from Office 365 and then recursively continues until finished. The
             * results are stored in custom WordPress table.
             * 
             * @since 3.0
             * 
             * @return mixed(bool|WP_Error) true if synchronization was successful otherwise WP_Error
             */
            public static function sync_users() {

                // Manual synchronization will cancel scheduled synchronization
                self::get_scheduled_events( true );
                Options_Service::add_update_option( 'use_cron', false );

                $settings = array();

                // read the posted variable
                $settings[ 'create_users' ] = isset( $_POST[ 'wpo_create_users' ] ) && $_POST[ 'wpo_create_users' ] == 'on' ? true : false;
                $settings[ 'update_users' ] = isset( $_POST[ 'wpo_update_users' ] ) && $_POST[ 'wpo_update_users' ] == 'on' ? true : false;
                $settings[ 'only_internal_users' ] = isset( $_POST[ 'wpo_internal_users' ] ) && $_POST[ 'wpo_internal_users' ] == 'on' ? true : false;
                $settings[ 'delete_users' ] = isset( $_POST[ 'wpo_delete_users' ] ) && $_POST[ 'wpo_delete_users' ] == 'on' ? true : false;
                $settings[ 'sync_user_id' ] = get_current_user_id();

                // Since v9.1 admins can define their own sync query
                $query = ltrim( Options_Service::get_global_string_var( 'user_sync_query' ), '/' );
                
                if ( empty( $query ) ) {
                    $query = 'myorganization/users?$filter=accountEnabled+eq+true+and+userType+eq+%27member%27&$top=10';
                }

                $fetch_result = self::fetch_users( "/$query", $settings );
            }

            /**
             * Fetches users from Microsoft Graph using the query supplied. Can be called recursively.
             *  
             * @since 3.0
             * 
             * @param string $graph_query query to call Microsoft Graph
             */
            public static function fetch_users( $graph_query, $settings, $init = true ) {

                if ( true === $init ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> A new user synchronization job is starting and the old job data will be deleted' );
                    self::delete_job_data();
                    $settings[ 'sync_job_id' ] = uniqid();
                    update_site_option( self::OPTION_JOB_ID, $settings[ 'sync_job_id' ] );
                    update_site_option( self::OPTION_LAST_RUN, time() );
                }

                $settings[ 'create_users' ] == 1 ? $settings[ 'create_users' ] = true : false;
                $settings[ 'update_users' ] == 1 ? $settings[ 'update_users' ] = true : false;
                $settings[ 'only_internal_users' ] == 1 ? $settings[ 'only_internal_users' ] = true : false;
                $settings[ 'delete_users' ] == 1 ? $settings[ 'delete_users' ] = true : false;

                self::process_fetch_result( Graph_Service::fetch( $graph_query, 'GET', false, array( 'Accept: application/json;odata.metadata=minimal' ) ), $settings );
            }

            /**
             * Processes a collection of Office 365 users returned from the corresponding Microsoft Graph query. Recursively
             * calls for the next collection when finished processing with the current collection.
             * 
             * @since 3.0
             * 
             * @param stdClass  $response   Response returned by the MS Graph client that needs to be processed
             * @param array     $settings   Sync settings e.g. create users, delete users etc.
             * 
             * @return void
             */
            private static function process_fetch_result( $response, $settings ) {

                if ( !Graph_Service::is_fetch_result_ok( $response, 'Could not fetch users for synchronization from the graph' ) ) {
                    return;
                }

                if ( !is_array( $response[ 'payload' ][ 'value' ] ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' ->No users returned from the graph.' );
                    return;
                }

                foreach ( $response[ 'payload' ][ 'value' ] as $o365_user ) {

                    // transform user to our own internal format
                    $wpo_usr = User_Service::user_from_graph_user( $o365_user );

                    // user without upn cannot be processed
                    if ( !isset( $wpo_usr->upn ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> O365 user without userPrincipalName' );
                        continue;
                    }

                    if ( true === $settings[ 'only_internal_users' ] && false !== stripos( $wpo_usr->upn, '#ext#' ) ) {
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> User is not an internal user: ' . $wpo_usr->preferred_username );
                        continue;
                    }
                    
                    $wp_usr = User_Service::try_get_user_by( $wpo_usr->upn, $wpo_usr->email );

                    $action_performed = 0;

                    // found a new Office 365 user
                    if ( null === $wp_usr ) {

                        if ( $settings[ 'create_users' ] ) {

                            $wp_id = User_Create_Update_Service::create_user( $wpo_usr, true );
                    
                            if ( empty( $wp_id ) ) continue;

                            $action_performed = 1;
                            $wp_usr = \get_user_by( 'ID', $wp_id );

                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Created new WordPress user for ' . $wpo_usr->preferred_username );
                        }
                    }

                    // update new and / or existing wp users with group and user info
                    if ( null !== $wp_usr ) {
                        
                        $action_performed = $action_performed == 0 ? 2 : $action_performed;

                        if ( $action_performed == 1 || $settings[ 'update_users' ] ) {

                            // Update role(s) assignment and extra user details
                            User_Create_Update_Service::update_user( $wp_usr->ID, $wpo_usr );
                            $user_id = $wp_usr->ID;
                        }

                        // tag wp user with sync job ID
                        update_user_meta(
                            $wp_usr->ID,
                            self::OPTION_JOB_ID,
                            $settings[ 'sync_job_id' ] );
                    }

                    // remember new user
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> WordPress user found for ' . $o365_user[ 'userPrincipalName' ] );

                    // log the new Office 365 user in our table
                    global $wpdb;

                    $table_name = Sync_Db::get_user_sync_table_name();

                    switch( $action_performed ) {
                        case 1:
                            $record_type = 'new_domain_user';
                            $sync_job_status = $settings[ 'create_users' ] ? 'created' : 'logged';
                            break;
                        case 2:
                            $record_type = 'existing_domain_user';
                            $sync_job_status = $settings[ 'update_users' ] ? 'updated' : 'logged';
                            break;
                        default:
                            $record_type = 'new_domain_user';
                            $sync_job_status = 'logged';
                    }

                    $wpdb->insert( 
                        $table_name, 
                        array( 
                            'wp_id'             => NULL !== $wp_usr ? $wp_usr->ID : -1,
                            'upn'               => $wpo_usr->upn, 
                            'first_name'        => $wpo_usr->first_name,
                            'last_name'         => $wpo_usr->last_name,
                            'full_name'         => $wpo_usr->full_name,
                            'email'             => $wpo_usr->email,
                            'sync_job_id'       => $settings[ 'sync_job_id' ],
                            'name'              => $wpo_usr->name,
                            'sync_job_status'   => $sync_job_status,
                            'record_type'       => $record_type,
                        ) 
                    );
                }

                // continue with the next batch of users
                if ( array_key_exists( '@odata.nextLink', $response[ 'payload' ] ) ) {

                    $graph_version = Options_Service::get_global_string_var( 'graph_version' );
                    $graph_version = empty( $graph_version ) || $graph_version == 'current' ? 'v1.0' : $graph_version;
                    $graph_url = 'https://graph.microsoft.com/' . $graph_version;

                    if ( Options_Service::get_global_boolean_var( 'use_cron' ) ) {
                        $next_link = str_replace( $graph_url, '', $response[ 'payload' ][ '@odata.nextLink' ] );
                        $result = wp_schedule_single_event( time() + 60, 'wpo_sync_users', [ $next_link, $settings, false ]);
                        Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Next event for hook "wpo_sync_users" has been scheduled' );
                        return;
                    }

                    ob_start();
                    include( $GLOBALS[ 'WPO_CONFIG' ][ 'extension_dir' ] . '/templates/fetch-users.php' );
                    echo ob_get_clean();
                    
                    exit();
                }
                else {
                    // finally read all the untagged users after the current run and persist
                    self::untagged_users( $settings );

                    // And inform the site admin that user sync has completed
                    self::sync_completed_notification();
                }
            }

            /**
             * Queries all users for the current job tag and if not found will add those users
             * to the user sync table as untagged users (no matching Office 365 user was found).
             * 
             * @since 3.0
             * 
             * @return void
             */
            private static function untagged_users( $settings ) {

                $untagged_users = new \WP_User_Query(
                    array(
                        // 'fields'     => array( 'ID', 'user_login', 'display_name', 'user_email' ),
                        'meta_query' => array(
                            'relation' => 'OR',
                            array(
                                'key'       => self::OPTION_JOB_ID,
                                'value'     => $settings[ 'sync_job_id' ],
                                'compare'   => '!=',
                            ),
                            array(
                                'key'       => self::OPTION_JOB_ID,
                                'compare'   => 'NOT EXISTS',
                            )
                        )
                    )
                );

                global $wpdb;

                $table_name = Sync_Db::get_user_sync_table_name();
                $sync_job_status = 'logged';
            
                // and fill it with the results of the last run
                $untagged_users = $untagged_users->get_results();
                $wp_ids = array();

                foreach ( $untagged_users as $untagged_user ) {

                    $wpdb->insert( 
                        $table_name, 
                        array( 
                            'wp_id'             => isset( $untagged_user->ID ) ? $untagged_user->ID : '', 
                            'upn'               => isset( $untagged_user->user_login ) ? $untagged_user->user_login : '', 
                            'first_name'        => '', // defined in user meta
                            'last_name'         => '', // defined in user meta
                            'full_name'         => isset( $untagged_user->display_name ) ? $untagged_user->display_name : '',
                            'email'             => isset( $untagged_user->user_email ) ? $untagged_user->user_email : '',
                            'sync_job_id'       => $settings[ 'sync_job_id' ],
                            'name'              => isset( $untagged_user->user_login ) ? $untagged_user->user_login : '',
                            'sync_job_status'   => $sync_job_status,
                            'record_type'       => 'untagged_user',
                        ) 
                    );

                    $wp_ids[] = $untagged_user->ID;
                }

                // finally commit deletion of WP users if requested
                if ( true === $settings[ 'delete_users' ] ) {

                    self::delete_users( $wp_ids );
                }
            }

            /**
             * Helper method to truncate the table and remove the job id and last run time.
             * 
             * @since 3.0
             * 
             * @return void
             */
            public static function delete_job_data() {

                delete_site_option( self::OPTION_JOB_ID );
                delete_site_option( self::OPTION_LAST_RUN );
                
                global $wpdb;

                $table_name = Sync_Db::get_user_sync_table_name();
                $wpdb->query( "TRUNCATE TABLE $table_name" );
            }

            /**
             * Sends the admin of the site an email to inform that user synchronization has completed.
             * 
             * @since 7.11
             * 
             * @return void
             */
            private static function sync_completed_notification() {

                // The blogname option is escaped with esc_html on the way into the database in sanitize_option
                // we want to reverse this for the plain text arena of emails.
                $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

                /* translators: %s: site title */
                $message = sprintf( __( 'User Sync completed on your site %s.', 'wpo365-login' ), $blogname ) . "\r\n\r\n";
        
                $sync_completed_email_admin = array(
                    'to'      => get_option( 'admin_email' ),
                    'subject' => __( '[%s] User Sync completed', 'wpo365-login' ),
                    'message' => $message,
                    'headers' => '',
                );

                @wp_mail(
                    $sync_completed_email_admin['to'],
                    wp_specialchars_decode( sprintf( $sync_completed_email_admin['subject'], $blogname ) ),
                    $sync_completed_email_admin['message'],
                    $sync_completed_email_admin['headers']
                );
            }
        }
    }