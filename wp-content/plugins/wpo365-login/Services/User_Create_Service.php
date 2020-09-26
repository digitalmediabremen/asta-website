<?php

    namespace Wpo\Services;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Core\Url_Helpers;
    use \Wpo\Core\Wpmu_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Options_Service;

    if ( !class_exists( '\Wpo\Services\User_Create_Service' ) ) {

        class User_Create_Service {

            /**
             * @since 11.0
             */
            public static function create_user( &$wpo_usr ) {

                if ( !Options_Service::get_global_boolean_var( 'create_and_add_users' ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> User not found and settings prevented creating a new user on-demand for user ' . $wpo_usr->preferred_username );
                    Authentication_Service::goodbye( Error_Service::USER_NOT_FOUND );
                    exit();
                }

                $usr_default_role = Options_Service::get_global_string_var( 'new_usr_default_role' );

                $userdata = array( 
                    'user_login'    => $wpo_usr->preferred_username,
                    'user_pass'     => wp_generate_password( 16, true, false ),
                    'role'          => $usr_default_role,
                );

                /**
                 * @since 9.4 
                 * 
                 * Optionally removing any user_register hooks as these more often than
                 * not interfer and cause unexpected behavior.
                 */

                $user_regiser_hooks = null;
                
                if ( Options_Service::get_global_boolean_var( 'skip_user_register_action' ) && isset( $GLOBALS[ 'wp_filter' ] ) && isset( $GLOBALS[ 'wp_filter' ][ 'user_register' ] ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Temporarily removing all filters for the user_register action to avoid interference' );
                    $user_regiser_hooks = $GLOBALS[ 'wp_filter' ][ 'user_register' ];
                    unset( $GLOBALS[ 'wp_filter' ][ 'user_register' ] );
                }

                // Insert in Wordpress DB
                $wp_usr_id = wp_insert_user( $userdata );

                if ( !empty( $GLOBALS[ 'wp_filter' ] ) && !empty( $user_regiser_hooks ) ) {
                    $GLOBALS[ 'wp_filter' ][ 'user_register' ] = $user_regiser_hooks;
                }

                if ( is_wp_error( $wp_usr_id ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> Could not create wp user. See next line for error information.' );
                    Log_Service::write_log( 'ERROR', $wp_usr_id );
                    Authentication_Service::goodbye( Error_Service::CHECK_LOG );
                    exit();
                }

                $wpo_usr->created = true;
                Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Created new user with ID ' . $wp_usr_id );

                if ( is_multisite() ) {
                    $blog_id = get_current_blog_id();
                    $mu_new_usr_default_role = Options_Service::get_global_string_var( 'mu_new_usr_default_role' );
                    
                    if ( !empty( $mu_new_usr_default_role ) && !is_user_member_of_blog( $wp_usr_id, $blog_id ) ) {
                        add_user_to_blog( $blog_id, $wp_usr_id->ID, $mu_new_usr_default_role );
                    }
                    else {
                        Log_Service::write_log( 'WARN', __METHOD__ . ' -> Could not add user ' . $wpo_usr->preferred_username . ' to current blog with ID ' . $blog_id );
                    }
                }
                
                Wpmu_Helpers::mu_delete_transient( 'wpo365_upgrade_dismissed' );
                Wpmu_Helpers::mu_set_transient( 'wpo365_user_created', date( 'd' ), 1209600 );

                return $wp_usr_id;
            }
            
        }
    }