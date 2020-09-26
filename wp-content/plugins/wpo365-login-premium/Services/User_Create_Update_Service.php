<?php

    namespace Wpo\Services;
    
    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();

    use \Wpo\Core\Url_Helpers;
    use \Wpo\Services\Log_Service;
    use \Wpo\Services\Mail_Notifications_Service;
    use \Wpo\Services\Options_Service;

    if ( !class_exists( '\Wpo\Services\User_Create_Update_Service' ) ) {

        class User_Create_Update_Service { 

            /**
             * @since 11.0
             */
            public static function create_user( &$wpo_usr, $skip_check = false ) {

                if ( !$skip_check && !Options_Service::get_global_boolean_var( 'create_and_add_users' ) ) {
                    Log_Service::write_log( 'ERROR', __METHOD__ . ' -> User not found and settings prevented creating a new user on-demand');
                    Authentication_Service::goodbye( Error_Service::USER_NOT_FOUND );
                    exit();
                }
                
                $userdata = array( 
                    'user_login'    => $wpo_usr->preferred_username,
                    'user_pass'     => wp_generate_password( 16, true, false ),
                    'display_name'  => $wpo_usr->full_name,
                    'user_email'    => $wpo_usr->email,
                    'first_name'    => $wpo_usr->first_name,
                    'last_name'     => $wpo_usr->last_name,
                    'role'          => '', // role will be added separately
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

                if ( \class_exists( '\Wpo\Services\User_Role_Service' ) && \method_exists( '\Wpo\Services\User_Role_Service', 'update_user_roles' ) ) {
                    \Wpo\Services\User_Role_Service::update_user_roles( $wp_usr_id, $wpo_usr );
                }

                // Try and send new user email
                if ( Options_Service::get_global_boolean_var( 'new_usr_send_mail' ) ) {
                    $notify = Options_Service::get_global_boolean_var( 'new_usr_send_mail_admin_only' )
                        ? 'admin'
                        : 'both';
                    Mail_Notifications_Service::new_user_notification( $wp_usr_id, null, $notify );
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Sent new user notification' );
                }
                else {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Did not sent new user notification' );
                }
                
                self::wpmu_add_user_to_blog( $wp_usr_id, $wpo_usr->preferred_username );

                return $wp_usr_id;
            }

            public static function update_user( $wp_usr_id, $wpo_usr ) {

                // Don't update roles (again) when the user has just been created
                if ( !$wpo_usr->created ) {

                    if ( \class_exists( '\Wpo\Services\User_Role_Service' ) && \method_exists( '\Wpo\Services\User_Role_Service', 'update_user_roles' ) ) {
                        \Wpo\Services\User_Role_Service::update_user_roles( $wp_usr_id, $wpo_usr );
                    }
                }

                // Update Avatar
                if ( Options_Service::get_global_boolean_var( 'use_avatar' ) && class_exists( '\Wpo\Services\Avatar_Service' ) ) {
                    $default_avatar = get_avatar( $wp_usr_id );
                }

                // Update custom fields
                if ( Options_Service::get_global_boolean_var( 'graph_user_details' ) && class_exists( '\Wpo\Services\User_Custom_Fields_Service' ) && method_exists( '\Wpo\Services\User_Custom_Fields_Service', 'update_custom_fields' ) ) {
                    \Wpo\Services\User_Custom_Fields_Service::update_custom_fields( $wp_usr_id, $wpo_usr );
                }

                // Update default WordPress user fields
                self::update_wp_user( $wp_usr_id, $wpo_usr );

                // WPMU -> Add user to current blog
                self::wpmu_add_user_to_blog( $wp_usr_id, $wpo_usr->preferred_username );
            }

            /**
             * @since 11.0
             */
            private static function update_wp_user( $wp_usr_id, $wpo_usr ) {

                if ( empty( $wpo_usr->graph_resource ) ) {
                    Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Cannot update WP user fields because the graph resource has not been retrieved' );
                    return;
                }

                // Update "core" WP_User fields
                $wp_user_data = array( 'ID' => $wp_usr_id );

                if ( !empty( $wpo_usr->graph_resource[ 'mail' ] ) ) {
                    $wp_user_data[ 'user_email' ] = $wpo_usr->graph_resource[ 'mail' ];
                }

                if ( !empty( $wpo_usr->graph_resource[ 'givenName' ] ) ) {
                    $wp_user_data[ 'first_name' ] = $wpo_usr->graph_resource[ 'givenName' ];
                }

                if ( !empty( $wpo_usr->graph_resource[ 'surname' ] ) ) {
                    $wp_user_data[ 'last_name' ] = $wpo_usr->graph_resource[ 'surname' ];
                }

                if ( !empty( $wpo_usr->graph_resource[ 'displayName' ] ) ) {
                    $wp_user_data[ 'display_name' ] = $wpo_usr->graph_resource[ 'displayName' ];
                }

                wp_update_user( $wp_user_data );
            }

            /**
             * @since 11.0
             */
            private static function wpmu_add_user_to_blog( $wp_usr_id, $preferred_user_name ) {
                
                if ( is_multisite() ) {
                    $blog_id = get_current_blog_id();
                    $mu_new_usr_default_role = Options_Service::get_global_string_var( 'mu_new_usr_default_role' );
                    
                    if ( !empty( $mu_new_usr_default_role ) ) {
                        if ( !is_user_member_of_blog( $wp_usr_id, $blog_id ) ) {
                            add_user_to_blog( $blog_id, $wp_usr_id, $mu_new_usr_default_role );
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Added user to blog with ID ' . $blog_id );
                        }
                        else {
                            Log_Service::write_log( 'DEBUG', __METHOD__ . ' -> Skipped adding user to blog with ID ' . $blog_id . ' because user already added' );
                        }
                    }
                    else {
                        Log_Service::write_log( 'WARN', __METHOD__ . ' -> Could not add user ' . $preferred_user_name . ' to current blog with ID ' . $blog_id . ' because the default role for the subsite is not valid' );
                    }
                }
            }
        }
    }